<div class="table-responsive panel panel-default">
    <div class="panel-heading bold">{lang 'Chatters'}</div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>{lang 'Chatter ID#'}</th>
            <th>{lang 'Chatter Name'}</th>
            <th>{lang 'Email Address'}</th>
            <th>{lang 'User'}</th>
            <th>{lang 'Action'}</th>
        </tr>
        </thead>

        <tfoot>
        <tr>
            <th colspan="5">
                <button
                        class="red btn btn-default btn-md"
                        type="button" onclick="document.location.href = '{{ $design->url(PH7_AGENCY_MOD,'chatter','add') }}';return false;"
                >{lang 'Add'}
                </button>
            </th>
        </tr>
        </tfoot>

        <tbody>
        {each $chatter in $browse}
        {{ $chatterId = (int)$chatter->profileId }}
            <tr>
                <td>{chatterId}</td>
                <td>{% $chatter->name %}</td>
                <td>{% $chatter->email %}</td>
                <td>{% $chatter->username %}</td>
                <td class="small">
                    <a href="{{ $design->url(PH7_AGENCY_MOD,'chatter','edit',$chatterId) }}" title="{lang 'Edit this Chatter'}">{lang 'Edit'}</a>
                    | {{ $design->popupLinkConfirm(t('Delete'), PH7_AGENCY_MOD, 'chatter', 'delete', $chatterId.'_'.$chatter->username) }}
                </td>
            </tr>
        {/each}
        </tbody>
    </table>
</div>