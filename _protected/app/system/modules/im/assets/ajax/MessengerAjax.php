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

class MessengerAjax extends PermissionCore
{
    /** @var HttpRequest */
    private $_oHttpRequest;

    /** @var MessengerModel */
    private $_oMessengerModel;

    public function __construct()
    {
        parent::__construct();

        Import::pH7App(PH7_SYS . PH7_MOD . 'im.models.MessengerModel');

        $this->_oHttpRequest = new HttpRequest;
        $this->_oMessengerModel = new MessengerModel;

        switch ($this->_oHttpRequest->get('act')) {
            case 'heartbeat':
                $this->heartbeat();
                break;

            case 'send':
                $this->send();
                break;

            case 'close':
                $this->close();
                break;

            case 'startsession':
                $this->startSession();
                break;

            case 'startchat':
                $sUser = $this->_oHttpRequest->get('with');
                $this->startChat($sUser);
                break;

            default:
                Http::setHeadersByCode(400);
                exit('Bad Request Error!');
        }

        if (empty($_SESSION['messenger_history'])) {
            $_SESSION['messenger_history'] = [];
        }

        if (empty($_SESSION['messenger_openBoxes'])) {
            $_SESSION['messenger_openBoxes'] = [];
        }
    }

    protected function heartbeat()
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

    protected function boxSession($sBox)
    {
        $sItems = '';

        if (isset($_SESSION['messenger_history'][$sBox]))
            $sItems = $_SESSION['messenger_history'][$sBox];

        return $sItems;
    }

    protected function startSession()
    {
        $sItems = '';
        if (!empty($_SESSION['messenger_openBoxes'])) {
            foreach ($_SESSION['messenger_openBoxes'] as $sBox => $sVoid) {
                $sItems .= $this->boxSession($sBox);
            }
        }

        if ($sItems != '') {
            $sItems = substr($sItems, 0, -1);
        }

        Http::setContentType('application/json');
        echo '{
            "user": "' . $_SESSION['messenger_username'] . '",
            "items": [' . $sItems . ']
        }';
        exit;
    }

    protected function startChat($sTo)
    {
        $sItems = '';
        if (!empty($_SESSION['messenger_openBoxes'])) {
            foreach ($_SESSION['messenger_openBoxes'] as $sBox => $sVoid) {
                $sItems .= $this->boxSession($sBox);
            }
        }

        if ($sItems != '') {
            $sItems = substr($sItems, 0, -1);
        }

        if (empty($sItems)) {
            $sFrom = $_SESSION['messenger_username'];

            $oQuery = $this->_oMessengerModel->selectFromTo($sFrom, $sTo);
            $aItems = [];
            foreach ($oQuery as $oData) {
                $aItems[] = [
                    'status' => $oData->fromUser == $sFrom ? 1 : 0,
                    'msg' => $oData->message,
                    'user' => $sTo,
                    'coins' => 0,
                ];
            }
            $sItems = json_encode($aItems);
//            $_SESSION['messenger_history'][$sTo] = $sItems;
            $_SESSION['messenger_openBoxes'][$sTo] = 1;
            if (!isset($_SESSION['messenger_boxes'][$sTo])) {
                $_SESSION['messenger_boxes'][$sTo] = 1;
            }
        } else {
            $sItems = "";
        }

        Http::setContentType('application/json');
        echo '{
            "user": "' . $_SESSION['messenger_username'] . '",
            "items": ' . $sItems . '
        }';
        exit;
    }

    protected function send()
    {
        $sFrom = $_SESSION['messenger_username'];
        $sTo = $_SESSION['messenger_username_to'] = $this->_oHttpRequest->post('to');
        $sMsg = $this->_oHttpRequest->post('message');

        $iSenderId = (new Session)->get('member_id');

        $_SESSION['messenger_openBoxes'][$this->_oHttpRequest->post('to')] = date('Y-m-d H:i:s', time());

        $sMsgTransform = $this->sanitize($sMsg);
        $sMsgTransform = Emoticon::init($sMsgTransform, false);

        if (!isset($_SESSION['messenger_history'][$this->_oHttpRequest->post('to')])) {
            $_SESSION['messenger_history'][$this->_oHttpRequest->post('to')] = '';
        }

//        if (!$this->checkMembership() || !$this->group->instant_messaging) {
//            $sMsgTransform = t("You need to <a href='%0%'>upgrade your membership</a> to be able to chat.", Uri::get('payment', 'main', 'index'));
//        } elseif (!$this->isOnline($sFrom)) {
//            $sMsgTransform = t('You must have the ONLINE status in order to chat with other users.');
//        } elseif (!$this->isOnline($sTo)) {
//            $sMsgTransform = '<small><em>' . t("%0% is offline. Send a <a href='%1%'>Private Message</a> instead.", $sTo, Uri::get('mail', 'main', 'compose', $sTo)) . '</em></small>';
//        }elseif (UserCore::countCredits($iSenderId) <= 0) {
        if (UserCore::countCredits($iSenderId) <= 0) {
            $sMsgTransform = '<small><em>' . t("You have not enough coins to send messages. Go to <a href='%1%'>shop</a> and purchase some coins.", Uri::get('payment', 'coins', 'index', '')) . '</em></small>';
        } else {
            $this->_oMessengerModel->insert($sFrom, $sTo, $sMsg, (new CDateTime)->get()->dateTime('Y-m-d H:i:s'));
            $oUserModel = new UserCoreModel;
            $oUserModel->decreaseCredits($iSenderId);
        }

        $_SESSION['messenger_history'][$this->_oHttpRequest->post('to')] .= $this->setJsonContent(['status' => '1', 'user' => $sTo, 'msg' => $sMsgTransform]);

        unset($_SESSION['messenger_boxes'][$this->_oHttpRequest->post('to')]);

        Http::setContentType('application/json');
        echo $this->setJsonContent(['user' => $sFrom, 'msg' => $sMsgTransform], false);
        exit;
    }

    protected function close()
    {
        unset($_SESSION['messenger_openBoxes'][$this->_oHttpRequest->post('box')]);
        exit(1);
    }

    protected function setJsonContent(array $aData, $bEndComma = true)
    {
        $iSenderId = (new Session)->get('member_id');

        // Default array
        $aDefData = [
            'status' => '0',
            'user' => '',
            'msg' => '',
            'coins' => UserCore::countCredits($iSenderId)
        ];

        // Update array
        $aData += $aDefData;

        $sJsonData = <<<EOD
        {
            "status": "{$aData['status']}",
            "user": "{$aData['user']}",
            "msg": "{$aData['msg']}",
            "coins": "{$aData['coins']}"
        }
EOD;
        return $bEndComma ? $sJsonData . ',' : $sJsonData;
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
    if (empty($_SESSION['messenger_username'])) {
        $_SESSION['messenger_username'] = $oSession->get('member_username');
    }
    unset($oSession);
    new MessengerAjax;
}
