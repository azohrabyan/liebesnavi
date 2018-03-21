<?php

namespace PFBC\Element;

use PH7\Framework\Mvc\Model\DbConfig;

class Captcha extends \PFBC\Element
{

    public function __construct($label = '', array $properties = null)
    {
        parent::__construct($label, 'recaptcha_response_field', $properties);
    }

    public function render()
    {
        $this->validation[] = new \PFBC\Validation\Captcha(DbConfig::getSetting('recaptchaPrivateKey'));
        require_once('static/PFBC/recaptchalib.php');

        $rc = new \ReCaptcha(DbConfig::getSetting('recaptchaPrivateKey'));
        echo $rc->getHtml(DbConfig::getSetting('recaptchaPublicKey'));
    }
}
