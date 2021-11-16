var socket = io();

var soundFileNotification = "/sound/iphone_notification.mp3";

var user_message_dictionary = {};
var user_id = null;

var private_btn_id = document.getElementsByClassName('private-message-send');
var modal_close_button = document.getElementsByClassName('close');
var message_audio = document.getElementById('message-received');


function scrollToBottom(id) {
    var messages = jQuery(id);
    var newMessage = messages.children('li:last-child');
    var clientHeight = messages.prop('clientHeight');
    var scrollTop = messages.prop('scrollTop');
    var scrollHeight = messages.prop('scrollHeight');
    var newMessageHeight = newMessage.innerHeight();
    var lastMessageHeight = newMessage.prev().innerHeight();

    if (clientHeight + scrollTop + newMessageHeight + lastMessageHeight >= scrollHeight) {
        messages.scrollTop(scrollHeight);
    }
}

function update() {
    var $worked = $("#show-time");
    var myTime = $worked.html();
    var ss = myTime.split(":");
    var dt = new Date();
    dt.setHours(0);
    dt.setMinutes(ss[0]);
    dt.setSeconds(ss[1]);

    var dt2 = new Date(dt.valueOf() + 1000);
    var temp = dt2.toTimeString().split(" ");
    var ts = temp[0].split(":");

    $worked.html(ts[1] + ":" + ts[2]);
    setTimeout(update, 1000);
}

function privateScrollToBottom() {
    var modal = document.getElementsByClassName("modal-body")[0];
    modal.scrollTop = modal.scrollHeight;
}

function privateScrollToTop() {
    var modal = document.getElementsByClassName("modal-body")[0];
    modal.scrollTop = 0;
}

window.addEventListener('load', function() {
    $('#myModal').modal('hide');
});

socket.on('connect', function() {
    var params = jQuery.deparam(window.location.search);

    socket.emit('join', params, function(err) {
        if (err) {
            alert(err);
            window.location.href = '/';
        }
        else {
            console.log('socket connect ok');
        }
    });
});

function private_chat(event) {
    var id = event.target.id;
    var userid = document.getElementById(event.target.id);

    if (id in user_message_dictionary) {
        var messages = user_message_dictionary[id];
        for (var i = 0; i < messages.length; i++) {
			var template = jQuery('#private-message-template').html();
			var html = Mustache.render(template, {
				text: messages[i].text,
				from: messages[i].from,
				createdAt: messages[i].createdAt
			});
			jQuery('#private_messages_list').append(html);
        }
        console.log("private_chat", user_message_dictionary);
    }
    label = userid.nextSibling;
    label.setAttribute('style', 'display:none');
    socket.emit('privateMessageWindow', {
        id: event.target.id
    });
}

socket.on('disconnect', function() {
    console.log('Disconnected from server');
});


socket.on('updateUserList', function(users) {

	console.log("updateUserList", users);

    var ol = jQuery('<ol></ol>');

	for (let user of users) {
		if (user_id == user.id) {
			var img = '<img class="bg-wrap text-center py-4" style="border-radius: 50%;" src=' + user.photo + ' width="60px;" height="60px;"/>';
			var span_name = '<span style="padding-left:20px;font-size:20px;">'+user.name+ '</span>';
			var list = $('<li>'+img+ span_name+'</li>');//.text(user.name);
			list.attr('class', 'user-list')


			ol.append(list);
			break;
		}
	}

    for (let user of users) {
		if (user_id != user.id) {
			var img = '<img class="bg-wrap text-center py-4" style="border-radius: 50%;" src=' + user.photo + ' width="60px;" height="60px;"/>';
			var span_name = '<span style="padding-left:20px;font-size:20px;">'+user.name+ '</span>';
			var list = $('<li>'+img+ span_name+'</li>');//.text(user.name);
			list.attr('class', 'user-list')


			ol.append(list);

			var button = document.createElement('button');
			button.innerHTML = 'Private Chat';
			button.classList.add('btn', 'btn-success', 'private-msg-btn');
			button.setAttribute('id', user.id);

			var label = document.createElement('label');
			label.setAttribute('class', 'new-message');
			list.append(button);
			list.append(label);
		}
    }

	//if private chat windows is open, check user

    jQuery('#users').html(ol);
    $('.private-msg-btn').click(private_chat);
});

socket.on('notifyUser', function(data) {

    var private_message_button = document.getElementsByClassName('private-message-send');
    if (private_message_button[0].id == '') {
        var set_id = data.user.id + '0';

        private_message_button[0].setAttribute('id', set_id);

        var modalbutton = document.createElement('button');
        modalbutton.setAttribute('type', 'button');
        modalbutton.setAttribute('data-toggle', 'modal');
        modalbutton.setAttribute('data-target', '#myModal');
        modalbutton.setAttribute('class', 'hide-modal-dynamic');
        document.getElementsByTagName('body')[0].appendChild(modalbutton);
        document.getElementsByClassName('modal-title')[0].innerHTML = data.user.name;
        modalbutton.click();
        
        message_audio.src = soundFileNotification;
        socket.emit('private_connection_successful', {
            user: data.user,
            otherUserId: data.otherUser
        });
    }
    else {
        socket.emit('private_connection_successful', {
            user: data.user,
            otherUserId: data.otherUser
        });
    }
});

socket.on('openChatWindow', function(user) {
    var modalbutton = document.createElement('button');
    modalbutton.setAttribute('type', 'button');
    modalbutton.setAttribute('data-toggle', 'modal');
    modalbutton.setAttribute('data-target', '#myModal');
    modalbutton.setAttribute('class', 'hide-modal-dynamic');
    document.getElementsByTagName('body')[0].appendChild(modalbutton);
    var set_id = user.user.id + '0';
    document.getElementsByClassName('private-message-send')[0].setAttribute('id', set_id);
    document.getElementsByClassName('modal-title')[0].innerHTML = user.user.name;
    modalbutton.click();

    console.log('openChatWindow', user.user);
});

document.getElementsByClassName('close')[0].addEventListener('click', function() {
    var private_message_button = document.getElementsByClassName('private-message-send');
    private_message_button[0].setAttribute('id', '');
    document.getElementById('private_messages_list').innerHTML = '';
});

$('#myModal').modal({
    backdrop: 'static',
    keyboard: true
})

socket.on('userInfo', function(message) {
	user_id = message.text;
	console.log("userInfo", user_id);
})

socket.on('newMessage', function(message) {
    var formattedTime = moment(message.createdAt).format('h:mm a');
    var template = jQuery('#message-template').html();
    var html = Mustache.render(template, {
        text: message.text,
        from: message.from,
        createdAt: formattedTime
    });

    jQuery('#messages').append(html);
    scrollToBottom('#messages');
});

socket.on('newPrivateMessage', function(message) {
    var formattedTime = moment(message.createdAt).format('h:mm a');
    var message_object = {
        text: message.message,
        from: message.user.name,
        createdAt: formattedTime,
        type: 'message'
    }
    add_message(message_object, message.user.id);
    var private_message_button = document.getElementsByClassName('private-message-send')[0].id;
    var slice_id = private_message_button.slice(0, -1);

    if (slice_id == message.user.id) {
        var template = jQuery('#private-message-template').html();
        var html = Mustache.render(template, {
            text: message.message,
            from: message.user.name,
            createdAt: formattedTime
        });
        jQuery('#private_messages_list').append(html);

        message_audio.src = soundFileNotification;
        socket.emit('privateMessageSendSuccessful', {
            message: message.message,
            userid: slice_id
        });
    }
    else {
        message_audio.src = soundFileNotification;
        socket.emit('privateMessageSendSuccessful', {
            message: message.message,
            userid: message.user.id
        });
        var userid = document.getElementById(message.user.id);
        label = userid.nextSibling;
        label.setAttribute('style', 'display:block');
        label.innerHTML = "";
    }
});

socket.on('privateMessageSuccessfulAdd', function(message) {
    var formattedTime = moment(message.createdAt).format('h:mm a');
    var message_object = {
        text: message.message,
        from: message.user.name,
        createdAt: formattedTime,
        type: 'message'
    }
    add_message(message_object, message.id);
    var template = jQuery('#private-message-template').html();
    var html = Mustache.render(template, {
        text: message.message,
        from: message.user.name,
        createdAt: formattedTime
    });
    jQuery('#private_messages_list').append(html);

});

$('#private-message-form').on('submit', function(e) {
    e.preventDefault();

    var private_messageTextbox = $('[name=private-message]');
    var private_userid = document.getElementsByClassName('private-message-send')[0].id;
    var slice_id = private_userid.slice(0, -1);
    console.log(private_messageTextbox.val());
    socket.emit('createPrivateMessage', {
        message: private_messageTextbox.val(),
        userid: slice_id
    });
    private_messageTextbox.val('');
});


jQuery('#message-form').on('submit', function(e) {
    e.preventDefault();

    var messageTextbox = jQuery('[name=message]');

    socket.emit('createMessage', {
        text: messageTextbox.val()
    }, function() {
        messageTextbox.val('')
    });
});

function add_message(message, id) {
    if (id in user_message_dictionary) {
        user_message_dictionary[id].push(message);
        console.log("add_message", message, user_message_dictionary);
    }
    else {
        user_message_dictionary[id] = [];
        user_message_dictionary[id].push(message);
        console.log("add_message", message, user_message_dictionary);
    }
}
