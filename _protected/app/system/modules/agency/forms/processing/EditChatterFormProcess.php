<?php

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Url\Header;
use PH7\Framework\Mvc\Router\Uri;

class EditChatterFormProcess extends Form
{
    private $bIsErr = false;

    public function __construct()
    {
        parent::__construct();

        $oValidate = new Validate;
        $oChatterModel = new ChatterModel;

        $iProfileId = $this->getProfileId();
        $oChatter = $oChatterModel->readProfile($iProfileId, 'Chatter');

        if (!$this->str->equals($this->httpRequest->post('username'), $oChatter->username)) {
            $iMinUsernameLength = DbConfig::getSetting('minUsernameLength');
            $iMaxUsernameLength = DbConfig::getSetting('maxUsernameLength');

            if (!$oValidate->username($this->httpRequest->post('username'), $iMinUsernameLength, $iMaxUsernameLength)) {
                \PFBC\Form::setError('form_agency_edit_account', t('Your username has to contain from %0% to %1% characters, your username is not available or your username already used by other agency.', $iMinUsernameLength, $iMaxUsernameLength));
                $this->bIsErr = true;
            } else {
                $oChatterModel->updateProfile('username', $this->httpRequest->post('username'), $iProfileId, 'Chatter');
                $this->session->set('agency_username', $this->httpRequest->post('username'));

                (new Cache)->start(UserCoreModel::CACHE_GROUP, 'username' . $iProfileId . 'Chatter', null)->clear();
            }
        }

        if (!$this->str->equals($this->httpRequest->post('mail'), $oChatter->email)) {
            if ((new ExistsCoreModel)->email($this->httpRequest->post('mail'))) {
                \PFBC\Form::setError('form_agency_edit_account', t('Invalid email address or this email is already used by another agency.'));
                $this->bIsErr = true;
            } else {
                $oChatterModel->updateProfile('email', $this->httpRequest->post('mail'), $iProfileId, 'Chatter');
                $this->session->set('agency_email', $this->httpRequest->post('mail'));
            }
        }

        if (!$this->str->equals($this->httpRequest->post('chatter_name'), $oChatter->name)) {
            $oChatterModel->updateProfile('name', $this->httpRequest->post('chatter_name'), $iProfileId, 'Chatter');
            $this->session->set('agency_name', $this->httpRequest->post('chatter_name'));

            (new Cache)->start(UserCoreModel::CACHE_GROUP, 'chatterName' . $iProfileId . 'Chatter', null)->clear();
        }

        unset($oValidate, $oChatterModel, $oChatter);

        (new Chatter)->clearReadProfileCache($iProfileId, 'Chatter');

        if (!$this->bIsErr) {
            \PFBC\Form::setSuccess('form_agency_edit_account', t('Profile successfully updated!'));
        }
        Header::redirect(Uri::get(PH7_AGENCY_MOD, 'chatter', 'index'), t('Chatter successfully added.'));
    }

    /**
     * @return string
     */
    private function getProfileId()
    {
        // Prohibit other admins to edit the Root Administrator (ID 1)
        if ($this->httpRequest->getExists('profile_id') ) {
            return $this->httpRequest->get('profile_id', 'int');
        }

        return $this->session->get('chatter_id');
    }
}
