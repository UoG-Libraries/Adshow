var screen;

function loadScreen() {
	screen = localStorage.getItem("screen");
	
	if (screen !== null) {
		screen = JSON.parse(screen);
		if (screen)
			return true;
	}
	
	return false;
}

function apiCall(callID, paramName, paramVal, response) {
	var url = "api.php?call=" + callID;
	if (paramName && paramVal) {
		url += "&" + paramName + "=" + paramVal;
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
				response(status == 200, re);
		}
	};
	
	xReq.send();
}

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
	}
});
