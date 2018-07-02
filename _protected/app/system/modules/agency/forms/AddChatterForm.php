<?php

namespace PH7;

use PH7\Framework\Url\Header;

class AddChatterForm
{
    public static function display()
    {
        if (isset($_POST['submit_add_chatter'])) {
            if (\PFBC\Form::isValid($_POST['submit_add_chatter'])) {
                new AddChatterFormProcess;
            }

            Header::redirect();
        }

        $oForm = new \PFBC\Form('form_add_chatter');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_add_chatter', 'form_add_chatter'));
        $oForm->addElement(new \PFBC\Element\Token('add_chatter'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Chatter Name:'), 'chatter_name', array('required' => 1, 'validation' => new \PFBC\Validation\Name)));
        $oForm->addElement(new \PFBC\Element\Username(t('Username:'), 'username', array('required' => 1, 'validation' => new \PFBC\Validation\Username('Chatter'))));
        $oForm->addElement(new \PFBC\Element\Email(t('Login Email:'), 'mail', array('required' => 1, 'validation' => new \PFBC\Validation\CEmail('guest', 'Chatter'))));
        $oForm->addElement(new \PFBC\Element\Password(t('Password:'), 'password', array('required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
