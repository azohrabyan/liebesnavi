<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Security;
use PH7\Framework\Url\Header;

class LoginFormProcess extends Form implements LoginableForm
{
    const BRUTE_FORCE_SLEEP_DELAY = 2;

    /** @var AgencyModel */
    private $oAgencyModel;

    /** @var ChatterModel */
    private $oChatterModel;

    public function __construct()
    {
        parent::__construct();

        $sIp = Ip::get();
        $this->oAgencyModel = new AgencyModel;
//        $this->oChatterModel = new ChatterModel;
        $oSecurityModel = new SecurityModel;

        $sEmail = $this->httpRequest->post('mail');
        $sUsername = $this->httpRequest->post('username');
        $sPassword = $this->httpRequest->post('password', HttpRequest::NO_CLEAN);


        /*** Security IP Login ***/
        $sIpLogin = DbConfig::getSetting('ipLogin');

        /*** Check if the connection is not locked ***/
        $bIsLoginAttempt = (bool)DbConfig::getSetting('isAdminLoginAttempt');
        $iMaxAttempts = (int)DbConfig::getSetting('maxAdminLoginAttempts');
        $iTimeDelay = (int)DbConfig::getSetting('loginAdminAttemptTime');

        if ($bIsLoginAttempt && !$oSecurityModel->checkLoginAttempt($iMaxAttempts, $iTimeDelay, $sEmail, $this->view, 'ChatAgency')) {
            \PFBC\Form::setError('form_agency_login', Form::loginAttemptsExceededMsg($iTimeDelay));
            return; // Stop execution of the method.
        }

        /*** Check Login ***/
        $bIsLogged = $this->oAgencyModel->agencyLogin($sEmail, $sUsername, $sPassword);
        $bIpNotAllowed = !empty($sIpLogin) && $sIpLogin !== $sIp;

        if (!$bIsLogged || $bIpNotAllowed) // If the login is failed or if the IP address is not allowed
        {
            $this->preventBruteForce(self::BRUTE_FORCE_SLEEP_DELAY);

            if (!$bIsLogged) {
                $oSecurityModel->addLoginLog($sEmail, $sUsername, $sPassword, 'Failed! Incorrect Email, Username or Password', 'Admins');

                if ($bIsLoginAttempt) {
                    $oSecurityModel->addLoginAttempt('Admins');
                }

                $this->enableCaptcha();
                \PFBC\Form::setError('form_admin_login', t('"Email", "Username" or "Password" is Incorrect'));
            } elseif ($bIpNotAllowed) {
                $this->enableCaptcha();
                \PFBC\Form::setError('form_admin_login', t('Incorrect Login!'));
                $oSecurityModel->addLoginLog($sEmail, $sUsername, $sPassword, 'Failed! Wrong IP address', 'Admins');
            }
        } else {
            $oSecurityModel->clearLoginAttempts('ChatAgency');
            $this->session->remove('captcha_agency_enabled');
            $iId = $this->oAgencyModel->getId($sEmail, null, 'ChatAgency');
            $oAgencyData = $this->oAgencyModel->readProfile($iId, 'ChatAgency');

            $this->updatePwdHashIfNeeded($sPassword, $oAgencyData->password, $sEmail);

            (new AgencyCore)->setAuth($oAgencyData, $this->oAgencyModel, $this->session, $oSecurityModel);

            Header::redirect(Uri::get(PH7_AGENCY_MOD, 'main', 'index'), t('You are successfully logged in!'));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function updatePwdHashIfNeeded($sPassword, $sUserPasswordHash, $sEmail)
    {
        if ($sNewPwdHash = Security::pwdNeedsRehash($sPassword, $sUserPasswordHash)) {
            $this->oAgencyModel->changePassword($sEmail, $sNewPwdHash, 'Admins');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function enableCaptcha()
    {
        $this->session->set('captcha_agency_enabled', 1);
    }
}
