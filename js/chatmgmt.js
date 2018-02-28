



function talkTo(Username) {


	var dy_id="chatbox_"+Username;

	var add_sinfo='<img align="left" class="avatar"  src="/data/system/modules/user/avatar/img/'+Username+'/7-64.jpg" ><span>'+Username+'</span><br clear="all">';

	var get_sinfo=document.getElementById('User_List_X').innerHTML;

	document.getElementById('main_message').style.display='inline';
	document.getElementById('chatbox_X').innerHTML='<div id="'+dy_id+'"><div class="chatboxcontent"></div></div>';
	document.getElementById('textarea_X').innerHTML='<textarea class="main_chatboxtextarea main_chatboxtextareaselected" onkeydown="return Messenger.checkBoxInputKey(event,this,\''+Username+'\');"></textarea>';

	document.getElementById('User_List_X').innerHTML= add_sinfo + get_sinfo;

//	alert("now shat for user:"+Username);
}
