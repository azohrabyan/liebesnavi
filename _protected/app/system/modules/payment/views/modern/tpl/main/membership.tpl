<div class="center">

<div class="img-circle"><a href=""><h2>50 Coins</h2><p>&#8364; 9,99</p></a></div>
<div class="img-circle"><a href=""><h2>180 Coins</h2><p>&#8364; 34,99</p></a></div>
<div class="img-circle"><a href=""><h2>485 Coins</h2><p>&#8364; 84,99</p></a></div>
<div class="img-circle"><a href=""><h2>900 Coins</h2><p>&#8364; 149,99</p></a></div>
<div class="img-circle"><a href=""><h2>1.550 Coins</h2><p>&#8364; 249,99</p></a></div>


    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <h3 class="panel-heading">{lang 'Choose your membership'}</h3>
            <div class="panel-body">
                <ul class="list-group">
                    {each $membership in $memberships}
                        {if $membership->enable == 1 AND $membership->price != 0}
                            <li class="list-group-item clearfix">
                                <div class="pull-left">
                                    <h4 class="underline">{% $membership->name %}</h4>
                                    <h5>{% $config->values['module.setting']['currency_sign'] %}{% $membership->price %}</h5>
                                    <p class="italic">{% $membership->description %}</p>
                                </div>
                                <p class="pull-right">
                                    <a class="btn btn-default" href="{{ $design->url('payment', 'main', 'pay', $membership->groupId) }}" title="{lang 'Purchase this membership!'}">{lang 'Choose It'}</a>
                                </p>
                            </li>
                        {/if}
                    {/each}
                </ul>
            </div>
        </div>
    </div>
</div>
