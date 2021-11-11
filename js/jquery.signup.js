$(document).ready(function() {
    // alert("sign up js");
    $("#signup").click(function() {
        if (checkForm()) {
            alert("everything okey");
        }

        signup($("#username").val(), $("#email").val(), $("#password").val());
    });
});

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
        password: password,
        role: user,

    });
    $.ajax({
        url: '/signup',
        type: 'POST',
        data: newUser,
        dataType: 'json',
        headers: { "Content-Type": "application/json; charset=UTF-8" },
        error: function(jqxhr, status, errorThrown) {
            httpErrorHandler(jqxhr, status, errorThrown);
        }
    }).done(function(newId) {
        alert("Record created with ID " + newId);
    }).always(function() {

    });
}