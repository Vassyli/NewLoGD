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

// JQuery extensions
jQuery.each( [ "put", "delete" ], function( i, method ) {
  jQuery[ method ] = function( url, data, callback, type ) {
    if ( jQuery.isFunction( data ) ) {
      type = type || callback;
      callback = data;
      data = undefined;
    }

    return jQuery.ajax({
      url: url,
      type: method,
      dataType: type,
      data: data,
      success: callback
    });
  };
});

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
        this.scene = new SceneWidget(this);
        
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
        /*submenu
            .mouseenter(this.toggleSubmenu)
            .mouseleave(this.toggleSubmenu);*/
        
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
        $(".closebutton", closables).click(function(app) {
            return function () {
                app.showScene();
            }
        }(this));
    },
    
    /**
     * Shows the Scene
     */
    showScene : function(){
        $(".col-center > div").hide();
        this.scene.showWidget();
    },
    
    /**
     * Refreshs the Scene
     */
    refreshScene : function(){
        console.log(this.scene);
        this.scene.reload();
        this.showScene();
    },
    
    /**
     * Hides all central widgets, including the scene
     * @returns {undefined}
     */
    hideCentralWidgets : function() {
        $("#online .col-center > div").hide();
        this.scene.hideWidget();
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
            
        this.scene.reload();
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
        console.log("[App] Load Basic stuff")
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
        console.log("Mainview toggled");
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

function SceneWidget(app) {
    this.app = app;
    this.scenedesc = $("#scenewidget");
    this.sceneactions = $("#sceneactions");
}

SceneWidget.prototype = {
    showWidget : function() {
        this.scenedesc.show();
        this.sceneactions.show();
        console.log("[Scene] Show");
    },
    
    hideWidget : function() {
        this.scenedesc.hide();
        this.sceneactions.hide();
    },
    
    reload : function() {
        console.log("[Scene] Reload");
        $.get("./scene")
            .done(function(scene) {
                return function(answer) {
                    scene.render(answer);
                }
            }(this));
    },
    
    render : function(answer) {
        this.clear();
        this.scenedesc.append("<h2>" + answer["title"] + "</h2>")
        $widgets = {};
        
        this.scenedesc.append(renderSceneDescription(answer["body"]));
        this.sceneactions.append(this.createActions(answer["actions"]));
        
        for(var id in $widgets) {
            console.log("Load", $widgets[id]);
            $widgets[id].load();
        }
    },
    
    clear : function() {
        this.scenedesc.html("");
        this.sceneactions.html("");
        console.log("[Scene] Clear")
    },
    
    createActions : function(actions) {
        var html = $("<nav class='w3-sidenav'></nav>");
        
        for(var key in actions) {
            var hasChilds = actions[key]["childs"].length > 0;
            
            if(hasChilds) {
                var divContainer = $("<div class='w3-accordion'></div>")
                var anchor = $("<a>" + actions[key]["title"] + "</a>");
                divContainer.append(anchor);
                
                var innerContainer = $("<div class='w3-accordion-content'></div>");
                for(var childkey in actions[key]["childs"]) {
                    var subanchor = $("<a>" + actions[key]["childs"][childkey]["title"] + "</a>");
                    
                    if(actions[key]["childs"][childkey]["target"] !== null) {
                        // Target is not NULL - add a link
                        subanchor.click(this.onActionClick(this, actions[key]["childs"][childkey]["id"]));
                    }
                    
                    innerContainer.append(subanchor);
                }
                divContainer.append(innerContainer);
                
                html.append(divContainer);
            }
            else {
                var anchor = $("<a>" + actions[key]["title"] +"</a>");
                html.append(anchor)
            }
            
            if(actions[key]["target"] !== null) {
                // Target is not NULL - add a link
                anchor.click(this.onActionClick(this, actions[key]["id"]));
            }
        }
        
        return html;
    },
    
    onActionClick : function(scene, actionid) {
        return function(event) {
            console.log("[Scene] Call action", actionid);
            $.post("./scene/change", {"id" : actionid})
                .done(function(answer) {scene.reload();})
                .fail(function(answer) {console.log("[Scene] Switch of Scene failed, reaspon: ", answer)})
        };
    }
}

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
        
        var entry = $("<div class='charentry'>"
            + "<div class='charhead'>"
                + "<div class='charname'><a></a></div>"
            + "</div><div class='charbody'>&nbsp;"
            + "</div>"
        + "</div");

        // Fill with data
        $(".charname a", entry).html(character["name"]);
        $(".charname", entry).click(function(app, charid) {
            return function(event) {
                console.log("[CharacterWidget] Set current character to", charid);
                $.put("./character/current/" + charid)
                    .done(function(answer) {
                        console.log("put success");
                        app.refreshScene();
                    });
            }
        }(app, character["id"]));

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
    validators : {
        "minlength" : "checkMinLength",
        "maxlength" : "checkMaxLength",
    },
    
    /**
     * Renders the form and returns it as a jQuery object
     * @returns {$}
     */
    render : function() {
        var self = this;
        // Create basic form
        this.form = $("<form autocomplete='off' class='w3-form w3-text-black'><h2></h2></form>");
        var html = this.form;
        // fill with data
        html.id = this.formid;
        html.method = this.formdata.method;
        html.target = this.formdata.target;
        html.addClass("created");
        $("h2", html).html(this.formdata.title);
        
        for(var name in this.formdata.form) {
            html.append(this.addItem(name, this.formdata.form[name]));
        }
        
        html.append(this.addSubmitButton());
        
        // Connect
        html.submit(function(event) {
            event.preventDefault();
            console.log(self.form);
            console.log(self.form.serialize());
            
            // Prevent form submission if one field has been marked as invalid by JS
            if($(".invalidated", self.form).length > 0) {
                return false;
            }
            
            // Clean up errors from the last time
            $("#" + self.formid + "__error").remove();
            $(".invalid", self.form).removeClass("invalid");
            
            if(self.method === HTTP_POST) {
                $.post(self.target, self.form.serialize())
                    .done(self.done)
                    .fail(function(form) {
                        return function(answer) {
                            if("responseJSON" in answer) {
                                form.invalidate(form, answer["responseJSON"]);
                            }
                            else {
                                form.invalidateOther(form, answer["responseText"]);
                            }
                        };
                    }(self));
            }
        });
        
        return html;
    },
    
    /**
     * Invalidates a Field based on the server answer
     * @param {Form} self
     * @param {type} answer
     * @returns {undefined}
     */
    invalidate : function(self, answer) {
        for(var key in answer) {
            var labelid = "#" + self.formid + "_" + key;
            console.log(labelid, $(labelid));
            $(labelid).addClass("invalid");
        }
    },
    
    /**
     * Displays general error messages that can only be checked on server side
     * @param Form self
     * @param {type} answer
     * @returns {undefined}
     */
    invalidateOther : function(self, answer) {
        var id = self.formid + "__error";
        self.form.prepend($("<div id='" + id + "' class='w3-container w3-red error'><p>" + answer + "</p></div>"));
    },
    
    /**
     * Observes the change of a form field and marks it as invalid when deemed invalid.
     * @param {Form} self
     * @param {type} event
     */
    onChange : function(self, event) {
        var field = event["currentTarget"];
        var fieldname = field.name;
        var fieldvalue = field.value;
        var labelid = "#" + self.formid + "_" + fieldname;
        var label = $(labelid);
        
        var fielddata = self.formdata["form"][fieldname];
        
        if("options" in fielddata && "validate" in fielddata["options"]) {
            var errors = 0;
            for(var validator in fielddata["options"]["validate"]) {
                if(validator in self.validators) {
                    // Get function callback
                    var fn = self[self.validators[validator]];
                    if(fn(fieldvalue, fielddata["options"]["validate"][validator]) === false) {
                        errors++;
                    }
                }
            }
            
            if(errors > 0) {
                console.log("[Form] field", fieldname, "has been deemed to be invalid");
                label.addClass("invalidated");
            }
            else {
                console.log("[Form] field", fieldname, "has been deemed to be valid");
                label.removeClass("invalidated")
            }
        }
        
        // Inform the Form about this change
        self.onFormChange();
    },
    
    /**
     * Deactivates the submit button if a field has been deemded invalid by JS
     */
    onFormChange : function() {
        if($(".invalidated", self.form).length > 0) {
            $("[name='_submit']", self.form).prop("disabled", true);
        }
        else {
            $("[name='_submit']", self.form).prop("disabled", false);
        }
    },
    
    /**
     * Adds an item to the Form
     * @param {string} name Identifier of the field
     * @param {object} data Additional data about the field to be added
     * @returns {$} The whole field including surrounding html tags
     */
    addItem : function(name, data) {
        var item = $("<label class='w3-label'></label>");
        var label = $("<span></span>");
        var inputwidget = $("<span></span>");
        
        label.html(data["label"]);
        item.attr("id", this.formid + "_" + name);
        
        switch(data["type"]) {
            case "varchar":
                var field = this.varchar(name, data);
                break;
        }
        
        field.change(function(self) {
            return function(event) {
                self.onChange(self, event);
            }
        }(this));
        inputwidget.append(field);
        
        item.append(label).append(inputwidget);
        return item;
    },
    
    addSubmitButton : function() {
        var item = $("<label class='w3-label'><button class='w3-btn' name='_submit' type='submit'>Submit</button></label>");
        return item;
    },
    
    varchar : function(name, data) {
        var widget = $("<input type='text' name='" + name + "' class='w3-input w3-validate'>");
        return widget;
    },
    
    text : function(name, data) {
        var widget = $("<textarea name='" + name + "'></textarea>");
        console.log("[Form] Add textarea with name", name);
        return widget;
    },
    
    checkMinLength : function(value, arguments) {
        return value.length >= arguments;
    },
    
    checkMaxLength : function(value, arguments) {
        return value.length <= arguments;
    }
};

function ListWidget(apiRoot, settings) {
    this.apiRoot = apiRoot;
    this.id = getUniqueId();
    this.page = 0;
    this.numComments = 0;
    this.maxPage = 0;
    
    this.setSettings(settings);
    
    $widgets[this.id] = this;
}

ListWidget.prototype = {
    data : {},
    connected : false,
    setSettings: function(settings) {
        this.reversed = "reversed" in settings && settings["reversed"] === true ? true : false;
        this.limit = "limit" in settings ? settings["limit"] : 20;
    },
    load : function() {
        // Get comments
        $.get(this.apiRoot + "/page_" + this.page + "_" + this.limit).done(function(self) {
            console.log("[ListWidget] load data")
            return function(answer) {
                console.log("[ListWidget] Data", answer);
                self.numComments = answer["count"];
                self.maxPage = answer["count"] / self.limit + 1;
                self.storeData(answer["comments"]);
                self.renderData();
                self.connect();
            };
        }(this));
    },
    storeData : function(data) {
        this.data = data;
    },
    renderData : function() {
        console.log("[ListWidget] render Data");
        var widget = this.getWidget(".comments");
        widget.empty();
        var numOfComments = this.data.length;
        for(var i = 0; i < numOfComments; i++) {
            var index = this.reversed ? numOfComments - i - 1: i;
            
            widget.append("<p>" + this.data[index]["line"] + "</p>");
        }
        
        // Buttons
        if(this.page >= this.maxPage - 2) {
            this.getWidget(".previous").addClass("inactive");
        }
        else {
            this.getWidget(".previous").removeClass("inactive")
        }
        
        if(this.page === 0) {
            this.getWidget(".next").addClass("inactive");
        }
        else {
            this.getWidget(".next").removeClass("inactive");
        }
    },
    display : function() {
        return "<div class='widget_list' id='" + this.id + "'>"
            + '<div class="w3-center"><ul class="w3-pagination">'
                + '<li class="w3-float-left previous inactive"><a>&#10094; Previous</a></li>'
                + '<li class="w3-float-left refresh"><a>Refresh</a></li>'
                + '<li class="w3-float-right next inactive"><a>Next &#10095;</a></li>'
            + '</ul></div>'
            + '<div class="comments">(loading)</div>'
        + "</div>";
    },
    getWidget : function(selectors) {
        if(typeof selectors === "undefined") {
            selectors = "";
        }
        
        return $("#" + this.id + " " + selectors);
    },
    connect : function() {
        if(this.connected === false) {
            this.connected = true;
            // Previous
            this.getWidget(".previous").click(function(self) {
                return function() {
                    if(self.page < self.maxPage - 2) {
                        self.page++;
                        self.load();
                    }
                }
            }(this));
            // Refresh
            this.getWidget(".refresh").click(function(self) {
                return function() {
                    self.load();
                }
            }(this));
            // Forward
            this.getWidget(".next").click(function(self) {
                return function() {
                    if(self.page > 0) {
                        self.page--;
                        self.load();
                    }
                }
            }(this));
            
            $(document).on("newlogd:refreshSceneWidgets", function(self) {
                return function(event) {
                    console.log("[ListWidget] Listened to event. Exec reload.");
                    self.load();
                }
            }(this));
        }
    }
};

function SimpleformWidget(apiRoot, displayKey) {
    this.apiRoot = apiRoot;
    this.displayKey = displayKey;
    this.id = getUniqueId();
    this.action = apiRoot;
    
    this.options = {
        "maxlength" : ("maxlength" in this.displayKey?" maxlength='" + this.displayKey["maxlength"] + "'":""),
        "name" : ("name" in this.displayKey ? this.displayKey["name"] : "line"),
        "text" : ("text" in this.displayKey ? this.displayKey["text"] : "Type something"),
        "submit" : ("submit" in this.displayKey ? this.displayKey["submit"] : "Submit"),
    }
    
    $widgets[this.id] = this;
}

SimpleformWidget.prototype = {
    connected : false,
    load : function() {
        this.connect();
    },
    connect : function() {
        if(this.connected === false) {
            this.connected = true;
            
            var form = this.getWidget("form");
            console.log("Ahja", form);
            form.submit(function(self, form) {
                return function() {
                    var name = self.options["name"];
                    var value = $("input[name=" + name + "]", form)[0].value;
                    var action = form.action;
                    var data = {};
                    data[name] = value;
                    
                    $.post(form.action, data)
                            .done(function(self) {
                                return function(answer) {
                                    console.log("[SimpleformWidget] Success", answer, data, self.apiRoot);
                                    $(document).trigger("newlogd:refreshSceneWidgets");
                                };
                            }(self))
                            .fail(function(self) {
                                return function(answer) {
                                    console.log("[SimpleformWidget] No Success", answer, data);
                                };
                            }(self));
                    return false;
                };
            }(this, form[0]));
        }
    },
    done : function() {
        
    },
    getWidget : function(selectors) {
        if(typeof selectors === "undefined") {
            selectors = "";
        }
        
        return $("#" + this.id + " " + selectors);
    },
    display : function() {
        return "<div class='widget_simpleform' id='" + this.id + "'>"
                +"<form method='POST' action='" + this.action + "' class='w3-form w3-text-black'>"
                    + "<label class='w3-label'><span>" + this.options["text"] + "</span><span><input class='w3-input' type='text'" + this.options["maxlength"] + " name='"+ this.options["name"] +"'></span></label>"
                    + "<label><button type='submit' class='w3-btn'>" + this.options["submit"] + "</button></label>"
                + "</form>"
            + "</div>";
    }
}

function getUniqueId() {
    return "_newlogd_id_" + Math.round(new Date().getTime() + Math.random() * 100)
}

function renderSceneDescription(raw) {
    var rendered = "";
    var splitted = raw.split("\n\n");
    
    for(var p in splitted) {
        var row = splitted[p];
        
        if(row.substring(0, 1) === "@") {
            var widget = createWidgetFromString(row);
            if(typeof widget === "string") {
                rendered = rendered + widget;
            }
            else {
                console.log(widget);
                rendered = rendered + widget.display();
            }
        }
        else {
            rendered = rendered + "<p>" + splitted[p] + "</p>";
        }
    }
    
    return rendered;
}

function createWidgetFromString(row) {
    if(row.substr(0, 2) === "@{" && row.substr(row.length-1, 1) === "}") {
        var creationString = row.substring(2, row.length-1);
        var creationParts = creationString.split("|");
        console.log("[Widgets] Widget String OK", creationString, creationParts);
        var apiRoot = "./ext/" + creationParts[0];
        // Options
        var options = {};
        var i = 0;
        for(var key in creationParts) {
            i++;
            if(i <= 2) {
                continue;
            }
            
            var pair = creationParts[key].split(":");
            if(pair.length === 1) {
                options[pair[0]] = true;
            }
            else {
                options[pair[0]] = pair[1];
            }
        }
        
        switch(creationParts[1]) {
            case "List":
                return new ListWidget(apiRoot, options);
                break;
            default:
                return new SimpleformWidget(apiRoot, options);
                break;
        }
    }
    else {
        console.log("[Widgets] Faulty Widget String", row.substr(0, 2), row.substr(row.length-1, 1));
        return "###Faulty Widget###" + row;
    }
}

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
$widgets = {};

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