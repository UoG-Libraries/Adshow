(function() {
	"use strict";
	
	var req = function(url, response) {
		var request = new XMLHttpRequest();
		request.open("GET", url, true);
		request.onload = function() {
			if (request.readyState == XMLHttpRequest.DONE) {
				response(request.response, request.status);
			}
		};
		request.send();
	}
	
	var Template = function Template(name) {
		this.name = name;
		this.htmlUrl = "templates/" + this.name + "/template.html";
		this.cssUrl = "templates/" + this.name + "/style.css";
		this.htmlContent = null;
		
		this.load = function(callback) {
			req(this.htmlUrl, (function(t, c) {
				return function(response, status) {
					if (status == 200) {
						t.htmlContent = response;
						c();
					} else {
						throw new Error("Couldn't load the template HTML");
					}
				};
			})(this, callback || new Function()));
		};
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
		this.playtime = parseInt(arr["playtime"] || "10");
		this.text = arr["text"] || "";
		this.title = arr["title"] || "";
		
		var name = arr["templateName"];
		if (!usedTemplates[name]) {
			usedTemplates[name] = new Template(name);
			usedTemplatesCount++;
		}
		
		this.template = usedTemplates[name];
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
		this.nextSlide = function() {
			var slide = this.slides[currentIndex++];
			if (slide)
				return slide;
			else {
				currentIndex = 0;
				return null;
			}
		};
	};
	
	var loadAllTemplates = function loadAllTemplates(callback) {
		var loadedCount = 0;
		
		for (var i in usedTemplates) {
			var template = usedTemplates[i];
			template.load(function() {
				if (++loadedCount == usedTemplatesCount) {
					callback();
				}
			});
		}
	};
	
	var getUsedTemplates = function getUsedTemplates() {
		return usedTemplates;
	};
	
	window.getUsedTemplates = getUsedTemplates;
	window.loadAllTemplates = loadAllTemplates;
	window.Playlist = playlist;
})();