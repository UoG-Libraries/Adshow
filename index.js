var screen;
var playlists = [];
var currentPlaylistIndex = 0;
var currentTemplateName = "";
var transitionDuration = 1;

var converter = new showdown.Converter({tables: true, headerLevelStart: 2, strikethrough: true});

function loadScreen() {
	screen = localStorage.getItem("screen");

	if (screen !== null) {
		screen = JSON.parse(screen);
		if (screen)
			return true;
	}

	return false;
}

function apiCall(callID, paramName, paramVal, response, dontCache) {
	var url = "api.php?call=" + callID;
	if (paramName && paramVal) {
		url += "&" + paramName + "=" + paramVal;
	}
	
	if (dontCache) {
		url += "&t=" + (new Date).getTime();
	}

	if (typeof response !== "function" && typeof paramName === "function") {
		response = paramName;
	}

	var xReq = new XMLHttpRequest();
	xReq.open("GET", url, true);
	xReq.onload = function(e) {
		if (xReq.readyState == XMLHttpRequest.DONE) {
			var status = xReq.status;
			var re = JSON.parse(xReq.response);

			if (response)
				response(xReq.status == 200, re);
		}
	};

	xReq.send();
}

function addCss(fileName, id, load) {
	var head = document.head, link = document.createElement('link');

	link.onload = load;
	link.type = 'text/css';
	link.rel = 'stylesheet';
	link.href = fileName;
	link.id = id;

	head.appendChild(link);
}

function processPlaylistResponse(response) {
	for (var i in response) {
		var list = response[i];
		try {
			var playlist = new Playlist(list);
			playlists.push(playlist);
		} catch (e) {
			displayPresentationError("Odd response", "The server returned a strange response", "Please contact IT");
			console.error(e);
		}
	}
}

function startPresentation() {
	loadAllTemplates(function() {
		$("#presentation").removeChild($("#loading"));
		presentationLoop();
	});
}

function presentationLoop() {
	var playlist = playlists[currentPlaylistIndex];
	var slide = playlist.nextSlide();
	if (slide == undefined) {
		currentPlaylistIndex = (++currentPlaylistIndex) % playlists.length;
		presentationLoop();
		return;
	} else if (!slide.active) {
		presentationLoop();
		return;
	}
	
	
	var display = function() {
		// CONTENT
		var id = "slide_" + Math.ceil(Math.random() * 1000) + "_" + currentTemplateName;
		var element = createElem("div").setClass("slide");
		element.id = id;
		element.innerHTML = slide.template.htmlContent;
		
		$("#presentation").append(element);
		
		try {
			$("#presentation #"+id+" #text").innerHTML = converter.makeHtml(slide.text);
			$("#presentation #"+id+" #title").innerHTML = slide.title;
		} catch (e) {
			console.group("Can't display slide");
			console.error("Can't replace the content of the slide");
			console.error(e);
			console.groupEnd();
		}
		
		setTimeout(function() {
			element.style.left = "0";
		
			// remove old template stylesheet
			if (oldTemplateName != currentTemplateName) {
				setTimeout(function() {
					var oldOne = document.getElementById(oldTemplateName);
					if (oldOne) {
						document.head.removeChild(oldOne);
					}
				}, transitionDuration * 1000);
			}
			
			// after the transition
			setTimeout(function() {
				if ($("#presentation").children.length >= 2) {
					$("#presentation").removeChild($("#presentation").children[0]);
				}
			}, transitionDuration * 1000);
		
			// LOOP
			setTimeout(function() {
				presentationLoop();
			}, slide.playtime * 1000 + transitionDuration * 1000);
		}, 2);
	};

	// STYLE
	oldTemplateName = currentTemplateName;
	currentTemplateName = slide.template.name;
	if (currentTemplateName != oldTemplateName) {
		addCss(slide.template.cssUrl, currentTemplateName, display);
	} else {
		display();
	}
}

function displayPresentationError(title, error, desc) {
	$("#loading").style.display = "block";
	$("#loading")
	.removeContent()
	.append(textNode(title));

	$("#loading").append(createElem("div").setClass("spacer"));
	$("#loading").append(
		createElem("p")
		.setClass("smallFont")
		.append(
			textNode(error)
		)
	);

	$("#loading").append(
		createElem("p")
		.setClass("smallerFont")
		.append(textNode(desc))
	);
}

window.addEventListener("load", function() {
	if (!loadScreen()) {
		apiCall(1, function(success, response) {
			if (success) {
				var p = document.querySelector("#setup #container #loadingMessage");
				p.removeContent();

				if (response.length > 0) {
					p.style.display = "none";
					var container = $("#setup #container");
					var select = createElem("select");

					var departments = {};
					for (var i in response) {
						var screen = response[i];
						if (departments[screen["department"]]) {
							departments[screen["department"]].push(screen);
						} else {
							departments[screen["department"]] = [screen];
						}
					}

					for (var i in departments) {
						var optgroup = createElem("optgroup");
						optgroup.setAttribute("label", i);

						for (var j in departments[i]) {
							var screen = departments[i][j];
							var opt = createElem("option").append(textNode(screen["location"]));
							opt.screen = screen;
							optgroup.append(
								opt
							);
						}

						select.append(optgroup);
					}

					container.append(select);

					var chooseButton = createElem("button").setClass(["btn", "btn-default"]).append(textNode("Choose"));
					container.append(chooseButton);

					chooseButton.addEventListener("click", function(e) {
						var option = select.selectedOptions[0];
						var screen = option.screen;

						localStorage.setItem("screen", JSON.stringify(screen));
						location.reload();
					});
				} else {
					p.append(textNode("There are no screens configured yet"));
				}
			}
		});
	} else {
		// A screen was set in the local store

		$("#setup").style.display = "none";
		$("#presentation").style.display = "block";

		apiCall(2, "screen", parseInt(screen.ID), function(success, response) {
			console.log(response);
			if (success) {
				processPlaylistResponse(response);
				startPresentation();
			} else {
				displayPresentationError("Can't load slides.", response.error, response.desc)
			}
		}, true);
	}
});

function $(sel) {
	return document.querySelector(sel);
}

function createElem(e) {
	return document.createElement(e);
}

function textNode(txt) {
	return document.createTextNode(txt);
}

HTMLElement.prototype.setClass = function setClass(cls) {
	if (cls instanceof Array) {
		for (var i in cls) {
			this.classList.add(cls[i]);
		}
	} else {
		this.classList.add(cls);
	}

	return this;
}

HTMLElement.prototype.append = function append(child) {
	this.appendChild(child);
	return this;
}

HTMLElement.prototype.removeContent = function removeContent() {
	this.innerHTML = "";
	return this;
}

HTMLElement.prototype.setAttr = function setAttr(name, value) {
	this.setAttribute(name, value);
	return this;
}

