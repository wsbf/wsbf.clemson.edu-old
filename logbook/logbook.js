/** The tour de force in JavaScript **/
/** **/

/** somewhat global? **/
var automation = true;
var starttime = '';
var showid = -1; //individual show
var numdjs = 1; //for multiple djs in signon
var djnames = "-1, ";
var gotonewdj = 0; 

var playlistTimer = 0;
var showTimer = 0;

/** http://www.geekpedia.com/tutorial138_Get-key-press-event-using-JavaScript.html **/
document.onkeyup = KeyCheck;       
function KeyCheck(e) {
	//kludge to allow firefox support. ie and chrome think event.keyCode is fine
	var keyid = (window.event) ? event.keyCode : e.keyCode;
	
	if(keyid == 13)
		enterActions();
}
function enterActions() {
	//window.alert($('#opt_submit').parent().html());
	//$('#opt_submit')
	
	//kiddies = $("#tbp .fsckit")[0];
	addOptional($('#opt_submit'));
}

/** 
"<td><select id='rot'><option value='-1'>&nbsp;</option><option value='N'>New</option>" + 
"<option value='H'>Heavy</option><option value=''>Medium</option><option value='L'>Light</option>" + 
"<option value='O'>Optional</option></select></td>" + 
**/

$.currentsong = ""; 
$.blankentry = "<tr class='row_optional'><td class='ano'>" + 
	"<input type='text' id='albumno' size='4' maxlength='4' onblur=\"albumAuto(this)\" /></td>" + 
	"<td><input type='text' id='trackno' size='2' maxlength='2' onblur=\"trackAuto(this)\" /></td>" + 
	"<td class='rot'></td>" + 
	"<td class='trk'><input type='text' id='track' class='std' /></td><td class='art'><input type='text' id='artist' class='std'/></td>" + 
	"<td class='alb'><input type='text' id='album' class='std'/></td><td class='lbl'><input type='text' id='label' class='std'/></td>" + 
	"<td><img src='add32.png' id='opt_submit' onclick=\"addOptional(this)\" /></td></tr>";

function urlencode( str ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir
    // %          note: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    // *     example 1: urlencode('Kevin van Zonneveld!');
    // *     returns 1: 'Kevin+van+Zonneveld%21'
    // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
    // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
    // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
    // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'
                             
    var histogram = {}, tmp_arr = [];
    var ret = str.toString();
    
    var replacer = function(search, replace, str) {
        var tmp_arr = [];
        tmp_arr = str.split(search);
        return tmp_arr.join(replace);
    };
    
    // The histogram is identical to the one in urldecode.
    histogram["'"]   = '%27';
    histogram['(']   = '%28';
    histogram[')']   = '%29';
    histogram['*']   = '%2A';
    histogram['~']   = '%7E';
    histogram['!']   = '%21';
    histogram['%20'] = '+';
    
    // Begin with encodeURIComponent, which most resembles PHP's encoding functions
    ret = encodeURIComponent(ret);
    
    for (search in histogram) {
        replace = histogram[search];
        ret = replacer(search, replace, ret); // Custom replace. No regexing
    }
    
    // Uppercase for full PHP compatibility
    return ret.replace(/(\%([a-z0-9]{2}))/g, function(full, m1, m2) {
        return "%"+m2.toUpperCase();
    });
    
    return ret;
}

function checkInputBoxes() {
	
	if(automation) {
		//window.alert("automation");
		$("#tbp input, #tbp select").attr("disabled", true);
	}
	else {
		//window.alert("not automation");
		$("#tbp input, #tbp select").removeAttr("disabled");
	}
	return;
}


/** THIS has onload code at the top, and then all the dialog box stuff **/
$(function() {
	/** pageload code here **/
	refreshCurrShow();
	addBlankOptional();
	refreshLog(true, false);
	$("input:text:visible:first").focus();
	
	var djname_in1 = $("#djname_in1");
	var show = $("#show_select");
	//var allFields = $([]).add(djname_in1).add(show);
	var tips = $("#validateTips");

	function updateTips(t) {
		tips.text(t).effect("highlight",{},1500);
	}
	
	$("#dialog_signon").dialog({
		bgiframe: true,
		autoOpen: false,
		height: 300,
		width: 500,
		modal: true,
		open: function(event, ui) {
			
			$.get('logging_getdjs.php', function(data){
				//if(!$('#dj_select').empty())
				$('#dj_select').append(data);
			});
			$.get('logging_getshows.php', function(data){
				$('#show_select').append(data);
			});
			
		},
		close: function() {
			//window.alert("closing signon...");
		},
		buttons: {
			'Sign On': function() {
				
				//window.alert( urlencode(djname_in1.val()) + show.val());
				//allFields.removeClass('ui-state-error');
				//window.alert("djnames: " + djnames);
				
				if(gotonewdj == 1) {
					$.get("logging_logout.php", { id: showid }, 
						function(data){ 
						} );
				}
				
				
				/** Error checking for at least 1 valid dj name! **/
				var djnamearr = djnames.split(", ");
				var valid = 0;
				/** this strange loop allows for the extra ', ' **/
				for (var itr = 0; itr < djnamearr.length - 1; itr++) {
					//window.alert("all names: " + djnamearr[itr]);
					if(djnamearr[itr] != "-1")
						valid = 1;
					
				}
				
				/** do magical signon shit here **/
			/** extra parameter to pass to logon 
			showname: urlencode('')
			be**/
				if(valid == 1) {
					
					$.get("logging_logon.php", 
						{
							djname: urlencode(djnames), 
							showsid: urlencode(show.val())
						
						}, 
						function(data){ 
							if (data > -1)
								showid = data;
							refreshCurrShow();
							checkInputBoxes();
							$("input:text:visible:first").focus();
						} );
				
					showid = -1;
					automation = false;
				}
					/** re-default the dj and show modules **/
					$(".djname").val(-1);
					$("#show_select").val(-1);
					$(this).dialog('close');
				
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
	
	$("#dialog_signoff").dialog( {
		bgiframe: true,
		autoOpen: false,
		height: 150,
		modal: true,
		buttons: {
			'Sign Off': function() {
				
				//allFields.removeClass('ui-state-error');
				
				//do magical signoff shit here
				$.get("logging_logout.php", { id: showid }, 
					function(data){ 
						//if (data != showid)
							//window.alert(data);
						showid = -1;
						refreshCurrShow(); 
					} );
				showid = -1;
				automation = true;
				$(this).dialog('close');
				
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			//window.alert("closing signoff...");
		}
	} );
	
	$("#dialog_fromonair").dialog( {
		bgiframe: true,
		autoOpen: false,
		height: 250,
		modal: true,
		buttons: {
			'Change DJ': function() {
				gotonewdj = 1;
				
				$(this).dialog('close');
				$('#dialog_signon').dialog('open');
				
			},
			'Sign Off': function () {
				gotonewdj = 0;
				$(this).dialog('close');
				$('#dialog_signoff').dialog('open');
			},
			
			Cancel: function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			//window.alert("closing fromonair...");
		}
	} );

	$('#change-dj').click(function() {
		if(automation)
			$('#dialog_signon').dialog('open');
		else
			$('#dialog_fromonair').dialog('open');
	})
	.hover(
		function(){ 
			$(this).addClass("ui-state-hover"); 
		},
		function(){ 
			$(this).removeClass("ui-state-hover"); 
		}
	).mousedown(function(){
		$(this).addClass("ui-state-active"); 
	})
	.mouseup(function(){
			$(this).removeClass("ui-state-active");
	});
	
	
		
});



function generateDJList() {
	djnames = "";
	
	$(".djname").each(function (i) { 
		djnames += this.value + ", ";
	});
	//window.alert(djnames);
}

function addExtraDJ() {
	
	//window.alert($(".djname").length + $(".djname:last").clone());
	
	if($(".djname").length < 3) {
		$(".djname:last").after( $(".djname:last").clone()  );
		
		if($(".djname").length >= 3)
			$('#addExtraDJ').remove();
	}
}

function refreshCurrShow() {
	
	clearTimeout(showTimer);
	$.ajax({
		type: "GET",
		url: "logging_getcurrentshow.php",
		cache: false,
		success: function(html) {
			
			if(html == '-1') {
				//window.alert('html is -1');
				automation = true;
				showid = -1;
				starttime = '';
				//refreshLog(false, false);
				
				$("#djname").text("Automation");
				$("#showname").text("Automation");
				$("#starttime").html("");
				$("#showtype").text("Automation");
			}
			else {
				automation = false;
				var row = html.split("|");
				showid = row[0];
				//window.alert('html not -1, showid is ' + showid);
				starttime = row[1];
				$("#djname").text(row[2]);
				$("#showname").text(row[4]);
				$("#starttime").html(row[1]);
				$("#showtype").text(row[3]);
				$("#listeners").text("Listeners: "+row[6]);
			}
			checkInputBoxes();
		}
	});
	
	showTimer = setTimeout("refreshCurrShow()", 2000);
}


function refreshLog(timer, scrollDown) {
	if(timer == true) {
		clearTimeout(playlistTimer);
	}
	//document.getElementById('playlistFrame').contentDocument.location.reload(true);
	
	$.get('logging_getplaylist.php', {sid: showid}, function(data){
		$('#playlist_div').empty();
		$('#playlist_div').append(data);
	});
	
	if(scrollDown == true) {
		/** thanks to DAC2 for showing me the correct way to do this! **/
		$("#playlist_div").animate({ scrollTop: $("#playlist_div").attr("scrollHeight") }, 'normal');
		//$('#playlist_div').animate({ scrollTop: 30000000 }, 3000);
	}
	
	/**
	$.ajax({
		type: "GET",
		url: "logging_getplaylist.php?showid=" + showid,
		dataType: "xml",
		success: function(xml) {
			
		//This change keeps the header row intact
		//	$("#log").empty();
		$("#log tr:not(#top)").remove();
		
		
			$(xml).find('entry').each(function(){
				var rot = $(this).find('rotation').text();
				var num = $(this).find('numinshow').text();
				var albnum = $(this).find('albnum').text();
				var trknum = $(this).find('trknum').text();
				
				var id = $(this).find('id').text();
				var title = $(this).find('track').text();
				var album = $(this).find('album').text();
				var artist = $(this).find('artist').text();
				var label = $(this).find('label').text();
				var nowp = $(this).find('nowplaying').text();
				addTrack(id, nowp, artist,album,title,label,   rot,num,albnum,trknum);
			});
		}
	});
	**/
	if(timer == true) {
		playlistTimer = setTimeout("refreshLog()", 3000);
	}
}


/** this function shouldn't be called anymore (22 aug 10) **/
function addTrack(id, nowp, artist, album, track,label, rot,num,albnum,trknum) {
	
	var app = "<tr id='" + id + "'><td><img ";
	if(nowp == 0)
		app += "class='gray' ";
	
	app += "src='next32.png' onclick='nowPlaying(" + id + ")'></td>" + 	
		"<td>" + albnum + "</td><td>" + trknum + "</td><td>" + rot + 
		"</td><td>" + track + "</td><td>" + artist + "</td><td>" + album + "</td>" + 
		"<td>" + label + "</td></tr>";
	$("#log").append(app);
}

function nowPlaying(pid) {

	$.ajax({
		type: "GET",
		url: "logging_updatecurrentsong.php?pid=" + pid,
		success: function() {
			//$("img[src='http://wsbf.net/i/icons/accept.png']").toggleClass("gray");
			
			//$("#log tr:not(#" + pid + ")").addClass("gray");
			
			$("tr[id='" + pid + "'] td img").removeClass("gray");
			$("tr[id!='" + pid + "'] td img").addClass("gray");
			//window.alert("updated " + pid);
			
			refreshLog(false, true); //the parm makes it scroll down
			/* document.getElementById('playlistFrame').contentDocument.location.reload(true);*/
			/* window.location.reload();*/
		}
	});
	
}


function addOptional(element) {
	
	if(automation)
		return;
	
	//	element = element.parent().parent();
	element = $(element).parent().parent();
	artist = element.find('#artist').val();
	album = element.find('#album').val();
	track = element.find('#track').val();
	label = element.find('#label').val();
	
	albumno = element.find('#albumno').val();
	trackno = element.find('#trackno').val();
	/** rotation = element.find('.rot').val(); **/
	
	if(artist == "" || track == "") //album == "" || 
		return;
	
/**	
	window.alert("log_song.php?showid=" + showid + "&album=" + urlencode(album) + 
			"&artist=" + urlencode(artist) + "&track=" + urlencode(track) + 
			"&label=" + urlencode(label) + "&albumno=" + urlencode(albumno) + 
			"&trackno=" + urlencode(trackno) + "&rotation=" + urlencode(rotation) );
**/	
	/**  + "&rotation=" + urlencode(rotation) **/
	
	$.ajax({
		type: "GET",
		url: "log_song.php?showid=" + showid + "&album=" + urlencode(album) + 
				"&artist=" + urlencode(artist) + "&track=" + urlencode(track) + 
				"&label=" + urlencode(label) + "&albumno=" + urlencode(albumno) + 
				"&trackno=" + urlencode(trackno),
		success: function() {
			//addTrack(artist,album,track,label, albumno, trackno);
			refreshLog(false, true);
			element.fadeOut("normal", function(){
				//optionalEntries = $('#tbp').find('tr').size() - $('#tbp').find('tr:hidden').size() - 1;
				//if (optionalEntries == 0) {
					addBlankOptional();
					$("input:text:visible:first").focus();
				//}
			});
		}
	});
	
}

/** http://www.peterbe.com/plog/isint-function **/
function isInt(x) {
	var y=parseInt(x);
	if (isNaN(y)) return false;
	return x==y && x.toString()==y.toString();
}

/** http://www.easy400.net/js/regexp/overview.html **/
function isLetter (c) {
	var reLetter = /^[a-zA-Z]$/   
	return reLetter.test(c);
}
function isAlbumCode(element) {
	$(element).val($(element).val().toUpperCase());
	if($(element).val().length == 4) {
		if(isInt($(element).val().substr(1,1)) && 
			isInt($(element).val().substr(2,1)) && 
			isInt($(element).val().substr(3,1)) && 
		isLetter($(element).val().substr(0,1)) ) {
				
			return true;
		}
	}
	return false;
}



function albumAuto(element) {
	
	//element = $(element);
	$(element).val($(element).val().toUpperCase());
	row = $(element).parent().parent();
	if(isAlbumCode(element)) {

		row.children(".trk").children("#track").val( "" );
		row.children(".trk").children("#track").removeClass("recc");
		row.children(".trk").children("#track").removeClass("noair");
		
		$.ajax({
			type: "GET",
			url: "logging_autocomplete.php?albno=" + $(element).val(),
			dataType: "xml",
			success: function(xml) {
				$(xml).find('autoinfo').each(function(){
					
					row.children(".rot").text( $(this).find('bin').text() );
					row.children(".alb").children("#album").val( $(this).find('album').text()  );
					row.children(".art").children("#artist").val( $(this).find('artist').text()  );
					row.children(".lbl").children("#label").val( $(this).find('label').text()  );
					
				});
			}
		});
	}
	else {
		row.children(".alb").children("#album").val( "" );
		row.children(".art").children("#artist").val( "" );
		row.children(".lbl").children("#label").val( "" );
		row.children(".trk").children("#track").val( "" );
		
		row.children(".trk").children("#track").removeClass("recc");
		row.children(".trk").children("#track").removeClass("noair");
	}
	
}
function trackAuto(element) {
	//window.alert('trackAuto');
	row = $(element).parent().parent();
	if( isInt($(element).val()) && isAlbumCode( row.children(".ano").children("#albumno") ) ) {
		$.ajax({
			type: "GET",
			url: "logging_autocomplete.php?albno=" + 
				row.children(".ano").children("#albumno").val() + 
				"&trkno=" + $(element).val(),
			dataType: "xml",
			success: function(xml) {
				$(xml).find('autoinfo').each(function(){
					
					row.children(".alb").children("#album").val( $(this).find('album').text()  );
					row.children(".art").children("#artist").val( $(this).find('artist').text()  );
					row.children(".lbl").children("#label").val( $(this).find('label').text()  );
					
					row.children(".trk").children("#track").val( $(this).find('track').text()  );
					
					row.children(".trk").children("#track").removeClass("recc");
					row.children(".trk").children("#track").removeClass("noair");
					
					if($(this).find('clean').text() == 0)
						row.children(".trk").children("#track").addClass("noair");
					else {
						if($(this).find('recc').text() == 1)
							row.children(".trk").children("#track").addClass("recc");
					}
					
				});
			}
		});
	}
	else {
		row.children(".trk").children("#track").val( "" );
		row.children(".trk").children("#track").removeClass("recc");
		row.children(".trk").children("#track").removeClass("noair");
	}
	
}

/** only allow ONE blank optional at any given time **/
function addBlankOptional() {
	$('.row_optional').remove();
	$('#tbp').append($.blankentry);
}