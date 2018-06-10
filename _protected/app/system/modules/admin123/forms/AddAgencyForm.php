<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PH7\Framework\Url\Header;

class AddAgencyForm
{
    public static function display()
    {
        if (isset($_POST['submit_add_agency'])) {
            if (\PFBC\Form::isValid($_POST['submit_add_agency'])) {
                new AddAgencyFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_add_agency');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_add_agency', 'form_add_agency'));
        $oForm->addElement(new \PFBC\Element\Token('add_agency'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Agency Name:'), 'agency_name', array('required' => 1, 'validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Username(t('Username:'), 'username', array('required' => 1, 'validation' => new \PFBC\Validation\Username('ChatAgency'))));
        $oForm->addElement(new \PFBC\Element\Email(t('Login Email:'), 'mail', array('required' => 1, 'validation' => new \PFBC\Validation\CEmail('guest', 'ChatAgency'))));
        $oForm->addElement(new \PFBC\Element\Password(t('Password:'), 'password', array('required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
