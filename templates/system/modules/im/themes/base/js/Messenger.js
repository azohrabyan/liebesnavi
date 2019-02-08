/*
 * Title:           Chat Messenger
 * Description:     This Chat Messenger allows users to instantly communicate via messages and smileys.
 *                  It also has a warning system to alert the arrival of new messages.
 *
 * Author:          Pierre-Henry Soria <ph7software@gmail.com>
 * Copyright:       (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * License:         GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * Version:         1.4
 */

// This feature is only for members!


// Global variables
var sOriginalTitle = '', bWindowFocus = true;

var Messenger = {

    // Properties
    sUsername: null,
    sBoxTitle: '',
    iHeartbeatCount: 0,
    iMinHeartbeat: 1000,
    iMaxHeartbeat: 40000,
    iBlinkOrder: 0,
    sMessage: '',

    aNewMessages: new Array,
    aNewMessagesWin: new Array,
    aBoxes: new Array,
    aBoxFocus: new Array,
    aMinimizedBoxes: new Array,

    selectedUser: "",

    // Constructor
    Messenger: function () {
        this.iHeartbeatTime = this.iMinHeartbeat;
        oMe = this; // Self Object

        return this;
    },

    show: function() {
	$('#messenger_blur').css('display', 'inline');
        $('#messenger').css('display', 'inline');
        var username = $('.messenger_user_list .selected').data('username');
        var chatContent = $('#chat_content_' + username);
        chatContent.scrollTop(chatContent[0].scrollHeight);
    },

    // Methods
    startSession: function () {
        $.ajax(
            {
                url: pH7Url.base + "im/asset/ajax/Messenger/?act=startsession",
                type: 'POST',
                cache: false,
                dataType: "json",
                success: function (oData) {
                    // console.log(oData);
                    oMe.sUsername = oData.user;
                    oMe.chats = oData.chats;

                    // oMe.createBox('asd', false);
                    var firstUser = '';
                    $.each(oData.chats, function (username, chat) {
                        if (firstUser === '') {
                            firstUser = username;
                        }

                        Messenger.createBox(username, chat.avatar_url);

                        $.each(chat.messages, function(i, m) {
                            $('#chat_content_' + username).append('<div class="chatboxmessage">' +
                                '<span class="chatboxmessagefrom">' + m.from + ':&nbsp;&nbsp;</span>' +
                                '<span class="chatboxmessagecontent">' + m.message + '</span></div>');
                        });
                    });

                    oMe.selectUser(firstUser);

                    setTimeout(function () {
                        oMe.heartbeat()
                    }, oMe.iHeartbeatTime);
                }
            });
    },

    toggleUser: function() {
        Messenger.hideSelectedUser();
        Messenger.selectUser($(this).data('username'));
    },

    hideSelectedUser: function() {
        $('#user_' + Messenger.selectedUser).removeClass('selected');
        $('#chat_content_' + Messenger.selectedUser).hide();
        $('#chat_input_' + Messenger.selectedUser).hide();
    },

    selectUser: function(username) {
        Messenger.selectedUser = username;
        $('#user_' + Messenger.selectedUser).addClass('selected');
        var chatContent = $('#chat_content_' + Messenger.selectedUser);
        chatContent.show();
        $('#chat_input_' + Messenger.selectedUser).show();
        $('#chat_input_' + Messenger.selectedUser + ' textarea').focus();
        chatContent.scrollTop(chatContent[0].scrollHeight);
        $('#user_' + Messenger.selectedUser).removeClass('has_new_messages');
        if ($('.messenger_user_list .has_new_messages').length === 0) {
            $('#top_chat').removeClass('has_new_messages');
        }
    },

    chatWith: function (username) {
        if (this._check(username)) {
            $('#messenger').css('display', 'inline');
            this.createBox(username);
            oMe.hideSelectedUser();
            oMe.selectUser(username);
        }
        else {
            $('.msg').addClass('alert alert-danger').text(pH7LangIM.cannot_chat_yourself).fadeOut(5000);
        }
    },

    createBox: function (username, avatarUrl) {
        if ($(".messenger_user_list #user_" + username).length <= 0) {
            $("<div />")
                .attr("id", "user_" + username)
                .data("username", username)
                .css('cursor', 'pointer')
                .html('<img align="left" class="avatar"  src="' + avatarUrl + '" ><a>'+username+'</a>')
                .appendTo($(".messenger_user_list"));

            $('#user_' + username)
                .click(Messenger.toggleUser);

            $("<div />")
                .attr("id", "chat_content_" + username)
                .css('display', 'none')
                .addClass('messenger_chat_content')
                .appendTo($(".messenger_chat_content_container"));

            $("<div />")
                .attr("id", "chat_input_" + username)
                .css('display', 'none')
                .html('<textarea class="messenger_textarea messenger_textareaselected" onkeydown="return Messenger.checkBoxInputKey(event,this,\'' + username + '\');"></textarea>')
                .appendTo($(".messenger_chat_send"));
        }
    },
    heartbeat: function () {
        /*var iItemsFound = 0;

        if (bWindowFocus == false) {
            var iBlinkNumber = 0, iTitleChanged = 0;

            for (x in this.aNewMessagesWin) {
                if (this.aNewMessagesWin[x] == true) {
                    ++iBlinkNumber;
                    if (iBlinkNumber >= this.iBlinkOrder) {
                        document.title = x + ' ' + pH7LangIM.say;
                        iTitleChanged = 1;
                        break;
                    }
                }
            }

            if (iTitleChanged == 0) {
                document.title = sOriginalTitle;
                this.iBlinkOrder = 0;
            }
            else {
                ++this.iBlinkOrder;
            }

        }
        else {
            for (x in this.aNewMessagesWin)
                this.aNewMessagesWin[x] = false;
        }

        for (x in this.aNewMessages) {
            if (this.aNewMessages[x] == true) {
                if (this.aBoxFocus[x] == false) {
                    oMe.soundAlert();
                    //TODO: Add toggle all or none policy, otherwise it looks awkward.
                    $('#chatbox_' + x + ' .chatboxhead').toggleClass('chatboxblink');
                }
            }
        }*/

        $.ajax(
            {
                url: pH7Url.base + "im/asset/ajax/Messenger/?act=heartbeat",
                type: 'POST',
                cache: false,
                dataType: "json",

                success: function (oData) {
                    oMe.sUsername = oData.user;
                    oMe.chats = oData.chats;

                    var found = false;
                    $.each(oData.chats, function (username, chat) {
                        Messenger.createBox(username, chat.avatar_url);
                        found = true;

                        $.each(chat.messages, function(i, m) {
                            $('#chat_content_' + username).append('<div class="chatboxmessage">' +
                                '<span class="chatboxmessagefrom">' + m.from + ':&nbsp;&nbsp;</span>' +
                                '<span class="chatboxmessagecontent">' + m.message + '</span></div>');
                        });

                        $('#chat_content_' + username).scrollTop($('#chat_content_' + username)[0].scrollHeight);

                        Messenger.markAsUnread(username);
                    });
                    if (found) {
                        $('#top_chat').addClass('has_new_messages');
                    }
                    /*$.each(oData.items, function (i, oItem) {
                        oMe.sBoxTitle = oItem.user;

                        if ($("#chatbox_" + oMe.sBoxTitle).length <= 0)
                            oMe.createBox(oMe.sBoxTitle);

                        if ($("#chatbox_" + oMe.sBoxTitle).css('display') == 'none') {
                            $("#chatbox_" + oMe.sBoxTitle).css('display', 'block');
                            oMe.restructureBoxes();
                        }

                        if (oItem.status == 1) {
                            oItem.user = oMe.sUsername;
                        }

                        if (oItem.status == 2) {
                            $("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxinfo">' + oItem.msg + '</span></div>');
                        }
                        else {
                            oMe.aNewMessages[oMe.sBoxTitle] = true;
                            oMe.aNewMessagesWin[oMe.sBoxTitle] = true;
                            $("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent").append('<div class="chatboxmessage"><span class="chatboxmessagefrom">' + oItem.user + ':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">' + oItem.msg + '</span></div>');
                        }

                        $("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent").scrollTop($("#chatbox_" + oMe.sBoxTitle + " .chatboxcontent")[0].scrollHeight);
                        iItemsFound += 1;
                    });

                    ++oMe.iHeartbeatCount;

                    if (iItemsFound > 0) {
                        oMe.iHeartbeatTime = oMe.iMinHeartbeat;
                        oMe.iHeartbeatCount = 1;
                    }
                    else if (oMe.iHeartbeatCount >= 10) {
                        oMe.iHeartbeatTime *= 2;
                        oMe.iHeartbeatCount = 1;
                        if (oMe.iHeartbeatTime > oMe.iMaxHeartbeat) {
                            oMe.iHeartbeatTime = oMe.iMaxHeartbeat;
                        }
                    }*/

                    setTimeout(function () {
                        oMe.heartbeat()
                    }, oMe.iHeartbeatTime);
                }
            });
    },

    closeBox: function (sBoxTitle) {
        $.post(pH7Url.base + "im/asset/ajax/Messenger/?act=close", {box: sBoxTitle});
    },

    checkBoxInputKey: function (oEvent, oBoxTextarea, username) {
        if (oEvent.keyCode === 13 && oEvent.shiftKey === false) {

            this.sMessage = $(oBoxTextarea).val();
            this.sMessage = this.sMessage.replace(/^\s+|\s+$/g, "");

            $(oBoxTextarea).val('');
            $(oBoxTextarea).focus();
            $(oBoxTextarea).css('height', '44px');
            if (this.sMessage != '') {
                $.post(pH7Url.base + "im/asset/ajax/Messenger/?act=send", {
                    to: username,
                    message: this.sMessage
                }, function (oData) {
                    console.log(oData);
                    oMe.sMessage = oMe.sMessage.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;");
                    var msg = oData.message;
                    $("#chat_content_" + username)
                        .append('<div class="chatboxmessage"><span class="chatboxmessagefrom">' + msg.from + ':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">' + msg.message + '</span></div>');
                    $("#chat_content_" + username).scrollTop($("#chat_content_" + username)[0].scrollHeight);

                    $('#top_coin_menu_left_text').text('Coins: ' + oData.coins);
                });
            }
            this.iHeartbeatTime = this.iMinHeartbeat;
            this.iHeartbeatCount = 1;

            return false;
        }

        var iAdjustedHeight = oBoxTextarea.clientHeight;
        var iMaxHeight = 94;

        if (iMaxHeight > iAdjustedHeight) {
            iAdjustedHeight = Math.max(oBoxTextarea.scrollHeight, iAdjustedHeight);
            if (iMaxHeight) iAdjustedHeight = Math.min(iMaxHeight, iAdjustedHeight);
            if (iAdjustedHeight > oBoxTextarea.clientHeight) $(oBoxTextarea).css('height', iAdjustedHeight + 8 + 'px');
        }
        else {
            $(oBoxTextarea).css('overflow', 'auto');
        }
    },

    markAsUnread: function(username) {
        $('#user_' + username).addClass('has_new_messages');
        Messenger.soundAlert();
    },

    soundAlert: function () {
        $.sound.play(pH7Url.stic + 'sound/purr.mp3');
    },

    _check: function (sUser) {
        if (sUser == this.sUsername) {
            return false;
        }
        return true;
    }

};

$(document).ready(function () {
    sOriginalTitle = document.title;

    Messenger.Messenger().startSession();

    $([window, document]).blur(function () {
        bWindowFocus = false;
    }).focus(function () {
        bWindowFocus = true;
        document.title = sOriginalTitle;
    });
});
