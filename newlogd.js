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
    
    // Menu creation
    submenu = $(".submenu");
    //$("ul", submenu).hide()
    submenu.mouseenter(function(submenu) {
        $("ul", submenu["currentTarget"]).show();
        console.log(submenu);
    }).mouseleave(function(submenu) {
        $("ul", submenu["currentTarget"]).hide();
        console.log(submenu);
    })
    
    // Add "closeable"
    closables = $(".closable");
    closables.prepend("<div class='closebutton'><a>Close</a></div><br class='clear'>");
    closables.click(function() {
        $(".col-center > div").hide();
        $("#sceneview").show();
    })
    
    // Display either Offline or Online version
	var ajax_Run = $.get("./").done(App_Run);
}

function App_Reload() {
    console.log("[App] Reload");
    $.get("./").done(function(a) {
        App_Run(a);
    });
}

function App_Run(answer) {
	console.log("[App] Run App, load data");
	console.log(answer);
    
    App_Run_Basic(answer);
	
	if("loginstate" in answer && answer["loginstate"] > 0) {
		// User is online
		App_Run_Online(answer);
	}
	else {
		// User is not online
        App_Run_Offline(answer);
	}
}

function App_Logout() {
    $.get("./logout").done(function() {
        App_Reload();
    });
}

function App_Run_Online(answer) {
    $("#offline").hide();
    $("#online").show();
    $("#logininfo a").click(function() {
        App_Logout();
    });
    $("#user_name").text(answer["activeuser"]["name"]);
    
    character_createMenu();
}

function character_createMenu() {
    $("#charactermenu_view").click(function() {
       $.get("./character").done(function(a) {
          character_showSelection("view", a); 
       });
       $("#charactermenu ul").hide();
    });
    
    $("#charactermenu_create").click(function() {
       $.get("./character").done(function(a) {
          character_showSelection("create", a); 
       });
       $("#charactermenu ul").hide();
    });
}

function character_showSelection(what, characters) {
    var row;
    $("#online .col-center > div").hide();
    
    var charselection = $("#characterselection");
    
    $(".enclosement *", charselection).remove();
    
    if(what == "view") {
        charselection.show();

        for(row in characters) {
            character_addSelectionEntry($(".enclosement", charselection), characters[row]);
        }
    }
    else if(what == "create") {
    }
}

function character_addSelectionEntry(charselection, character) {
    console.log("[Chars] Add Character", character);
    entry = $("<div class='charentry'>\n\
        <div class='charname'></div>\n\
        <div class='charimage'></div>\n\
        <ul class='charinfo'><li></li></ul>\n\
    </div>");
    
    // Fill with data
    $(".charname", entry).html(character["name"]);
    
    charselection.append(entry);
}

function App_Run_Offline(answer) {
    $("#online").hide();
    $("#offline").show();
    
    // Social Logins
	$.get("./auth").done(function(a){
        $("#sociallogin .socialbutton").remove();
        var key;
        for(key in a) {
			console.log("[App][Social] Add login provider " + a[key]["name"]);  
            $("#sociallogin").prepend(createSocialbutton(key, a[key]));
        }
	});
};

function App_Run_Basic(answer) {
	$("#title").html(answer["gametitle"]);
	$("#version").html(answer["version"]);
	
	console.log("[App] Number of Session Hits: " + answer["pagehits"]);
}

function createSocialbutton(provider, providerdata) {
    var button = $("<div><a></a></div>");
    $("a", button).text(providerdata["name"]);
    
    button.addClass("socialbutton").addClass(provider);
    button.click(function() {
        App_Authorize_Start(provider)
    });
    
    return button;
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