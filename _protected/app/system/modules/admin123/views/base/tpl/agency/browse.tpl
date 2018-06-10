<form method="post" action="{{ $design->url(PH7_ADMIN_MOD,'agency','browse') }}">
    {{ $designSecurity->inputToken('admin_action') }}

    <div class="table-responsive panel panel-default">
        <div class="panel-heading bold">{lang 'Agency Manager'}</div>
        <table class="table table-striped">
            <thead>
                <tr>
                  <th>{lang 'Agency ID#'}</th>
                  <th>{lang 'Agency Name'}</th>
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
                          type="button" onclick="document.location.href = '{{ $design->url(PH7_ADMIN_MOD,'agency','add') }}';return false;"
                          >{lang 'Add'}
                      </button>
                  </th>
                </tr>
            </tfoot>

            <tbody>
                {each $admin in $browse}
                    {{ $adminId = (int)$admin->profileId }}
                    <tr>
                      <td>{adminId}</td>
                      <td>{% $admin->agency_name %}</td>
                      <td>{% $admin->email %}</td>
                      <td>{% $admin->username %}</td>
                      <td class="small">
                          <a href="{{ $design->url(PH7_ADMIN_MOD,'agency','edit',$adminId) }}" title="{lang 'Edit this Agency'}">{lang 'Edit'}</a>
                              | {{ $design->popupLinkConfirm(t('Delete'), PH7_ADMIN_MOD, 'agency', 'delete', $adminId.'_'.$admin->username) }}
                      </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>
</form>

