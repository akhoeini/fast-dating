window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

});
var isDark = true;

$(document).ready(function() {
    var jsonToken = validateToken();
    if (jsonToken == null) {
        window.location.href = '/admin/login'
        return;
    } else {
        $("#adminUserName").append(jsonToken['nbf']);

        $("#toggle-theme").click(function(e) {
            if (isDark) {
                $(".sb-sidenav").addClass("sb-sidenav-light").removeClass("sb-sidenav-dark");
                $("#toggle-theme").text("Light Sidenav");
                isDark = false;
            } else {
                $(".sb-sidenav").addClass("sb-sidenav-dark").removeClass("sb-sidenav-light");
                $("#toggle-theme").text("Dark Sidenav");
                isDark = true;
            }
        });
        $("#logout").click(function(e) {
            localStorage.removeItem("fastdating-token");
            window.location.href = '/admin/login'
        });

        getUsers();
        getGenders()
    }
});

function getGenders() {
    $.ajax({
        url: '/admin/genders',
        type: 'GET',
        dataType: 'json',
        error: function(jqxhr, status, errorThrown) {
            httpErrorHandler(jqxhr, status, errorThrown);
        }
    }).done(function(genders) {
        if (!genders.length) {
            return
        }
        setCharts(genders.map(item => parseInt(Object.values(item['count']))), genders.map(item => item['genderId'] != null ? Object.values(item['genderId']) : "Null"));
    });
}

function setCharts(genders, keys) {
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#292b2c';

    // Pie Chart Example
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: keys,
            datasets: [{
                data: genders,
                backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745'],
            }],
        },
    });
}

function getUsers() {
    $.ajax({
        url: '/admin/users',
        type: 'GET',
        dataType: 'json',
        error: function(jqxhr, status, errorThrown) {
            httpErrorHandler(jqxhr, status, errorThrown);
        }
    }).done(function(userList) {
        if (!userList.length) {
            return
        }

        let table = new simpleDatatables.DataTable("#datatablesSimple", {
            data: {
                headings: Object.keys(userList[0]),
                data: userList.map(item => Object.values(item))
            },
        })
    });
}

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
            if (new Date(y, m, d) >= new Date(JSON.parse(jsonPayload).exd)) {
                return null;
            }
            if (JSON.parse(jsonPayload).role == "admin") {
                return JSON.parse(jsonPayload);
            }

        } catch (e) {
            return null;
        }
    }
    return null;
}