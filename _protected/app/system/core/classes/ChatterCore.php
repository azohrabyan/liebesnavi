<?php

namespace PH7;

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Session\Session;
use PH7\Framework\Util\Various;
use stdClass;

// Abstract Class
class ChatterCore extends UserCore
{
    /**
     * Agency' levels.
     *
     * @return bool
     */
    public static function auth()
    {
        $oSession = new Session;
        $bIsConnected = ((int)$oSession->exists('chatter_id')) && $oSession->get('chatter_ip') === Ip::get() && $oSession->get('chatter_http_user_agent') === (new Browser)->getUserAgent();
        unset($oSession);

        return $bIsConnected;
    }

    /**
     * @param AgencyCoreModel $oAgencyModel
     *
     * @return bool TRUE if the IP is the one the site was installed, FALSE otherwise.
     */
//    public static function isAgencyIp(AgencyCoreModel $oAgencyModel)
//    {
//        return $oAgencyModel->getRootIp() === Ip::get();
//    }

    /**
     * Set an admin authentication.
     *
     * @param stdClass $oAdminData User database object.
     * @param UserCoreModel $oAdminModel
     * @param Session $oSession
     * @param SecurityModel $oSecurityModel
     *
     * @return void
     */
    public function setAuth(stdClass $oChatterData, UserCoreModel $oChatterModel, Session $oSession, SecurityModel $oSecurityModel)
    {
        // Regenerate the session ID to prevent session fixation attack
        $oSession->regenerateId();

        $aSessionData = [
            'agency_role' => 'chatter',
            'chatter_id' => $oChatterData->profileId,
            'chatter_agency_id' => $oChatterData->agency_id,
            'chatter_email' => $oChatterData->email,
            'chatter_username' => $oChatterData->username,
            'chatter_ip' => Ip::get(),
            'chatter_http_user_agent' => (new Browser)->getUserAgent(),
            'chatter_token' => Various::genRnd($oChatterData->email),
        ];
        $oSession->set($aSessionData);
        $oSecurityModel->addLoginLog($oChatterData->email, $oChatterData->username, '*****', 'Logged in!', 'Chatter');
//        $oAgencyModel->setLastActivity($oChatterData->profileId, 'Chatter');
    }
}
