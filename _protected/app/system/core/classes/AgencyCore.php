<?php

namespace PH7;

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Session\Session;
use PH7\Framework\Util\Various;
use stdClass;

// Abstract Class
class AgencyCore extends UserCore
{
    /**
     * Agency' levels.
     *
     * @return bool
     */
    public static function auth()
    {
        $oSession = new Session;
        $bIsConnected = ((int)$oSession->exists('agency_id')) && $oSession->get('agency_ip') === Ip::get() &&
            $oSession->get('agency_http_user_agent') === (new Browser)->getUserAgent() &&
            $oSession->get('agency_role') == 'admin';
        unset($oSession);

        return $bIsConnected;
    }

    /**
     * @param AgencyCoreModel $oAgencyModel
     *
     * @return bool TRUE if the IP is the one the site was installed, FALSE otherwise.
     */
    public static function isAgencyIp(AgencyCoreModel $oAgencyModel)
    {
        return $oAgencyModel->getRootIp() === Ip::get();
    }

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
    public function setAuth(stdClass $oAgencyData, UserCoreModel $oAgencyModel, Session $oSession, SecurityModel $oSecurityModel)
    {
        // Regenerate the session ID to prevent session fixation attack
        $oSession->regenerateId();

        $aSessionData = [
            'agency_role' => 'admin',
            'agency_id' => $oAgencyData->profileId,
            'agency_email' => $oAgencyData->email,
            'agency_username' => $oAgencyData->username,
            'agency_ip' => Ip::get(),
            'agency_http_user_agent' => (new Browser)->getUserAgent(),
            'agency_token' => Various::genRnd($oAgencyData->email),
        ];
        $oSession->set($aSessionData);
        $oSecurityModel->addLoginLog($oAgencyData->email, $oAgencyData->username, '*****', 'Logged in!', 'ChatAgency');
        $oAgencyModel->setLastActivity($oAgencyData->profileId, 'ChatAgency');
    }
}
