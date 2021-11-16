$(document).ready(function() {
    if (validateToken()) {
        window.location.href = './main'
        return;
    }

    $("#signup").click(function() {
        if (checkForm()) {
            alert("form fields okey");
            signup($("#username").val(), $("#email").val(), $("#password").val());
        }
    });

    $("#login-link").click(function() {
        window.location.href = './login'
    });
});

function validateToken() {
    var token = window.localStorage.getItem("fastdating-token");
    if (token != null) {
        try {
            var base64Url = token.split('.')[1];
            var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
            var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
            var q = new Date();
            var m = q.getMonth() + 1;
            var d = q.getDay();
            var y = q.getFullYear();
            if (new Date(y, m, d) < new Date(JSON.parse(jsonPayload).exd)) {
                return true;
            }
        } catch (e) {
            return false;
        }
    }
    return false;
}

function validateEmail(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function checkForm() {
    var usernameVal = $("#username").val();
    var userEmailVal = $("#email").val();
    var passwordVal = $("#password").val();
    var passwordRepeatVal = $("#password_confirm").val();
    if (usernameVal == "") {
        alert("Error: Username cannot be blank!");
        $("#username").focus();
        return false;
    }
    re = /^\w+$/;
    if (!re.test(usernameVal)) {
        alert("Error: Username must contain only letters, numbers and underscores!");
        $("#username").focus();
        return false;
    }
    if (userEmailVal == "") {
        alert("Error: Email cannot be blank!");
        $("#email").focus();
        return false;
    }
    if (!validateEmail(userEmailVal)) {
        alert("Email is not valid");
        $("#email").focus();
        return false;
    }

    if (passwordVal != "" && passwordVal == passwordRepeatVal) {
        if (passwordVal < 6) {
            alert("Error: Password must contain at least six characters!");
            $("#password").focus();
            return false;
        }
        if (passwordVal == usernameVal) {
            alert("Error: Password must be different from Username!");
            $("#password").focus();
            return false;
        }
        re = /[0-9]/;
        if (!re.test(passwordVal)) {
            alert("Error: password must contain at least one number (0-9)!");
            $("#password").focus();
            return false;
        }
        re = /[a-z]/;
        if (!re.test(passwordVal)) {
            alert("Error: password must contain at least one lowercase letter (a-z)!");
            $("#password").focus();
            return false;
        }
        re = /[A-Z]/;
        if (!re.test(passwordVal)) {
            alert("Error: password must contain at least one uppercase letter (A-Z)!");
            $("#password").focus();
            return false;
        }
    } else {
        alert("Error: Please check that you've entered and confirmed your password!");
        $("#password").focus();
        return false;
    }
    return true;
    console.log(userEmailVal + "\n" + passwordVal + "\n" + passwordRepeatVal + "\n" + usernameVal);
}

function signup(username, email, password) {
    var newUser = JSON.stringify({
        username: username,
        email: email,
        password: password
    });
    $.ajax({
        url: '/admin/signup',
        type: 'POST',
        data: newUser,
        dataType: 'json',
        headers: { "Content-Type": "application/json; charset=UTF-8" },
        error: function(jqxhr, status, errorThrown) {
            httpErrorHandler(jqxhr, status, errorThrown);
            alert("username or password is incorrect");

        }
    }).done(function(responseJSON) {
        var result = JSON.parse(JSON.stringify(responseJSON));
        window.localStorage.setItem("fastdating-token", result.token);
        alert("Record created successfuly");
        window.location.href = './main'
    }).always(function() {

    });
}