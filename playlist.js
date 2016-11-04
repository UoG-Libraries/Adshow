(function () {
    "use strict";

    String.prototype.empty = function empty() {
        return this.length == 0;
    };

    var req = function (url, dontCache, response) {
        var request = new XMLHttpRequest();
        request.open("GET", url + (dontCache ? ((/\?/.test(url) ? "&" : "?") + "t=" + (new Date).getTime()) : ""), true);
        request.onload = function () {
            if (request.readyState == XMLHttpRequest.DONE) {
                response(request.response, request.status);
            }
        };
        request.send();
    };

    var Template = function Template(name) {
        if (name != "_delete") {
            this.name = name;
            this.htmlUrl = "templates/?name=" + this.name;
            this.cssUrl = "templates/" + this.name + "/style.css";
            this.htmlContent = null;
            this.hasLoaded = false;

            this.load = function (callback) {
                if (this.hasLoaded) {
                    callback();
                    return;
                }

                req(this.htmlUrl, true, (function (t, c) {
                    return function (response, status) {
                        if (status == 200) {
                            t.htmlContent = response;
                            t.hasLoaded = true;
                            c();
                        } else {
                            throw new Error("Couldn't load the template HTML");
                        }
                    };
                })(this, callback || new Function()));
            };
        } else {
            this.name = null;
            this.htmlUrl = null;
            this.cssUrl = null;
            this.htmlContent = null;
            this.hasLoaded = true;
            this.load = function (c) {
                c();
            };
        }
    };

    var usedTemplatesCount = 0;
    var usedTemplates = {};

    var Slide = function Slide(arr) {
        if (!arr.hasOwnProperty("ID")) {
            throw new Error("A slide must have an ID");
        } else if (!arr.hasOwnProperty("templateName")) {
            throw new Error("A slide must have a template");
        }

        this.id = arr["ID"];
        this.active = (arr["active"] || "0") == "1";
        this.playtime = Math.max(parseInt(arr["playtime"] || "10"), 0.3);
        this.text = arr["text"] || "";
        this.title = arr["title"] || "";
        this.image = null;
        this.timestamp = new Date(arr["timestamp"] || (new Date).toGMTString());
        this.hasMarkdown = (arr["mdEnabled"] || "1") == "1";

        var name = arr["templateName"];
        if (!usedTemplates[name]) {
            usedTemplates[name] = new Template(name);
            usedTemplatesCount++;
        }

        this.template = usedTemplates[name];

        var imageURL;
        try {
            var fromServer = arr["imageURL"];
            if (fromServer != undefined && fromServer != null) {
                imageURL = "upload_files/" + fromServer;
                console.log(imageURL);
            }
        } catch (_) {
        }

        this.load = function load(callback) {
            if (imageURL && !imageURL.empty() && imageURL !== "upload_files/") {
                this.image = new Image();
                this.image.onload = (function (callback) {
                    return function (e) {
                        callback(true, e);
                    };
                })(callback);
                this.image.onerror = (function (callback) {
                    return function () {
                        callback(false, event);
                    };
                })(callback);
                this.image.src = imageURL;
            } else
                callback();
        };
    };

    var playlist = function Playlist(arr) {
        if (!arr.hasOwnProperty("ID")) {
            throw new Error("A playlist must have an ID");
        }

        this.id = arr["ID"];
        this.name = arr["name"] || "Unnamed playlist";
        this.active = (arr["active"] || "0") == "1";
        this.global = (arr["global"] || "0") == "1";
        this.slides = [];

        for (var i in arr["slides"]) {
            this.slides.push(new Slide(arr["slides"][i]));
        }

        var currentIndex = 0;
        this.nextSlide = function () {
            var slide = this.slides[currentIndex++];
            if (slide)
                return slide;
            else {
                currentIndex = 0;
                return null;
            }
        };

        this.preload = function preload(callback) {
            if (this.slides.length == 0) {
                callback();
                return;
            }

            var loadedCount = 0;

            for (var i in this.slides) {
                var slide = this.slides[i];
                slide.load((function (t, callback) {
                    return function () {
                        if (++loadedCount == t.slides.length) {
                            callback();
                        }
                    };
                })(this, callback));
            }
        };
    };

    var preloadAllPlaylists = function preloadAllPlaylists(playlists, callback) {
        var loadedCount = 0;

        for (var i in playlists) {
            var playlist = playlists[i];
            playlist.preload(function () {
                if (++loadedCount == playlists.length) {
                    callback();
                }
            });
        }
    };

    var loadAllTemplates = function loadAllTemplates(callback) {
        var loadedCount = 0;

        for (var i in usedTemplates) {
            var template = usedTemplates[i];
            template.load(function () {
                if (++loadedCount == usedTemplatesCount) {
                    callback();
                }
            });
        }
    };

    window.getUsedTemplates = function getUsedTemplates() {
        return usedTemplates;
    };
    window.loadAllTemplates = loadAllTemplates;
    window.preloadAllPlaylists = preloadAllPlaylists;
    window.Playlist = playlist;
})();
