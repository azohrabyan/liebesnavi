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

class EditChatterForm
{
    public static function display()
    {
        if (isset($_POST['submit_chatter_edit_account'])) {
            if (\PFBC\Form::isValid($_POST['submit_chatter_edit_account'])) {
                new EditChatterFormProcess;
            }

            Header::redirect();
        }

        $oHR = new Http;
        // Prohibit other admins to edit the Root Administrator (ID 1)
        $iProfileId = $oHR->getExists('profile_id') ? $oHR->get('profile_id', 'int') : '';

        $oChatter = (new ChatterModel)->readProfile($iProfileId, 'Chatter');

        $oForm = new \PFBC\Form('form_chatter_edit_account');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_chatter_edit_account', 'form_chatter_edit_account'));
        $oForm->addElement(new \PFBC\Element\Token('edit_account'));

        if ($oHR->getExists('profile_id')) {
            $oForm->addElement(
                new \PFBC\Element\HTMLExternal('<p class="center"><a class="bold btn btn-default btn-md" href="' . Uri::get(PH7_AGENCY_MOD, 'chatter', 'index') . '">' . t('Back to Browse Chatter') . '</a></p>')
            );
        }
        unset($oHR);

        $oForm->addElement(new \PFBC\Element\Textbox(t('Chatter Name:'), 'chatter_name', array('value' => $oChatter->name, 'required' => 1, 'validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Username:'), 'username', array('value' => $oChatter->username, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Email(t('Login Email:'), 'mail', array('value' => $oChatter->email, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
