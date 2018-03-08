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

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Date\Various as VDate;
use PH7\Framework\File\Import;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Parse\Emoticon;
use PH7\Framework\Session\Session;

class Chat implements \JsonSerializable
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
}

class MessengerAjax extends PermissionCore
{
    /** @var HttpRequest */
    private $_oHttpRequest;

    /** @var MessengerModel */
    private $_oMessengerModel;

    /** @var AvatarDesignCore */
    private $avatarDesign;

    /** @var string */
    private $loggedinUser;

    /** @var Chat[] */
    private $chats;

    public function __construct($username)
    {
        parent::__construct();

        Import::pH7App(PH7_SYS . PH7_MOD . 'im.models.MessengerModel');

        $this->_oHttpRequest = new HttpRequest;
        $this->_oMessengerModel = new MessengerModel;
        $this->avatarDesign = new AvatarDesignCore();
        $this->loggedinUser = $username;

        if (!isset($_SESSION['messenger_chats'])) {
            $_SESSION['messenger_chats'] = [];
        }
        $this->chats = &$_SESSION['messenger_chats'];

        switch ($this->_oHttpRequest->get('act')) {
            case 'startsession':
                $this->startSession();
                break;

            case 'heartbeat':
                $this->heartbeat();
                break;

            case 'send':
                $this->send();
                break;

            case 'close':
                $this->close();
                break;

            default:
                Http::setHeadersByCode(400);
                exit('Bad Request Error!');
        }
    }

    /**
     * @deprecated
     * @throws Framework\Http\Exception
     */
    protected function _heartbeat()
    {
        $sFrom = $_SESSION['messenger_username'];
        $sTo = !empty($_SESSION['messenger_username_to']) ? $_SESSION['messenger_username_to'] : 0;

        $oQuery = $this->_oMessengerModel->select($sFrom);
        $sItems = '';

        foreach ($oQuery as $oData) {
            $sFrom = escape($oData->fromUser, true);
            $sSent = escape($oData->sent, true);
            $sMsg = $this->sanitize($oData->message);
            $sMsg = Emoticon::init($sMsg, false);

            if (!isset($_SESSION['messenger_openBoxes'][$sFrom]) && isset($_SESSION['messenger_history'][$sFrom]))
                $sItems = $_SESSION['messenger_history'][$sFrom];

            $sItems .= $this->setJsonContent(['user' => $sFrom, 'msg' => $sMsg]);

            if (!isset($_SESSION['messenger_history'][$sFrom])) {
                $_SESSION['messenger_history'][$sFrom] = '';
            }

            $_SESSION['messenger_history'][$sFrom] .= $this->setJsonContent(['user' => $sFrom, 'msg' => $sMsg]);

            unset($_SESSION['messenger_boxes'][$sFrom]);
            $_SESSION['messenger_openBoxes'][$sFrom] = $sSent;
        }

        if (!empty($_SESSION['messenger_openBoxes'])) {
            foreach ($_SESSION['messenger_openBoxes'] as $sBox => $sTime) {
                if (!isset($_SESSION['messenger_boxes'][$sBox])) {
                    $iNow = time() - strtotime($sTime);
                    $sMsg = t('Sent at %0%', VDate::textTimeStamp($sTime));
                    if ($iNow > 180) {
                        $sItems .= $this->setJsonContent(['status' => '2', 'user' => $sBox, 'msg' => $sMsg]);

                        if (!isset($_SESSION['messenger_history'][$sBox]))
                            $_SESSION['messenger_history'][$sBox] = '';

                        $_SESSION['messenger_history'][$sBox] .= $this->setJsonContent(['status' => '2', 'user' => $sBox, 'msg' => $sMsg]);
                        $_SESSION['messenger_boxes'][$sBox] = 1;
                    }
                }
            }
        }

//        if (!$this->isOnline($sFrom)) {
//            $sMsg = t('You must have the ONLINE status in order to speak instantaneous.');
//            $sItems .= $this->setJsonContent(['status' => '2', 'user' => '', 'msg' => $sMsg]);
//        } elseif ($sTo !== 0 && !$this->isOnline($sTo)) {
//            $sMsg = '<small><em>' . t("%0% is offline. Send a <a href='%1%'>Private Message</a> instead.", $sTo, Uri::get('mail', 'main', 'compose', $sTo)) . '</em></small>';
//            $sItems .= $this->setJsonContent(['status' => '2', 'user' => '', 'msg' => $sMsg]);
//        } else {
            $this->_oMessengerModel->update($sFrom, $sTo);
//        }

        if ($sItems != '') {
            $sItems = substr($sItems, 0, -1);
        }

        Http::setContentType('application/json');
        echo '{"items": [' . $sItems . ']}';
        exit;
    }

    protected function startSession()
    {
        if (empty($this->chats)) {
            $messages = $this->_oMessengerModel->selectAllRead($this->loggedinUser);
            foreach ($messages as $m) {
                if ($m->fromUser == $this->loggedinUser) {
                    $chatUser = $m->toUser;
                } elseif ($m->toUser == $this->loggedinUser) {
                    $chatUser = $m->fromUser;
                } else {
                    // should never happen
                    continue;
                }
                $chat = $this->getChatWith($chatUser);
                $msg = new Message($m->fromUser, $m->toUser, $m->message, $m->sent);
                $chat->add($msg);
            }
        }
        Http::setContentType('application/json');
        echo json_encode([
            'user' => $this->loggedinUser,
            'chats' => $this->chats,
        ]);
        exit;
    }

    private function getChatWith($username)
    {
        if (!isset($this->chats[$username])) {
            $avatarUrl = $this->avatarDesign->getUserAvatar($username, '', 64, false);
            $this->chats[$username] = new Chat($username, $avatarUrl);
        }
        return $this->chats[$username];
    }

    protected function send()
    {
        $toUser = $this->_oHttpRequest->post('to');
        $sMsg = $this->_oHttpRequest->post('message');

        $iSenderId = (new Session)->get('member_id');

        $sMsgTransform = $this->sanitize($sMsg);
        $sMsgTransform = Emoticon::init($sMsgTransform, false);

        $chat = $this->getChatWith($toUser);

        if (UserCore::countCredits($iSenderId) <= 0) {
            $sMsgTransform = '<small><em>' . t("You have not enough coins to send messages. Go to <a href='%1%'>shop</a> and purchase some coins.", Uri::get('payment', 'coins', 'index', '')) . '</em></small>';
        } else {
            $this->_oMessengerModel->insert($this->loggedinUser, $toUser, $sMsg, (new CDateTime)->get()->dateTime('Y-m-d H:i:s'));
            $oUserModel = new UserCoreModel;
            $oUserModel->decreaseCredits($iSenderId);
        }

        $msg = new Message($this->loggedinUser, $toUser, $sMsgTransform, date('Y-m-d H:i:s'));
        $chat->add($msg);

        Http::setContentType('application/json');
        echo json_encode([
            'coins' => UserCore::countCredits($iSenderId),
            'message' => $msg,
        ]);
        exit;
    }

    protected function heartbeat()
    {
        $messages = $this->_oMessengerModel->selectUnread($this->loggedinUser);
        $latestChats = [];
        $messageIds = [];
        foreach ($messages as $m) {
            if ($m->toUser == $this->loggedinUser) {
                $chatUser = $m->fromUser;
            } else {
                // should never happen
                continue;
            }
            $msg = new Message($m->fromUser, $m->toUser, $m->message, $m->sent);

            $chat = $this->getChatWith($chatUser);
            $chat->add($msg);
            $messageIds[] = $m->messengerId;

            if (!isset($latestChats[$chatUser])) {
                $avatarUrl = $this->avatarDesign->getUserAvatar($chatUser, '', 64, false);
                $latestChats[$chatUser] = new Chat($chatUser, $avatarUrl);
            }
            $latestChats[$chatUser]->add($msg);

        }
        $this->_oMessengerModel->markAsRead($messageIds);

        Http::setContentType('application/json');
        echo json_encode([
            'user' => $this->loggedinUser,
            'chats' => $latestChats,
        ]);
        exit;
    }

    protected function close()
    {
        unset($_SESSION['messenger_openBoxes'][$this->_oHttpRequest->post('box')]);
        exit(1);
    }

    protected function isOnline($sUsername)
    {
        $oUserModel = new UserCoreModel;
        $iProfileId = $oUserModel->getId(null, $sUsername);
        $bIsOnline = $oUserModel->isOnline($iProfileId, DbConfig::getSetting('userTimeout'));
        unset($oUserModel);
        return $bIsOnline;
    }

    protected function sanitize($sText)
    {
        $sText = escape($sText, true);
        $sText = str_replace("\n\r", "\n", $sText);
        $sText = str_replace("\r\n", "\n", $sText);
        $sText = str_replace("\n", "<br>", $sText);

        return $sText;
    }
}

// Go only if the user is logged
if (UserCore::auth()) {
    $oSession = new Session; // Go start_session() function.
    $loggedInUsername = $oSession->get('member_username');
    unset($oSession);
    new MessengerAjax($loggedInUsername);
}
