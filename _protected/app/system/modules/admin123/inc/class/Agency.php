<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class Agency extends AdminCore
{
    /**
     * Logout function for admins.
     *
     * @return void
     */
    public function logout()
    {
        (new Session)->destroy();

        Header::redirect(
            Uri::get(PH7_AGENCY_MOD, 'main', 'login'),
            t('You are successfully logged out.')
        );
    }

    /**
     * Delete Admin.
     *
     * @param int $iProfileId
     * @param string $sUsername
     *
     * @return void
     */
    public function delete($iProfileId, $sUsername)
    {
        $iProfileId = (int)$iProfileId;

        (new AgencyModel)->delete($iProfileId, $sUsername);
    }
}
