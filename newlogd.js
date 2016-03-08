var API = "/";
var loggedin = false;

var HTTP_GET = "GET";
var HTTP_POST = "POST";

var MAINVIEW_ONLINE = 1;
var MAINVIEW_OFFLINE = 0;

var submenu_connections = {
    "#charactermenu_view" : ["./character", "showSelectionScreen"],
};

function App(body) {
    this.body = body;
}

App.prototype = {
    body : null,
    loggedin : false,
    characters : null,
    scene : null,
    mail : null,
    
    run : function() {
        this.loginProcedure();
        
        // Create controls
        this.createSubmenus();
        this.createModals();
        
        // Create bigger controls
        this.characters = new CharacterWidget(this);
        
        this.reload();
    },
    
    loginProcedure : function() {
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
    },
    
    createSubmenus : function() {
        var app = this;
        var submenu = $(".submenu");
        submenu
            .mouseenter(this.toggleSubmenu)
            .mouseleave(this.toggleSubmenu);
        
        // Connect Submenu items with click actions
        for(var id in submenu_connections) {
            var target = submenu_connections[id];
            var argnum = target.length;
            
            if(argnum === 2) {
                $(id).click(function() {
                    console.log("[App] Fetch", target[0])
                    $.get(target[0])
                        .done(function(answer) {
                            fn = app.characters[target[1]];
                            fn(app.characters, answer);
                        })
                })
            }
            /*if(target.count )
            $(id).click
            
            $("#charactermenu_view").click(function() {
       $.get("./character").done(function(a) {
          character_showSelection("view", a); 
       });
       $("#charactermenu ul").hide();
    });*/
        }
    },
    
    toggleSubmenu : function(event) {
        var target = $("ul", event["currentTarget"]);
        if(event.type === "mouseenter") {
            target.show();
        }
        else {
            target.hide();
        }
    },
    
    createModals : function() {
        var closables = $(".closable");
        closables.prepend("<div class='closebutton'><a>Close</a></div><br class='clear'>");
        closables.click(function() {
            $(".col-center > div").hide();
            $("#scenewidget").show();
        });
    },
    
    hideCentralWidgets : function() {
        $("#online .col-center > div").hide();
        $("#scenewidget").hide();
    },
    
    reload : function() {
        console.log("[App] Fetch root data");
        
        var app = this;
        $.get("./")
            .done(function(answer){
                app.load(answer);
            });
    },
    
    load : function(answer) {
        this.loggedin = this.isLoggedin(answer);
        console.log("[App] Number of Session Hits: " + answer["pagehits"]);
        
        this.loadBasic(answer);
        
        if(this.loggedin) {
            this.loadOnline(answer);
        }
        else {
            this.loadOffline(answer);
        }
    },
    
    loadBasic : function(answer) {
        this.setGametitle(answer["gametitle"]);
        this.setGameversion(answer["version"]);
    },
    
    loadOffline : function(answer) {
        var app = this;
        
        this.toggleMainview(MAINVIEW_OFFLINE);
        
        // Social Logins
        $.get("./auth")
            .done(function(anwer) {
                $("#sociallogin .socialbutton").remove();
                var key;
                for(key in anwer) {
                    console.log("[App][Social] Add login provider " + anwer[key]["name"]);  
                    $("#sociallogin").prepend(app.createSocialbutton(key, anwer[key]));
                }
            });
    },
    
    loadOnline : function(answer) {
        var app = this;
        
        this.toggleMainview(MAINVIEW_ONLINE);
        
        // Connect logout button
        $("#logininfo a").click(function() {
            app.logout();
        });
        $("#user_name").text(answer["activeuser"]["name"]);
    },
            
    isLoggedin : function(answer) {
        if("loginstate" in answer && answer["loginstate"] > 0) {
            return true;
        }
        else {
            return false;
        }
    },
    
    logout : function() {
        var app = this;
        $.get("./logout").done(function() {
            app.reload();
        });
    },
    
    setGametitle : function(title) {
        $("#title").html(title); 
    },
    
    setGameversion : function(version) {
        $("#version").html(version);
    },
    
    toggleMainview : function(what) {
        if(what === MAINVIEW_OFFLINE) {
            $("#offline").show();
            $("#online").hide();
        }
        else {
            $("#online").show();
            $("#offline").hide();
        }
    },
    
    createSocialbutton : function(provider, providerdata) {
        var app = this;
        var button = $("<div><a></a></div>");
        $("a", button).text(providerdata["name"]);

        button.addClass("socialbutton").addClass(provider);
        button.click(function() {
            app.authorizationStart(provider);
        });

        return button;
    },
    
    authorizationStart : function(provider) {
        var app = this;
        var uri = "./auth/" + provider;
        console.log("[Auth] Get Authorization Details from NewLoGD for provider [" + provider + "]");
	
        $.get(uri).done(function(answer) {
            app.authorizationSend(answer);
        });
    },
    
    authorizationSend : function(answer) { 
        var providerurl;
        
        answer[1]["redirect_uri"] = window.location.href;
	
        providerurl = answer[0] + "?" + jQuery.param(answer[1]);
        console.log("[Auth] Call authorization request to " + providerurl);

        window.location = providerurl;
    },
};

/**
 * Class which provides functions for the character widget
 * @param {App} app the instance of the app
 * @returns {CharacterWidget}
 */
function CharacterWidget(app) {
    this.app = app;
    this.charselection = $("#characterwidget");
    this.charenclosement = $(".enclosement", this.charselection);
}

CharacterWidget.prototype = {
    /**
     * Hides all central elements and clears the CharacterWidget from old content
     * @returns {undefined}
     */
    clearWidget : function() {
        // Hide Everything
        this.app.hideCentralWidgets();
        
        // Remove everything in the enclosement
        $("*", this.charenclosement).remove();
    },
    
    /**
     * Shows this widget
     * @returns {undefined}
     */
    showWidget : function() {
        this.charselection.show();
    },
    
    /**
     * shows the Selection Screen for Characters
     * @param {CharacterSelection} self A reference to an instance of this class
     * @param {object} listOfCharacters List of characters as a JSON object
     * @returns {undefined}
     */
    showSelectionScreen : function(self, listOfCharacters) {
        var row;
        
        // Prepare the widget
        self.clearWidget();
        
        // Add central elements
        for(var row in listOfCharacters) {
            var character = listOfCharacters[row];
            self.addSelectionEntry(character);
        }
        
        // Show Widget
        self.showWidget();
    },
    
    /**
     * Adds a character entry for the character selection
     * @param {object} character Object containing the information about a single character
     * @returns {undefined}
     */
    addSelectionEntry : function(character) {
        console.log("[Chars] Add Character", character);
        
        var entry = $("<div class='charentry'>\n\
            <div class='charname'></div>\n\
            <div class='charimage'></div>\n\
            <ul class='charinfo'><li></li></ul>\n\
        </div>");

        // Fill with data
        $(".charname", entry).html(character["name"]);

        this.charenclosement.append(entry);
    }
};


function getUrlParams(query) {
	var urlParams = {};
    var match,
	
	pl     = /\+/g,  // Regex for replacing addition symbol with a space
	search = /([^&=]+)=?([^&]*)/g,
	decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); };

	while (match = search.exec(query)) {
		urlParams[decode(match[1])] = decode(match[2]);
	}
	
	console.log("[App] URL parameters loaded");
	console.log(urlParams);
	
	return urlParams;
}

$body = $("body");

$(document).ready(function() {
	console.log("[App] Document DOM is ready");
    
    app = new App($body);
    app.run();
	
    //App_Init();
});

$(document).on({
    ajaxStart: function() { $body.addClass("loading");    },
     ajaxStop: function() { $body.removeClass("loading"); }    
});