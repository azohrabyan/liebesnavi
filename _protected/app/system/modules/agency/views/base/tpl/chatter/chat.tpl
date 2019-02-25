<style>
    #chat_container {
        margin: 0;
        padding: 0;
	padding-top: 1px;
        width: 800px;
	border: 1px solid #B70014;
	border-top: 0px solid #B70014;
        border-right: 1px solid #B70014;
	border-left: 1px solid #B70014;

        border-top-left-radius:10px;
        border-top-right-radius:10px;
        border-bottom-left-radius:10px;
        border-bottom-right-radius:10px;
    }
    .partners-container {
        width: 100%
    }
    .partner-list {
        width: 20%;
        padding-left: 0px;
        padding-top: 8px;
        border: 1px solid #B70014;
    }
    .partner-chats {
        width: 80%;
	    border-bottom: 1px solid #B70014;
    }
    .messages-container {
        height: 420px;
	    padding-left: 8px;
        overflow: auto;
        border: 0px solid black;
        border-bottom: 1px solid #B70014;
        border-left: 0px solid #B70014;
        border-right: 0px solid #B70014;
        border-color: #B70014;
    }
    .input-container {
        width: 100%;
        border-color: #B70014;
        border: 0px;
    }
    .editbox {
        width: 617px;
        height: 90px;
	border: 0px;
    }

    .debug-border {
        border: 0px solid black;
    }

    .float-left {
        float: left;
        border-left: 1px solid #B70014;
        border-right: 0px solid #B70014;
        border-top: 1px solid #B70014;
        border-top-left-radius:10px;
        border-top-right-radius:10px;
        border-bottom: 1px solid #B70014;
    }
    .has_new_messages {
        font-weight: bold;
    }

    .chatboxmessage .chatboxmessagefrom {
        font-weight: bold;
    }

    .partner-list .selected .close-chat-btn {
        width: 16px;
        height: 16px;
        background-image: url(/templates/themes/modern/img/icon/close_pop.png);
        background-size: 16px;
        background-repeat: no-repeat;
        float: right;
    }

</style>


<div id="chat_container" class=" container-fluid debug-border">
    <div class="fakes-container debug-border">
        <div data-fake-username="wenwen" class="float-left col-lg-3 debug-border">wenwen</div>
        <div data-fake-username="kukus" class="float-left col-lg-3 debug-border">kukus</div>
    </div>
    <div class="clear"></div>
    <div class="partners-container debug-border" id="chats_of_wenwen">
        <div class="float-left col-lg-3 partners-list debug-border">
            <div id="chat_of_wenwen_with_avo19_selector" class="partner" data-fake-username="wenwen" data-partner-username="avo19">avo19</div>
            <div id="chat_of_wenwen_with_arsenjan_selector" class="partner" data-fake-username="wenwen" data-partner-username="arsenjan">arsenjan</div>
        </div>
        <div class="partner-chats float-left col-lg-9 debug-border">
            <div id="chat_of_wenwen_with_avo19" style="display:none;">
                <div class="messages-container debug-border" data-fake-username="wenwen" data-partner-username="avo19">
                    <div>message 1</div>
                    <div>message 2</div>
                    <div>message 3</div>
                </div>
                <div class="input-container debug-border" data-fake-username="wenwen" data-partner-username="avo19">
                    <textarea class="editbox"></textarea>
                </div>
            </div>
            <div id="chat_of_wenwen_with_arsenjan" >
                <div class="messages-container debug-border" data-fake-username="wenwen" data-partner-username="avo19">
                    <div>message 11</div>
                    <div>message 22</div>
                    <div>message 33</div>
                </div>
                <div class="input-container debug-border" data-fake-username="wenwen" data-partner-username="avo19">
                    <textarea class="editbox"></textarea>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="partners-container debug-border" id="chats_of_kukus" style="display: none;">
    </div>
</div>

