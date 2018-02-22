<?php
/**
 * @title          Main Controller
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Controller
 * @version        1.4
 */

namespace PH7;

use Braintree_Transaction;
use PH7\Framework\Cache\Cache;
use PH7\Framework\File\File;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Payment\Gateway\Api\Api as ApiInterface;
use stdClass;

class CoinsController extends MainController
{

    /** @var bool Payment status. Default is failure (FALSE) */
    private $bStatus = false;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new AffiliateCoreModel;
        $this->oPayModel = new PaymentModel;
        $this->iProfileId = $this->session->get('member_id');
    }

    public function index()
    {
        $oCreditPackages = $this->oPayModel->getCreditPackages();

        if (empty($oCreditPackages)) {
            $this->displayPageNotFound(t('No membership found!'));
        } else {
            $this->view->page_title = $this->view->h1_title = t('Coin Packages');
            $this->view->creditPackages = $oCreditPackages;
            $this->output();
        }
    }

    /**
     * @param null|int $iPackageId
     *
     * @return void
     */
    public function pay($iPackageId = null)
    {
        $iPackageId = (int)$iPackageId;

        $oPackageData = $this->oPayModel->getCreditPackages($iPackageId);

        if (empty($iPackageId) || empty($oPackageData)) {
            $this->displayPageNotFound(t('No package found!'));
        } else {
            // Adding the stylesheet for Gatway Logo
            $this->design->addCss(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS, 'common.css');

            // Regenerate the session ID to prevent the session fixation attack
            $this->session->regenerateId();

            $this->view->page_title = $this->view->h1_title = t('Payment Option');
            $this->view->package = $oPackageData;
            $this->output();
        }
    }

    /**
     * @param string $sProvider
     *
     * @return void
     */
    public function process($sProvider = '')
    {
        switch ($sProvider) {
            case static::PAYPAL_GATEWAY_NAME: {
                $oPayPal = new PayPal($this->config->values['module.setting']['sandbox.enabled']);
                if ($oPayPal->valid() && $this->httpRequest->postExists('custom')) {
                    $aData = explode('|', base64_decode($this->httpRequest->post('custom')));
                    $iItemNumber = (int)$aData[0];
                    if ($this->oUserModel->updateUserCoins(
                        $iItemNumber,
                        $this->iProfileId
                    )) {
                        $this->bStatus = true; // Status is OK
                        // PayPal will call automatically the "notification()" method thanks its IPN feature and "notify_url" form attribute.
                    }
                }
                unset($oPayPal);
            } break;

            case static::STRIPE_GATEWAY_NAME: {
                if ($this->httpRequest->postExists('stripeToken')) {
                    \Stripe\Stripe::setApiKey($this->config->values['module.setting']['stripe.secret_key']);
                    $sAmount = $this->httpRequest->post('amount');

                    try {
                        $oCharge = \Stripe\Charge::create(
                            [
                                'amount' => Stripe::getAmount($sAmount),
                                'currency' => $this->config->values['module.setting']['currency'],
                                'source' => $this->httpRequest->post('stripeToken'),
                                'description' => t('Membership charged for %0%', $this->httpRequest->post('stripeEmail'))
                            ]
                        );

                        $iItemNumber = $this->httpRequest->post('item_number');
                        if ($this->oUserModel->updateUserCoins(
                            $iItemNumber,
                            $this->iProfileId
                        )) {
                            $this->bStatus = true; // Status is OK
                            $this->notification(Stripe::class, $iItemNumber);
                        }
                    }
                    catch (\Stripe\Error\Card $oE) {
                        // The card has been declined
                        // Do nothing here as "$this->bStatus" is by default FALSE and so it will display "Error occurred" msg later
                    }
                    catch (\Stripe\Error\Base $oE) {
                        $this->design->setMessage( $this->str->escape($oE->getMessage(), true) );
                    }
                }
            } break;

            case static::BRAINTREE_GATEWAY_NAME: {
                if ($bNonce = $this->httpRequest->post('payment_method_nonce')) {
                    Braintree::init($this->config);

                    $oResult = Braintree_Transaction::sale([
                        'amount' => $this->httpRequest->post('amount'),
                        'paymentMethodNonce' => $bNonce,
                        'options' => ['submitForSettlement' => true]
                    ]);

                    if ($oResult->success) {
                        $iItemNumber = $this->httpRequest->post('item_number');
                        if ($this->oUserModel->updateUserCoins(
                            $iItemNumber,
                            $this->iProfileId
                        )) {
                            $this->bStatus = true; // Status is OK
                            $this->notification(Braintree::class, $iItemNumber);
                        }
                    } elseif ($oResult->transaction) {
                        $sErrMsg = t('Error processing transaction: %0%', $oResult->transaction->processorResponseText);
                        $this->design->setMessage( $this->str->escape($sErrMsg, true) );
                    }
                }
            } break;

            case static::TWO_CHECKOUT_GATEWAY_NAME: {
                $o2CO = new TwoCO($this->config->values['module.setting']['sandbox.enabled']);
                $sVendorId = $this->config->values['module.setting']['2co.vendor_id'];
                $sSecretWord = $this->config->values['module.setting']['2co.secret_word'];

                $iItemNumber = $this->httpRequest->post('cart_order_id');
                if ($o2CO->valid($sVendorId, $sSecretWord)
                    && $this->httpRequest->postExists('sale_id')
                ) {
                    if ($this->oUserModel->updateUserCoins(
                        $iItemNumber,
                        $this->iProfileId
                    )) {
                        $this->bStatus = true; // Status is OK
                        $this->notification(TwoCO::class, $iItemNumber);
                    }
                }
                unset($o2CO);
            } break;

            case static::CCBILL_GATEWAY_NAME: {
                // Still in developing...
                // You are more than welcome to contribute on Github: https://github.com/pH7Software/pH7-Social-Dating-CMS
            } break;

            default:
                $this->displayPageNotFound(t('Provider Not Found!'));
        }

        // Set the page titles
        $this->sTitle = ($this->bStatus) ? t('Thank you!') : t('Error occurred!');
        $this->view->page_title = $this->view->h2_title = $this->sTitle;

        if ($this->bStatus) {
            $this->updateAffCom();
            $this->clearCache();
        }

        // Set the valid template page
        $this->manualTplInclude($this->getTemplatePageName() . $this->view->getTplExt());

        if ($this->bStatus) {
            $this->setAutomaticRedirectionToHomepage();
        }

        // Output
        $this->output();
    }



    /**
     * Send a notification email to the admin about the payment (IPN -> Instant Payment Notification).
     *
     * @param int $iPackageId
     *
     * @return int Number of recipients who were accepted for delivery.
     */
    protected function sendNotifyMail($iPackageId)
    {
        $oPackageData = $this->oPayModel->getCreditPackages($iPackageId);

        $sTo = DbConfig::getSetting('adminEmail');

        $sUsername = $this->session->get('member_username');
        $sProfileLink = ' (' . $this->design->getProfileLink($sUsername, false) . ')';
        $sBuyer = $this->session->get('member_first_name') . $sProfileLink;

        $this->view->intro = t('Hello!') . '<br />' . t('Congratulation! You received a new payment from %0%', $sBuyer);
        $this->view->date = t('Date of the payment: %0%', $this->dateTime->get()->date());
        $this->view->package_name = t('Package: %0% coins', $oPackageData->credits);
        $this->view->package_price = t('Amount: %1%%0%', $oPackageData->price, $this->config->values['module.setting']['currency_sign']);
        $this->view->browser_info = t('User Web browser info: %0%', $this->browser->getUserAgent());
        $this->view->ip = t('Buyer IP address: %0%', $this->design->ip(null, false));

        $sMessageHtml = $this->view->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/payment/ipn.tpl', $sTo);

        $aInfo = [
            'to' => $sTo,
            'subject' => t('New Payment Received from %0%', $sBuyer)
        ];

        return (new Mail)->send($aInfo, $sMessageHtml);
    }

    /**
     * Create a Payment Log file.
     *
     * @param ApiInterface $oProvider A provider class.
     * @param string $sMsg
     *
     * @return void
     */
    protected function log(ApiInterface $oProvider, $sMsg)
    {
        if ($this->config->values['module.setting']['log_file.enabled']) {
            $sLogTxt = $sMsg . File::EOL . File::EOL . File::EOL;
            $oProvider->saveLog($sLogTxt . print_r($_POST, true), $this->registry);
        }
    }

    /**
     * Clear Membership cache.
     *
     * @return void
     */
    protected function clearCache()
    {
        (new Cache)->start(UserCoreModel::CACHE_GROUP, 'packages' . $this->iProfileId, null)->clear();
    }

    /**
     * Set automatic redirection to homepage if payment was successful.
     *
     * @return void
     */
    private function setAutomaticRedirectionToHomepage()
    {
        $this->design->setRedirect($this->registry->site_url, null, null, 4);
    }

    /**
     * @return string
     */
    private function getTemplatePageName()
    {
        return $this->bStatus ? 'success' : 'error';
    }
}
