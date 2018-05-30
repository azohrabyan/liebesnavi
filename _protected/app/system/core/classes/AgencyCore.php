<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

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
    const ROOT_PROILE_ID = 1;

    /**
     * Agency' levels.
     *
     * @return bool
     */
    public static function auth()
    {
        $oSession = new Session;
        $bIsConnected = ((int)$oSession->exists('agency_id')) && $oSession->get('agency_ip') === Ip::get() && $oSession->get('agency_http_user_agent') === (new Browser)->getUserAgent();
        unset($oSession);

        return $bIsConnected;
    }

    /**
     * Determines if the ID is from Root Admin (main AGANCY).
     *
     * @param int $iProfileId
     *
     * @return bool
     */
    public static function isRootProfileId($iProfileId)
    {
        return $iProfileId === static::ROOT_PROILE_ID;
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
        // Remove the session if the admin is logged in as "user" or "affiliate".
        if (UserCore::auth() || AffiliateCore::auth()) {
            $oSession->destroy();
        }

        // Regenerate the session ID to prevent session fixation attack
        $oSession->regenerateId();

        $aSessionData = [
            'agency_id' => $oAgencyData->profileId,
            'agency_email' => $oAgencyData->email,
            'agency_username' => $oAgencyData->username,
            'agency_first_name' => $oAgencyData->firstName,
            'agency_ip' => Ip::get(),
            'agency_http_user_agent' => (new Browser)->getUserAgent(),
            'agency_token' => Various::genRnd($oAgencyData->email),
        ];
        $oSession->set($aSessionData);
        $oSecurityModel->addLoginLog($oAgencyData->email, $oAgencyData->username, '*****', 'Logged in!', 'Agency');
        $oAgencyModel->setLastActivity($oAgencyData->profileId, 'Agency');
    }
}
