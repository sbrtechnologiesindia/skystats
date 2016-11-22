jQuery(function ($) {

    $('#to ,#from').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        maxDate: new Date()
    });
    checkStats($);
    $('#fb_page_fetch').click(function () {
        checkStats($)
    });

});

function checkStats($) {
    var from = $('#from').val();
    var to = $('#to').val();

    var days = dtDiff(from, to);

    if (days > 90) {
        alert("Date-range must not be greater than 90");
        return false;
    }
    $('#result').html('<div class="loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');
    api_key = $('#api_key').val();



    // get days

    if (!dtParse(from)) {
        alert("Enter valid from date!");
        $('#result').html('');
        return false;
    }
    if (!dtParse(to)) {
        $('#result').html('');
        alert("Enter valid to date!");
        return false;
    }

    if (from == '') {
        $('#result').html('');
        alert("Please From date");
        return false;
    } else if (to == '') {
        $('#result').html('');
        alert("Please select To date");
        return false;
    }
    var data = {
        'action': 'fetch_fb_page_data',
        'api_key': api_key,
        'from': from,
        'to': to

    };
    $.post(ajaxurl, data, function (response) {
        if (response != '') {
            $('#result').html(response);

        } else {
            $('#result').html('no results.');
        }
    });
   

}

function dtParse(dt) {
    var s = dt.split('-');


    if ((typeof s[0] == 'undefined') || (typeof s[1] == 'undefined') || (typeof s[2] == 'undefined')) {
        return false;
    } else {
        s[0] = parseInt(s[0]);
        s[1] = parseInt(s[1]);
        s[2] = parseInt(s[2]);
        if ((s[0] == 0) || (s[1] == 0) || (s[2] == 0)) {
            return false;
        }
        var ret = new Date(s[0], s[1] - 1, s[2]);
        return !isNaN(ret.getTime());
    }
}

function dtDiff(f, u) {
    var s = f.split('-');
    var t = u.split('-');


    if ((typeof s[0] == 'undefined') || (typeof s[1] == 'undefined') || (typeof s[2] == 'undefined') || (typeof t[0] == 'undefined') || (typeof t[1] == 'undefined') || (typeof t[2] == 'undefined')) {
        return 0;
    } else {
        s[0] = parseInt(s[0]);
        s[1] = parseInt(s[1]);
        s[2] = parseInt(s[2]);
        t[0] = parseInt(t[0]);
        t[1] = parseInt(t[1]);
        t[2] = parseInt(t[2]);

        if ((s[0] == 0) || (s[1] == 0) || (s[2] == 0) || (t[0] == 0) || (t[1] == 0) || (t[2] == 0)) {
            return 0;
        }
        var d1 = new Date(s[0], s[1] - 1, s[2]);
        var d2 = new Date(t[0], t[1] - 1, t[2]);
        if (isNaN(d1.getTime()) || isNaN(d2.getTime())) {
            return 0;

        }
        var diff = (d2.getTime() - d1.getTime()) / 1000 / 60 / 60 / 24;

        return diff;
    }
}