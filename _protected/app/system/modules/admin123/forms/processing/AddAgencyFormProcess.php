<?php

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AddAgencyFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $aData = [
            'email' => $this->httpRequest->post('mail'),
            'username' => $this->httpRequest->post('username'),
            'password' => $this->httpRequest->post('password', Http::NO_CLEAN),
            'agency_name' => $this->httpRequest->post('agency_name'),
        ];
        (new AgencyModel)->add($aData);

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'agency', 'browse'), t('Agency successfully added.'));
    }
}
