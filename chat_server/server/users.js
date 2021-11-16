class Users {

  constructor () {
    this.users = [];
  }

  addUser (id, uid, name, room, photo) {
    var user = {id, uid, name, room, photo};
    this.users.push(user);
    return user;
  }

  removeUser (id) {
    var user = this.getUser(id);

    if (user) {
      this.users = this.users.filter((user) => user.id !== id);
    }

    return user;
  }

  getUser (id) {
    return this.users.filter((user) => user.id === id)[0]
  }

  getUserList (room) {
    var users = this.users.filter((user) => user.room === room);
    //var namesArray = users.map((user) => user.name);

    return users;
  }
}

module.exports = {Users};