<?php

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AddChatterFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $agencyId = $this->session->get('agency_id');

        $aData = [
            'email' => $this->httpRequest->post('mail'),
            'username' => $this->httpRequest->post('username'),
            'password' => $this->httpRequest->post('password', Http::NO_CLEAN),
            'chatter_name' => $this->httpRequest->post('chatter_name'),
            'agency_id' => $agencyId,
        ];
        (new ChatterModel)->add($aData);

        Header::redirect(Uri::get(PH7_AGENCY_MOD, 'chatter', 'index'), t('Chatter successfully added.'));
    }
}
