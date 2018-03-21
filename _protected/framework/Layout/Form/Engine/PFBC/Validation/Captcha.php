<?php
/**
 * This code has been modified by made this code pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

class Captcha extends \PFBC\Validation
{
    protected $message;
    protected $privateKey;

    public function __construct($privateKey, $message = '')
    {
        parent::__construct($message);
        $this->privateKey = $privateKey;

        if (empty($message))
            $this->message = t('Please confirm you are not a robot.');
    }

    public function isValid($value)
    {
        require_once('static/PFBC/recaptchalib.php');
        $rc = new \ReCaptcha($this->privateKey);
        $resp = $rc->verifyResponse($_SERVER['REMOTE_ADDR'], $_POST['g-recaptcha-response']);

        return ($resp->success) ? true : false;
    }
}
