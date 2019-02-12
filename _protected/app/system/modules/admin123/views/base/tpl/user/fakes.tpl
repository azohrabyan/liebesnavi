<form method="post" action="{{ $design->url(PH7_ADMIN_MOD,'user','fakes') }}">
    {{ $designSecurity->inputToken('user_action') }}

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><input type="checkbox" name="all_action" /></th>
                    <th>{lang 'User ID#'}</th>
                    <th>{lang 'Email Address'}</th>
                    <th>{lang 'User'}</th>
                    <th>{lang 'Avatar'}</th>
                    <th>{lang 'Registration Date'}</th>
                    <th>{lang 'Last Activity'}</th>
                    <th>{lang 'Last Edit'}</th>
                    <th>{lang 'Action'}</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                  <th><input type="checkbox" name="all_action" /></th>
                  <th>
                      <button
                          class="red btn btn-default btn-md"
                          type="submit"
                          onclick="return checkChecked()"
                          formaction="{{ $design->url(PH7_ADMIN_MOD,'user','deleteall') }}"
                          >{lang 'Delete'}
                      </button>
                  </th>
                  <th> </th>
                  <th> </th>
                  <th> </th>
                  <th> </th>
                  <th> </th>
                  <th> </th>
                  <th> </th>
                </tr>
            </tfoot>

            <tbody>
                {each $user in $browse}
                    <tr>
                        <td><input type="checkbox" name="action[]" value="{% $user->profileId %}_{% $user->username %}" /></td>
                        <td>{% $user->profileId %}</td>
                        <td>{% $user->email %}</td>
                        <td>
                            {{ $design->getProfileLink($user->username) }}<br />
                            <span class="gray">{% $user->firstName %}</span>
                        </td>
                        <td>{{ $avatarDesign->get($user->username, $user->firstName, null, 32) }}</td>
                        <td class="small">{% $dateTime->get($user->joinDate)->dateTime() %}</td>
                        <td class="small">
                            {if !empty($user->lastActivity)}
                                {% $dateTime->get($user->lastActivity)->dateTime() %}
                            {else}
                                {lang 'No login'}
                            {/if}
                        </td>
                        <td class="small">
                            {if !empty($user->lastEdit)}
                                {% $dateTime->get($user->lastEdit)->dateTime() %}
                            {else}
                                {lang 'No editing'}
                            {/if}
                        </td>
                        <td class="small">
                            <a href="{{ $design->url('user','setting','edit',$user->profileId) }}" title="{lang "Edit User's Profile"}">{lang 'Edit'}</a> |
                            <a href="{{ $design->url('user','setting','avatar',"$user->profileId,$user->username,$user->firstName,$user->sex", false) }}" title="{lang "Edit User's Avatar"}">{lang 'Edit Avatar'}</a> |
                            <a href="{{ $design->url('user','setting','design',"$user->profileId,$user->username,$user->firstName,$user->sex", false) }}" title="{lang "Edit User's Wallpaper"}">{lang 'Edit Wallpaper'}</a>
                            | <a href="{{ $design->url(PH7_ADMIN_MOD,'user','loginuseras',$user->profileId) }}" title="{lang 'Login as a user (to edit all this user account).'}">{lang 'Login as'}</a>

                            {if $user->active != 1}
                                | {{ $design->popupLinkConfirm(t('Approve'), PH7_ADMIN_MOD, 'user', 'approve', $user->profileId) }}
                                or {{ $design->popupLinkConfirm(t('Disapprove (notified user by email)'), PH7_ADMIN_MOD, 'user', 'disapprove', $user->profileId) }}
                            {/if}

                            | {{ $design->popupLinkConfirm(t('Delete'), PH7_ADMIN_MOD, 'user', 'delete', $user->profileId.'_'.$user->username) }}
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>
</form>

{main_include 'page_nav.inc.tpl'}
