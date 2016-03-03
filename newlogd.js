var API = "/";
var loggedin = false;

var HTTP_GET = "GET";
var HTTP_POST = "POST";

function App_Init() {
	// Get query string information after the hash (#)
	var params = getUrlParams(location.hash.substring(1));
	
	// Check if we have an answer from the auth service
	if("access_token" in params && "state" in params) {
		console.log("[Auth] Access token received from Auth-Service, now send it to the NewLoGD-Server");
		console.log("[Auth] Token is [" + params["access_token"] + "]");
		
		var redirect_uri = [location.protocol, '//', location.host, location.pathname].join('');
		
		var jqHXR = $.post("./auth/", params)
			.done(function(a) {
				console.log("[Auth] Token successfully confirmed by NewLoGD");
				console.log("[Auth] Redirect to [" + redirect_uri +"]");
				console.log(a);
				window.location = redirect_uri;
			})
			.fail(function(a) {
				console.log("[Auth] Token was determined to be invalid by NewLoGD");
				console.log("[Auth] Redirect to [" + redirect_uri +"]");
				console.log(a);
				window.location = redirect_uri + "#authfail";
			});
	}
	
	// Normal page run
	var ajax_Run = $.get("./").done(App_Run);
}

function App_Reload() {
    var ajax_Run = $.get("./").done(App_Run);
}

function App_Run(answer) {
	console.log("[App] Run App, load data");
	console.log(answer);
	
	if("loginstate" in answer && answer["loginstate"] > 0) {
		// User is online
		App_Run_Loggedin(answer);
	}
	else {
		// User is not online
		App_Run_Basic(answer);
	}
}

function App_Logout() {
    $.get("./logout").done(App_Reload());
}

function App_Run_Loggedin(answer) {
    $("#offline").hide();
    $("#online").show();
    $("#logininfo").html(
        "You are loggedin via <span id=\"socialprovider\">" 
        + answer["auth.provider"]
        + "</span>. In order to logout, click "
        + "<a onClick=\"App_Logout()\">here.</a>"
    );
}

function App_Run_Basic(answer) {
    $("#online").hide();
    $("#offline").show();
	$("#logintitle").html("Hallo Welt");
	$("#version").html(answer["version"]);
	
	console.log("[App] Number of Session Hits: " + answer["pagehits"]);
	
	// Social Logins
	$.get("./auth").done(function(a){
		for(var i = 0; i < a.length; i++) {
			console.log("[App][Social] Add login provider " + a[i]);
			$("#sociallogin").append('<div class="socialbutton"><a onClick="App_Authorize_Start(\'' + a[i] + '\')">' + a[i] + '</a></div>');
		}
	});
}

function App_Authorize_Start(provider) {
	var uri = "./auth/" + provider;
	console.log("[Auth] Get Authorization Details from NewLoGD for provider [" + provider + "]")
	
	$.get(uri).done(App_Authorize_Send);
}

function App_Authorize_Send(answer) {
	answer[1]["redirect_uri"] = window.location.href;
	
	providerurl = answer[0] + "?" + jQuery.param(answer[1]);
	console.log("[Auth] Call authorization request to " + providerurl);
	
	window.location = providerurl;
}

function getUrlParams(query) {
	var urlParams = {};
    var match,
	
	pl     = /\+/g,  // Regex for replacing addition symbol with a space
	search = /([^&=]+)=?([^&]*)/g,
	decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); }

	while (match = search.exec(query)) {
		urlParams[decode(match[1])] = decode(match[2]);
	}
	
	console.log("[App] URL parameters loaded");
	console.log(urlParams);
	
	return urlParams
}

$body = $("body");

$(document).ready(function() {
	console.log("[App] Document DOM is ready");
	App_Init();
});

$(document).on({
    ajaxStart: function() { $body.addClass("loading");    },
     ajaxStop: function() { $body.removeClass("loading"); }    
});