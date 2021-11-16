const path = require('path');
const http = require('http');
const express = require('express');
const socketIO = require('socket.io');
const moment = require('moment');

const publicPath = path.join(__dirname, '../public');
const port = process.env.PORT || 9000;
const app = express();
const server = http.createServer(app);
const io = socketIO(server);

app.all('*', function(req, res, next) {
  res.setHeader('Access-Control-Allow-Origin', '*')
  res.setHeader('Access-Control-Allow-Methods', 'GET,PUT,POST,DELETE,OPTIONS')
  res.setHeader('Access-Control-Allow-Headers', 'Content-type,Accept,X-Access-Token,X-Key')
  next()
})

app.use(express.static(publicPath));

const {Users} = require('./users');
const users = new Users();

var isRealString = (str) => {
  return typeof str === 'string' && str.trim().length > 0;
};

var generateMessage = (from, text) => {
  return {
    from,
    text,
    createdAt: moment().valueOf()
  };
};

io.on('connection', (socket) => {
    console.log('New user connected');

    socket.on('join', (params, callback) => {
		console.log('New user joined', JSON.stringify(params));
        if (!isRealString(params.name) || !isRealString(params.room) ||
			!isRealString(params.uid) || !isRealString(params.token) ||
			!isRealString(params.photo)
			) {
            return callback('Invalid params');
        }

		if(params.token != "fsd01"){
           return callback('Invalid params');
		}
		
		var roomName = {};
		roomName["1"] = "Chat Room - General";
		roomName["2"] = "Chat Room - Homework-help";
		roomName["3"] = "Chat Room - Off-topic";

		console.log("room name:",params);
		if(roomName[params.room] == undefined) {
			return callback('Invalid params');
		}

        socket.join(params.room);
        users.removeUser(socket.id);
        users.addUser(socket.id, params.uid, params.name, params.room, params.photo);
		console.log("new user:", params.uid, params.name, params.room, params.photo);
		socket.emit('userInfo', generateMessage('[SYSTEM]', ''+ socket.id));
        io.to(params.room).emit('updateUserList', users.getUserList(params.room));
        socket.emit('newMessage', generateMessage('[SYSTEM]', 'Welcome to ' + roomName[params.room]));
        //socket.broadcast.to(params.room).emit('newMessage', generateMessage('[SYSTEM]', `${params.name} has joined.`));
        callback();
    });

    socket.on('createMessage', (message, callback) => {
        const user = users.getUser(socket.id);
        console.log("createMessage",user.name, message.text);

        if (user && isRealString(message.text)) {
            io.to(user.room).emit('newMessage', generateMessage(user.name, message.text));
        }
        callback();
    });

    socket.on('createPrivateMessage', (message) => {
       socket.broadcast.to(message.userid).emit('newPrivateMessage',{
           message:message.message,
           user:users.getUser(socket.id)
       });
       console.log(message.message);
    });

    socket.on('privateMessageWindow', (userid) => {
        const user = users.getUser(socket.id);
        console.log('privateMessageWindow',userid);
        socket.broadcast.to(userid.id).emit('notifyUser',{
            user:users.getUser(socket.id),
            otherUser:userid.id
        });
    });

    socket.on('private_connection_successful',(user) => {
        console.log('private_connection_successful',user.otherUserId);
        socket.broadcast.to(user.user.id).emit('openChatWindow',{
            user:users.getUser(user.otherUserId)
        });
    });

    socket.on('privateMessageSendSuccessful',function (message) {
        console.log('privateMessageSendSuccessful', users.getUser(socket.id));
        const message_object ={
            message:message.message,
            user:users.getUser(message.userid),
            id:socket.id
        }
        socket.broadcast.to(message.userid).emit('privateMessageSuccessfulAdd',message_object);
    });

    socket.on('disconnect', () => {
        const user = users.removeUser(socket.id);
		if(user != undefined){
			console.log("user leave", user.name, user.room);
		}
		else{
			console.log("user leave invalid name");
		}		
        if (user) {
            io.to(user.room).emit('updateUserList', users.getUserList(user.room));
            //io.to(user.room).emit('newMessage', generateMessage('[SYSTEM]', `${user.name} has left.`));
        }
    });
});

server.listen(port, () => {
    console.log(`Server is up on ${port}`);
});
