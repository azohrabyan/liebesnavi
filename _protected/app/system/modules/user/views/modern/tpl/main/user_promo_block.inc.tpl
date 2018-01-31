<h1 class="s_tMarg"  id="promo_text" >{slogan}</h1>
{if $is_users_block}
    <div class="center profiles_window thumb pic_block">
        {{ $userDesignModel->profiles(0, $number_profiles) }}
    </div>
{/if}

<div class="s_tMarg" id="promo_text">
    <h3>{lang 'Meet &amp; date amazing people near %0%! ', $design->geoIp(false)}</h3>
    <h4>{lang 'You are on the best place for meeting new people nearby! Chat, Flirt, Socialize and have Fun!'}</h4>
</div>
