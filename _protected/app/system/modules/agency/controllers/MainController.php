<?php
/**
 * @title          Main Controller
 *
 */

namespace PH7;

use PH7\Framework\Layout\Html\Meta;

class MainController extends Controller
{
    public function index()
    {
        $this->view->page_title = t('Agency Panel');
        $this->view->h1_title = t('Agency Dashboard');

        $this->output();
    }

    public function login()
    {
        // Prohibit the referencing in search engines of the admin panel
        $this->view->header = Meta::NOINDEX;

        $this->view->page_title = t('Sign in to Agency Panel');
        $this->view->h1_title = t('Agency Panel - Login');
        $this->output();
    }

    public function logout()
    {
        (new ChatAgency)->logout();
    }

}
