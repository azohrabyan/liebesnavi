
// This feature is only for chatters!


// Global variables
var sOriginalTitle = '', bWindowFocus = true;

var Messenger = {

    // Properties
    iMinHeartbeat: 1000,
    iMaxHeartbeat: 40000,

    // Constructor
    Messenger: function () {
        this.iHeartbeatTime = this.iMinHeartbeat;
        oMe = this; // Self Object

        return this;
    },

    // Methods
    startSession: function () {
        $('#chat_container').html('');
        $('<div />').addClass('fakes-container').appendTo('#chat_container');
        $('<div />')
            .addClass('clear')
            .appendTo('#chat_container');

        $.ajax(
            {
                url: pH7Url.base + "im/asset/ajax/Messenger/?act=startsession",
                type: 'POST',
                cache: false,
                dataType: "json",
                success: function (oData) {
                    //$('#chat_wrapper').text(oData);
                    // console.log(oData);
                    Messenger.selectedFake = '';
                    Messenger.selectedPartner = Array();
                    Messenger.processResponse(oData, false);
                    Messenger.selectFake(Messenger.selectedFake);

                    setTimeout(function () {
                        Messenger.heartbeat();
                    }, Messenger.iHeartbeatTime);
                }
            });
    },

    heartbeat: function () {
        $.ajax(
            {
                url: pH7Url.base + "im/asset/ajax/Messenger/?act=heartbeat",
                type: 'POST',
                cache: false,
                dataType: "json",

                success: function (oData) {
                    // console.log(oData);
                    Messenger.processResponse(oData, true);

                    setTimeout(function () {
                        Messenger.heartbeat()
                    }, Messenger.iHeartbeatTime);
                }
            });
    },

    processResponse: function(response, markAsUnread) {
        $.each(response.chats, function (i, chat) {
            if (Messenger.selectedFake === '') {
                Messenger.selectedFake = chat.fake;
            }
            if (Messenger.selectedPartner[chat.fake] === undefined) {
                Messenger.selectedPartner[chat.fake] = chat.partner;
            }

            // console.log(chat);
            $.each(chat.messages.messages, function(j, msg) {
                Messenger.newMessage(msg.from, msg.to, msg.message, chat.fake, markAsUnread, chat.partner_avatar, chat.fake_avatar, chat.notes);
            });
        });
    },

    soundAlert: function () {
        $.sound.play(pH7Url.stic + 'sound/purr.mp3');
    },

    newMessage: function(from, to, msg, fake, markAsUnread, partnerAvatar, fakeAvatar, notes) {
        Messenger.addFake(fake, fakeAvatar);
        var partner = '';
        if (fake === from) {
            partner = to;
        } else if (fake === to) {
            partner = from;
        }
        if (partner !== '') {
            Messenger.addPartner(fake, partner, partnerAvatar, notes);
        }

        Messenger.addMessage(from, msg, fake, partner, markAsUnread);
    },

    addFake: function(fake, fakeAvatar) {
        if ($('.fakes-container #fake_selector_' + fake).length <= 0) {
            var selector = $('<div />')
                .attr('id', 'fake_selector_' + fake)
                .data('fake-username', fake)
                .addClass('float-left col-lg-3')
                .appendTo('.fakes-container');
            $('<img />')
                .attr('src', fakeAvatar)
                .addClass('avatar')
                .appendTo(selector)
            ;
            $('<a href="/' + fake +'.html" target="_blank">' + fake + '</a>').appendTo(selector);

            $('#fake_selector_' + fake)
                .click(Messenger.toggleFake);

            $('<div />')
                .attr('id', 'chats_of_' + fake)
                .css('display', 'none')
                .appendTo('#chat_container');

            $('<div />')
                .addClass('partner-list float-left')
                .appendTo('#chats_of_' + fake);
            $('<div />')
                .addClass('partner-chats float-left')
                .appendTo('#chats_of_' + fake);
            $('<div />')
                .addClass('clear')
                .appendTo('#chats_of_' + fake);
        }
    },

    toggleFake: function() {
        // console.log('toggleFake');
        Messenger.hideCurrentFake();
        Messenger.selectFake($(this).data('fake-username'));
    },

    hideCurrentFake: function() {
        $('#fake_selector_' + Messenger.selectedFake).removeClass('selected');
        $('#chats_of_' + Messenger.selectedFake).hide();
    },

    selectFake: function(fake) {
        // console.log(fake);
        Messenger.selectedFake = fake;
        $('#fake_selector_' + fake).addClass('selected');
        $('#chats_of_' + fake).show();
        Messenger.selectPartner(Messenger.getSelectedPartner());
    },

    addPartner: function(fake, partner, partnerAvatar, notes) {
        if ($('#chats_of_' + fake + '_with_' + partner + '_selector').length <= 0) {
            // console.log('addPartner');
            var selector = $('<div />')
                .attr('id', 'chats_of_' + fake + '_with_' + partner + '_selector')
                .data('fake-username', fake)
                .data('partner-username', partner)
                .addClass('partner-selector')
                .appendTo('#chats_of_' + fake + ' .partner-list');
            $('#chats_of_' + fake + '_with_' + partner + '_selector')
                .click(Messenger.togglePartner);
            $('<img />')
                .addClass('avatar')
                .attr('src', partnerAvatar)
                .appendTo(selector)
            ;
            $('<a href="/agency/chatter/view_profile/' + partner + '.html?f='+fake+'" target="_blank">' + partner + '</a>')
                .appendTo(selector)
            ;
            $("<div />")
                .addClass('close-chat-btn')
                .click(function() {
                    console.log('close ', fake, partner);
                    Messenger.closeChat(fake, partner);
                })
                .appendTo(selector)
            ;
            $('<div />')
                .attr('id', 'chats_of_' + fake + '_with_' + partner)
                .addClass('partner-container')
                .data('fake-username', fake)
                .data('partner-username', partner)
                .css('display', 'none')
                .appendTo('#chats_of_' + fake + ' .partner-chats');
            $('<div />')
                .addClass('messages-container-wrapper float-left col-lg-6')
                .appendTo('#chats_of_' + fake + '_with_' + partner);
            $('<div />')
                .addClass('messages-container')
                .appendTo('#chats_of_' + fake + '_with_' + partner + ' .messages-container-wrapper');
            $('<div />')
                .addClass('input-container')
                .appendTo('#chats_of_' + fake + '_with_' + partner + ' .messages-container-wrapper');
            $('<textarea />')
                .addClass('editbox')
                .appendTo('#chats_of_' + fake + '_with_' + partner + ' .input-container');
            $('#chats_of_' + fake + '_with_' + partner + ' textarea')
                .keydown(Messenger.onTextKeyDown);

            $('<div />')
                .addClass('notes-wrapper float-left col-lg-6')
                .appendTo('#chats_of_' + fake + '_with_' + partner);
            $('<textarea />')
                .addClass('notesbox')
                .val(notes)
                .appendTo('#chats_of_' + fake + '_with_' + partner + ' .notes-wrapper');
            $('<button>Save</button>')
                .appendTo('#chats_of_' + fake + '_with_' + partner + ' .notes-wrapper')
                .click(Messenger.onSaveNote)
            ;
        }
    },

    togglePartner: function() {
        // console.log('togglePartner');
        Messenger.hideCurrentPartner();
        Messenger.selectPartner($(this).data('partner-username'));
    },

    hideCurrentPartner: function() {
        $('#chats_of_' + Messenger.selectedFake + '_with_' + Messenger.getSelectedPartner() + '_selector').removeClass('selected');
        $('#chats_of_' + Messenger.selectedFake + '_with_' + Messenger.getSelectedPartner()).hide();
    },

    selectPartner: function(partner) {
        // console.log(Messenger.selectedFake, partner);
        Messenger.selectedPartner[Messenger.selectedFake] = partner;
        $('#chats_of_' + Messenger.selectedFake + '_with_' + Messenger.getSelectedPartner() + '_selector')
            .addClass('selected')
            .removeClass('has_new_messages');
        $('#chats_of_' + Messenger.selectedFake + '_with_' + Messenger.getSelectedPartner()).show();
        if ($('#chats_of_' + Messenger.selectedFake + ' .has_new_messages').length === 0) {
            $('#fake_selector_' + Messenger.selectedFake).removeClass('has_new_messages');
        }
    },

    getSelectedPartner: function() {
        return Messenger.selectedPartner[Messenger.selectedFake];
    },

    addMessage: function(from, msg, fake, partner, markAsUnread) {
        var msgContainer = $('#chats_of_' + fake + '_with_' + partner + ' .messages-container');
        // console.log(msgContainer);
        msgContainer.append('<div class="chatboxmessage">' +
            '<span class="chatboxmessagefrom">' + from + ':&nbsp;&nbsp;</span>' +
            '<span class="chatboxmessagecontent">' + msg + '</span></div>');
        if (markAsUnread) {
            Messenger.markAsUnread(fake, partner);
        }
        msgContainer.scrollTop(msgContainer[0].scrollHeight);
    },

    markAsUnread: function(fake, partner) {
        $('#chats_of_' + fake + '_with_' + partner + '_selector').addClass('has_new_messages');
        $('#fake_selector_' + fake).addClass('has_new_messages');
        Messenger.soundAlert();
    },

    onTextKeyDown: function(event) {
        // console.log();
        var fake = $(this).closest('.partner-container').data('fake-username');
        var partner = $(this).closest('.partner-container').data('partner-username');
        if (event.keyCode === 13 && event.shiftKey === false) {
            var msg = $(this).val();
            msg = msg.replace(/^\s+|\s+$/g, "");

            $(this).val('');
            $(this).focus();
            if (msg !== '') {
                $.post(pH7Url.base + "im/asset/ajax/Messenger/?act=send", {
                    from: fake,
                    to: partner,
                    message: msg
                }, function (oData) {
                    msg = oData[0];
                    //console.log(msg, fake);
                    Messenger.addMessage(msg.from, msg.message, fake, partner, false);
                });
            }
            return false;
        }
    },

    onSaveNote: function() {
        var fake = $(this).closest('.partner-container').data('fake-username');
        var partner = $(this).closest('.partner-container').data('partner-username');

        var notes = $(this).siblings('textarea').val();
        notes = notes.replace(/^\s+|\s+$/g, "");

        $.post(pH7Url.base + "im/asset/ajax/Messenger/?act=save_notes", {
            fake: fake,
            partner: partner,
            notes: notes
        }, function (oData) {

        });
    },

    closeChat: function(fake, partner) {
        $.post(pH7Url.base + "im/asset/ajax/Messenger/?act=close", {fake: fake, partner: partner});
        $('#chats_of_'+fake+'_with_'+partner+'_selector').remove();
        $('#chats_of_'+fake+'_with_'+partner).remove();

        if ($('#chats_of_'+fake + ' .partner-list .partner-selector').length === 0) {
            $('.fakes-container #fake_selector_' + fake).remove();
            $('#chats_of_'+fake + ' .partner-list').remove();
            $('#chats_of_'+fake).remove();
        }
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
