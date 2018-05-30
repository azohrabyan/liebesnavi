<div class="center">
    {{ $is_paypal = $config->values['module.setting']['paypal.enabled'] }}
    {{ $is_stripe = $config->values['module.setting']['stripe.enabled'] }}
    {{ $is_braintree = $config->values['module.setting']['braintree.enabled'] }}
    {{ $is_2co = $config->values['module.setting']['2co.enabled'] }}
    {*
         Still in development. Fork the project at https://github.com/pH7Software/pH7-Social-Dating-CMS/ and contribute to it,
         then, open a pull request :-)

         {{ $is_ccbill = $config->values['module.setting']['ccbill.enabled'] }}
     *}
    {{ $is_ccbill = false }} {* Has to be removed once CCBill will be totally integrated *}


    {if !$is_paypal AND !$is_stripe AND !$is_braintree AND !$is_2co AND !$is_ccbill}
        <p class="err_msg">{lang 'No Payment System Enabled!'}</p>
    {else}

        {if $package->enabled == 1 AND $package->price != 0}
            {{ $oDesign = new PaymentDesign }}
            {{ $oPc = new PaymentContext }}
            {{ $oPc->id = $package->packageId }}
            {{ $oPc->price = $package->price }}
            {{ $oPc->name = $package->credits . ' Coins'}}
            {{ $oPc->module = 'coins' }}

            <div class="paypal_logo left">
              <!--  <img src="{url_tpl_mod_img}payment-icon.png" alt="Payment Gateways" title="{lang 'Purchase your subscription safely!'}" /> -->
            </div>

            {if $is_braintree}
                <div class="left vs_marg">
                    {{ $oDesign->buttonBraintree($oPc) }}
                </div>
            {/if}

            {if $is_paypal}
                <div class="left vs_marg">
                    {{ $oDesign->buttonPayPal($oPc) }}
                </div>
            {/if}

            {if $is_stripe}
                <div class="left vs_marg">
                    {{ $oDesign->buttonStripe($oPc) }}
                </div>
            {/if}

            {if $is_2co}
                <div class="left vs_marg">
                    {{ $oDesign->button2CheckOut($oPc) }}
                </div>
            {/if}

            {if $is_ccbill}
                <div class="left vs_marg">
                    {{ $oDesign->buttonCCBill($oPc) }}
                </div>
            {/if}
        {else}
            <p class="err_msg">{lang 'Package requested is not available!'}</p>
        {/if}
    {/if}
</div>
