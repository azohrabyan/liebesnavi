   {* Creating Objects *}
      {{ $oSession = new Framework\Session\Session() }}


<div class="row">
    {* "My Profile" block don't really fit well on small mobile devices, so ignore it if it's the case *}
    {if !$browser->isMobile()}
        <div class="animated fadeInLeft left col-xs-12 col-sm-4 col-md-3 profile_submenu_all">
      {*       <h2>{lang 'My Profile'}</h2> *}

            {{ $avatarDesign->lightBox($username, $first_name, $sex, 400) }}

            <ul  class="profile_submenu_ul">
		<br>
		<a href="{{ $design->url('user','setting','avatar') }}" >
                <li class="profile_submenu">
		    &nbsp;<i class="fa fa-upload "></i>&nbsp; {lang 'Change Profile Photo'}
                </li>
		</a>

		 <a href="{{ $design->url('user','setting','edit') }}">
                <li  class="profile_submenu">
			&nbsp;<i class="fa fa-cog fa-fw"></i>&nbsp; {lang 'Edit Profile'}
               </li>
		</a>

		 <a href="{% (new UserCore)->getProfileLink($oSession->get('member_username')) %}">
		<li class="profile_submenu">
			&nbsp;<i class="fa fa-user fa-fw"></i>&nbsp; {lang 'See My Profile'}
		</li>
		</a>

		 <a href="{{ $design->url('picture','main','albums', $oSession->get('member_username')) }}">
 		<li  class="profile_submenu" >
			&nbsp;<i class="fa fa-picture-o"></i>&nbsp; {lang 'My Albums'}
		</li></a>

		<a href="{{ $design->url('user','add-album','') }}">
 		<li  class="profile_submenu" >
			&nbsp;<i class="fa fa-picture-o"></i>&nbsp; {lang 'Add a new album'}
		</li></a>

		<a href="{{ $design->url('user','setting','password') }}">
                <li class="profile_submenu" >
			&nbsp;<i class="fa fa-key fa-fw"></i>&nbsp; {lang 'Change My Password'}
		</li></a>

		<a href="{{ $design->url('user','main','logout') }}" >
              <li class="profile_submenu">
			&nbsp;<i class="fa fa-sign-out"></i>&nbsp; {lang 'Logout'}
		 </li></a>



            </ul>
	{*  <div class="site_quick_search"> * }
	{*  {SearchUserCoreForm::quick() } * }
	{*  </div> *}
		<br>
        </div>
    {/if}

    <div class="left col-xs-12 col-sm-6 col-md-6">
        {{ $userDesignModel->profilesBlock() }}



        {if $is_picture_enabled}
<!--
	<div id="dashboard_photo_gallery">
            <div class="content" id="picture">
                <script>
                    var url_picture_block = '{{ $design->url('picture','main','albums',$username) }}';
                    $('#picture').load(url_picture_block + ' #picture_block');
                </script>
            </div>
            <div class="clear"></div>
	</div>
-->
        {/if}

	<div class="make_space">

	</div>

<!--
        <div class="clear"></div>
-->
	<div id="dashboard_recent_view">

        <div class="content" id="visitor">
            <script>
                var url_visitor_block = '{{ $design->url('user','visitor','index',$username) }}';
                $('#visitor').load(url_visitor_block + ' #visitor_block');
            </script>
        </div>
	</div>

        {if $is_video_enabled}
            <h2 class="center underline">{lang 'My video albums'}</h2>
            <div class="content" id="video">
                <script>
                    var url_video_block = '{{ $design->url('video','main','albums',$username) }}';
                    $('#video').load(url_video_block + ' #video_block');
                </script>
            </div>
            <div class="clear"></div>
        {/if}

        {if $is_forum_enabled}
            <h2 class="center underline">{lang 'My discussions'}</h2>
            <div class="content" id="forum">
                <script>
                    var url_forum_block = '{{ $design->url('forum','forum','showpostbyprofile',$username) }}';
                    $('#forum').load(url_forum_block + ' #forum_block');
                </script>
            </div>
            <div class="clear"></div>
        {/if}

        {if $is_note_enabled}
            <h2 class="center underline">{lang 'My notes'}</h2>
            <div class="content" id="note">
                <script>
                    var url_note_block = '{{ $design->url('note','main','author',$username) }}';
                    $('#note').load(url_note_block + ' #note_block');
                </script>
            </div>
            <div class="clear"></div>
        {/if}

    </div>

    </div>
</div>

<script>
    $(document).ready(function() {
        $('ul.zoomer_pic').slick({
            dots: true,
            infinite: false,
            slidesToShow: 6,
            slidesToScroll: 6,
            adaptiveHeight: true
        })
    });
</script>
