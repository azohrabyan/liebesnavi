{if !empty($img_background)}
  {* Sets The Profile Background *}
  <script>
    document.body.style.backgroundImage="url('{url_data_sys_mod}user/background/img/{username}/{img_background}')";
    document.body.style.backgroundRepeat='no-repeat';
    document.body.style.backgroundPosition='center';
    document.body.style.backgroundSize='cover';
  </script>
{/if}

{if empty($error)}

  <div class="content" id="general">
    {{ UserDesignCoreModel::userStatus($id) }}
    {{ $avatarDesign->lightBox($username, $first_name, $sex, 400) }}

    {if $is_logged AND !$is_own_profile AND $is_mail_enabled}
      <a rel="nofollow" href="{mail_link}"><i class="fa fa-send"></i> <span>{lang 'Send Message'}</span></a> &nbsp;
    {/if}
    {if $is_logged AND !$is_own_profile AND $is_im_enabled}
       <a rel="nofollow" href="{messenger_link}"><span><i class="fa fa-pencil"></i> {lang 'Live Chat'}</span></a>
    {/if}

<br>
<br>

    <p><span class="bold">{lang 'I am a:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&match_sex='.$sex) }}">{lang $sex}</a></span></p>
    <div class="break"></div>

    {if !empty($match_sex)}
      <p><span class="bold">{lang 'Looking for a:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code) }}{match_sex_search}">{lang $match_sex}</a></span></p>
      <div class="break"></div>
    {/if}

    <p><span class="bold">{lang 'First name:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&first_name='.$first_name) }}">{first_name}</a></span></p>
    <div class="break"></div>

    {if !empty($middle_name)}
      <p><span class="bold">{lang 'Middle name:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&middle_name='.$middle_name) }}">{middle_name}</a></span></p>
      <div class="break"></div>
    {/if}

    {if !empty($last_name)}
      <p><span class="bold">{lang 'Last name:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&last_name='.$last_name) }}">{last_name}</a></span></p>
      <div class="break"></div>
    {/if}

    {if !empty($age)}
      <p><span class="bold">{lang 'Age:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&age='.$birth_date) }}">{age}</a> <span class="gray">({birth_date_formatted})</span></span></p>
      <div class="break"></div>
    {/if}

    {* Profile's Fields *}
    {each $key => $val in $fields}

      {if $key != 'description' AND $key != 'middleName' AND !empty($val)}
        {{ $val = escape($val, true) }}

        {if $key == 'height'}
          <p><span class="bold">{lang 'Height:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&height='.$val) }}">{{ (new Framework\Math\Measure\Height($val))->display(true) }}</a></span></p>

        {elseif $key == 'weight'}
          <p><span class="bold">{lang 'Weight:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&weight='.$val) }}">{{ (new Framework\Math\Measure\Weight($val))->display(true) }}</a></span></p>

        {elseif $key == 'country'}
          <p><span class="bold">{lang 'Country:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code) }}">{country}</a></span>&nbsp;&nbsp;<img src="{{ $design->getSmallFlagIcon($country_code) }}" title="{country}" alt="{country}" /></p>

        {elseif $key == 'city'}
          <p><span class="bold">{lang 'City/Town:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&city='.$city) }}">{city}</a></span></p>

        {elseif $key == 'state'}
          <p><span class="bold">{lang 'State/Province:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&state='.$state) }}">{state}</a></span></p>

        {elseif $key == 'zipCode'}
          <p><span class="bold">{lang 'Postal Code:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&zip_code='.$val) }}">{val}</a></span></p>

        {elseif $key == 'website'}
          <p>{{ $design->favicon($val) }}&nbsp;&nbsp;<span class="bold">{lang 'Site/Blog:'}</span> <span class="italic">{{ $design->urlTag($val) }}</span></p>

        {elseif $key == 'socialNetworkSite'}
          <p>{{ $design->favicon($val) }}&nbsp;&nbsp;<span class="bold">{lang 'Social Profile:'}</span> <span class="italic">{{ $design->urlTag($val) }}</span></p>

        {else}
          {{ $lang_key = strtolower($key) }}

          {if strstr($key, 'url')}
            <p>{{ $design->favicon($val) }}&nbsp;&nbsp;<span class="bold">{lang $lang_key}</span> <span class="italic">{{ $design->urlTag($val) }}</span></p>
          {else}
            <p><span class="bold">{lang $lang_key}</span> <span class="italic">{val}</span></p>
          {/if}
        {/if}

        <div class="break"></div>
      {/if}

    {/each}

    {if !empty($join_date)}
      <p><span class="bold">{lang 'Join Date:'}</span> <span class="italic">{join_date}</span></p>
      <div class="break"></div>
    {/if}

    {if !empty($last_activity)}
      <p><span class="bold">{lang 'Last Activity:'}</span> <span class="italic">{last_activity}</span></p>
      <div class="break"></div>
    {/if}

    <p><span class="bold">{lang 'Views:'}</span> <span class="italic">{% Framework\Mvc\Model\Statistic::getView($id,'Members') %}</span></p>
    <div class="break"></div>

    {{ RatingDesignCore::voting($id,'Members') }}

    {if !empty($description)}
      <div class="profile_desc">
        <p class="bold">{lang 'Description:'}</p> <div class="quote italic">{description}</div>
        <div class="ad_336_280">{{ $designModel->ad(336,280) }}</div>
      </div>
    {/if}

  </div>

  {if $is_friend_enabled}
    <div class="content" id="friend">
      <script>
        var url_friend_block = '{{ $design->url('friend','main','index',$username) }}';
        $('#friend').load(url_friend_block + ' #friend_block');
      </script>
    </div>
  {/if}

  {if $is_friend_enabled AND $is_logged AND !$is_own_profile}
    <div class="content" id="mutual_friend">
      <script>
        var url_mutual_friend_block = '{{ $design->url('friend','main','mutual',$username) }}';
        $('#mutual_friend').load(url_mutual_friend_block + ' #friend_block');
      </script>
    </div>
  {/if}

  {if $is_picture_enabled}
    <div class="content" id="picture">
      <script>
        var url_picture_block = '{{ $design->url('picture','main','albums',$username) }}';
        $('#picture').load(url_picture_block + ' #picture_block');
      </script>
    </div>
  {/if}


  <div class="content" id="visitor">
    <script>
      var url_visitor_block = '{{ $design->url('user','visitor','index',$username) }}';
      $('#visitor').load(url_visitor_block + ' #visitor_block');
    </script>
  </div>

  <div class="clear"></div>
  <p class="center">{{ $design->like($username, $first_name, $sex) }} | {{ $design->report($id, $username, $first_name, $sex) }}</p>
  {{ $design->likeApi() }}

  {{ CommentDesignCore::link($id, 'Profile') }}

  {* Setup the profile tabs *}
  <script src="{url_static_js}tabs.js"></script>
  <script>
    tabs('p', [
          'general',
          'map',
          {if $is_relatedprofile_enabled}'related_profile',{/if}
          {if $is_friend_enabled}
            'friend',
            {if $is_logged AND !$is_own_profile}'mutual_friend',{/if}
          {/if}
          {if $is_picture_enabled}'picture',{/if}
          {if $is_video_enabled}'video',{/if}
          {if $is_forum_enabled}'forum',{/if}
          {if $is_note_enabled}'note',{/if}
          'visitor'
        ]);
  </script>

  <script>
    /* Google Map has issues with the screen map (it displays only gray screen) when it isn't visible when loaded (through profile ajax tabs), so just refresh the page to see correctly the map */
    $('ol#toc li a[href=#map]').click(function() {
      location.reload();
    });
  </script>

  {* Signup Popup *}
  {if !$is_logged AND !AdminCore::auth()}
    {{ $design->staticFiles('js', PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS, 'signup_popup.js') }}
  {/if}

{else}
  <p class="center">{error}</p>
{/if}
