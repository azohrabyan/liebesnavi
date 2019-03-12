<?php

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Security as HtmlSecurity;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;
use PH7\Framework\File\Import;
use PH7\Framework\Url\Url;

class ChatterController extends Controller
{
    /** @var ChatterModel */
    private $oChatterModel;

    /** @var int */
    private $iTotalChatters;

    /** @var string */
    private $sTitle;

    public function __construct()
    {
        parent::__construct();

        $this->oChatterModel = new ChatterModel;
    }

    public function index()
    {
        $agencyId = $this->session->get('agency_id');

        $this->iTotalChatters = $this->oChatterModel->searchChatters($agencyId, true,
            $this->httpRequest->get('order'), $this->httpRequest->get('sort'));

        $oSearch = $this->oChatterModel->searchChatters($agencyId, false,
            $this->httpRequest->get('order'), $this->httpRequest->get('sort'));

        // Adding the JS form file
        $this->design->addJs(PH7_STATIC . PH7_JS, 'form.js');

        // Assigns variables for views
        $this->view->designSecurity = new HtmlSecurity; // Security Design Class
        $this->view->dateTime = $this->dateTime; // Date Time Class

        $this->sTitle = t('Browse Chatters');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->h3_title = nt('%n% Chatters', '%n% Chatters', $this->iTotalChatters);
        $this->view->browse = $oSearch;

        $this->output();
    }

    public function add()
    {
        $this->sTitle = t('Add a Chatter');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function edit()
    {
        $this->sTitle = t('Edit Chatter account');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function delete()
    {
        $aData = explode('_', $this->httpRequest->post('id'));
        $iId = (int)$aData[0];
        $sUsername = (string)$aData[1];

        (new Chatter)->delete($iId, $sUsername);

        Header::redirect(
            Uri::get(PH7_AGENCY_MOD, 'chatter', 'index'),
            t('The chatter has been deleted.')
        );
    }

    public function logout()
    {
        (new Chatter())->logout();
    }

    public function chat()
    {
        $this->output();
    }

    public function view_profile()
    {
        Import::pH7App(PH7_SYS . PH7_MOD . 'user.models.UserModel');
        Import::pH7App(PH7_SYS . PH7_MOD . 'user.models.VisitorModel');

        $oUserModel = new UserModel;
        $partner = $this->httpRequest->get('username', 'string');
        $fake = $this->httpRequest->get('f', 'string');
        $iProfileId = $oUserModel->getId(null, $partner);
        $iVisitorId = $oUserModel->getId(null, $fake);

        $oVisitorModel = new VisitorModel($iProfileId, $iVisitorId, $this->dateTime->get()->dateTime('Y-m-d H:i:s'));

        if (!$oVisitorModel->already()) {
            // Add a new visit
            $oVisitorModel->set();
        } else {
            // Update the date of last visit
            $oVisitorModel->update();
        }
        unset($oVisitorModel);

        $userCore = new UserCore();
        Header::redirect($userCore->getProfileLink($partner));
    }


    public function reports()
    {
        $mData = $this->oChatterModel->messageCountReport();

        $this->sTitle = t('Reports');
        $this->view->page_title = $this->sTitle;

        $groupped = [];
        foreach($mData as $row) {
            if (!isset($groupped[$row->chatter_id])) {
                $groupped[$row->chatter_id] = [
                    'name' => $row->name,
                    'stats' => []
                ];
            }
            $groupped[$row->chatter_id]['stats'][] = $row;
        }

        $this->view->report = $groupped;

        $this->output();
    }

}
