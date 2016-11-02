var screen;
var errMsgCount = 0;
var playlists = [];
var currentPlaylistIndex = 0;
var currentTemplateName = "";
var transitionDuration = 1;

var converter = new showdown.Converter({
	tables: true, 
	headerLevelStart: 2, 
	strikethrough: true
});

var Call = {
	GET_AVAILABLE_SCREENS: 1,
	GET_SCREEN_DATA: 2,
	CHECK_FOR_CHANGES: 3
};

function loadScreen() {
	screen = localStorage.getItem("screen");

	if (screen !== null) {
		screen = JSON.parse(screen);
		if (screen)
			return true;
	}
	
	return false;
}

function apiCall(callID, params, response, dontCache) {
	var url = "api.php?call=" + callID;
	if (params) {
		for (var key in params) {
			var val = params[key];
			
			url += "&" + key + "=" + val;
		}
	}
	
	if (dontCache) {
		url += "&t=" + (new Date).getTime();
	}

	if (typeof response !== "function" && typeof params === "function") {
		response = params;
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
	
	xReq.ontimeout = function(e) {
		if (response)
			response(false, {
				error: "Timeout",
				desc: "The request timed out",
				code: 100
			})
	};
	
	xReq.timeout = 6000;
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
	
	if (playlists.length > 0) {
		playlists.unshift(splashScreen);
	}
}

function getPlaylistIndexWithPlaylistID(id) {
	for (var i in playlists) {
		var playlist = playlists[i];
		if (playlist.id == id)
			return i;
	}
	
	return -1;
}

function getSlideIndexOfPlaylistWithSlideID(playlist, slideID) {
	for (var i in playlist.slides) {
		var slide = playlist.slides[i];
		if (slide.id == slideID) 
			return i;	
	}
	
	return -1;
}

function startPresentation() {
	loadAllTemplates(function() {
		preloadAllPlaylists(playlists, function() {
			$("#presentation").removeChild($("#loading"));
			presentationLoop();
			updateLoop();
		});
	});
}

function updateLoop() {
	setTimeout(function() {
		var params = {
			screen: parseInt(screen.ID)
		};
		
		var timestamps = {};
		var leadingZero = function leadingZero(v) { 
			return v < 10 ? "0" + v : v; 
		};
		
		for (var i in playlists) {
			if (i == 0)
				continue; // Don't send the splash screen timestamp to the API
				
			var playlist = playlists[i];
			timestamps[playlist.id] = {};
			
			for (var j in playlist.slides) {
				var slide = playlist.slides[j];
				var time = slide.timestamp;
				timestamps[playlist.id][slide.id] = (
					  time.getFullYear() + "-" 
					+ (time.getMonth() + 1) + "-" 
					+ leadingZero(time.getDate()) + " " 
					+ leadingZero(time.getHours()) + ":" 
					+ leadingZero(time.getMinutes()) + ":" 
					+ leadingZero(time.getSeconds())
				);
			}
		}
		
		params["timestamps"] = JSON.stringify(timestamps);
		
		apiCall(Call.CHECK_FOR_CHANGES, params, function(success, response) {
			if (success) {
				if (response.length > 0) {
					// DEBUGGING:
					var playlistsAdded = 0;
					var slidesAdded = 0;
					var slidesChanged = 0;
					
					console.log(response);
					// END DEBUGGING
					
					for (var i in response) {
						var list = response[i];
						try {
							var newPlaylist = new Playlist(list);
							var index = getPlaylistIndexWithPlaylistID(newPlaylist.id);
							if (index >= 0) {
								var oldPlaylist = playlists[index];
								
								for (var j in newPlaylist.slides) {
									var newSlide = newPlaylist.slides[j];
									var slideIndex = getSlideIndexOfPlaylistWithSlideID(oldPlaylist, newSlide.id);
																		
									newSlide.load(function() {
										if (slideIndex >= 0) {
											if (!newSlide.template.hasLoaded) {
												newSlide.template.load(function() {
													oldPlaylist.slides[slideIndex] = newSlide;
													console.info("Changed a slide and loaded the new template"); // DEBUG
												});
											} else {
												oldPlaylist.slides[slideIndex] = newSlide;
												slidesChanged++; // DEBUG
											}
										} else {
											if (!newSlide.template.hasLoaded) {
												newSlide.template.load(function() {
													oldPlaylist.slides.push(newSlide);
													console.info("Added a slide and loaded the new template"); // DEBUG
												});
											} else {
												oldPlaylist.slides.push(newSlide);
												slidesAdded++; // DEBUG
											}
										}
									});
								}
							} else {
								playlists.push(newPlaylist);
								
								playlistsAdded++; // DEBUG
							}
						} catch (e) {
							console.error("Can't update playlist:");
							console.dir(e);
						}
					}
					
					// DEBUGGING:
					var summary = "";
					if (playlistsAdded > 0) {
						summary += playlistsAdded + " playlist" + (playlistsAdded == 1 ? "" : "s") + " added, ";
					} 
					if (slidesAdded > 0) {
						summary += slidesAdded + " slide" + (slidesAdded == 1 ? "" : "s") + " added, ";
					}
					if (slidesChanged > 0) {
						summary += slidesChanged + " slide" + (slidesChanged == 1 ? "" : "s") + " changed, ";
					}
					if (summary.length > 0) {
						console.info(summary.substr(0, summary.length - 2));
					}
					// END DEBUG
				}
			} else {
				console.group("Can't update changed playlists");
				console.error("While trying to refresh the playlists, an error occurred");
				console.error("Error: ");
				console.dir(response);
				console.groupEnd();
			}
			
			updateLoop();
		}, true);
	}, 10000)
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
		element.style.zIndex = "1000";
		
		$("#presentation").append(element);
		
		try {
			$("#presentation #"+id+" #title").innerHTML = slide.title;
		} catch (e) {
			if (errMsgCount < 10) {
				console.group("Can't display slide");
				console.error("Can't replace the title of the slide");
				console.error(e);
				console.groupEnd();
			}
			
			errMsgCount++;
		}
		
		try {
			$("#presentation #"+id+" #text").innerHTML = slide.hasMarkdown ? converter.makeHtml(slide.text) : slide.text;
		} catch (e) {
			if (errMsgCount < 10) {
				console.group("Can't fully display slide");
				console.info("Can't replace the content of the slide");
				console.log(e);
				console.groupEnd();
			}
			
			errMsgCount++;
		}
		
		if (slide.image) {
			try {
				var container = $("#presentation #"+id+" #imageWrapper");
				/*var rect = container.getBoundingClientRect();
				if (rect.width > 0) {
					slide.image.width = rect.width;
				}
				
				if (rect.height > 0) {
					slide.image.height = rect.height;
				}*/
				
				if (container.children.length > 0) {
					var oldImgTag = container.getElementsByTagName("img");
					if (oldImgTag.length > 0) {
						oldImgTag = oldImgTag[0];
						
						slide.image.className = oldImgTag.className;
						slide.image.alt = oldImgTag.alt;
					}
				}
				
				container.removeContent().append(slide.image);
			} catch (e) {
				
			}
		}
		
		if ($("#presentation").children.length >= 2) {
			$("#presentation").children[0].setClass("slideOut");
		}
		
		setTimeout(function() {
			//element.style.left = "0";
		
			// remove old template stylesheet
			if (oldTemplateName != currentTemplateName) {
				setTimeout(function() {
					var oldOne = document.getElementById(oldTemplateName);
					if (oldOne) {
						document.head.removeChild(oldOne);
					}
				}, transitionDuration * 1000 + 2);
			}
			
			// after the transition
			setTimeout(function() {
				if ($("#presentation").children.length >= 2) {
					$("#presentation").removeChild($("#presentation").children[0]);
				}
				
				element.style.zIndex = "100";
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
		apiCall(Call.GET_AVAILABLE_SCREENS, function(success, response) {
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

		apiCall(Call.GET_SCREEN_DATA, {"screen": parseInt(screen.ID)}, function(success, response) {
			console.log(response);
			if (success) {
				processPlaylistResponse(response);
				startPresentation();
			} else {
				displayPresentationError("Can't load slides.", response.error, response.desc);
			}
		}, true);
	}
});

// HELPERS
function $(sel) {
	if (document.querySelector)
		return document.querySelector(sel);
		
	if (/^#\S+$/.test(sel))
		return document.getElementById(sel);
	else if (/^\.\S+$/.test(sel))
		return document.getElementsByClassName(sel)[0];
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
};

HTMLElement.prototype.append = function append(child) {
	this.appendChild(child);
	return this;
};

HTMLElement.prototype.removeContent = function removeContent() {
	this.innerHTML = "";
	return this;
};

HTMLElement.prototype.setAttr = function setAttr(name, value) {
	this.setAttribute(name, value);
	return this;
};
