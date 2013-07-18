// **** This js contains all the manual client side scripts****
// **** for Concierge HTML5 App *******************************
// **** Author : Anubhav Trivedi ******************************
// **** CopyRight : Mobikon Asia Technologies Pte. Ltd.********

//var API = "";
 var API = 'http://beta.mobikontech.com:8181/ConciergeAPI.php';
//var API = 'http://192.168.1.52:8090/ConciergeAPI.php';
//var API = 'http://getkonekt.com:8181/ConciergeAPI.php';
var today = '';     // This  mm-dd-yy
var today1 = '';    // This is yyyy-mm-dd
var time = '';
var currentts = '';
var timestamp = '';
var tbnarr = new Array();
var tbiarr = new Array();
var ctime = '';
if (sessionStorage.CSID == null || sessionStorage.CSID == "") {
    window.location.href = "index.html";
}
var csid = sessionStorage.CSID;
var acid = sessionStorage.Acid;
var eid = sessionStorage.OutletId;
var timerinterval = "";
var dtime = "";

function timercontrol()
{
   dtime = dateFormat(new Date(), "shortTime", false);
    $('#txtTime').val(dtime);
    //********** below code updates the time every 1 second
    timerinterval = setInterval(function () {
        dtime = dateFormat(new Date(), "shortTime", false);
        $('#txtTime').val(dtime);
    }, 1000);
}
$(document).ready(function () {
    $("#drpPreference").click(function () {
        $('.simplemodal-close').click();
    });

    $("#drpPreference").multiselect({
        selectedText: "# of # selected",
        noneSelectedText: 'All',
        classes: 'FieldBox',
        selectedList: 4
    });

    $('#drpCCC option[value="' + sessionStorage.CountryCode + '"]').prop('selected', 'selected');

    //    $("#drpPreference").multiselect("widget").find(":checkbox").each(function () {
    //        this.click(function () {
    //            alert("1");
    //            var selpref = $("#drpPreference").multiselect("getChecked").map(function () {
    //                return this.value;
    //            }).get();

    //            $('#selprefs').html(selpref);
    //        });

    //    });


    today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    var hours = today.getHours();
    var minutes = today.getMinutes();
    var seconds = today.getSeconds();

    if (dd < 10) { dd = '0' + dd } if (mm < 10) { mm = '0' + mm }
    today = mm + '/' + dd + '/' + yyyy;
    today1 = yyyy + '-' + mm + '-' + dd;

    var dispdate = dateFormat(new Date(), "dispDate", false)
    if (hours < 10) { hours = '0' + hours } if (minutes < 10) { minutes = '0' + minutes } if (seconds < 10) { seconds = '0' + seconds }
    currentts = today + " " + hours + ":" + minutes + ":" + seconds;
    time = hours + ":" + minutes;

    $('#txtfilterdate').val(dispdate);
    $('#txtDate').val(dispdate);

    //****** Below code displays time rounded to 15 min interval ************
    //  var currentTimeRounded = new Date().getHours() + ":" + (15 * Math.round(new Date().getMinutes() / 15));
    //  dtime = dateFormat("1900/1/1 " + currentTimeRounded, "shortTime", false);
    //******************************
   
		timercontrol();

    //  alert(today);
    //   alert(currentts);
    timestamp = currentts;
    /*
    $('#txtTime').timepicker(
    {
    showPeriod: false,              // Define whether or not to show AM/PM with selected time
    showPeriodLabels: false,
    hours: {
    starts: 0,                  // first displayed hour
    ends: 23                    // last displayed hour
    },
    minutes: {
    starts: 0,                  // first displayed minute
    ends: 45,                   // last displayed minute
    interval: 15                 // interval of displayed minutes
    }        // Show the AM/PM labels on the left of the time picker
    });

    */



    // CODE TO IMPLEMENT TIME INTERVALS BETWEEN TWO TIME PERIODS [New Time Picker]

    $('#txtTime').click(function () {
        SetTimings();
        $('#basic-modal-content11').modal();
    });

	//$('#txtCIeml').jqte();
	
	//$('#txtCOeml').jqte();

    /*
    $('.intable').click(function () {
    alert("!");
    SetTime($(this).html());
    });
    */
    function SetTimings() {

        var bs = sessionStorage.BrkFastStart;
        var be = sessionStorage.BrkFastEnd;
        var ls = sessionStorage.LunchStart;
        var le = sessionStorage.LunchEnd;
        var ds = sessionStorage.DinnerStart;
        var de = sessionStorage.DinnerEnd;
        var timings = "";

        var clickFunction = ((document.ontouchstart !== null) ? 'onclick' : 'ontouchstart');
        var count = 0;
        var tim = "";
        //BREAKFAST TIME DISPLAY
        if (bs != null && bs != "00:00:00" && be != "00:00:00") {
            timings += "<div style='width:100%;color:#1C94C4;font-size:1.8em' align='center'>Please Select Booking Time</div>";
            timings += "<table cellspacing='4' cellpadding='4' border='1' style='width:100%; margin-top:3%'>";
            timings += "<tr><td align='center' colspan='10' style='background-color:#F7B54A;color:#000000'>Breakfast</td></tr>";

            var bst = new Date('1900/1/1 ' + bs);

            var bet = new Date('1900/1/1 ' + be);

            count = 0;
            timings += "<tr>";

            d1 = new Date(bst);
            d1.setMinutes(d1.getMinutes() - 15);
            for (t = 0; 1 == 1; t + 15) {
                d2 = new Date(d1);
                d2.setMinutes(d1.getMinutes() + 15);
                tim = dateFormat(d2, "shortTime", false)
                d1 = new Date(d2);

                if (d1 > bet)
                    break;

                if (count % 10 == 0)
                    timings += "</tr><tr>";

                count++;

                timings += "<td style='background-color:#FDF6D4'><a href='javascript:void(0);' style='color:black' onclick='clearInterval("+ timerinterval +"); SetTime($(this).html());'>" + tim + "</a></td>";
            }
            timings += "</tr></table>";

        }
        if (timings != "")
            timings += "<br/>";

        //LUNCH TIME DISPLAY
        if (ls != null && ls != "00:00:00") {
            timings += "<table cellspacing='4' cellpadding='4' border='1' style='width:100%' >";
            timings += "<tr><td align='center' colspan='10' style='background-color:#F7B54A;color:#000000'>Lunch</td></tr>";

            var lst = new Date('1900/1/1 ' + ls);

            var let = new Date('1900/1/1 ' + le);

            count = 0;
            timings += "<tr>";

            d1 = new Date(lst);
            d1.setMinutes(d1.getMinutes() - 15);
            for (t = 0; 1 == 1; t + 15) {
                d2 = new Date(d1);
                d2.setMinutes(d1.getMinutes() + 15);
                tim = dateFormat(d2, "shortTime", false)
                d1 = new Date(d2);

                if (d1 > let)
                    break;

                if (count % 10 == 0)
                    timings += "</tr><tr>";

                count++;
                timings += "<td style='background-color:#FDF6D4'><a href='javascript:void(0);' style='color:black' onclick='clearInterval("+ timerinterval +"); SetTime($(this).html());'>" + tim + "</a></td>";

            }
            timings += "</tr></table>";

        }

        if (timings != "")
            timings += "<br/>";
        //DINNER TIME DISPLAY
        if (ds != null && ds != "00:00:00") {
            timings += "<table cellspacing='4' cellpadding='4' border='1' style='width:100%' >";
            timings += "<tr><td align='center' colspan='10' style='background-color:#F7B54A;color:#000000'>Dinner</td></tr>";

            var dst = new Date('1900/1/1 ' + ds);
			strHr = dst.getHours();
            var det = new Date('1900/1/1 ' + de);
            endHr = det.getHours();

            if (endHr < strHr)
                det = new Date('1900/1/2 ' + de);

            count = 0;
            timings += "<tr>";

            d1 = new Date(dst);

            d1.setMinutes(d1.getMinutes() - 15);
            for (t = 0; 1 == 1; t + 15) {
                d2 = new Date(d1);
                d2.setMinutes(d1.getMinutes() + 15);
                tim = dateFormat(d2, "shortTime", false)
                d1 = new Date(d2);

                if (d1 > det)
                    break;

                if (count % 10 == 0)
                    timings += "</tr><tr>";

                count++;
                timings += "<td style='background-color:#FDF6D4'><a href='javascript:void(0);' style='color:black' onclick='clearInterval("+ timerinterval +"); SetTime($(this).html());'>" + tim + "</a></td>";

            }
            timings += "</tr></table>";

        }

        if (timings != "")
            timings += "<br/>";

        $('#basic-modal-content11').html(timings);

    }



    //******************************************************

    //****** I have Changed the Call To this Plugin in order to display Pax No in a pop up ****	
    $('#img_px').click(function () {
        $('#txtPax').timepicker('show');
        $('#txtTable').val('');
        $('#hdnTno').val('');
    });

    $('#txtPax').timepicker(
	{
	    hourText: 'Pax',
	    amPmText: ['', ''],
	    hours: {
	        starts: 1,
	        ends: 50
	    },
	    showMinutes: false,
	    rows: 5,
	    defaultTime: ''
	});

    /*   $('#txtTime').click(function () {
    $('#txtTable').val('');
    $('#hdnTno').val('');
    sessionStorage.tid = "";
    sessionStorage.tnm = "";
    });
    */
    $('#txtPax').click(function () {
        $('#txtTable').val('');
        $('#hdnTno').val('');

    });

    $("#drpPreference").change(function () {
        if ($('#hdnStatus').val() == "") {
            $('#txtTable').val('');
            $('#hdnTno').val('');
        }
    });

    //****** I have Changed the Call To this Plugin in order to display Pax No in a pop up Ends ****	


    $("#txtDate").datepicker({
        minDate: 0,
        buttonImage: 'img/calender.jpg',
        buttonText: "Click For Calender To pop up",
        buttonImageOnly: true,
        showOn: 'both',
        dateFormat: 'dd M yy'
    });

    $("#txtfilterdate").datepicker(
    {
        dateFormat: 'dd M yy'
    });

    $("#tabs").tabs();
    //************ Token Input Call To Fetch Customer Details*****************

    // $("#customerfilter").tokenInput("http://getkonekt.com:8181/ConciergeAPI.php?input=" + $('#customerfilter').val() + "&accountid=" + acid + "&tp=GCI");
    $("#customerfilter").tokenInput("http://beta.mobikontech.com:8181/ConciergeAPI.php?input=" + $('#customerfilter').val() + "&accountid=" + acid + "&tp=GCI");
    // $("#customerfilter").tokenInput("http://192.168.1.52:8090/ConciergeAPI.php?input=" + $('#customerfilter').val() + "&accountid=" + acid + "&tp=GCI");


    /**************** JSON PARSING*********************
    
    *** SAMPLE ON HOW TO PARSE JSON  ***
    var json = '{"result":true,"count":1}',
    obj = JSON && JSON.parse(json) || $.parseJSON(json);

    alert(obj.count);
    */

    // Below Function would be called in intervals to check whether there are any tables to be released

    setInterval(function () {
        //*************************AJAX CALL FOR FETCHING CHECKED IN TABLES*************************************

        var o = '{"csid":' + csid + ',"bdt":"' + today + '"}';
        $.ajax({
            type: "POST",
            url: API,
            data: { 'd': o, 'tp': 'GRELTBL' },
            contentType: "application/json; charset=utf-8",
            dataType: 'jsonp',
            jsonpCallback: 'jsonpCBDTFn15',
            timeout: 20000,
            success: function (data) {

                if (data[0].Success == 1) {
                    FlashReleaseButton(data);

                }

            },

            error: function () {

                // $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to retreive checked-in tables.");
            }
        });
    }, 60000);

    //*************************AJAX CALL FOR GET TABLES ACCORDING TO PREFERENCES*************************************
    var p = '{"csid":' + csid + ',"prefs":"","bdt":"' + today + '","bt":"' + time + '"}';

    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': p, 'tp': 'GPREFTBL' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn16',
        timeout: 20000,
        success: function (data) {

            if (data[0].Success == 1) {
                DynamicAvailableTables();
                $('.simplemodal-close').click();
            }

        }

    });


   
    //****************************************************************
    // Below Call is to  Get Booking Details

    var i = '{"csid": ' + csid + ',"dt":"' + today + '","st":"","dit":""}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': i, 'tp': 'GBT' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn5',
        timeout: 20000,
        success: function (data) {

            if (data[0].Success == 1) {
                sessionStorage.BookingDetails = JSON.stringify(data);
                dynamicDivs(data);
            }
            else {
                $('#outer_tableb').html('<tr><td>No Bookings Found</td></tr>');
                $('#outer_tablel').html('<tr><td>No Bookings Found</td></tr>');
                $('#outer_tabled').html('<tr><td>No Bookings Found</td></tr>');
                $('#outer_tabledi').html('<tr><td>No Bookings Found</td></tr>');
                sessionStorage.BookingDetails = null;
            }
        },

        error: function () {
            $('#outer_tableb').html('<tr><td>No Bookings Found</td></tr>');
            $('#outer_tablel').html('<tr><td>No Bookings Found</td></tr>');
            $('#outer_tabled').html('<tr><td>No Bookings Found</td></tr>');
            $('#outer_tabledi').html('<tr><td>No Bookings Found</td></tr>');
            $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to get the Bookings.");
        }
    });


    //****************AJAX CALL FOR PAX AND CHECKINS*********************
    var j = '{"csid":' + csid + ',"dt":"' + today + '"}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': j, 'tp': 'GCHK' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn4',
       // timeout: 20000,
        success: function (data) {
            if (data[0].TotChk != "" && data[0].TotChk != null && data[0].TotPax != "" && data[0].TotPax != null) {

                // sessionStorage.TotalCheckins = data[0].TotChk;
                // sessionStorage.TotalPax = data[0].TotPax;

                $("#txtTotalCheckIn").val(data[0].TotChk);
                $("#txtTotalPax").val(data[0].TotPax);
            }
        },

        error: function () {

            $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to get the Total Checkins and Pax.");
        }
    });
    // alert(sessionStorage.TotalCheckins);
    //**************** AJAX CALL FOR FETCHING PREFERENCES *********************
    var p = '{"csid":' + csid + '}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': p, 'tp': 'GSPREF' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn9',
        timeout: 20000,
        success: function (data) {
            if (data[0].Success == 1) {
                //alert(data[0].PrefDtls[1].StPrefId);
                //alert(data[0].PrefDtls[1].Pref)

                for (var i = 0; i < data[0].PrefDtls.length; i++) {
                    $('#drpPreference').append("<option value=\"" + data[0].PrefDtls[i].StPrefId + "\">" + data[0].PrefDtls[i].Pref + "</option>");
                }
                $('#drpPreference').multiselect('refresh');   /* This is to refresh the list. Refresh is default method in plugin*/
                $('#drpPreference').multiselect('uncheckAll');   /*This method is to unselect all options*/
            }
        },

        error: function () {

            $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to get your preferences.");
        }
    });
    //*************************AJAX CALL FOR FETCHING CHECK  IN OUT MESSAGES*************************************
/*
    var l = '{"csid":' + csid + '}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': l, 'tp': 'GCHKMSG' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn3',
        timeout: 20000,
        success: function (data) {
            // alert(data);

            if (data[0].Success == 1) {

                $('#txtCIsms').val(unescape(data[0].ChkMsg[0].ChkISms));
                $('#txtCIeml').html(unescape(data[0].ChkMsg[0].ChkIEmail));
                $('#txtCOsms').val(unescape(data[0].ChkMsg[0].ChkOSms));
                $('#txtCOeml').html(unescape(data[0].ChkMsg[0].ChkOEmail));
				 $('#txtCIeml').jqte();
				 $('#txtCOeml').jqte();
            }
        },

        error: function () {

            $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to get your messages.");
        }
    });
*/

    //*************************AJAX CALL FOR OFFERS*************************************
    var m = '{"csid":' + csid + '}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': m, 'tp': 'GAOFF' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn1',
        timeout: 20000,
        success: function (data) {

            if (data[0].Success == 1) {

                DynamicOffers(data);

            }
            else {
                $('#ofrtab').html('<tr><td>No Offers Found</td></tr>');

            }

        },

        error: function () {
            $('#ofrtab').html('<tr><td>No Offers Found</td></tr>');

            $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to get your offers.");
        }
    });


    //**************************AJAX CALL FOR BADGER - GET BOOKING NOTIFICATIONS***********************************


    $(function () {
        //alert("a");
        //setTimeout('notify()', 1000);
        notify();
    });




    //*************************AJAX CALL FOR FETCHING AVAILABLE TABLES FROM PREFERENCES*************************************
    /*	 $('#txtTable').click(function (){
    var Pref = $("#drpPreference").multiselect("getChecked").map(function(){
    return this.value;	
    }).get();
    var BookingDate = $('#txtDate').val();
    var BookingTime = $('#txtTime').val();
    var Pax = $('#txtPax').val();
    var n = '{"csid":1,"spref":"'+ Pref +'","px":"'+ Pax +'","bd":"'+ BookingDate +'","bt":"'+ BookingTime +'"}';
    $.ajax({
    type: "POST",
    url: API,
    data: { 'd': n, 'tp': 'GAT' },
    contentType: "application/json; charset=utf-8",
    dataType: 'jsonp',
    jsonpCallback: 'jsonpCBDTFn10',

    success: function (data) {

    if (data[0].Success == 1) {
					
					
    }


    }
    });
    });
    */

    // Below Are call back functions for all different ajax calls.. These must be different as when putting the same callback, same json is picked as response for different methods 	


    function jsonpCBDTFn1() {       /* JSON CALL BACK FOR GET ALL OFFERS */

    }
    function jsonpCBDTFn2() {        /* JSON CALL BACK FOR POSTING CHECK IN OUT MSGS */

    }

    function jsonpCBDTFn3() {       /* JSON CALL BACK FOR FETCHING CHECK IN OUT MSGS */

    }

    function jsonpCBDTFn4() {       /* JSON CALL BACK FOR FETCHING TOTAL CHECKING AND TOTAL PAX */

    }

    function jsonpCBDTFn5() {       /* JSON CALL BACK FOR GET ALL BOOKINGS FOR TODAY */

    }

    function jsonpCBDTFn6() {        /* JSON CALL BACK FOR TABLE CHECK IN */

    }

    function jsonpCBDTFn7() {        /* JSON CALL BACK FOR TABLE Cancellation */

    }

    function jsonpCBDTFn8() {		/* JSON CALL BACK FOR TABLE BOOKING */

    }

    function jsonpCBDTFn9() {		/* JSON CALL BACK FOR FETCHING PREFERENCES */

    }

    function jsonpCBDTFn10() {		/* JSON CALL BACK FOR FETCHING TABLES */

    }

    function jsonpCBDTFn11() {		/* JSON CALL BACK FOR EDITING TABLES */

    }

    function jsonpCBDTFn12() {		/* JSON CALL BACK FOR SPECIFIC BOOKING DETAILS */

    }

    function jsonpCBDTFn13() {		/* JSON CALL BACK FOR COVER STATS DETAILS */

    }

    function jsonpCBDTFn14() {		/* JSON CALL BACK FOR RELEASE TABLE */

    }

    function jsonpCBDTFn15() {		/* JSON CALL BACK FOR FETCHING CHECKED IN TABLES */

    }

    function jsonpCBDTFn16() {		/* JSON CALL BACK FOR FETCHING TABLES ACCORDING TO PREFERENCES */

    }

    function jsonpCBDTFn17() {		/* JSON CALL BACK FOR FETCHING OFFERS ACCORDING TO BOOKING DATE TIME */

    }

    function jsonpCBDTFn18() {		/* JSON CALL BACK FOR FETCHING BOOKINGS ACCORDING TO TIME */

    }


    var chr = new Date().getHours();
    var cmin = new Date().getMinutes();
    var csec = new Date().getSeconds();
    if (chr < 10) { chr = '0' + chr } if (cmin < 10) { cmin = '0' + cmin } if (csec < 10) { csec = '0' + csec }
    ctime = chr + ':' + cmin + ':' + csec;
    // alert(sessionStorage.LunchStart);
    // alert(sessionStorage.BrkFastEnd);

    // alert(ctime);
    if (ctime > sessionStorage.BrkFastStart && ctime < sessionStorage.BrkFastEnd) {
        $("#tabs").tabs({ active: 0 });
    }
    else if (ctime > sessionStorage.LunchStart && ctime < sessionStorage.LunchEnd) {
        $("#tabs").tabs({ active: 1 });
    }
    else if (ctime > sessionStorage.DinnerStart && ctime < sessionStorage.DinnerEnd) {
        // alert('show dinner tab');
        $("#tabs").tabs({ active: 2 });
    }
    else {
        $("#tabs").tabs({ active: 3 });
    }




    //************** Below Code is to show Simple Modal Dialog Pop Up *******************

    $('#offerbox').click(function (e) {
        $('#basic-modal-content5').modal();

        return false;
    });

    $('#Badger').click(function (e) {
        $('#basic-modal-content8').modal();

        return false;
    });
    $('#noti').click(function (e) {
        $('#basic-modal-content8').modal();

        return false;
    });
    $('#inoutbox').click(function (e) {
        $('#basic-modal-content4').modal();

        return false;
    });

    $('#release').click(function (e) {
        $('#basic-modal-content').modal();

        return false;
    });

    $('.amend').click(function (e) {
        $('#basic-modal-content2').modal();

        return false;
    });

    $('#txtTable').click(function (e) {
        if ($('#txtTime').val() != "" && $('#txtPax').val() != "") {
            GetAvailableTables();
            return false;
        }
        else {
            $().toastmessage('showWarningToast', "Please enter Pax and Time");
        }
    });

    $('#imgtab').click(function (e) {
        if ($('#txtTime').val() != "" && $('#txtPax').val() != "") {
            GetAvailableTables();
            return false;
        }
        else {
            $().toastmessage('showWarningToast', "Please enter Pax and Time");
        }
    });

    $('#coverstats').click(function (e) {
        //**************************AJAX CALL FOR COVER STATS***********************************
        var n = '{"entid":"' + eid + '"}';
        $.ajax({
            type: "POST",
            url: API,
            data: { 'd': n, 'tp': 'CS' },
            contentType: "application/json; charset=utf-8",
            dataType: 'jsonp',
            jsonpCallback: 'jsonpCBDTFn13',
            timeout: 20000,
            success: function (data) {

                if (data[0].Success == 1) {
                    //sessionStorage.CoverStats = JSON.stringify(data);
                    //alert(data[0].CoverStats[0].BrkCheckedIn);
                    $('#bcc').html(data[0].CoverStats[0].BrkBookings);
                    $('#bcp').html(data[0].CoverStats[0].BrkPAXToDine);
                    $('#lcc').html(data[0].CoverStats[0].LunchBookings);
                    $('#lcp').html(data[0].CoverStats[0].LunchPAXToDine);
                    $('#dcc').html(data[0].CoverStats[0].DinnerBookings);
                    $('#dcp').html(data[0].CoverStats[0].DinnerPAXToDine);
                    $('#bab').html(data[0].CoverStats[0].BrkAdvBooking);
                    $('#bap').html(data[0].CoverStats[0].BrkAdvPAX);
                    $('#lab').html(data[0].CoverStats[0].LunchAdvBooking);
                    $('#lap').html(data[0].CoverStats[0].LunchAdvPAX);
                    $('#dab').html(data[0].CoverStats[0].DnnrAdvBooking);
                    $('#dap').html(data[0].CoverStats[0].DnnrAdvPAX);
                    $('#bdc').html(data[0].CoverStats[0].BrkCheckedIn);
                    $('#bdp').html(data[0].CoverStats[0].BrkPAXDined);
                    $('#ldc').html(data[0].CoverStats[0].LunchCheckedIn);
                    $('#ldp').html(data[0].CoverStats[0].LunchPAXDined);
                    $('#ddc').html(data[0].CoverStats[0].DinnerCheckedIn);
                    $('#ddp').html(data[0].CoverStats[0].DinnerPAXDined);
                }

            },

            error: function () {

                $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to get your stats.");
            }
        });
        $('#basic-modal-content7').modal();

        return false;
    });


    // $('#btnBook').click(function (e) {
    //     $('#basic-modal-content3').modal();

    //     return false;
    //  });



});

/**********************  Document Ready Event Ends Here*******************/
function dynamicDivs(data) {

    //"+ data[0].BDtls[i].SeatingPreferenceIDs + "
    //var r = new Array();
    //r[0] = "Sea Facing";
    //r[1] = "Baby Chair,Non Smoking";
    $('#outer_tableb').html('');
    $('#outer_tablel').html('');
    $('#outer_tabled').html('');
    $('#outer_tabledi').html('');
    for (i = 0; i < data[0].BDtls.length; i++) {

        var strText = "";
        strText = strText + " <table id='inner_table'  style='border:1px solid #000000; border-radius:5px; width:100%;' cellpadding='1'>";
        strText = strText + " <tr id= \'detailrow" + i + "\' >";
        strText = strText + " <td style='font-size:1.1em; color:#104E7F; font-family:Calibri; width:50%; text-align:left;'>" + data[0].BDtls[i].CustomerName + "</td>";
        strText = strText + " <td style='width:50%;'>";
        strText = strText + " <div style='float:left;text-align:right;width:40%;'>Sex :</div>";
        strText = strText + " <div style='text-align:left;float:left;width:60%; color:#104E7F; font-family:Cambria;font-size:1.1em;'>" + data[0].BDtls[i].Gender + "</div>";
        strText = strText + " </td>";
        strText = strText + " </tr>";
        strText = strText + " <tr>";
        if (data[0].BDtls[i].Cell_Number != "" && data[0].BDtls[i].Cell_Number != null) {
            strText = strText + " <td style='font-size:1.1em; font-family:Calibri; width:50%; text-align:left;'>" + data[0].BDtls[i].CountryCallingCode + '-' + data[0].BDtls[i].Cell_Number + "</td>";
        }
        else {
            strText = strText + " <td style='font-size:1.1em; font-family:Calibri; width:50%; text-align:left;'>" + data[0].BDtls[i].Cell_Number + "</td>";

        }
        strText = strText + " <td style='width:50%;'>";
        strText = strText + " <div style='float:left;text-align:right;width:40%;'>Pax :</div>";
        strText = strText + " <div style='text-align:left;float:left;width:60%; color:#104E7F; font-family:Cambria;font-size:1.1em;'>" + data[0].BDtls[i].Pax + "</div>";
        strText = strText + " </td>";
        strText = strText + " </tr>";
        strText = strText + " <tr>";
        strText = strText + " <td class='wrapword' style='font-size:1.1em; font-family:Calibri; width:50%; text-align:left'>" + data[0].BDtls[i].EmailId + "</td>";
        strText = strText + " <td style='width:50%;'>";
        strText = strText + " <div style='float:left;text-align:right;width:40%;'>Date :</div>";
        strText = strText + " <div style='text-align:left;float:left;width:60%; color:#104E7F; font-family:Cambria;font-size:1.1em;'>" + data[0].BDtls[i].DisplayDate + "</div>";
        strText = strText + " </td>";
        strText = strText + " </tr>";

        strText = strText + " <tr>";
        strText = strText + " <td style='font-size:1.1em; font-family:Calibri; width:50%; text-align:left;'>"
		strText = strText + " <div style='float:left;text-align:left;width:20%;'>Table :</div>";
		strText = strText + " <div class='wrapword' style='text-align:left;float:left;width:80%; color:#104E7F; font-family:Cambria;font-size:1.1em;'>" + data[0].BDtls[i].TableNos + "</div>";
        strText = strText + " </td>";
		strText = strText + " <td style='width:50%;'>";
        strText = strText + " <div style='float:left;text-align:right;width:40%;'>Booking Time :</div>";
        strText = strText + " <div style='text-align:left;float:left;width:60%; color:#104E7F; font-family:Cambria;font-size:1.1em;'>" + data[0].BDtls[i].DisplayTime + "</div>";
        strText = strText + " </td>";
        strText = strText + " </tr>";
        strText = strText + " <tr>";
        strText = strText + " <td class='wrapword' style='font-size:1.1em; font-family:Calibri; width:50%; text-align:left;'><span style='color:#104E7F'>Preferences : </span>" + data[0].BDtls[i].SeatingPrefNames + "</td>";
        strText = strText + " <td style='width:50%;'>";
        strText = strText + " <div style='float:left;text-align:right;width:40%;'>Check-In Time :</div>";
        strText = strText + " <div style='text-align:left;float:left;width:60%; color:#104E7F; font-family:Cambria;font-size:1.1em;'>" + data[0].BDtls[i].CheckInTime + "</div>";
        strText = strText + " </td>";
        strText = strText + " </tr>";
        strText = strText + " <tr>";
        strText = strText + " <td colspan='3' style='width:100%'><div style='font-size:1.1em; color:#104E7F; font-family:Calibri;float:left;text-align:left;'>Note: </div><div style='font-size:1.1em; font-family:Calibri;float:left;text-align:left;'>" + unescape(data[0].BDtls[i].RequestNote) + "</div></td>";
        strText = strText + " </tr>";
        strText = strText + " <tr>";
        strText = strText + " <td colspan='3'>";
        strText = strText + " <div style='width:100%;float:left;text-align:left;'>";
        if (data[0].BDtls[i].CheckedIn == "No" && data[0].BDtls[i].BookingDate == today1 && data[0].BDtls[i].TableNos != "") {
            strText = strText + " <a href='javascript:void(0)' onclick='TableCheckIn(" + data[0].BDtls[i].CBId + ",\"" + data[0].BDtls[i].BookingTime + "\");'><img src='img/checkinbutton.jpg' style='padding-right:2%'></a>";
        }
        if (data[0].BDtls[i].CheckOutStatus == "n" && data[0].BDtls[i].BookingDate >= today1) {
            strText = strText + " <a href='javascript:void(0)' onclick='AmendBooking(" + i + ");'><img src='img/editbutton.jpg' style='padding-right:2%'></a>";
            strText = strText + "<a href='javascript:void(0)' onclick='CancelBooking(" + data[0].BDtls[i].CBId + ");'><img src='img/cancelbooking.jpg' style='padding-right:2%'></a>";
        }
		else if(data[0].BDtls[i].BookingDate < today1){
		    strText = strText + "<span style='color:red'></span>";
		}
        else {
            strText = strText + "<span style='color:red'>Checked Out</span>";
        }
       // strText = strText + "<a href='javascript:void(0)' onclick='FetchProfile(" + i + ")'><img src='img/profilebutton.jpg' class='profile'></a>";
        if (data[0].BDtls[i].BookingSource == "Facebook") {
            strText = strText + "<img src='img/facebook.png' style='padding-right:2%'>";
        }
        else if (data[0].BDtls[i].BookingType == "Walking") {
            strText = strText + "<img src='img/walking.png' style='padding-right:2%'>";
        }
        else {
            strText = strText + "<img src='img/phone.png' style='padding-right:2%'>";
        }
       
          

         strText = strText + "</div>"; //<a href='javascript:void(0)' onclick=''><img src='img/profilebutton.jpg' class='profile'></a>";

        strText = strText + " </td>";
        strText = strText + " </tr>";
        strText = strText + " </table>";
        strText = strText + " <br />";


        if (data[0].BDtls[i].Category == 'Breakfast') {
            if (data[0].BDtls[i].CheckedIn == "Yes") {
                $('#outer_tabledi').append(strText);
            }
            else
                $('#outer_tableb').append(strText);
        }

        if (data[0].BDtls[i].Category == 'Lunch') {
            if (data[0].BDtls[i].CheckedIn == "Yes") {
                $('#outer_tabledi').append(strText);
            }
            else
                $('#outer_tablel').append(strText);
        }

        if (data[0].BDtls[i].Category == 'Dinner') {
            if (data[0].BDtls[i].CheckedIn == "Yes") {
                $('#outer_tabledi').append(strText);
            }
            else
                $('#outer_tabled').append(strText);
        }


        //$('#tabs-1 #outer_table').append(strText);
    }

}


function TableCheckIn(bid,btime) {

    today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    var hours = today.getHours();
    var minutes = today.getMinutes();
    var seconds = today.getSeconds();
     var flag = false;
    if (dd < 10) { dd = '0' + dd } if (mm < 10) { mm = '0' + mm }
    today = mm + '/' + dd + '/' + yyyy;

    if (hours < 10) { hours = '0' + hours } if (minutes < 10) { minutes = '0' + minutes } if (seconds < 10) { seconds = '0' + seconds }
    currentts = today + " " + hours + ":" + minutes + ":" + seconds;

    var chktime;
    chktime = hours + ":" + minutes + ":" + seconds;
/*
    if (chktime >= sessionStorage.BrkFastStart && chktime <= sessionStorage.BrkFastEnd && btime >= sessionStorage.BrkFastStart && btime <= sessionStorage.BrkFastEnd) {
        flag = true;
    }
    else if (chktime >= sessionStorage.LunchStart && chktime <= sessionStorage.LunchEnd && btime >= sessionStorage.LunchStart && btime <= sessionStorage.LunchEnd) {
        flag = true;
    }
    else if (chktime >= sessionStorage.DinnerStart && chktime <= sessionStorage.DinnerEnd && btime >= sessionStorage.DinnerStart && btime <= sessionStorage.DinnerEnd) {
        flag = true;
    }
    
    else {
        flag = false;
    }

   if (flag == true) {
*/
        $.confirm({
            'title': 'Check In Confirmation',
            'message': 'You are about to check in this booking. <br />Do you want to Continue?',
            'buttons': {
                'Yes': {
                    'class': 'blue',
                    'action': function () {
                        $('#content').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
                        var l = '{"csid":' + csid + ',"cbid":' + bid + ',"chkdt":"' + currentts + '"}';
                        $.ajax({
                            type: "POST",
                            url: API,
                            data: { 'd': l, 'tp': 'SCHKTBL' },
                            contentType: "application/json; charset=utf-8",
                            dataType: 'jsonp',
                            jsonpCallback: 'jsonpCBDTFn6',
							//timeout: 20000,
                            success: function (data) {
                                if (data[0].Success == 1) {

                                    $('.loading').remove();
                                    $().toastmessage('showSuccessToast', "Table Checked In Successfully");
                                    $(this).attr('disabled', true);
                                    RefreshDynamicDivs();
                                    RefreshCounter();

                                }
                                else if (data[0].Success == -1) {
                                    $('.loading').remove();
                                    $().toastmessage('showWarningToast', "This table is not available.<br \> Please wait or edit this booking");
                                }
                                else if (data[0].Success == -2) {
                                    $('.loading').remove();
                                   // $().toastmessage('showWarningToast', "Table can only be checked in <br \> + - 15 minutes from booking time.<br \> Please edit this booking to check-in");
                                   $().toastmessage('showWarningToast', "Sorry, Outlet timing is over.");
							   }
                                else {
                                    $('.loading').remove();
                                    $().toastmessage('showErrorToast', "Table Checked In Failed");
                                }
                            },

                            error: function () {
                                $('.loading').remove();
                                $().toastmessage('showErrorToast', "Timed Out");
                            }
                        });

                    }
                },
                'No': {
                    'class': 'gray',
                    'action': function () { } // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
  /*  }
    else {
        $().toastmessage('showWarningToast', "This table cannot be checked-in now.");
    }*/
}

function AmendBooking(i) {
    today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    var hours = today.getHours();
    var minutes = today.getMinutes();
    var seconds = today.getSeconds();

    if (dd < 10) { dd = '0' + dd } if (mm < 10) { mm = '0' + mm }
    today = mm + '/' + dd + '/' + yyyy;

    if (hours < 10) { hours = '0' + hours } if (minutes < 10) { minutes = '0' + minutes } if (seconds < 10) { seconds = '0' + seconds }
    currentts = today + " " + hours + ":" + minutes + ":" + seconds;

    $.confirm({
        'title': 'Edit Confirmation',
        'message': 'You are about to edit this booking\'s details. <br />Do you want to Continue?',
        'buttons': {
            'Yes': {
                'class': 'blue',
                'action': function () {
                    $('#drpPreference').multiselect('uncheckAll');
                    while (tbnarr.length > 0) {
                        tbnarr.pop();
                    }
                    while (tbiarr.length > 0) {
                        tbiarr.pop();
                    }
                    var temp = $.parseJSON(sessionStorage.BookingDetails);
                    var ConciergeSettingId = temp[0].BDtls[i].CSId;
                    var ConciergeBookingId = temp[0].BDtls[i].CBId;
					var TName = temp[0].BDtls[i].CustomerName.split('. ');
					
					if(TName[0] == "Mr" || TName[0] == "Ms")
					{
					  var Title = TName[0];
                      var Name = TName[1];
					}
					else
					{
						var Title = "";
						var Name = temp[0].BDtls[i].CustomerName;
					}
                    var CountryCallingCode = temp[0].BDtls[i].CountryCallingCode;
                    var Cell_Number = temp[0].BDtls[i].Cell_Number;
                    var Email = temp[0].BDtls[i].EmailId;
                    var Pax = temp[0].BDtls[i].Pax;
                    var d = new Date(temp[0].BDtls[i].DisplayDate + " " + temp[0].BDtls[i].BookingTime);
                    var BookingDate = dateFormat(d, "dispDate", false);
                    var d1 = new Date(BookingDate + " " + temp[0].BDtls[i].BookingTime);
                    var BookingTime = dateFormat(d1, "shortTime", false);
                    var DineType = temp[0].BDtls[i].BookingType;
                    var Source = temp[0].BDtls[i].BookingSource;
                    var Note = temp[0].BDtls[i].RequestNote;
                    var Gender = temp[0].BDtls[i].Gender;
                    var Pref = temp[0].BDtls[i].SeatingPreferenceIDs;
                    var TabId = temp[0].BDtls[i].TableIDs;
                    var TabNo = temp[0].BDtls[i].TableNos;
                    var CheckedIn = temp[0].BDtls[i].CheckedIn;
                    var Status = temp[0].BDtls[i].BookingStatus;
                    var SysDate = currentts;
                    var tnarr = new Array();
                    var tiarr = new Array();
                    sessionStorage.tid = TabId;
                    sessionStorage.tnm = TabNo;

                    tiarr = TabId.split(",");
                    tnarr = TabNo.split(",");
                    for (var j = 0; j < tnarr.length; j++) {
                        tbnarr.push(tnarr[j]);
                        tbiarr.push(tiarr[j]);
                    }

                    clearInterval(timerinterval);
                    // alert(tbnarr + "--" + tbiarr);
                    $('#txtName').css('border', '1px solid #DDDDDD');
                    $('#txtMobile').css('border', '1px solid #DDDDDD');
                    $('#txtEmail').css('border', '1px solid #DDDDDD');
                    $('#txtPax').css('border', '1px solid #DDDDDD');
                    $('#txtTable').css('border', '1px solid #DDDDDD');
                    $('#txtName').attr('placeholder', '');
                    $('#txtEmail').attr('placeholder', '');
                    $('#txtMobile').attr('placeholder', '');
 
					$('#drpTitle').val(Title);
                    $('#txtName').val(Name);
                    $('#txtName').attr('disabled', 'true');
                    $('#drpCCC').val(CountryCallingCode);
                    $('#drpCCC').attr('disabled', 'true');
                    $('#txtMobile').val(Cell_Number);
                    $('#txtMobile').attr('disabled', 'true');
                    $('#txtEmail').val(Email);
                    $('#txtEmail').attr('disabled', 'true');
                    $('#txtPax').val(Pax);
                    $('#txtTable').val(TabNo);
                    $('#txtDate').val(BookingDate);
                    if (CheckedIn == "Yes") {
                        $('#txtDate').attr('disabled', 'true');
                        $('#txtTime').attr('disabled', 'true');
                    }
                    $('#txtTime').val(BookingTime);
                    $('#txtNote').val(unescape(Note));
                    $('#drpSex').val(Gender);
                    $('#hdnStatus').val(Status);
                    $('#hdnChecked').val(CheckedIn);
                    $('#hdnBid').val(ConciergeBookingId);
                    $('#hdnTno').val(TabId);
                    var valArr = new Array();
                    if (Pref != "" && Pref != null) {
                        valArr = Pref.split(",");

                        //var valArr = ["1","2","3"];
                        //var size = valArr.length;

                        /* Below Code Matches current objects (i.e. option) value with the array */
                        $("#drpPreference").multiselect("widget").find(":checkbox").each(function () {
                            if (jQuery.inArray(this.value, valArr) != -1)
                                this.click();

                        });
                    }

                }
            },
            'No': {
                'class': 'gray',
                'action': function () { } // Nothing to do in this case. You can as well omit the action property.
            }
        }
    });
}


function CancelBooking(bid) {
    //alert(bid);

    $.confirm({
        'title': 'Cancel Confirmation',
        'message': 'You are about to cancel this booking. <br />Do you want to Continue?',
        'buttons': {
            'Yes': {
                'class': 'blue',
                'action': function () {
                    var l = '{"csid":' + csid + ',"cbid":' + bid + '}';
                    $('#content').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
                    $.ajax({
                        type: "POST",
                        url: API,
                        data: { 'd': l, 'tp': 'SCANTBL' },
                        contentType: "application/json; charset=utf-8",
                        dataType: 'jsonp',
                        jsonpCallback: 'jsonpCBDTFn7',
                        //	timeout: 20000,
                        success: function (data) {
                            if (data[0].Success == 1) {
                                $().toastmessage('showSuccessToast', "Table Cancelled Successfully");
                                $('.loading').remove();
                                RefreshDynamicDivs();
								 RefreshCounter();
                            }
                            else {
                                $().toastmessage('showErrorToast', "Table Cancellation Failed");
                                $('.loading').remove();
                            }
                        },

                        error: function () {
                            $('.loading').remove();
                            $().toastmessage('showErrorToast', "Timed Out");
                        }
                    });
                }
            },
            'No': {
                'class': 'gray',
                'action': function () { } // Nothing to do in this case. You can as well omit the action property.
            }
        }
    });


}

function showoffers() {
    
    $('#content').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
    var DiningType = $('input[name=custype]:radio:checked').val();
    var Pax = $('#txtPax').val();
    var BookingDate = $('#txtDate').val();
    var Time = $('#txtTime').val();
    var d2 = new Date('1900/01/01 ' + Time);
    var BookingTime = dateFormat(d2, "isoTime", false);  
    //*************************AJAX CALL FOR FETCHING SPECIFIC OFFERS*************************************
    var o = '{"csid":' + csid + ',"bdt":"' + BookingDate + ' ' + BookingTime + '","px":"' + Pax + '", "src" : "' + DiningType + '"}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': o, 'tp': 'GBBOFF' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn17',

        success: function (data) {

            if (data[0].Success == 1) {
				$('#basic-modal-content10').modal();
                SpecificOffers(data);
                $('.loading').remove();
            }
            else {
                $('.loading').remove();
                //$('.simplemodal-close').click();
               // $('#sofrtab').html('<tr><td>No Offers Found</td></tr><tr><td><input type="button" class="ckmsgbtn" id="proceed" onclick="Booking(this);" value="Proceed" /></td></tr>');
                Booking(this);
            }

        },

        error: function () {
            $('.loading').remove();
            $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to get your offers.");
        }
    });

   

}

function bookTable() {
    

    $('#txtName').css('border', '1px solid #DDDDDD');
    $('#txtMobile').css('border', '1px solid #DDDDDD');
    $('#txtEmail').css('border', '1px solid #DDDDDD');
    $('#txtPax').css('border', '1px solid #DDDDDD');
    $('#txtTable').css('border', '1px solid #DDDDDD');
    $('#txtTime').css('border', '1px solid #DDDDDD');
    $('#txtName').attr('placeholder', '');
    $('#txtEmail').attr('placeholder', '');
    $('#txtMobile').attr('placeholder', '');
    var Name = $('#txtName').val();
    var Email = $('#txtEmail').val();
    var Pax = $('#txtPax').val();
    var Cell_Country = $('#drpCCC').val();
    var Cell_Number = $('#txtMobile').val();
    var RequestNote = $('#txtNote').val();
    var BookingDate = $('#txtDate').val();
    var BookingTime = $('#txtTime').val();
    var TableNos = $('#txtTable').val();
    var TabNo = $('#hdnTno').val();
    var DiningType = $('input[name=custype]:radio:checked').val();
    var Bid = $('#hdnBid').val();
    var Pref = $("#drpPreference").multiselect("getChecked").map(function () {
        return this.value;
    }).get();
    //alert(Pref);
    var Gender = $('#drpSex').val();
    var Note = escape($('#txtNote').val());
    var BookingStatus = $('#hdnStatus').val();
    var CheckedInStatus = $('#hdnChecked').val();
    
	
    //***** Below Code is for Confirmation pop up in case of Table Booking ****
	
    if (ValidateFields()) {
	
        $.confirm({
            'title': 'Submit Confirmation',
            'message': 'You are about to submit details of ' + Name + ' for ' + Pax + ' people at ' + BookingDate + ',' + BookingTime + ' <br />Do you want to Continue?',
            'buttons': {
                'Yes': {
                    'class': 'blue',
                    'action': function () {

                        $('.simplemodal-close').click();

                        if (BookingStatus == "") {
                            showoffers();
                        }
                        else {
                            Booking(this);
                        }

                        //$('#basic-modal-content3').modal();

                    }
                },
                'No': {
                    'class': 'gray',
                    'action': function () { } // Nothing to do in this case. You can as well omit the action property.
                }
            }
        });
    }

}

function Booking(obj) {
clearInterval(timerinterval);
    $('#gtofr').attr('disabled', 'true');
    $('#skofr').attr('disabled', 'true');

    today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    var hours = today.getHours();
    var minutes = today.getMinutes();
    var seconds = today.getSeconds();

    if (dd < 10) { dd = '0' + dd } if (mm < 10) { mm = '0' + mm }
    today = mm + '/' + dd + '/' + yyyy;

    if (hours < 10) { hours = '0' + hours } if (minutes < 10) { minutes = '0' + minutes } if (seconds < 10) { seconds = '0' + seconds }
    currentts = today + " " + hours + ":" + minutes + ":" + seconds;

    $('#content').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
    $('#basic-modal-content10').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
   // setTimeout(function () { $(".loading").remove(); $().toastmessage('showErrorToast', "Request Timed Out."); $('.simplemodal-close').click(); }, 15000)
    if($('#drpTitle').val() != "")
	 var Name = $('#drpTitle').val() +'. '+$.trim($('#txtName').val());
	else
     var Name = $.trim($('#txtName').val());	
    var Email = $.trim($('#txtEmail').val());
    var Pax = $.trim($('#txtPax').val());
    var Cell_Country = $('#drpCCC').val();
    var Cell_Number = $.trim($('#txtMobile').val());
    var RequestNote = $('#txtNote').val();
    var d2 = new Date('1900/01/01 ' + $('#txtTime').val());
    var BookingTime = dateFormat(d2, "isoTime", false);
    var d1 = new Date($('#txtDate').val() + " " + BookingTime);
    var BookingDate = dateFormat(d1, "shortDate", false);
    var Time = $('#txtTime').val();
    var TableNos = $('#txtTable').val();
    var TabNo = $('#hdnTno').val();
    var DiningType = $('input[name=custype]:radio:checked').val();
    var Bid = $('#hdnBid').val();
    var Pref = $("#drpPreference").multiselect("getChecked").map(function () {
        return this.value;
    }).get();
    var Gender = $('#drpSex').val();
    var Note = escape($('#txtNote').val());
    var postData = "";
    var BookingStatus = $('#hdnStatus').val();
    // alert(BookingStatus);
    var CheckedInStatus = $('#hdnChecked').val();
    // alert(CheckedInStatus);
    var OfferId = "";
    if ($(obj).attr('id') == "gtofr") {
        OfferId = $('input[name=offer]:radio:checked').val();
    }

    if (BookingStatus == "") {
        postData = '{"bmod":"n","csid":' + csid + ',"cbid":"","custid":"","custnm":"' + Name + '","callco":"' + Cell_Country + '", "custno":"' + Cell_Number + '","custem":"' + Email + '","px":"' + Pax + '","bdt":"' + BookingDate + '","bt":"' + BookingTime + '","dinetp":"' + DiningType + '","reqnt":"' + Note + '","source":"Concierge","gen":"' + Gender + '","spref":"' + Pref + '","tabid":"' + TabNo + '","tabno":"' + TableNos + '", "sysdt":"' + currentts + '", "offid" : "' + OfferId + '"}';
    }
    else if (BookingStatus == "Booked" && CheckedInStatus == "No") {
        postData = '{"bmod":"e","csid":' + csid + ',"cbid":' + Bid + ',"custid":"","custnm":"' + Name + '","callco":"' + Cell_Country + '", "custno":"' + Cell_Number + '","custem":"' + Email + '","px":"' + Pax + '","bdt":"' + BookingDate + '","bt":"' + BookingTime + '","dinetp":"' + DiningType + '","reqnt":"' + Note + '","source":"Concierge","gen":"' + Gender + '","spref":"' + Pref + '","tabid":"' + TabNo + '","tabno":"' + TableNos + '", "sysdt":"' + currentts + '", "offid" : "' + OfferId + '"}';
    }

    else if (CheckedInStatus == "Yes") {
        postData = '{"bmod":"ep","csid":' + csid + ',"cbid":' + Bid + ',"custid":"","custnm":"' + Name + '","callco":"' + Cell_Country + '", "custno":"' + Cell_Number + '","custem":"' + Email + '","px":"' + Pax + '","bdt":"' + BookingDate + '","bt":"' + BookingTime + '","dinetp":"' + DiningType + '","reqnt":"' + Note + '","source":"Concierge","gen":"' + Gender + '","spref":"' + Pref + '","tabid":"' + TabNo + '","tabno":"' + TableNos + '", "sysdt":"' + currentts + '", "offid" : "' + OfferId + '"}';
    }
    //alert(postData);

    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': postData, 'tp': 'SBT' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn8',
        // timeout: 30000,
        success: function (data) {

            if (data[0].Success == 1) {
                var dispdate = dateFormat(new Date(), "dispDate", false)
                $('.loading').remove();
                $('.simplemodal-close').click();
                $().toastmessage('showSuccessToast', "Table Booked Successfully");
                RefreshDynamicDivs();
                RefreshCounter();
                $('#txtName').val('');
                $('#txtName').removeAttr('disabled');
                $('#drpCCC').val('91');
                $('#drpCCC').removeAttr('disabled');
                $('#txtMobile').val('');
                $('#txtMobile').removeAttr('disabled');
                $('#txtEmail').val('');
                $('#txtEmail').removeAttr('disabled');
                $('#txtPax').val('');
                $('#txtDate').val(dispdate);
                $('#txtDate').removeAttr('disabled');
                timercontrol();
                $('#txtTime').removeAttr('disabled');
                $('#txtTable').val('');
                $('#txtNote').val('');
                $('#drpPreference').multiselect('uncheckAll');
                $('#hdnStatus').val('');
                $('#hdnChecked').val('');
                $('#hdnBid').val('');
                $('#hdnTno').val('');
                $('#gtofr').removeAttr('disabled');
                $('#skofr').removeAttr('disabled');
                $('#drpSex').val('Male');
            }
			 else if (data[0].Success == -1) {
                $('.loading').remove();
                $('.simplemodal-close').click();
                $().toastmessage('showSuccessToast', "Table already booked");
            }
            else {
                $('.loading').remove();
                $('#gtofr').removeAttr('disabled');
                $('#skofr').removeAttr('disabled');
                $('.simplemodal-close').click();
                $().toastmessage('showErrorToast', "Table Booking Failed");
            }
        },

        error: function () {
            $('.loading').remove();
            $('.simplemodal-close').click();
            $('#gtofr').removeAttr('disabled');
            RefreshDynamicDivs();
			$().toastmessage('showErrorToast', "Some Error Occured.");
        }
    });
}

function resetFields() {
    $.confirm({
        'title': 'Reset Confirmation',
        'message': 'You are about to reset all the fields. <br />Do you want to Continue?',
        'buttons': {
            'Yes': {
                'class': 'blue',
                'action': function () {
                    var dispdate = dateFormat(new Date(), "dispDate", false)
                    $('#txtName').val('');
                    $('#txtName').removeAttr('disabled');
                    $('#drpCCC').val('91');
                    $('#drpCCC').removeAttr('disabled');
                    $('#txtMobile').val('');
                    $('#txtMobile').removeAttr('disabled');
                    $('#txtEmail').val('');
                    $('#txtEmail').removeAttr('disabled');
                    $('#txtPax').val('');
                    $('#txtDate').val(dispdate);
                    $('#txtDate').removeAttr('disabled');
                    timercontrol();
                    $('#txtTime').removeAttr('disabled');
                    $('#txtTable').val('');
                    $('#txtNote').val('');
                    $('#drpPreference').multiselect('uncheckAll');
                    $('#hdnStatus').val('');
                    $('#hdnChecked').val('');
                    $('#hdnBid').val('');
                    $('#hdnTno').val('');
                    $('#txtName').css('border', '1px solid #DDDDDD');
                    $('#txtMobile').css('border', '1px solid #DDDDDD');
                    $('#txtEmail').css('border', '1px solid #DDDDDD');
                    $('#txtPax').css('border', '1px solid #DDDDDD');
                    $('#txtTable').css('border', '1px solid #DDDDDD');
                    $('#txtTime').css('border', '1px solid #DDDDDD');
                    $('#txtName').attr('placeholder', '');
                    $('#txtEmail').attr('placeholder', '');
                    $('#txtMobile').attr('placeholder', '');
                    $('#drpSex').val('Male');
                    while (tbnarr.length > 0) {
                        tbnarr.pop();
                    }
                    while (tbiarr.length > 0) {
                        tbiarr.pop();
                    }
                    $().toastmessage('showSuccessToast', "Reset Successfull");
                }
            },
            'No': {
                'class': 'gray',
                'action': function () { } // Nothing to do in this case. You can as well omit the action property.
            }
        }
    });
}

function ValidateEmail(elementValue) {
    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    return emailPattern.test(elementValue);
}
function ValidateMobileNumber(elementValue) {
    var mob_num = /^\d{1,10}$/;
    return mob_num.test(elementValue);
}

function ValidatePaxNumber(elementValue) {
    var px_num = /^\d{1,3}$/;
    return px_num.test(elementValue);
}


function ValidateFields() {

    var Name = $.trim($('#txtName').val());    
    var Cell_Number = $.trim($('#txtMobile').val());
    var Email = $.trim($('#txtEmail').val());
    var Pax = $.trim($('#txtPax').val());
    var Name = $.trim($('#txtName').val());
    var Table = $.trim($('#txtTable').val());
    var Time = $.trim($('#txtTime').val());
    var d2 = new Date('1900/01/01 ' + Time);
    var BookingTime = dateFormat(d2, "isoTime", false);
    var d1 = new Date($('#txtDate').val() + " " + BookingTime);
    var BookingDate = dateFormat(d1, "shortDate", false);
    var BookingStatus = $('#hdnStatus').val();
	
    var day = new Date();
    day = dateFormat(day, "shortDate", false);
	
	// This is added for better booking date and time validation when period of booking is over a day 03/07/2013
	var BookingDateTime = dateFormat(BookingDate + " " + BookingTime, "custDate", false);
	var de = dateFormat(day + " " + sessionStorage.DinnerEnd, "custDate", false);
	var ds = dateFormat(day + " " + sessionStorage.DinnerStart, "custDate", false);
	var ls = dateFormat(day + " " + sessionStorage.LunchStart, "custDate", false);
	var le = dateFormat(day + " " + sessionStorage.LunchEnd, "custDate", false);
	var bs = dateFormat(day + " " + sessionStorage.BrkFastStart, "custDate", false);
	var be = dateFormat(day + " " + sessionStorage.BrkFastEnd, "custDate", false);
	//********************************************************************************************
	var chr = new Date().getHours();
    var cmin = new Date().getMinutes();
    var csec = new Date().getSeconds();
   
    if (chr < 10) { chr = '0' + chr } if (cmin < 10) { cmin = '0' + cmin } if (csec < 10) { csec = '0' + csec }
    var vtime = chr + ':' + cmin + ':' + csec;
   // alert(parseInt(d1.getMinutes(),10) - parseInt(cmin,10));
   

    if (Name == "") {
        $('#txtName').attr('placeholder', 'Please enter name');
        $('#txtName').css('border', '1px solid #F51500');
        return false;
    }
    else if (Cell_Number == "" && Email == "") {

        $('#txtMobile').attr('placeholder', 'Please enter Mobile Number or Email');
        $('#txtMobile').css('border', '1px solid #F51500');
        $('#txtEmail').attr('placeholder', 'Please enter Mobile Number or Email');
        $('#txtEmail').css('border', '1px solid #F51500');
        return false;

    }
    else if (Cell_Number != "" && !ValidateMobileNumber(Cell_Number)) {

        $('#txtMobile').val("");
        $('#txtMobile').attr('placeholder', 'Please enter number');
        $('#txtMobile').css('border', '1px solid #F51500');
        return false;
    }

    else if (Email != "" && !ValidateEmail(Email)) {

        $('#txtEmail').val("");
        $('#txtEmail').attr('placeholder', 'Please enter valid email');
        $('#txtEmail').css('border', '1px solid #F51500');
        return false;
    }

    else if (Pax == "") {
        $('#txtPax').attr('placeholder', 'Please enter pax');
        $('#txtPax').css('border', '1px solid #F51500');
        return false;
    }
    else if (Pax != "" && !ValidatePaxNumber(Pax)) {
        $('#txtPax').val("");
        $('#txtPax').attr('placeholder', 'Please enter number');
        $('#txtPax').css('border', '1px solid #F51500');
        return false;
    }
    else if(Time == "")
    {
        $().toastmessage('showWarningToast', "Please select time");
        $('#txtTime').css('border', '1px solid #F51500');

        return false;
    }

    else if (BookingDate == day && parseInt(d1.getHours(), 10) == parseInt(chr,10) && parseInt(cmin, 10) - parseInt(d1.getMinutes(), 10) > 15)
    {
        $().toastmessage('showWarningToast', "Invalid Time <br \> You cannot enter 15 minutes past time.");
        $('#txtTime').css('border', '1px solid #F51500');

        return false;
    }

    else if (BookingDate == day && parseInt(chr, 10) - parseInt(d1.getHours(), 10) > 1) {
        $().toastmessage('showWarningToast', "Invalid Time <br \> You cannot enter 15 minutes past time.");
        $('#txtTime').css('border', '1px solid #F51500');

        return false;
    }

    else if (BookingDate == day && parseInt(chr, 10) - parseInt(d1.getHours(), 10) == 1 && parseInt(d1.getMinutes(), 10) + 60 - parseInt(cmin, 10) > 15) {
        $().toastmessage('showWarningToast', "Invalid Time <br \> You cannot enter 15 minutes past time.");
        $('#txtTime').css('border', '1px solid #F51500');

        return false;
    }
    //else if (BookingTime <= sessionStorage.BrkFastStart || BookingTime >= sessionStorage.BrkFastEnd && BookingTime <= sessionStorage.LunchStart || BookingTime >= sessionStorage.LunchEnd && BookingTime <= sessionStorage.DinnerStart && BookingTime >= sessionStorage.DinnerEnd) {
else if (BookingDateTime <= bs || BookingDateTime >= be && BookingDateTime <= ls || BookingDateTime >= le && BookingDateTime <= ds && BookingDateTime >= de) {
        $().toastmessage('showWarningToast', "Invalid Time <br \> Your time must fall in Outlet's Timings.");
        $('#txtTime').css('border', '1px solid #F51500');
        return false;
    }

    else if (Table == "") {
        $('#txtTable').attr('placeholder', 'Please enter Table Number');
        $('#txtTable').css('border', '1px solid #F51500');
        return false;
    }
    else if (BookingStatus != "Booked" && BookingStatus != "Amended" && sessionStorage.SBookingDetails != null && sessionStorage.SBookingDetails != "") {
	
        var temp = $.parseJSON(sessionStorage.SBookingDetails);
		
		for(var i=0;i<temp[0].BDtls.length;i++) {
		
		// CODE ADDED BY ANUBHAV FOR REPEAT ENTRY VALIDATION
		var TName = temp[0].BDtls[i].CustomerName.split('. ');
			var SName = "";	
			var SCell = "";
			var SEmail = "";
					if(TName[0] == "Mr" || TName[0] == "Ms")
					{
			
                       SName = TName[1];
					}
					else
					{
			
						 SName = temp[0].BDtls[i].CustomerName;
					}
					
					if(temp[0].BDtls[i].Cell_Number == "")
					{
					    SCell = "null";
					}
					else
					{
					   SCell = temp[0].BDtls[i].Cell_Number;
					}
					
					if(temp[0].BDtls[i].EmailId == "")
					{
					    SEmail = "null";
					}
					else
					{
					   SEmail = temp[0].BDtls[i].EmailId;
					}
		    if (Name.toLowerCase() == SName.toLowerCase() && (Cell_Number == SCell || Email.toLowerCase() == SEmail.toLowerCase())) {
			  $().toastmessage('showWarningToast', "Oops! We're sorry but you cannot make a second booking!");
			  return false;
			}
		}
    }

    return true;

}

function DynamicOffers(data) {
    $('#ofrtab').html('');
    var strtext = '';
    for (i = 0; i < data[0].OffDtls.length; i++) {
        if (data[0].OffDtls[i].ValidFromTime != "") {
            strtext = strtext + '<tr><td style=\'width:25%;color:#000000;background-color:#F7B54A\'>' + data[0].OffDtls[i].Title + '</td><td style=\'width:75%;background-color:#FDF6D4\'colspan=2>' + data[0].OffDtls[i].OfferDescription + '</td></tr><tr><td style=\'width:50%;background-color:#FDF6D4\'>Valid On Days : ' + data[0].OffDtls[i].ValidOnWeekDayNames + '</td><td style=\'width:50%;background-color:#FDF6D4\'> Validity : ' + data[0].OffDtls[i].ValidFromTime + ' to ' + data[0].OffDtls[i].ValidToTime + '</td></tr>';
        }
        else {
            strtext = strtext + '<tr><td style=\'width:25%;color:#000000;background-color:#F7B54A\'>' + data[0].OffDtls[i].Title + '</td><td style=\'width:75%;background-color:#FDF6D4\'colspan=2>' + data[0].OffDtls[i].OfferDescription + '</td></tr><tr><td style=\'width:50%;background-color:#FDF6D4\'>Valid On Days : ' + data[0].OffDtls[i].ValidOnWeekDayNames + '</td><td style=\'width:50%;background-color:#FDF6D4\'></td></tr>';
        }
         strtext = strtext + '<tr><td style=\'border:1px solid #000000; width=100%;\' colspan=2></td></tr>';

    }
    $('#ofrtab').append(strtext);
}

function SpecificOffers(data) {
    $('#sofrtab').html('');
    var strtext = '';
    for (i = 0; i < data[0].OffDtls.length; i++) {
        if (data[0].OffDtls[i].ValidFromTime != "") {
            strtext = strtext + '<tr><td style=\'width:25%;color:#000000\'><input name="offer" value="' + data[0].OffDtls[i].COffId + '" type="radio" />' + data[0].OffDtls[i].Title + '</td><td style=\'width:75%\'colspan=2>' + data[0].OffDtls[i].OfferDescription + '</td></tr><tr><td style=\'width:50%\'>Valid On Days : ' + data[0].OffDtls[i].ValidOnWeekDayNames + '</td><td style=\'width:50%\'> Validity : ' + data[0].OffDtls[i].ValidFromTime + ' to ' + data[0].OffDtls[i].ValidToTime + '</td></tr>';

        }
        else {
            strtext = strtext + '<tr><td style=\'width:25%;color:#000000\'><input name="offer" value="' + data[0].OffDtls[i].COffId + '" type="radio" />' + data[0].OffDtls[i].Title + '</td><td style=\'width:75%\'colspan=2>' + data[0].OffDtls[i].OfferDescription + '</td></tr><tr><td style=\'width:50%\'>Valid On Days : ' + data[0].OffDtls[i].ValidOnWeekDayNames + '</td><td style=\'width:50%\'></td></tr>';
        }
          strtext = strtext + '<tr><td style=\'border:1px solid #dddddd; width=100%;\' colspan=2></td></tr>';

    }
    strtext = strtext + '<tr><td><input type="button" class="ckmsgbtn" id="gtofr" onclick="PreBooking(this);" value="Get This Offer" /></td><td><input type="button" class="restbtn" value="Skip Offers" id="skofr" onclick="Booking(this);" /></td></tr>';
    $('#sofrtab').append(strtext);

}

function PreBooking(obj) {

    if ($('input[name=offer]:radio:checked').length == 0) {
        $().toastmessage('showNoticeToast', "Please select an offer.");
    }
    else {
        Booking(obj);
    }

}

function postCheckInOutMsgs() {
    $('#basic-modal-content4').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
    //String.prototype.escapeSpecialChars = function() { return this.replace(/\\/g, "\\"). replace(/\n/g, "\\n"). replace(/\r/g, "\\r"). replace(/\t/g, "\\t"). replace(/\f/g, "\\f"); }
   var myJSON = '{"csid":' + csid + ',"chkisms":"' + escape($('#txtCIsms').val()) + '","chkiemail":"' + escape($('#txtCIeml').val()) + '","chkosms":"' + escape($('#txtCOsms').val()) + '","chkoemail":"' + escape($('#txtCOeml').val()) + '"}';
   $.ajax({
        type: "POST",
        url: API,
        data: { 'd': myJSON, 'tp': 'SCHKIOM' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn2',
		timeout: 20000,
        success: function (data) {
            if (data[0].Success == 1) {
                // alert('Data Posted');
                $('.loading').remove();
                $('.simplemodal-close').click();
                $().toastmessage('showSuccessToast', "CheckIn/Out Messages Updated");
            }
            else {
                $('.loading').remove();
                $().toastmessage('showErrorToast', "Some Error Occured");
            }

        },
		error: function(){
		 $().toastmessage('showErrorToast', "Some Error Occured");
		}
    });
}

function FetchCheckInOutMsgs() {

    var l = '{"csid":' + csid + '}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': l, 'tp': 'GCHKMSG' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn3',
		timeout: 20000,
        success: function (data) {
            if (data[0].Success == 1) {
                $('#txtCIsms').val(unescape(data[0].ChkMsg[0].ChkISms));
                $('#txtCIeml').html(unescape(data[0].ChkMsg[0].ChkIEmail));
                $('#txtCOsms').val(unescape(data[0].ChkMsg[0].ChkOSms));
                $('#txtCOeml').html(unescape(data[0].ChkMsg[0].ChkOEmail));
				 $('#txtCIeml').jqte();
				 $('#txtCOeml').jqte();
            }
        },
		error: function(){
		 $().toastmessage('showErrorToast', "Some Error Occured");
		}
    });
}

function BadgerCallBack() {
    //alert('33 New Booking request');
}

function RefreshDynamicDivs() {
    $('#content').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
    var dispdate = dateFormat(new Date(), "dispDate", false);
     $('#txtfilterdate').val(dispdate);
     $('#tablefilter').val('');
    var i = '{"csid":' + csid + ',"dt":"' + today + '","st":"","dit":""}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': i, 'tp': 'GBT' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn12',
        timeout: 20000,
        success: function (data) {

            if (data[0].Success == 1) {
                sessionStorage.BookingDetails = JSON.stringify(data);

                //alert(temp[0].BDtls[0].CBId);
                dynamicDivs(data);
				RefreshCounter();
                var chr = new Date().getHours();
                var cmin = new Date().getMinutes();
                var csec = new Date().getSeconds();
                if (chr < 10) { chr = '0' + chr } if (cmin < 10) { cmin = '0' + cmin } if (csec < 10) { csec = '0' + csec }
                var ctime = chr + ':' + cmin + ':' + csec;
                // alert(sessionStorage.LunchStart);
                // alert(sessionStorage.BrkFastEnd);
                // alert(ctime);
                if (ctime > sessionStorage.BrkFastStart && ctime < sessionStorage.BrkFastEnd) {
                    $("#tabs").tabs({ active: 0 });
                }
                else if (ctime > sessionStorage.LunchStart && ctime < sessionStorage.LunchEnd) {
                    $("#tabs").tabs({ active: 1 });
                }
                else if (ctime > sessionStorage.DinnerStart && ctime < sessionStorage.DinnerEnd) {
                    // alert('show dinner tab');
                    $("#tabs").tabs({ active: 2 });
                }
                else {
                    $("#tabs").tabs({ active: 3 });
                }
                $('.loading').remove();
			
            }
            else {

                $('.loading').remove();
                $('#outer_tableb').html('<tr><td>No Bookings Found</td></tr>');
                $('#outer_tablel').html('<tr><td>No Bookings Found</td></tr>');
                $('#outer_tabled').html('<tr><td>No Bookings Found</td></tr>');
                $('#outer_tabledi').html('<tr><td>No Bookings Found</td></tr>');
                sessionStorage.BookingDetails = null;
                // $().toastmessage('showErrorToast', "Some Error Occured! Please try again");
            }
        },

        error: function () {
            $('.loading').remove();
            $('#outer_tableb').html('<tr><td>No Bookings Found</td></tr>');
            $('#outer_tablel').html('<tr><td>No Bookings Found</td></tr>');
            $('#outer_tabled').html('<tr><td>No Bookings Found</td></tr>');
            $('#outer_tabledi').html('<tr><td>No Bookings Found</td></tr>');
            $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to refresh the tabs.");
        }
    });
}

function ThirdPartyBookings(data) {
    $('#outer_tablen').html('');

    for (i = 0; i < data[0].BDtls.length; i++) {

        var strText = "";
        strText = strText + " <table id='inner_table'  style='border:1px solid #000000; border-radius:5px; width:100%;' cellpadding='1'>";
        strText = strText + " <tr id= \'detailrow" + i + "\' >";
        strText = strText + " <td style='font-size:1.1em; color:#104E7F; font-family:Calibri; width:50%; text-align:left;'>" + data[0].BDtls[i].CustomerName + "</td>";
        strText = strText + " <td style='width:50%;'>";
        strText = strText + " <div style='float:left;text-align:right;width:40%;'>Sex:</div>";
        strText = strText + " <div style='text-align:left;float:left;width:60%; color:#104E7F; font-family:Cambria;font-size:1.1em;'>" + data[0].BDtls[i].Gender + "</div>";
        strText = strText + " </td>";
        strText = strText + " </tr>";
        strText = strText + " <tr>";
        if (data[0].BDtls[i].Cell_Number != "" && data[0].BDtls[i].Cell_Number != null) {
            strText = strText + " <td style='font-size:1.1em; font-family:Calibri; width:50%; text-align:left;'>" + data[0].BDtls[i].CountryCallingCode + '-' + data[0].BDtls[i].Cell_Number + "</td>";
        }
        else {
            strText = strText + " <td style='font-size:1.1em; font-family:Calibri; width:50%; text-align:left;'>" + data[0].BDtls[i].Cell_Number + "</td>";

        }
        strText = strText + " <td style='width:50%;'>";
        strText = strText + " <div style='float:left;text-align:right;width:40%;'>Pax:</div>";
        strText = strText + " <div style='text-align:left;float:left;width:60%; color:#104E7F; font-family:Cambria;font-size:1.1em;'>" + data[0].BDtls[i].Pax + "</div>";
        strText = strText + " </td>";
        strText = strText + " </tr>";
        strText = strText + " <tr>";
        strText = strText + " <td style='font-size:1.1em; font-family:Calibri; width:50%; text-align:left;'>" + data[0].BDtls[i].EmailId + "</td>";
        strText = strText + " <td style='width:50%;'>";
        strText = strText + " <div style='float:left;text-align:right;width:40%;'>Date:</div>";
        strText = strText + " <div style='text-align:left;float:left;width:60%; color:#104E7F; font-family:Cambria;font-size:1.1em;'>" + data[0].BDtls[i].DisplayDate + "</div>";
        strText = strText + " </td>";
        strText = strText + " </tr>";

        strText = strText + " <tr>";
        strText = strText + " <td style='font-size:1.1em;color:#104E7F; font-family:Calibri; width:50%; text-align:left;'>FROM " + data[0].BDtls[i].BookingSource + "</td>"
        strText = strText + " <td style='width:50%;'>";
        strText = strText + " <div style='float:left;text-align:right;width:40%;'>Time:</div>";
        strText = strText + " <div style='text-align:left;float:left;width:60%; color:#104E7F; font-family:Cambria;font-size:1.1em;'>" + data[0].BDtls[i].DisplayTime + "</div>";
        strText = strText + " </td>";
        strText = strText + " </tr>";
        strText = strText + " <tr>";
        strText = strText + " <td style='font-size:1.1em; font-family:Calibri; width:50%; text-align:left;'><span style='color:#104E7F'>Preferences : </span>" + data[0].BDtls[i].SeatingPrefNames + "</td>";
        strText = strText + " <td style='width:50%;'>";
        strText = strText + " <div style='float:left;text-align:right;width:40%;'>Table:</div>";
        strText = strText + " <div style='text-align:left;float:left;width:60%; color:#104E7F; font-family:Cambria;font-size:1.1em;'>" + data[0].BDtls[i].TableNos + "</div>";
        strText = strText + " </td>";
        strText = strText + " </tr>";
        strText = strText + " <tr>";
        strText = strText + " <td colspan='3' style='width:100%'><div style='font-size:1.1em; color:#104E7F; font-family:Calibri;float:left;text-align:left;'>Note: </div><div style='font-size:1.1em; font-family:Calibri;float:left;text-align:left;'>" + unescape(data[0].BDtls[i].RequestNote) + "</div></td>";
        strText = strText + " </tr>";
        strText = strText + " </table>";
        strText = strText + " <br />";

        $('#outer_tablen').append(strText);

    }

}

function notify() {
    var PostData = '{"csid":' + csid + ',"sysdt":"' + timestamp + '"}&tp=GBN';

    $.ajax({
        type: 'GET',
        url: API + '?d=' + PostData,
        success: function (data) {
            if (data[0].Success == 1) {
                timestamp = data[0].LastTimeExecutedOn;

                $('#noti').badger(data[0].NCnt);  // BADGER IMPLEMENTATION
                //alert('1');
                ThirdPartyBookings(data);
                setTimeout('notify()', 30000);
            }
            else {
                //alert('2');
				$('#outer_tablen').html('<td>No New Bookings Available</td>');
                setTimeout('notify()', 30000);
            }
        }, dataType: 'jsonp',
        error: function (xhr, ajaxOptions, thrownError) {
           // alert('3')
            setTimeout('notify()', 45000);
        }
    });
}


function ReleaseTable(bid) {
    today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    var hours = today.getHours();
    var minutes = today.getMinutes();
    var seconds = today.getSeconds();

    if (dd < 10) { dd = '0' + dd } if (mm < 10) { mm = '0' + mm }
    today = mm + '/' + dd + '/' + yyyy;

    if (hours < 10) { hours = '0' + hours } if (minutes < 10) { minutes = '0' + minutes } if (seconds < 10) { seconds = '0' + seconds }
    currentts = today + " " + hours + ":" + minutes + ":" + seconds;

    $.confirm({
        'title': 'Release Confirmation',
        'message': 'You are about to release this table. All Tables associated with this will also be released.<br />Do you want to Continue?',
        'buttons': {
            'Yes': {
                'class': 'blue',
                'action': function () {
                    $('#basic-modal-content').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
                    var i = '{"csid":' + csid + ',"cbid":' + bid + ',"chkodt":"'+ currentts +'"}';
                    $.ajax({
                        type: "POST",
                        url: API,
                        data: { 'd': i, 'tp': 'SRELTBL' },
                        contentType: "application/json; charset=utf-8",
                        dataType: 'jsonp',
                        jsonpCallback: 'jsonpCBDTFn14',
						//timeout: 20000,
                        success: function (data) {

                            if (data[0].Success == 1) {
                                $('.loading').remove();
                                $('.simplemodal-close').click();
                                $().toastmessage('showSuccessToast', "Table Release Successfully");
                                RefreshDynamicDivs();
                                RefreshCounter();
                            }
                            else {
                                $('.simplemodal-close').click();
                                $('.loading').remove();
                                $().toastmessage('showErrorToast', "Some Error Occured! Please try again");
                            }

                        },
						error: function (){
								$('.loading').remove();
                                $().toastmessage('showErrorToast', "Timed Out");
						}
                    });
                }
            },
            'No': {
                'class': 'gray',
                'action': function () { } // Nothing to do in this case. You can as well omit the action property.
            }
        }
    });

}



function GetAvailableTables() {
    $('#content').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
 //   setTimeout(function () { $(".loading").remove(); $().toastmessage('showErrorToast', "Request Timed Out."); }, 15000)
    var tiarr = new Array();
    var tnarr = new Array();
    /******* TEMPORARY BUG FIX FOR TABLE ARRAYS *********/
    while (tbnarr.length > 0) {
        tbnarr.pop();
    }
    while (tbiarr.length > 0) {
        tbiarr.pop();
    }

    if ($("#hdnStatus").val() != "") {
         tiarr = sessionStorage.tid.split(",");
         tnarr = sessionStorage.tnm.split(",");
        // alert(tiarr);
      //  alert(tnarr);
      //  alert(tiarr);
        for (var j = 0; j < tnarr.length; j++) {
          
                tbnarr.push(tnarr[j]);
        }
        for (var j = 0; j < tiarr.length; j++) {
          
                tbiarr.push(tiarr[j]);
        }
    }

/******* TEMPORARY BUG FIX FOR TABLE ARRAYS ENDS  *********/




    var BookingDate = $('#txtDate').val();
    var Time = $.trim($('#txtTime').val());
    var d2 = new Date('1900/01/01 ' + Time);
    var BookingTime = dateFormat(d2, "isoTime", false);

    var Pref = $("#drpPreference").multiselect("getChecked").map(function () {
        return this.value;
    }).get();
    //*************************AJAX CALL FOR GET TABLES ACCORDING TO PREFERENCES*************************************
    var p = '{"csid":' + csid + ',"prefs":"' + Pref + '","bdt":"' + BookingDate + '","bt":"' + BookingTime + '"}';

    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': p, 'tp': 'GPREFTBL' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn16',
		timeout: 20000,
        success: function (data) {

            if (data[0].Success == 1) {
                sessionStorage.Avl = JSON.stringify(data);
                $(".loading").remove();
                DynamicAvailableTables();
            }

            else {
                $(".loading").remove();
                $('#tabavl').html('<tr><td>Sorry No Tables Found Matching Your Preferences</td></tr>');
                $('#basic-modal-content9').modal();
            }
        },

        error: function () {
            $(".loading").remove();
            $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to get available tables.");
        }
    });
}
var caps = 0;

function DynamicAvailableTables() {

    caps = parseInt("0", 10);
    var temp = $.parseJSON(sessionStorage.Avl);
    $('#paxcal').html('');
    $('#tabavl').html('');
    var str = "";
    //  alert(tbnarr + "--" + tbiarr);
    str = str + "<tr>";
    for (var i = 0; i < temp[0].PrefTblDtls.length; i++) {
        str = str + "<td style='border:1px solid'>";
        str = str + "<div align='center'><span>" + temp[0].PrefTblDtls[i].tnm +"(" + temp[0].PrefTblDtls[i].scap + ")</span><br />";
		var scap = "";
		var scaps = new Array();
		scaps = ["1","2","3","4","5","6","8","10","12","20"];
		if($.inArray(temp[0].PrefTblDtls[i].scap,scaps) != -1)
		{
		    scap = temp[0].PrefTblDtls[i].scap;			
		}
		else
		{
		 
			scap = "default";		
		}
        if (temp[0].PrefTblDtls[i].ias == "A") {

            str = str + "<img src=\"images/" + scap + ".png\" onclick=\"changeIcon(this," + i + ");\" /></a></div>";
        }
        else if (temp[0].PrefTblDtls[i].ias == "NA") {
            str = str + "<img src=\"images/" + scap + "_na.png\" /></a></div>";
        }
        else {
            if (jQuery.inArray(temp[0].PrefTblDtls[i].tblno, tbiarr) != -1) {
                caps = parseInt(caps, 10) + parseInt(temp[0].PrefTblDtls[i].mcap, 10);
			    str = str + "<img src=\"images/" + scap + "_marked.png\" onclick=\"changeIcon(this," + i + ");\" /></a></div>";
			 
            }
            
            else {
                str = str + "<img src=\"images/" + scap + "_booked.png\" /></a>";
            }
        }
       // str = str + "<div><span>Seating Capacity: " + temp[0].PrefTblDtls[i].scap + "</span>";
        str = str + "<div align='center'>Max. Pax: " + temp[0].PrefTblDtls[i].mcap + "</div>";
       str = str + "</td>";
        if (i != 0 && (i + 1) % 6 == 0) {
		 
          str = str + "</tr>";
        }
    }
  $('#tabavl').append(str);
 
  if (caps == 0) {
      var pcal = $('#txtPax').val() + " people need to be allocated";
      $('#paxcal').html(pcal);
  }
  else {
     // alert(caps);
      if (caps < parseInt($('#txtPax').val(), 10)) {
          var pcal = "Pax : " + $('#txtPax').val() + " | Seats Allocated : " + caps;
      }
      else {
          var pcal = "Pax : " + $('#txtPax').val() + " | Seats Allocated : " + caps;
          $().toastmessage('showNoticeToast', "Everybody is settled.");
      }
      $('#paxcal').html(pcal);
  }
    $('#basic-modal-content9').modal();
}

function DynamicReleaseTable(data) {
    $('#tabrel').html('');
    var str = "";
    var cnt = 0;
    str = str + "<tr>";
    for (var i = 0; i < data[0].RelTbls.length; i++) {
        var Tabname = new Array();
        var Tabid = new Array();
        var Tabcap = new Array();
        Tabname = data[0].RelTbls[i].tnm.split(",");
        Tabid = data[0].RelTbls[i].tid.split(",");
        Tabcap = data[0].RelTbls[i].tcap.split(",");
        today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();
        var hours = today.getHours();
        var minutes = today.getMinutes();
        var seconds = today.getSeconds();
        var flag = false;
        if (dd < 10) { dd = '0' + dd } if (mm < 10) { mm = '0' + mm }
        today = yyyy + '-' + mm + '-' + dd;
        if (hours < 10) { hours = '0' + hours } if (minutes < 10) { minutes = '0' + minutes } if (seconds < 10) { seconds = '0' + seconds }
        currentts = today + " " + hours + ":" + minutes + ":" + seconds;
        //var col = new Array();
        //col.push('#473942', '#b8a6b1', '#b1b8a6');
        for (var k = 0; k < Tabname.length; k++) {
            var t1 = data[0].RelTbls[i].chin.replace(/\-/g, '/');
            var t2 = new Date(t1);
            t2.setMinutes(t2.getMinutes() + parseInt(data[0].RelTbls[i].mtat, 10));
            var lt = dateFormat(t2, "custDate", false);
            if (currentts > lt) {
                str = str + "<td class='flash' style='background:#b8a6b1'>";

                setInterval(function () {
                    $('.flash').effect("highlight", {}, 1000)
                }, 500);
               
            }
            else {
                str = str + "<td>";
            }
            str = str + "<div align='center'><span>" + Tabname[k] + "</span><br />";
            str = str + "<a align='center' href=\"javascript:void(0);\" onclick=\"ReleaseTable(" + data[0].RelTbls[i].cbid + ");\">";
//*******************************
            var scap = "";
            var scaps = new Array();
            scaps = ["1", "2", "3", "4", "5", "6", "8", "10", "12", "20"];
            if ($.inArray(Tabcap[k], scaps) != -1) {
                str = str + "<img src=\"images/" + Tabcap[k] + "_booked.png\" /></a></div>";
            }
            else {

                str = str + "<img src=\"images/default_booked.png\" /></a></div>";
            }
//********************************

            
            str = str + "<div align='center'>" + data[0].RelTbls[i].cnm + "</div>";
            str = str + "</td>";
           
            if (cnt != 0 && (cnt + 1) % 6 == 0) {             
                str = str + "</tr>";
            }
            cnt++;
        }
        
    }
   
    $('#tabrel').append(str);


}

function changeIcon(obj, i) {

        var temp = $.parseJSON(sessionStorage.Avl);
        var tbl = $(obj).attr('src');

        //  alert($("#hdnStatus").val());
        var tempi = new Array();
        var tempn = new Array();

        tempi = tbiarr;
        tempn = tbnarr;
		var scap = "";
		var scaps = new Array();
		scaps = ["1","2","3","4","5","6","8","10","12","20"];
		if($.inArray(temp[0].PrefTblDtls[i].scap,scaps) != -1)
		{
		    scap = temp[0].PrefTblDtls[i].scap;			
		}
		else
		{
		 
			scap = "default";		
		}
        if ($("#hdnStatus").val() != "") {

            //   alert("in edit");
            // alert(tbnarr + "--" + tbiarr);
			
            if (tbl == "images/" + scap + "_marked.png") {
                $(obj).attr("src", "images/" + scap + ".png");
                caps = caps - parseInt(temp[0].PrefTblDtls[i].mcap, 10);

                tempn.pop(temp[0].PrefTblDtls[i].tnm);
                tempi.pop(temp[0].PrefTblDtls[i].tblno);
                if (caps < parseInt($('#txtPax').val(), 10)) {
                    var pcal = "Pax : " + $('#txtPax').val() + " | Seats Allocated : " + caps;
                }
                else {
                    var pcal = "Pax : " + $('#txtPax').val() + " | Seats Allocated : " + caps;
                    $().toastmessage('showNoticeToast', "Everybody is settled.");
                }
                $('#paxcal').html(pcal);
            }
            else {
             if (caps < parseInt($('#txtPax').val(), 10)) {
                $(obj).attr("src", "images/" + scap + "_marked.png");
                caps = caps + parseInt(temp[0].PrefTblDtls[i].mcap, 10);
              
                    tempn.push(temp[0].PrefTblDtls[i].tnm + " ");
                    tempi.push(temp[0].PrefTblDtls[i].tblno);

                if (caps < parseInt($('#txtPax').val(), 10)) {
                    var pcal = "Pax : " + $('#txtPax').val() + " | Seats Allocated : " + caps;
                }
                else {
                    var pcal = "Pax : " + $('#txtPax').val() + " | Seats Allocated : " + caps;
                    $().toastmessage('showNoticeToast', "Everybody is settled.");
                }
                $('#paxcal').html(pcal);
            }
            else {
                $().toastmessage('showNoticeToast', "Everybody is settled.");
            }
            }
        }
        else {
            // alert("in normal");
            if (tbl == "images/" + scap + "_marked.png") {
                $(obj).attr("src", "images/" + scap + ".png");
                caps = caps - parseInt(temp[0].PrefTblDtls[i].mcap, 10);
                tbnarr.pop(temp[0].PrefTblDtls[i].tnm);
                tbiarr.pop(temp[0].PrefTblDtls[i].tblno);
                if (caps < parseInt($('#txtPax').val(), 10)) {
                    var pcal = "Pax : " + $('#txtPax').val() + " | Seats  Allocated : " + caps;
                }
                else {
                    var pcal = "Pax : " + $('#txtPax').val() + " | Seats  Allocated : " + caps;
                    $().toastmessage('showNoticeToast', "Everybody is settled.");
                }
                $('#paxcal').html(pcal);
            }
            else {
              if (caps < parseInt($('#txtPax').val(), 10)) {
                $(obj).attr("src", "images/" + scap + "_marked.png");
                caps = caps + parseInt(temp[0].PrefTblDtls[i].mcap, 10);
                tbnarr.push(temp[0].PrefTblDtls[i].tnm + " ");
                tbiarr.push(temp[0].PrefTblDtls[i].tblno);
                if (caps < parseInt($('#txtPax').val(), 10)) {
                    var pcal = "Pax : " + $('#txtPax').val() + " | Seats  Allocated : " + caps;
                }
                else {
                    var pcal = "Pax : " + $('#txtPax').val() + " | Seats  Allocated : " + caps;
                    $().toastmessage('showNoticeToast', "Everybody is settled.");
                }
                $('#paxcal').html(pcal);
            }
            else {
                $().toastmessage('showNoticeToast', "Everybody is settled.");
            }
            }
        }
   
}

function AddTable() {

    if (caps >= parseInt($('#txtPax').val(), 10)) {
     if (sessionStorage.tid == "" && $('#hdnStatus').val() != "") {

            tbiarr.shift();
            tbnarr.shift();
        }
        $('.simplemodal-close').click();
        $("#txtTable").val(tbnarr);
        $("#hdnTno").val(tbiarr);

        // if ($("#hdnStatus").val() == "") 
        //  {
        while (tbnarr.length > 0) {
            tbnarr.pop();
        }
        while (tbiarr.length > 0) {
            tbiarr.pop();
        }

        //  }
        caps = 0;
    }
    else {
        $().toastmessage('showErrorToast', "Pax size is greater than tables capacity");
    }
 
}

function TablesToRelease() {
    //*************************AJAX CALL FOR FETCHING CHECKED IN TABLES*************************************
    $('#basic-modal-content').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
    var o = '{"csid":' + csid + ',"bdt":"' + today + '"}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': o, 'tp': 'GRELTBL' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn15',
		timeout: 20000,
        success: function (data) {

            if (data[0].Success == 1) {

                DynamicReleaseTable(data);
                $('.loading').remove();
            }
            else {
                $('.loading').remove();
                $('#tabrel').html('<tr><td><h3>None of the tables are checked-in.</h3></td></tr>');
            }
        },

        error: function () {
            $('.loading').remove();
            $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to retreive checked-in tables.");
        }
    });
}



function logout() {
    sessionStorage.CSID = "";
    window.location.href = "index.html";
}

function SearchBookings() {
    $('#content').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
    var date = $('#txtfilterdate').val();
    var dispdate = dateFormat(new Date(), "dispDate", false);
    var text = $('#tablefilter').val();
    var i = '{"csid":' + csid + ',"dt":"' + date + '","st":"' + text + '","dit":""}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': i, 'tp': 'GBT' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn5',
		timeout: 20000,
        success: function (data) {
          
            if (data[0].Success == 1) {
                 sessionStorage.BookingDetails = JSON.stringify(data);
                 
                //alert(temp[0].BDtls[0].CBId);
                dynamicDivs(data);
                if (data[0].BDtls[0].CheckedIn == "Yes") {
                    $("#tabs").tabs({ active: 3 });
                }
                else if (data[0].BDtls[0].Category == "Breakfast") {
                    $("#tabs").tabs({ active: 0 });
                }
                else if (data[0].BDtls[0].Category == "Lunch") {
                    $("#tabs").tabs({ active: 1 });
                }
                else if (data[0].BDtls[0].Category == "Dinner") {
                    $("#tabs").tabs({ active: 2 });
                }
               // var date = $('#txtfilterdate').val(dispdate);
               // var text = $('#tablefilter').val('');
                $('.loading').remove();
            }
            else {
               // var date = $('#txtfilterdate').val(dispdate);
               // var text = $('#tablefilter').val('');
                $('#outer_tableb').html('<tr><td>No Bookings Found</td></tr>');
                $('#outer_tablel').html('<tr><td>No Bookings Found</td></tr>');
                $('#outer_tabled').html('<tr><td>No Bookings Found</td></tr>');
                $('#outer_tabledi').html('<tr><td>No Bookings Found</td></tr>');
                $('.loading').remove();
            }
        },

        error: function () {

            $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to search the bookings.");
        }
    });
}

function RefreshCounter() {
    var j = '{"csid":' + csid + ',"dt":"' + today + '"}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': j, 'tp': 'GCHK' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn4',
		//timeout: 20000,
        success: function (data) {
            if (data[0].TotChk != "" && data[0].TotChk != null && data[0].TotPax != "" && data[0].TotPax != null) {

                // sessionStorage.TotalCheckins = data[0].TotChk;
                // sessionStorage.TotalPax = data[0].TotPax;

                $("#txtTotalCheckIn").val(data[0].TotChk);
                $("#txtTotalPax").val(data[0].TotPax);
            }
        },

        error: function () {

         //   $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to get the Total Checkins and Pax.");
        }
    });
}


function SetTime(val) {
    $('#txtTime').val(val);
    $('#txtTime').focus();
    $('.simplemodal-close').click();
    $('#txtTable').val('');
    $('#hdnTno').val('');
    sessionStorage.tid = "";
    sessionStorage.tnm = "";
	GetSBookings();      // This function Gets Specific Booking for lunch,dinner,brunch
}


function FlashReleaseButton(data) {
    today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    var hours = today.getHours();
    var minutes = today.getMinutes();
    var seconds = today.getSeconds();
    var flag = false;
    if (dd < 10) { dd = '0' + dd } if (mm < 10) { mm = '0' + mm }
    today = yyyy + '-' + mm + '-' + dd;
    if (hours < 10) { hours = '0' + hours } if (minutes < 10) { minutes = '0' + minutes } if (seconds < 10) { seconds = '0' + seconds }
    currentts = today + " " + hours + ":" + minutes + ":" + seconds;

    for (var i = 0; i < data[0].RelTbls.length; i++) {
        var t1 = data[0].RelTbls[i].chin.replace(/\-/g, '/');
        var t2 = new Date(t1);
        t2.setMinutes(t2.getMinutes() + parseInt(data[0].RelTbls[i].mtat, 10));
        var lt = dateFormat(t2, "custDate", false);
        if (currentts > lt) {

           var timer = setInterval(function () {
                $('.flrl').effect("highlight", {}, 1000)
            }, 1000);
            clearTimeout(stm);
            var stm = setTimeout(function () { clearInterval(timer); }, 5000);
        }
        else {
            $('.flrl').stop();
        }

    }
}


function FetchProfile(i) {
    $('#basic-modal-content12').modal();
}


function PrintWindow() {
    var url = "printscreen.html";
    var windowName = "Bookings";
    window.open(url, windowName, "width=700,height=500,scrollbars=yes");

    event.preventDefault();
}

function GetSBookings()
{
    var Time = $.trim($('#txtTime').val());
    var d2 = new Date('1900/01/01 ' + Time);
    var BookingTime = dateFormat(d2, "isoTime", false);
    //********** Below call is to fetch profiles according to time***********
   
    if (BookingTime >= sessionStorage.BrkFastStart && BookingTime <= sessionStorage.BrkFastEnd) {
        var a = '{"csid": ' + csid + ',"dt":"' + today + '","st":"","dit":"b"}';
    }
    else if (BookingTime >= sessionStorage.LunchStart && BookingTime <= sessionStorage.LunchEnd) {
        var a = '{"csid": ' + csid + ',"dt":"' + today + '","st":"","dit":"l"}';
    }
    else if (BookingTime >= sessionStorage.DinnerStart && BookingTime <= sessionStorage.DinnerEnd) {

        var a = '{"csid": ' + csid + ',"dt":"' + today + '","st":"","dit":"d"}';
    }
    else {
        var a = '{"csid": ' + csid + ',"dt":"' + today + '","st":"","dit":""}';
    }

    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': a, 'tp': 'GBT' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn18',
        timeout: 20000,
        success: function (data) {

            if (data[0].Success == 1) {
                sessionStorage.SBookingDetails = JSON.stringify(data);
            }
			else
			{
				sessionStorage.SBookingDetails = "";
			}

        },

        error: function () {
		     sessionStorage.SBookingDetails = "";
            // $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to get the Bookings.");
        }
    });
	
	//**********************************************************************************
}
/*
function GetAllOffers (){
 $('#basic-modal-content5').append('<div class="loading"><img src="img/loading.gif" alt="Loading..." /></div>');
//*************************AJAX CALL FOR OFFERS*************************************
    var m = '{"csid":' + csid + '}';
    $.ajax({
        type: "POST",
        url: API,
        data: { 'd': m, 'tp': 'GAOFF' },
        contentType: "application/json; charset=utf-8",
        dataType: 'jsonp',
        jsonpCallback: 'jsonpCBDTFn1',
		timeout: 20000,
        success: function (data) {

            if (data[0].Success == 1) {

                DynamicOffers(data);
				 $('.loading').remove();
            }
            else {
                $('#ofrtab').html('<tr><td>No Offers Found</td></tr>');
				 $('.loading').remove();
            }


        },

        error: function () {
			$('#ofrtab').html('<tr><td>No Offers Found</td></tr>');
			 $('.loading').remove();
            $().toastmessage('showErrorToast', "Sorry! Due To Technical Reasons. We are not able to get your offers.");
        }
    });

}
*/


