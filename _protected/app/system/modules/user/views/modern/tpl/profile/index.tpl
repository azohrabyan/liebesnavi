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
  <ol id="toc">
    {if $is_logged AND !$is_own_profile AND $is_mail_enabled}
     <a rel="nofollow" href="{mail_link}"><span>{lang 'Send Message'}</span></a>
    {/if}
     |
    {if $is_logged AND !$is_own_profile AND $is_im_enabled}
      <a rel="nofollow" href="{messenger_link}"><span>{lang 'Live Chat'}</span></a>
    {/if}
    {if $is_logged AND !$is_own_profile AND $is_lovecalculator_enabled}
      <li><a href="{{ $design->url('love-calculator','main','index',$username) }}" title="{lang 'Love Calculator'}"><span>{lang 'Match'} <b class="pink2">&hearts;</b></span></a></li>
    {/if}
  </ol>

  <div class="content" id="general">
    {{ UserDesignCoreModel::userStatus($id) }}
    {{ $avatarDesign->lightBox($username, $first_name, $sex, 400) }}


    <h2> {first_name} {middle_name} {last_name}</h2>
    <div class="break"></div>

    <p><span class="bold">{lang $sex} | {lang 'Age:'} {age}</span></p>
    <p>{country} &nbsp;<img src="{{ $design->getSmallFlagIcon($country_code) }}" title="{country}" alt="{country}" /> | {city}</p>
	 <div class="break"></div>
    {if !empty($description)}
	<div class="quote italic">{description}</div>
    {/if}


    <div class="break"></div>

    {* Profile's Fields *}
    {each $key => $val in $fields}

      {if $key != 'description' AND $key != 'middleName' AND !empty($val)}
        {{ $val = escape($val, true) }}

        {if $key == 'height'}
          <p><span class="bold">{lang 'Height:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&height='.$val) }}">{{ (new Framework\Math\Measure\Height($val))->display(true) }}</a></span></p>

        {elseif $key == 'weight'}
          <p><span class="bold">{lang 'Weight:'}</span> <span class="italic"><a href="{{ $design->url('user','browse','index', '?country='.$country_code.'&weight='.$val) }}">{{ (new Framework\Math\Measure\Weight($val))->display(true) }}</a></span></p>

        {elseif $key == 'website'}
          <p>{{ $design->favicon($val) }}&nbsp;&nbsp;<span class="bold">{lang 'Site/Blog:'}</span> <span class="italic">{{ $design->urlTag($val) }}</span></p>

        {elseif $key == 'socialNetworkSite'}
          <p>{{ $design->favicon($val) }}&nbsp;&nbsp;<span class="bold">{lang 'Social Profile:'}</span> <span class="italic">{{ $design->urlTag($val) }}</span></p>

        {else}
          {{ $lang_key = strtolower($key) }}

        {/if}

      {/if}

    {/each}


    
    {if !empty($join_date) && !empty($last_activity)}
      <p><span class="bold">{lang 'Join Date:'}</span> <span class="italic">{join_date}</span> |
      <span class="bold">{lang 'Last Activity:'}</span> <span class="italic">{last_activity}</span></p>

      <div class="break"></div>
    {/if}

    <p><span class="bold">{lang 'Views:'}</span> <span class="italic">{% Framework\Mvc\Model\Statistic::getView($id,'Members') %}

    {{ RatingDesignCore::voting($id,'Members') }} </span></p>
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

  {if $is_video_enabled}
    <div class="content" id="video">
      <script>
        var url_video_block = '{{ $design->url('video','main','albums',$username) }}';
        $('#video').load(url_video_block + ' #video_block');
      </script>
    </div>
  {/if}

  {if $is_forum_enabled}
    <div class="content" id="forum">
      <script>
        var url_forum_block = '{{ $design->url('forum','forum','showpostbyprofile',$username) }}';
        $('#forum').load(url_forum_block + ' #forum_block');
      </script>
    </div>
  {/if}

  {if $is_note_enabled}
    <div class="content" id="note">
      <script>
        var url_note_block = '{{ $design->url('note','main','author',$username) }}';
        $('#note').load(url_note_block + ' #note_block');
      </script>
    </div>
  {/if}

  <div class="content_visitor" id="visitor">
    <script>
      var url_visitor_block = '{{ $design->url('user','visitor','index',$username) }}';
      $('#visitor').load(url_visitor_block + ' #visitor_block');
    </script>
  </div>

  <div class="clear"></div>
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
