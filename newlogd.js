var API = "/";
var loggedin = false;

var HTTP_GET = "GET";
var HTTP_POST = "POST";

var MAINVIEW_ONLINE = 1;
var MAINVIEW_OFFLINE = 0;

var submenu_connections = {
    "#charactermenu_view" : ["./character", "characters", "showSelectionScreen"],
    "#charactermenu_create" : ["./character/create", "characters", "showCreationScreen"],
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
    
    /**
     * Prepares and runs the app
     * @returns {undefined}
     */
    run : function() {
        this.loginProcedure();
        
        // Create controls
        this.createSubmenus();
        this.createModals();
        
        // Create bigger controls
        this.characters = new CharacterWidget(this);
        
        this.reload();
    },
    
    /**
     * Does the login procedure: If a user is returned from a OAuth site via a hash
     * url, this procedure takes the information from it and posts the informations
     * received to the server.
     * @returns {undefined}
     */
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
    
    /**
     * Creates real submenus and connects them to their actions
     * @returns {undefined}
     */
    createSubmenus : function() {
        var app = this;
        var submenu = $(".submenu");
        submenu
            .mouseenter(this.toggleSubmenu)
            .mouseleave(this.toggleSubmenu);
        
        // Connect Submenu items with click actions
        var id;
        for(id in submenu_connections) {
            var target = submenu_connections[id];
            var argnum = target.length;
            
            if(argnum === 3) {
                // Remember: We need to call a callback function here that returns a callback
                // Otherwise, the callback function would only contain the information about
                // the last item in submenu_connections, meaning that all menu clicks will have
                // the same target and effect!
                var onClick = function(id, uri, widget, func, app) {
                    console.log("[App] Connect menu point for ", id, uri, widget, func);
                    
                    return function() {
                        console.log("[App] Clicked on menu", id, "fetch from", uri);
                        
                        $.get(uri).done(function(answer){
                            var fn = app[widget][func];
                            fn(app[widget], answer);
                        });
                    };
                }(id, target[0], target[1], target[2], this);
                
                // Bind the onClick-callback to this item
                $(id).click(onClick);
            }
        }
    },
    
    /**
     * Hides and shows a given submenu on mouseenter/mouseleave
     * @param {type} event
     * @returns {undefined}
     */
    toggleSubmenu : function(event) {
        var target = $("ul", event["currentTarget"]);
        if(event.type === "mouseenter") {
            target.show();
        }
        else {
            target.hide();
        }
    },
    
    /**
     * Equips all div-tags with certain classes with features.
     * It equips all .closable with a close-button that hides the modal and shows
     * the scene again.
     * @returns {undefined}
     */
    createModals : function() {
        var closables = $(".closable");
        closables.prepend("<div class='closebutton'><a>Close</a></div><br class='clear'>");
        $(".closebutton", closables).click(function() {
            $(".col-center > div").hide();
            $("#scenewidget").show();
        });
    },
    
    /**
     * Hides all central widgets, including the scene
     * @returns {undefined}
     */
    hideCentralWidgets : function() {
        $("#online .col-center > div").hide();
        $("#scenewidget").hide();
    },
    
    /**
     * Reloads fundamental page informations
     * @returns {undefined}
     */
    reload : function() {
        console.log("[App] Fetch root data");
        
        var app = this;
        $.get("./")
            .done(function(answer){
                app.load(answer);
            });
    },
    
    /**
     * Loads the Application and either shows the online or the offline variant
     * @param {type} answer
     * @returns {undefined}
     */
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
    
    /**
     * Loads common features of the online and offline versions
     * @param {type} answer
     * @returns {undefined}
     */
    loadBasic : function(answer) {
        this.setGametitle(answer["gametitle"]);
        this.setGameversion(answer["version"]);
    },
    
    /**
     * Loads features only found in the offline version
     * @param {type} answer
     * @returns {undefined}
     */
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
    
    /**
     * Loads features only found in the online version
     * @param {type} answer
     * @returns {undefined}
     */
    loadOnline : function(answer) {
        var app = this;
        
        this.toggleMainview(MAINVIEW_ONLINE);
        
        // Connect logout button
        $("#logininfo a").click(function() {
            app.logout();
        });
        $("#user_name").text(answer["activeuser"]["name"]);
    },
    
    /**
     * Checks the loginstate and returns true if the user is online, false if not.
     * @param {type} answer Root-Answer from /
     * @returns {Boolean} True if loggedin, false if not
     */
    isLoggedin : function(answer) {
        if("loginstate" in answer && answer["loginstate"] > 0) {
            return true;
        }
        else {
            return false;
        }
    },
    
    /**
     * Calls the logout node and logs the user out.
     * @returns {undefined}
     */
    logout : function() {
        var app = this;
        $.get("./logout").done(function() {
            app.reload();
        });
    },
    
    /**
     * Sets the game title
     * @param {string} title
     * @returns {undefined}
     */
    setGametitle : function(title) {
        $("#title").html(title); 
    },
    
    /**
     * Sets the game version
     * @param {string} version
     * @returns {undefined}
     */
    setGameversion : function(version) {
        $("#version").html(version);
    },
    
    /**
     * Depending on the loginstate, this function either hides offline and shows 
     * online widgets, or vice-versa.
     * @param {type} what
     * @returns {undefined}
     */
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
    
    /**
     * Creates a button for social-login and connects it.
     * @param {string} provider The Provider
     * @param {type} providerdata Providerdata from /auth
     * @returns {$} The Button jQuery object
     */
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
    
    /**
     * Starts the authorization by getting more detailled information about the 
     * provider from /auth/{provider}.
     * @param {type} provider
     * @returns {undefined}
     */
    authorizationStart : function(provider) {
        var app = this;
        var uri = "./auth/" + provider;
        console.log("[Auth] Get Authorization Details from NewLoGD for provider [" + provider + "]");
	
        $.get(uri).done(function(answer) {
            app.authorizationSend(answer);
        });
    },
    
    /**
     * Relocates the user to the OAuth provider login page
     * @param {type} answer
     * @returns {undefined}
     */
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
        this.charenclosement.html("");
    },
    
    /**
     * Shows this widget
     * @returns {undefined}
     */
    showWidget : function() {
        this.charselection.show();
    },
    
    /**
     * Shows the character creation screen
     * @param {type} self
     * @param {type} form
     * @returns {undefined}
     */
    showCreationScreen : function(self, form) {
        self.clearWidget();
        
        // Create formular from JSON answer
        var form = new Form(form);
        form.done = function(self) { return function(answer) {
            self.showCreationMessage(self, answer);
        }}(self);
        
        self.charenclosement.html(form.render());
                
        self.showWidget();
    },
    
    showCreationMessage : function(self, answer) {
        self.clearWidget();
        
        self.charenclosement.html(answer[0]);
        
        self.showWidget();
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

function Form(formdata) {
    this.formdata = formdata;
    this.formid = formdata["target"].replace(/[^a-zA-Z]/g, "_");
    this.method = formdata["method"];
    this.target = "." + formdata["target"];
}

Form.prototype = {
    /** @type {object} Basic form data */
    formdata : {},
    method : "POST",
    form : null,
    done : null,
    
    /**
     * Renders the form and returns it as a jQuery object
     * @returns {$}
     */
    render : function() {
        var self = this;
        // Create basic form
        this.form = $("<form><fieldset><legend></legend></fieldset></form>");
        var html = this.form;
        // fill with data
        html.id = this.formid;
        html.method = this.formdata.method;
        html.target = this.formdata.target;
        html.addClass("created");
        $("legend", html).html(this.formdata.title);
        
        for(var name in this.formdata.form) {
            $("fieldset", html).append(this.addItem(name, this.formdata.form[name]));
        }
        
        $("fieldset", html).append(this.addSubmitButton());
        
        // Connect
        html.submit(function(event) {
            event.preventDefault();
            console.log(self.form);
            console.log(self.form.serialize());
            
            if(self.method === HTTP_POST) {
                $.post(self.target, self.form.serialize())
                    .done(self.done)
                    .fail(function(answer) {
                        console.log("[Form] Error from server");
                        console.log(answer);
                    });
            }
        })
        
        return html;
    },
    
    addItem : function(name, data) {
        var item = $("<label></label>");
        var label = $("<span></span>");
        var inputwidget = $("<span></span>");
        
        label.html(data["label"]);
        
        switch(data["type"]) {
            case "varchar":
                inputwidget.append(this.varchar(name, data));
                break;
        }
        
        item.append(label).append(inputwidget);
        return item;
    },
    
    addSubmitButton : function() {
        var item = $("<label><button name='_submit' type='submit'>Submit</button></label>");
        return item;
    },
    
    varchar : function(name, data) {
        var widget = $("<input type='text' name='" + name + "'>");
        return widget;
    },
    
    text : function(name, data) {
        var widget = $("<textarea name='" + name + "'></textarea>");
        console.log("[Form] Add textarea with name", name);
        return widget;
    },
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