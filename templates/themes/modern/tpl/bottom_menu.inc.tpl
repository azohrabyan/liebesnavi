  <nav class="bottom_nav">
	<span class="dropdown_item_css">
          <a rel="nofollow" href="{{ $design->url('page','main','faq') }}" class="footer" >{lang 'HILFE'}</a>
        </span>
	| &nbsp;

        <span class="dropdown_item_css">
          <a rel="nofollow" href="{{ $design->url('page','main','terms') }}" class="footer" >{lang 'NUTZUNGSBEDIGUNGEN'}</a>
        </span>
        | &nbsp;


        <span class="dropdown_item_css">
          <a rel="nofollow" href="{{ $design->url('page','main','privacy') }}" class="footer" >{lang 'DATENSCHUTZ'}</a>
        </span>
        | &nbsp;


        <span class="dropdown_item_css">
          <a rel="nofollow" href="{{ $design->url('contact','contact','index') }}" class="footer" >{lang 'KONTAKT'}</a>
        </span>
        | &nbsp;

        <span class="dropdown_item_css">
          <a rel="nofollow" href="{{ $design->url('page','main','legalnotice') }}" class="footer" >{lang 'IMPRESSUM'}</a>
        </span>


	&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;

      {if !$is_user_auth AND $is_newsletter_enabled}
        <a href="{{ $design->url('newsletter','home','subscription') }}" title="{lang 'Subscribe to our newsletter!'}" data-popup="block-page">{lang 'Newsletter'}</a> |
      {/if}
      {if $is_invite_enabled}
        <a rel="nofollow" href="{{ $design->url('invite','home','invitation') }}" title="{lang 'Invite your friends!'}" data-popup="block-page">{lang 'Invite'}</a> |
      {/if}

      {if $is_blog_enabled}
      <a href="{{ $design->url('xml','sitemap','index') }}" title="{lang 'Site Map'}" data-load="ajax">{lang 'Site Map'}</a> &nbsp; &nbsp;
      {/if}
  </nav>

