<style>
    #chat_container {
        margin: 0;
        padding: 0;
        width: 800px;
    }
    .partners-container {
        width: 100%
    }
    .partner-list {
        width: 20%;
        border: 1px solid black;
    }
    .partner-chats {
        width: 80%;
    }
    .messages-container {
        height: 450px;
        overflow: scroll;
        border: 1px solid black;
    }
    .input-container {
        width: 100%;
        border: 1px solid black;
    }
    .editbox {
        width: 617px;
        height: 90px;
    }
    .debug-border {
        border: 1px solid black;
    }
    .float-left {
        float: left;
    }
    .has_new_messages {
        font-weight: bold;
    }
</style>
<div id="chat_container" class="container-fluid debug-border">
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
