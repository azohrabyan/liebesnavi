<div class="center" id="visitor_block">
    {if $user_views_setting == 'no'}
        <div class="center alert alert-warning">{lang 'To see the new members who view your profile, you must first change'} 
<a href="{{ $design->url('user','setting','privacy') }}">{lang 'your privacy settings'}</a>.</div>
    {/if}

    {if empty($error)}
<!--
        <p class="red3"> {lang 'Recently Viewed By:'} <strong><a href="{{ $design->url('user','visitor','index',$username) }}">{visitor_number}</a></strong></p><br />
-->
        <a href="{{ $design->url('user','visitor','index',$username) }}" class="red_link">{lang 'Recently Viewed By'}</a>

        {each $v in $visitors}
            <div class="s_photo_s">
                {{ $avatarDesign->get($v->username, $v->firstName, $v->sex, 100, true) }}
            </div>
        {/each}

        {main_include 'page_nav.inc.tpl'}
        <br />
        </p>
    {else}
        <p>{error}</p>
    {/if}
</div>
