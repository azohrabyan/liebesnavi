<?php

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class Chatter extends ChatterCore
{
    public function logout()
    {
        (new Session)->destroy();

        Header::redirect(
            Uri::get(PH7_AGENCY_MOD, 'main', 'login'),
            t('You are successfully logged out.')
        );
    }

    /**
     * Delete Chatter.
     *
     * @param int $iProfileId
     * @param string $sUsername
     *
     * @return void
     */
    public function delete($iProfileId, $sUsername)
    {
        $iProfileId = (int)$iProfileId;

        (new ChatterModel)->delete($iProfileId, $sUsername);
    }

}
