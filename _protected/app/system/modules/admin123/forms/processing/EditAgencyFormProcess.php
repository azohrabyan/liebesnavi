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
use PH7\Framework\Url\Header;
use PH7\Framework\Mvc\Router\Uri;

class EditAgencyFormProcess extends Form
{
    private $bIsErr = false;

    public function __construct()
    {
        parent::__construct();

        $oValidate = new Validate;
        $oAgencyModel = new AgencyModel;

        $iProfileId = $this->getProfileId();
        $oAgency = $oAgencyModel->readProfile($iProfileId, 'ChatAgency');

        if (!$this->str->equals($this->httpRequest->post('username'), $oAgency->username)) {
            $iMinUsernameLength = DbConfig::getSetting('minUsernameLength');
            $iMaxUsernameLength = DbConfig::getSetting('maxUsernameLength');

            if (!$oValidate->username($this->httpRequest->post('username'), $iMinUsernameLength, $iMaxUsernameLength)) {
                \PFBC\Form::setError('form_agency_edit_account', t('Your username has to contain from %0% to %1% characters, your username is not available or your username already used by other agency.', $iMinUsernameLength, $iMaxUsernameLength));
                $this->bIsErr = true;
            } else {
                $oAgencyModel->updateProfile('username', $this->httpRequest->post('username'), $iProfileId, 'ChatAgency');
                $this->session->set('agency_username', $this->httpRequest->post('username'));

                (new Cache)->start(UserCoreModel::CACHE_GROUP, 'username' . $iProfileId . 'ChatAgency', null)->clear();
            }
        }

        if (!$this->str->equals($this->httpRequest->post('mail'), $oAgency->email)) {
            if ((new ExistsCoreModel)->email($this->httpRequest->post('mail'))) {
                \PFBC\Form::setError('form_agency_edit_account', t('Invalid email address or this email is already used by another agency.'));
                $this->bIsErr = true;
            } else {
                $oAgencyModel->updateProfile('email', $this->httpRequest->post('mail'), $iProfileId, 'ChatAgency');
                $this->session->set('agency_email', $this->httpRequest->post('mail'));
            }
        }

        if (!$this->str->equals($this->httpRequest->post('agency_name'), $oAgency->agency_name)) {
            $oAgencyModel->updateProfile('agency_name', $this->httpRequest->post('agency_name'), $iProfileId, 'ChatAgency');
            $this->session->set('agency_name', $this->httpRequest->post('agency_name'));

            (new Cache)->start(UserCoreModel::CACHE_GROUP, 'agencyName' . $iProfileId . 'ChatAgency', null)->clear();
        }

        unset($oValidate, $oAgencyModel, $oAgency);

        (new ChatAgency)->clearReadProfileCache($iProfileId, 'ChatAgency');

        if (!$this->bIsErr) {
            \PFBC\Form::setSuccess('form_agency_edit_account', t('Profile successfully updated!'));
        }
        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'agency', 'browse'), t('Agency successfully added.'));
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

        return $this->session->get('agency_id');
    }
}
