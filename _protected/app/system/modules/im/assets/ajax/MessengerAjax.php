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

include_once(__DIR__ . '/ChatterIMController.php');

class MessengerAjax extends PermissionCore
{
    /** @var HttpRequest */
    private $_oHttpRequest;

    /** @var ChatterModel */
    private $chatterModel;

    /** @var MessengerModel */
    private $_oMessengerModel;

    /** @var AvatarDesignCore */
    private $avatarDesign;

    /** @var string */
    private $loggedinUser;

    /** @var int $chatterId */
    private $chatterId;
    /** @var bool $isChatter */
    private $isChatter;

    /** @var UserMessages[] $chat Represents chats of logged in user, the array keys are usernames of chat partners */
    private $chats;

    /** @var ChatterIMController  */
    private $chatterController = null;

    public function __construct($username, $chatterId = 0)
    {
        parent::__construct();

        Import::pH7App(PH7_SYS . PH7_MOD . 'im.models.MessengerModel');

        $this->_oHttpRequest = new HttpRequest;
        $this->chatterModel = new ChatterModel;
        $this->_oMessengerModel = new MessengerModel;
        $this->avatarDesign = new AvatarDesignCore();
        $this->loggedinUser = $username;
        $this->chatterId = $chatterId;
        $this->isChatter = $chatterId != 0;

        if ($this->isChatter) {
            $this->chatterController = new ChatterIMController($chatterId, $this->chatterModel, $this->_oMessengerModel);
        }

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

    protected function startSession()
    {
        Http::setContentType('application/json');
        if ($this->isChatter) {
            die(json_encode($this->chatterController->start()));
        }
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
            $this->chats[$username] = new UserMessages($username, $avatarUrl);
        }
        return $this->chats[$username];
    }

    protected function send()
    {
        $toUser = $this->_oHttpRequest->post('to');
        $sMsg = $this->_oHttpRequest->post('message');

        $sMsgTransform = $this->sanitize($sMsg);
        $sMsgTransform = Emoticon::init($sMsgTransform, false);

        if ($this->isChatter) {
            $fromUser = $this->_oHttpRequest->post('from');
            $msg = new Message($fromUser, $toUser, $sMsgTransform, date('Y-m-d H:i:s'));
            $this->_oMessengerModel->insert($fromUser, $toUser, $sMsg, (new CDateTime)->get()->dateTime('Y-m-d H:i:s'), $this->chatterId);
            Http::setContentType('application/json');
            die(json_encode([$msg]));
        }

        $iSenderId = (new Session)->get('member_id');

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
        if ($this->isChatter) {
            Http::setContentType('application/json');
            die(json_encode($this->chatterController->heartbeat()));
        }

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
                $latestChats[$chatUser] = new UserMessages($chatUser, $avatarUrl);
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
        if ($this->isChatter) {
            die(json_encode($this->chatterController->close($this->_oHttpRequest->post('fake'), $this->_oHttpRequest->post('partner'))));
        }
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

if (ChatterCore::auth()) {
    $oSession = new Session;
    $loggedInUsername = $oSession->get('chatter_username');
    $loggedInId= $oSession->get('chatter_id');
    new MessengerAjax($loggedInUsername, $loggedInId);
}
