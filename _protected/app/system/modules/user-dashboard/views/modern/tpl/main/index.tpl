   {* Creating Objects *}
      {{ $oSession = new Framework\Session\Session() }}


<div class="row">
    {* "My Profile" block don't really fit well on small mobile devices, so ignore it if it's the case *}
    {if !$browser->isMobile()}
        <div class="left col-xs-12 col-sm-4 col-md-3">
      {*       <h2>{lang 'My Profile'}</h2> *}

            {{ $avatarDesign->lightBox($username, $first_name, $sex, 400) }}

            <ul>
                <li>
                    <a href="{{ $design->url('user','setting','avatar') }}" title="{lang 'Change My Profile Photo'}"><i class="fa fa-upload"></i> {lang 'Change Profile Photo'}</a>
                </li>
                <li><a href="{{ $design->url('user','setting','edit') }}" title="{lang 'Edit My Profile'}"><i class="fa fa-cog fa-fw"></i> {lang 'Edit Profile'}</a>
               </li>

              <li><a href="{% (new UserCore)->getProfileLink($oSession->get('member_username')) %}" title="{lang 'See My Profile'}"><i class="fa fa-user fa-fw"></i> {lang 'See My Profile'}</a></li>


 <li><a href="{{ $design->url('picture','main','albums', $oSession->get('member_username')) }}" title="{lang 'My Albums'}" data-load="ajax"><i class="fa fa-picture-o"></i> {lang 'My Albums'}</a></li>


 	<li><a href="{{ $design->url('user','add-album','') }}" title="{lang 'Add a new album'}" data-load="ajax"><i class="fa fa-picture-o"></i> {lang 'Add a new album'}</a></li>

          {if $is_mail_enabled}
            <li><a href="{{ $design->url('mail','main','inbox') }}" title="{lang 'My Emails'}" ><i class="fa fa-envelope-o fa-fw"></i> {lang 'Mail'} {if $count_unread_mail}<span class="badge">{count_unread_mail}</span>{/if}
                <li>&nbsp; &nbsp;<a href="{{ $design->url('mail','main','compose') }}" title="{lang 'Compose'}"><i class="fa fa-pencil"></i> {lang 'Compose'}</a></li>
                <li>&nbsp; &nbsp;<a href="{{ $design->url('mail','main','inbox') }}" title="{lang 'Inbox - Messages Received'}"><i class="fa fa-inbox"></i> {lang 'Inbox'}</a></li>
                <li>&nbsp; &nbsp;<a href="{{ $design->url('mail','main','outbox') }}" title="{lang 'Messages Sent'}"><i class="fa fa-paper-plane-o"></i> {lang 'Sent'}</a></li>
                <li>&nbsp; &nbsp;<a href="{{ $design->url('mail','main','trash') }}" title="{lang 'Trash'}"><i class="fa fa-trash-o"></i> {lang 'Trash'}</a></li>

	   {/if}

                <li>
                    <a href="{{ $design->url('user','setting','privacy') }}" title="{lang 'My Privacy Settings'}"><i class="fa fa-user-secret"></i> {lang 'Privacy Setting'}</a>
                </li>
                {if $is_valid_license}
                    <li>
                        <a href="{{ $design->url('payment','main','info') }}" title="{lang 'My Membership'}"><i class="fa fa-credit-card"></i> {lang 'Membership Details'}</a>
                    </li>
                {/if}
                <li><a href="{{ $design->url('user','setting','password') }}" title="{lang 'Change My Password'}"><i class="fa fa-key fa-fw"></i> {lang 'Change Password'}</a></li>


              <li><a href="{{ $design->url('user','main','logout') }}" title="{lang 'Logout'}"><i class="fa fa-sign-out"></i> {lang 'Logout'}</a></li>

            </ul>
	{*  <div class="site_quick_search"> * }
	{*  {SearchUserCoreForm::quick() } * }
	{*  </div> *} 

        </div>
    {/if}

    <div class="left col-xs-12 col-sm-6 col-md-6">
        {{ $userDesignModel->profilesBlock() }}

        <div class="clear"></div>


        {if $is_picture_enabled}
	<div id="dashboard_photo_gallery">
            <div class="content" id="picture">
                <script>
                    var url_picture_block = '{{ $design->url('picture','main','albums',$username) }}';
                    $('#picture').load(url_picture_block + ' #picture_block');
                </script>
            </div>
            <div class="clear"></div>
	</div>
        {/if}

        <div class="clear"></div>

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
