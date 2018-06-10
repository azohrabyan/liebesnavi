<?php

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class ChatAgency extends AgencyCore
{
    public function logout()
    {
        (new Session)->destroy();

        Header::redirect(
            Uri::get(PH7_AGENCY_MOD, 'main', 'login'),
            t('You are successfully logged out.')
        );
    }
}
