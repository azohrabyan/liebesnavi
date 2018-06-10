
<div class="guest_Section_0">
	<div class="guest_Section_1_bkg">

	<div class="guest_Section_1">
		<br clear=all><br>
        	<div class="guest_Section_1_slogon">{lang 'Navigiere dich zu <br>deinem Glück !'}</div>

		<div class="right  animated fadeInRight">
        		<a href="{{ $design->url('user','main','login') }}" class="right btn btn-primary btn-lg">
            		<strong>{lang 'Login'}</strong>
        		</a>

			<br clear=all><br>

			<div class="join_form_1">
			{{ JoinForm::step1() }}
			</div>
		</div>

	</div>
	</div>

	<div class="center guest_Section_2">
		<br><br>
		<span class="H2_LBR">Top Mitglieder auf Liebesnavi </span>
		<br><br>
		<span class="P_LBR">Navigiere dich mithilfe von Liebesnavi zu deinem Traumpartner ! </span>
    		{if $is_users_block}
			{{ $userDesignModel->carouselProfiles() }}
    		{/if}
		<br>
	</div>

        <div class="guest_Section_3">
		<div class="guest_Section_3_bkg">
		</div>
  	        <div class="guest_Section_3_content">
		<span class="H2_LBR">	Entdecke die Vielfalt des Datings !</span>
		<br><br>
		<span class="P_LBR">
		Du bist einsam und findest einfach nicht die Liebe deines Lebens ? Dann wird es Zeit für Liebesnavi !
		<br>
		Wir ermöglichen dir eine Plattform mit Singles die auf der Suche nach Flirts, Spaß oder nach der großen Liebe sind. 
		<br>
		Hier sind tausende von attraktiven Menschen, die genauso wie DU es satt haben alleine zu sein !
		</span>
                </div>

        </div>
	<br>

        <div class="guest_Section_4">
		<div class="guest_Section_4_content">
		<span class="H2_LBR">Navigiere dich selber zu deinem Glück !</span>
		<br><br>
		<span class="P_LBR">
		Wer kennt es nicht, man sieht eine attraktive Person auf der Straße oder im Cafe , aber traut sich nicht genau diese Person anzusprechen. Man hat Angst sich zu verhaspeln oder man denkt selbst dass man einfach nicht attraktiv genug ist. 
		<br>
		Genau das wird dir auf Liebesnavi nicht passieren !
		<br>
		Um dir genau DIESEN ersten Schritt zu erleichtern, bieten wir dir die Möglichkeit mithilfe von Liebesnavi einfach und simpel mit Singles aus ganz Deutschland in Kontakt zu treten !
		<br><br>
		</span>
		</div>
        </div>
	<div class="guest_Section_5">
		<div class="ft_copy">
          		&copy; <strong>{site_name}</strong>  {{ $design->link() }} {lang '| 2018 ALLE RECHTE VORBEHALTEN'}
		</div>
        	{{ $design->langList() }}

	 	{main_include 'bottom_menu.inc.tpl'}
	</div>
</div>
