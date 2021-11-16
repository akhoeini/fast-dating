$(document).ready(function() {
    if (validateToken()) {
        window.location.href = './main'
        return;
    }

    $("#login").click(function() {
        if (checkForm()) {
            alert("form fields okey");
            login($("#email").val(), $("#password").val());
        }
    });
    $("#signup-link").click(function() {
        window.location.href = './signup'
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
    var userEmailVal = $("#email").val();
    var passwordVal = $("#password").val();
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

    if (passwordVal == "") {
        alert("Error: Please check that you've entered your password!");
        $("#password").focus();
        return false;
    }
    return true;
}

function login(email, password) {
    var newUser = JSON.stringify({
        email: email,
        password: password
    });
    $.ajax({
        url: '/admin/login',
        type: 'POST',
        data: newUser,
        dataType: 'json',
        headers: { "Content-Type": "application/json; charset=UTF-8" },
        error: function(jqxhr, status, errorThrown) {
            httpErrorHandler(jqxhr, status, errorThrown);
        }
    }).done(function(responseJSON) {
        var result = JSON.parse(JSON.stringify(responseJSON));
        window.localStorage.setItem("fastdating-token", result.token);
        window.location.href = './main'
    }).always(function() {

    });
}