<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Security\Validate\Validate;

class EditAgencyFormProcess extends Form
{
    private $bIsErr = false;

    public function __construct()
    {
        parent::__construct();

        $oValidate = new Validate;
        $oAgencyModel = new AgencyModel;

        $iProfileId = $this->getProfileId();
        $oAgency = $oAgencyModel->readProfile($iProfileId, 'Agency');

        if (!$this->str->equals($this->httpRequest->post('username'), $oAgency->username)) {
            $iMinUsernameLength = DbConfig::getSetting('minUsernameLength');
            $iMaxUsernameLength = DbConfig::getSetting('maxUsernameLength');

            if (!$oValidate->username($this->httpRequest->post('username'), $iMinUsernameLength, $iMaxUsernameLength)) {
                \PFBC\Form::setError('form_agency_edit_account', t('Your username has to contain from %0% to %1% characters, your username is not available or your username already used by other agency.', $iMinUsernameLength, $iMaxUsernameLength));
                $this->bIsErr = true;
            } else {
                $oAgencyModel->updateProfile('username', $this->httpRequest->post('username'), $iProfileId, 'Agency');
                $this->session->set('agency_username', $this->httpRequest->post('username'));

                (new Cache)->start(UserCoreModel::CACHE_GROUP, 'username' . $iProfileId . 'Agency', null)->clear();
            }
        }

        if (!$this->str->equals($this->httpRequest->post('mail'), $oAgency->email)) {
            if ((new ExistsCoreModel)->email($this->httpRequest->post('mail'))) {
                \PFBC\Form::setError('form_agency_edit_account', t('Invalid email address or this email is already used by another agency.'));
                $this->bIsErr = true;
            } else {
                $oAgencyModel->updateProfile('email', $this->httpRequest->post('mail'), $iProfileId, 'Agency');
                $this->session->set('agency_email', $this->httpRequest->post('mail'));
            }
        }

        if (!$this->str->equals($this->httpRequest->post('first_name'), $oAgency->firstName)) {
            $oAgencyModel->updateProfile('firstName', $this->httpRequest->post('first_name'), $iProfileId, 'Agency');
            $this->session->set('agency_first_name', $this->httpRequest->post('first_name'));

            (new Cache)->start(UserCoreModel::CACHE_GROUP, 'firstName' . $iProfileId . 'Agency', null)->clear();
        }

        if (!$this->str->equals($this->httpRequest->post('last_name'), $oAgency->lastName)) {
            $oAgencyModel->updateProfile('lastName', $this->httpRequest->post('last_name'), $iProfileId, 'Agency');
        }

        if (!$this->str->equals($this->httpRequest->post('sex'), $oAgency->sex)) {
            $oAgencyModel->updateProfile('sex', $this->httpRequest->post('sex'), $iProfileId, 'Agency');

            (new Cache)->start(UserCoreModel::CACHE_GROUP, 'sex' . $iProfileId . 'Agency', null)->clear();
        }

        if (!$this->str->equals($this->httpRequest->post('time_zone'), $oAgency->timeZone)) {
            $oAgencyModel->updateProfile('timeZone', $this->httpRequest->post('time_zone'), $iProfileId, 'Agency');
        }

        $oAgencyModel->setLastEdit($iProfileId, 'Agency');

        unset($oValidate, $oAgencyModel, $oAgency);

        (new Agency)->clearReadProfileCache($iProfileId, 'Agency');

        if (!$this->bIsErr) {
            \PFBC\Form::setSuccess('form_agency_edit_account', t('Profile successfully updated!'));
        }
    }

    /**
     * @return string
     */
    private function getProfileId()
    {
        // Prohibit other admins to edit the Root Administrator (ID 1)
        if ($this->httpRequest->getExists('profile_id') && !AdminCore::isRootProfileId($this->httpRequest->get('profile_id', 'int'))) {
            return $this->httpRequest->get('profile_id', 'int');
        }

        return $this->session->get('agency_id');
    }
}
