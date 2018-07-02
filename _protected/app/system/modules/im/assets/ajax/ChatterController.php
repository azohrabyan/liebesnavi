<?php
/**
 * @title          Chat Messenger Ajax
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / IM / Asset / Ajax
 * @version        1.6
 * @required       PHP 5.4 or higher.
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use Exception;

class ChatterController
{
    /** @var Chatter[] $chatters */
    private $chatters;

    /** @var int */
    private $chatterId;

    /** @var ChatterModel $chatterModel */
    private $chatterModel;

    /** @var MessengerModel $chatterModel */
    private $messengerModel;

    public function __construct($loggedInChatterId, $chatterModel, $messengerModel)
    {
        $this->chatterId = $loggedInChatterId;
        $this->chatterModel = $chatterModel;
        $this->messengerModel = $messengerModel;

        $this->chatters[$this->chatterId] = new Chatter($this->chatterId, true);

        $chattersChatsRows = $this->chatterModel->getAllChattersChats();
        foreach ($chattersChatsRows as $row) {
            $chatterId = $row->chatter_id;
            if (!isset($this->chatters[$chatterId])) {
                $ch = new Chatter($chatterId, false);
                $this->chatters[$chatterId] = $ch;
            }
            $this->chatters[$chatterId]->addChat($row->fake_user, $row->chat_partner);
        }
    }

    /**
     * Fetches all messages per fake user for current chatter
     * Should be called only first time on session start
     */
    public function start()
    {
        foreach ($this->chatters[$this->chatterId]->getChats() as $chat) {
            /** @var Chat $chat */
            $messages = $this->messengerModel->selectFromToRead($chat->getFakeUser(), $chat->getChatPartner());
            foreach ($messages as $m) {
                $msg = new Message($m->fromUser, $m->toUser, $m->message, $m->sent);
                $chat->add($msg);
            }
        }
        return $this->chatters[$this->chatterId];
    }

    /**
     * Fetches all unread messages per fake user for current chatter
     */
    public function heartbeat()
    {
        $messages = $this->messengerModel->selectUnreadForFakes();
        foreach ($messages as $m) {
            $msg = new Message($m->fromUser, $m->toUser, $m->message, $m->sent);
            $this->send($msg, $m->toUser);
        }

        $messageIds = [];
        foreach ($this->chatters[$this->chatterId]->getChats() as $chat) {
            /** @var Chat $chat */
            $messages = $this->messengerModel->selectFromToUnread($chat->getFakeUser(), $chat->getChatPartner());
            foreach ($messages as $m) {
                $msg = new Message($m->fromUser, $m->toUser, $m->message, $m->sent);
                $chat->add($msg);
                $messageIds[] = $m->messengerId;
            }
        }
        $this->messengerModel->markAsRead($messageIds);

        return $this->chatters[$this->chatterId];
    }

    public function send(Message $msg, $fakeUser)
    {
        if (!$this->chatExists($msg->getFrom(), $msg->getTo())) {
            $chatter = $this->findFreeChatter();
            if ($chatter) {
                return $this->addChat($chatter, $msg, $fakeUser);
            }
        }
        return false;
    }

    /**
     * @param $from
     * @param $to
     * @return bool
     */
    private function chatExists($from, $to)
    {
        foreach ($this->chatters as $ch) {
            if ($ch->hasChat($from, $to)) {
                return true;
            }
        }
        return false;
    }

    private function findFreeChatter()
    {
        $minChatsOwner = null;
        $minChats = 9999999;
        foreach ($this->chatters as $ch) {
            if ($ch->isOnline()) {
                if ($ch->getChatsCount() < $minChats) {
                    $minChats = $ch->getChatsCount();
                    $minChatsOwner = $ch;
                }
            }
        }
        return $minChatsOwner;
    }

    public function addChat(Chatter $chatter, Message $msg, $fakeUser)
    {
        /**
         * Make a db insert here to the chatter_chats table (does not exists yet).
         * The [fake_user, user] pair will be a unique key in that table.
         * If the key already exists (another session already reserved that key),
         * an exception will be thrown and nothing happens.
         * If the key does not exists, it will be inserted.
         */
        if ($msg->getTo() == $fakeUser) {
            $chatPartner = $msg->getFrom();
        } elseif ($msg->getFrom() == $fakeUser) {
            $chatPartner = $msg->getTo();
        } else {
            // no fake user found
            return false;
        }
        try {
            $this->insertDb($chatter, $fakeUser, $chatPartner);
            $chatter->addChat($fakeUser, $chatPartner);
        } catch (Exception $ex) {
            return false;
        }
        return true;
    }

    /**
     * @param Chatter $chatter
     * @param string $fakeUser
     * @param string $chatPartner
     */
    private function insertDb($chatter, $fakeUser, $chatPartner)
    {
        $this->chatterModel->createChat($chatter->getId(), $fakeUser, $chatPartner, date("Y-m-d H:i:s"));
    }
}

class Chatter implements \JsonSerializable
{
    /** @var Chat[] $chats */
    private $chats = [];

    /** @var bool $isOnline */
    private $isOnline;

    /** @var int $chatterId */
    private $chatterId;

    public function __construct($chatterId, $isOnline)
    {
        $this->chatterId = $chatterId;
        $this->isOnline = $isOnline;
    }

    public function getId()
    {
        return $this->chatterId;
    }

    /**
     * @param $from
     * @param $to
     * @return bool
     */
    public function hasChat($from, $to)
    {
        foreach ($this->chats as $ch) {
            if ($ch->matchPair($from, $to)) {
                return true;
            }
        }
        return false;
    }

    public function isOnline()
    {
        return $this->isOnline;
    }

    public function getChatsCount()
    {
        return count($this->chats);
    }

    public function addChat($fakeUser, $chatPartner)
    {
        $this->chats[] = new Chat($fakeUser, $chatPartner);
    }

    public function getChats()
    {
        return $this->chats;
    }
    public function jsonSerialize()
    {
        return [
            'chats' => $this->chats,
        ];
    }
}

class Chat implements \JsonSerializable
{
    /** @var string */
    private $fakeUser;
    /** @var string */
    private $chatPartner;

    /** @var UserMessages $messages */
    private $messages;

    public function __construct($fakeUser, $chatPartner)
    {
        $this->fakeUser = $fakeUser;
        $this->chatPartner = $chatPartner;
        $this->messages = new UserMessages($fakeUser, '');
    }

    public function matchPair($from, $to)
    {
        return ($from == $this->fakeUser && $to == $this->chatPartner) ||
            ($from == $this->chatPartner && $to == $this->fakeUser);
    }

    public function add(Message $msg)
    {
        $this->messages->add($msg);
    }

    public function getFakeUser()
    {
        return $this->fakeUser;
    }

    public function getChatPartner()
    {
        return $this->chatPartner;
    }

    public function jsonSerialize()
    {
        return [
            'fake' => $this->fakeUser,
            'partner' => $this->chatPartner,
            'messages' => $this->messages,
        ];
    }
}

class UserMessages implements \JsonSerializable
{
    private $user;
    private $avatarUrl;
    private $messages = [];

    public function __construct($user, $avatarUrl)
    {
        $this->user = $user;
        $this->avatarUrl = $avatarUrl;
    }
    public function add($m)
    {
        $this->messages[] = $m;
    }

    public function jsonSerialize()
    {
        return [
            'user' => $this->user,
            'avatar_url' => $this->avatarUrl,
            'messages' => $this->messages,
        ];
    }
}

class Message implements \JsonSerializable
{
    private $from;
    private $to;
    private $message;
    private $sentAt;

    public function __construct($from, $to, $message, $sentAt)
    {
        $this->from = $from;
        $this->to = $to;
        $this->message = $message;
        $this->sentAt = $sentAt;
    }

    public function jsonSerialize()
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'message' => $this->message,
            'sentAt' => $this->sentAt,
        ];
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }
}
