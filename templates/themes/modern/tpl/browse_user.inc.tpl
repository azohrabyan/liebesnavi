<div class="animated fadeInLeft left box-left">
    <div role="search" class="design-box">
        {{ SearchUserCoreForm::quick(PH7_WIDTH_SEARCH_FORM) }}
    </div>
</div>
<div class="box-right">
    {if empty($users)}
        <p class="center bold">{lang 'Whoops! No users found.'}</p>
    {else}

        {each $user in $users}
            {{ $country_name = t($user->country) }}

            {* Members Age *}
            {{ $aAge = explode('-', $user->birthDate); $age = (new Framework\Math\Measure\Year($aAge[0], $aAge[1], $aAge[2]))->get() }}

	  <div class="search_avatar">
            <div class="thumb_photo">
                {{ UserDesignCoreModel::userStatus($user->profileId) }}

                {* Sex Icon *}
                {if $user->sex === 'male'}
                    {{ $sex_ico = ' <span class=green>&#9794;</span>' }}
                {elseif $user->sex === 'female'}
                    {{ $sex_ico = ' <span class=pink>&#9792;</span>' }}
                {else}
                    {{ $sex_ico = '' }}
                {/if}

                {{ $avatarDesign->get($user->username, $user->firstName, $user->sex, 100, true) }}
<!--
                <p class="cy_ico">
                    <a href="{% (new UserCore)->getProfileLink($user->username) %}" title="{lang 'Name: %0%', $user->firstName}<br> {lang 'Gender: %0% %1%', t($user->sex), $sex_ico}<br> {lang 'Seeking: %0%', t($user->matchSex)}<br> {lang 'Age: %0%', $age}<br> {lang 'From: %0%', $country_name}<br> {lang 'City: %0%', $this->str->upperFirst($user->city)}<br> {lang 'State: %0%', $this->str->upperFirst($user->state)}">
		</p>
-->
                <p class="cy_ico">
                    <a href="{% (new UserCore)->getProfileLink($user->username) %}" class="red2">
                </p>

		</div>
                       <strong><br>{% $this->str->extract($user->firstName,0,10, '...') %}</strong>
                    </a>
		<br>  {lang '%0% |  %1% Jahre', t($user->sex),$age}
		<br>  {lang '%0% | %1% ', $country_name, $this->str->upperFirst($user->city)}
		<br>
		<br>  {lang '%0%', substr($user->description,0,50)}

		<div class="search_avatar_inside">
		 <a rel="nofollow" onclick="Messenger.chatWith('{lang '%0%',$user->username}', '{% (new UserDesignCore)->getUserAvatar($user->username, $user->sex, '64') %}')" href="javascript:void(0)"><img src="/images/chat_s_off.png" border=0/ class="chat_link"><a>
		</div>
<!--
		 | <a href="/mail/compose/{lang '%0%',$user->username}"><img src="/images/mail_s_off.jpg" border=0/></a> |

                {if $is_admin_auth}
                       | <a href="{{ $design->url(PH7_ADMIN_MOD,'user','loginuseras',$user->profileId) }}" title="{lang 'Login As a member'}"><img src="/images/login_as_s_off.jpg"></a> |
                        {if $user->ban == '0'}
                            {{ $design->popupLinkConfirm(t('<img src="/images/user_ban_s_off.jpg">'), PH7_ADMIN_MOD, 'user', 'ban', $user->profileId) }}
                        {else}
                            {{ $design->popupLinkConfirm(t('UnBan'), PH7_ADMIN_MOD, 'user', 'unban', $user->profileId) }}
                        {/if}
                        | {{ $design->popupLinkConfirm(t('<img src="/images/user_del_s_off.jpg">'), PH7_ADMIN_MOD, 'user', 'delete', $user->profileId.'_'.$user->username) }}
                {/if}
-->
	  </div>
        {/each}
        {main_include 'page_nav.inc.tpl'}
    {/if}
</div>
