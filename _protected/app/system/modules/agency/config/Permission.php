<?php

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        $bAdminAuth = AgencyCore::auth();
        $bChatterAuth = ChatterCore::auth();

        /***** Levels for admin module *****/

        // Overall levels

        if (!$bAdminAuth && !$bChatterAuth && $this->registry->action !== 'login') {
            Header::redirect(
                Uri::get(PH7_AGENCY_MOD, 'main', 'login'),
                $this->signInMsg(),
                Design::ERROR_TYPE
            );
        }

        if ($bAdminAuth && $this->registry->action === 'login') {
            Header::redirect(
                Uri::get(PH7_AGENCY_MOD, 'main', 'index'),
                t('Oops! You are already logged in as agency.'),
                Design::ERROR_TYPE
            );
        }
        if ($bAdminAuth && $this->registry->action === 'chatter') {
            Header::redirect(
                Uri::get(PH7_AGENCY_MOD, 'main', 'index'),
                t('Oops! You cannot open this page'),
                Design::ERROR_TYPE
            );
        }

        if ($bChatterAuth && $this->registry->action === 'login') {
            Header::redirect(
                Uri::get(PH7_AGENCY_MOD, 'chatter', 'chat'),
                t('Oops! You are already logged in as chatter'),
                Design::ERROR_TYPE
            );
        }

        if ($bChatterAuth && $this->registry->action === 'index') {
            Header::redirect(
                Uri::get(PH7_AGENCY_MOD, 'chatter', 'chat'),
                t('Oops! You cannot open this page'),
                Design::ERROR_TYPE
            );
        }

        // Options ...
    }
}
