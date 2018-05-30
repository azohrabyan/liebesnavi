<div class="center">
        <div class="panel panel-default">
            <h3 class="panel-heading">{lang 'Choose your package'}</h3>
            <div class="panel-body">
                <ul class="list-group">
                    {each $package in $creditPackages}
                        {if $package->enabled == 1 AND $package->price != 0}
                            <div class="img-circle"><a href="{{ $design->url('payment', 'coins', 'pay', $package->packageId) }}"><h2>{% $package->credits %} Coins</h2><p>{% $config->values['module.setting']['currency_sign'] %}{% $package->price %}</p></a></div>
                        {/if}
                    {/each}
                </ul>
            </div>
        </div>
</div>
