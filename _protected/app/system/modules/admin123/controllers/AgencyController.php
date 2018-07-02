<?php

namespace PH7;

use PH7\Framework\Layout\Html\Security as HtmlSecurity;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\CSRF\Token as SecurityToken;
use PH7\Framework\Url\Header;

class AgencyController extends Controller
{
    const PROFILES_PER_PAGE = 15;

    /** @var AgencyModel */
    private $oAgencyModel;

    /** @var string */
    private $sTitle;

    /** @var string */
    private $sMsg;

    /** @var int */
    private $iTotalAgancies;

    public function __construct()
    {
        parent::__construct();

        $this->oAgencyModel = new AgencyModel;
    }

    public function index()
    {
        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'agency', 'browse')
        );
    }

    public function browse()
    {
        $this->iTotalAgancies = $this->oAgencyModel->searchAgency($this->httpRequest->get('looking'), true,
            $this->httpRequest->get('order'), $this->httpRequest->get('sort'), null, null);

        $oPage = new Page;
        $this->view->total_pages = $oPage->getTotalPages($this->iTotalAgancies, self::PROFILES_PER_PAGE);
        $this->view->current_page = $oPage->getCurrentPage();
        $oSearch = $this->oAgencyModel->searchAgency($this->httpRequest->get('looking'), false,
            $this->httpRequest->get('order'), $this->httpRequest->get('sort'), $oPage->
            getFirstItem(), $oPage->getNbItemsPerPage());
        unset($oPage);

        // Adding the JS form file
        $this->design->addJs(PH7_STATIC . PH7_JS, 'form.js');

        // Assigns variables for views
        $this->view->designSecurity = new HtmlSecurity; // Security Design Class
        $this->view->dateTime = $this->dateTime; // Date Time Class

        $this->sTitle = t('Browse Agency');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->h3_title = nt('%n% Agency', '%n% Agency', $this->iTotalAgancies);
        $this->view->browse = $oSearch;

        $this->output();
    }

    public function search()
    {
        $this->sTitle = t('Agency Search');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function add()
    {
        $this->sTitle = t('Add an Agency');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function edit()
    {
        $this->sTitle = t('Edit your account');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function delete()
    {
        $aData = explode('_', $this->httpRequest->post('id'));
        $iId = (int)$aData[0];
        $sUsername = (string)$aData[1];

        (new ChatAgency)->delete($iId, $sUsername);

        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'agency', 'browse'),
            t('The agency has been deleted.')
        );
    }

}
