<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Agency / From
 */

namespace PH7;

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class EditAgencyForm
{
    public static function display()
    {
        if (isset($_POST['submit_agency_edit_account'])) {
            if (\PFBC\Form::isValid($_POST['submit_agency_edit_account'])) {
                new EditAgencyFormProcess;
            }

            Header::redirect();
        }

        $oHR = new Http;
        // Prohibit other admins to edit the Root Administrator (ID 1)
        $iProfileId = ($oHR->getExists('profile_id') && !AdminCore::isRootProfileId($oHR->get('profile_id', 'int'))) ? $oHR->get('profile_id', 'int') : (new Session)->get('admin_id');

	$iProfileId=4;

	print $iProfileId;

        $oAgency = (new AgencyModel)->readProfile($iProfileId, 'Admins');

        $oForm = new \PFBC\Form('form_agency_edit_account');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_agency_edit_account', 'form_agency_edit_account'));
        $oForm->addElement(new \PFBC\Element\Token('edit_account'));

        if ($oHR->getExists('profile_id') && !AgencyCore::isRootProfileId($oHR->get('profile_id', 'int'))) {
            $oForm->addElement(
                new \PFBC\Element\HTMLExternal('<p class="center"><a class="bold btn btn-default btn-md" href="' . Uri::get(PH7_ADMIN_MOD, 'agency', 'browse') . '">' . t('Back to Browse Agencies') . '</a></p>')
            );
        }
        unset($oHR);

        $oForm->addElement(new \PFBC\Element\Textbox(t('Username:'), 'username', array('value' => $oAgency->username, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Email(t('Login Email:'), 'mail', array('value' => $oAgency->email, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('First Name:'), 'first_name', array('value' => $oAgency->firstName, 'required' => 1, 'validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Last Name:'), 'last_name', array('value' => $oAgency->lastName, 'required' => 1, 'validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Radio(t('Gender:'), 'sex', array('male' => t('Man'), 'female' => t('Female')), array('value' => $oAgency->sex, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Timezone('Time Zone:', 'time_zone', array('value' => $oAgency->timeZone, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
