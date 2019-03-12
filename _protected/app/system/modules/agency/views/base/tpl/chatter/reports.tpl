<div class="table-responsive panel panel-default" style="width:500px;">
    <div class="panel-heading bold">{lang 'Reports'}</div>
    <table class="table table-striped">
        {each $chatter in $report}
        {{ $chatterName = $chatter['name'] }}
            <tr>
                <th colspan="3">{chatterName}</th>
            </tr>
            <tr>
                <th>{lang 'Month'}</th>
                <th>{lang 'Sent'}</th>
                <th>{lang 'Received'}</th>
            </tr>
            {each $m in $chatter['stats']}
                <tr>
                    <th>{% $m->mnth %}</th>
                    <td>{% $m->sent %}</td>
                    <td>{% $m->recv %}</td>
                </tr>
            {/each}
        {/each}
    </table>
</div>
