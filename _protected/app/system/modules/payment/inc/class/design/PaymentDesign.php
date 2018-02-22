<?php
/**
 * @title          Payment Design
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class / Design
 */

namespace PH7;

use Braintree_ClientToken;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Payment\Gateway\Api\Api as PaymentApi;
use stdClass;

class PaymentDesign extends Framework\Core\Core
{
    const DIV_CONTAINER_NAME = 'payment-form';

    /**
     * @param PaymentContext $oPc
     *
     * @return void
     */
    public function buttonPayPal(PaymentContext $oPc)
    {
        $oPayPal = new PayPal($this->config->values['module.setting']['sandbox.enabled']);

        $oPayPal
            ->param('business', $this->config->values['module.setting']['paypal.email'])
            ->param('custom', base64_encode($oPc->id . '|' . $oPc->price))// Use base64_encode() to discourage curious people
            ->param('amount', $oPc->price)
            ->param('item_number', $oPc->id)
            ->param('item_name', $this->registry->site_name . ' ' . $oPc->name)
            ->param('no_note', 1)
            ->param('no_shipping', 1)
            ->param('currency_code', $this->config->values['module.setting']['currency'])
            ->param('tax_cart', $this->config->values['module.setting']['vat_rate'])
            ->param('return', Uri::get('payment', $oPc->module, 'process', 'paypal'))
            ->param('rm', 2)// Auto redirection in POST data
            ->param('notify_url', Uri::get('payment', $oPc->module, 'notification', 'PH7\PayPal,' . $oPc->id))
            ->param('cancel_return', Uri::get('payment', $oPc->module, 'membership', '?msg=' . t('The payment was aborted. No charge has been taken from your account.'), false));

        $this->displayGatewayForm($oPayPal, $oPc->name, 'PayPal');

        unset($oPayPal, $oPc);
    }

    /**
     * Generates Stripe payment form Stripe API.
     *
     * @param PaymentContext $oPc
     *
     * @return void
     */
    public function buttonStripe(PaymentContext $oPc)
    {
        $oStripe = new Stripe;

        $oStripe
            ->param('item_number', $oPc->id)
            ->param('amount', $oPc->price);

        echo
        '<form action="', $oStripe->getUrl(), '" method="post">',
            $oStripe->generate(),
            '<script
                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key="', $this->config->values['module.setting']['stripe.publishable_key'], '"
                data-name="', $this->registry->site_name, '"
                data-description="', $oPc->name, '"
                data-amount="', Stripe::getAmount($oPc->price), '"
                data-currency="', $this->config->values['module.setting']['currency'], '"
                data-allow-remember-me="true"
                data-bitcoin="true">
            </script>
        </form>';

        unset($oStripe);
    }

    /**
     * Generates Braintree payment form Braintree API.
     *
     * @param PaymentContext $oPc
     *
     * @return void
     */
    public function buttonBraintree(PaymentContext $oPc)
    {
        $fPrice = $oPc->price;
        $sCurrency = $this->config->values['module.setting']['currency'];
        $sLocale = PH7_LANG_NAME;

        Braintree::init($this->config);
        $sClientToken = Braintree_ClientToken::generate();

        echo '<script src="https://js.braintreegateway.com/v2/braintree.js"></script>';

        $oBraintree = new Braintree;
        $oBraintree
            ->param('item_number', $oPc->id)
            ->param('amount', $fPrice);

        $this->displayGatewayForm($oBraintree, $oPc->name, '<u>Braintree</u>');

        unset($oBraintree);

        echo '<script>';
        echo '$(function () {';
        echo "braintree.setup('$sClientToken', 'dropin', {";
        echo "container: '" . self::DIV_CONTAINER_NAME . "',";
        echo "paypal: {singleUse: true, amount: '$fPrice', currency: '$sCurrency', locale: '$sLocale'}";
        echo '})})';
        echo '</script>';
    }

    /**
     * @param PaymentContext $oPc
     *
     * @return void
     */
    public function button2CheckOut(PaymentContext $oPc)
    {
        $o2CO = new TwoCO($this->config->values['module.setting']['sandbox.enabled']);

        $o2CO
            ->param('sid', $this->config->values['module.setting']['2co.vendor_id'])
            ->param('id_type', 1)
            ->param('cart_order_id', $oPc->id)
            ->param('merchant_order_id', $oPc->id)
            ->param('c_prod', $oPc->id)
            ->param('c_price', $oPc->price)
            ->param('total', $oPc->price)
            ->param('c_name', $this->registry->site_name . ' ' . $oPc->name)
            ->param('tco_currency', $this->config->values['module.setting']['currency'])
            ->param('c_tangible', 'N')
            ->param('x_receipt_link_url', Uri::get('payment', $oPc->module, 'process', '2co'));

        $this->displayGatewayForm($o2CO, $oPc->name, '2CO');

        unset($o2CO);
    }

    /**
     * @param stdClass $oMembership
     *
     * @return void
     */
    public function buttonCCBill(stdClass $oMembership)
    {
        // Not implemented yet.
        // Feel free to contribute: https://github.com/pH7Software/pH7-Social-Dating-CMS
    }

    /**
     * @param PaymentApi $oPaymentProvider
     * @param string $sMembershipName
     * @param string $sProviderName The payment provider name.
     *
     * @return void HTML output,
     */
    private function displayGatewayForm(PaymentApi $oPaymentProvider, $sMembershipName, $sProviderName)
    {
        echo '<form action="', $oPaymentProvider->getUrl(), '" method="post">';

        if ($oPaymentProvider instanceof Braintree) {
            echo $this->getDivFormContainer();
        }

        echo $oPaymentProvider->generate();
        echo '<button class="btn btn-primary btn-md" type="submit" name="submit">', $this->buyTxt($sMembershipName, $sProviderName), '</button>';
        echo '</form>';
    }

    /**
     * Build a "buy text" message.
     *
     * @param string $sMembershipName Membership name (e.g., Platinum, Silver, ...).
     * @param string $sProviderName Provider name (e.g., PayPal, 2CO, ...).
     *
     * @return string
     */
    private function buyTxt($sMembershipName, $sProviderName)
    {
        return t('Buy %0% with %1%!', $sMembershipName, '<b>' . $sProviderName . '</b>');
    }

    /**
     * @return string
     */
    private function getDivFormContainer()
    {
        return '<div id="' . self::DIV_CONTAINER_NAME . '"></div>';
    }
}
