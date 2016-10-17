/*
 *
 * Ver: 0.2
 * Filename: default.js
 * Description: Basic jquery for Adshow webpages
 *
 * Author: Paul Griffiths
 *
 * Created 11.07.2013
 * Last modified: 10.03.2015
 *
 */

// set some defaults
var screenID = getURLParameter('id');
var currSlide = '0'; // current slide, initially set to 0
var currPlaylist = '0'; // current playlist, initially set to 0
var imgDir = 'images/lis/' // cannot send as JSON as PHP version does not support JSON_UNESCAPED_SLASHES
var title = '';


// when page is loaded
$(document).ready(function() {
	initShow();
});


/*
 * FUNCTIONS
 */


// initialise slideshow
function initShow() {
	// get defaults for layout, header text etc
	$.ajax({
		url: "api.php",
		type: "POST",
		data: {call:'getDefaults', screen:screenID},
		dataType: "json",
		success: function(data) {
			title = data.title;
			$('#header').append('<p id="title">' + title + '</p>');
		}
	});
	// show splash image
	$('body').append('<img id="splashImg" src="images/splashImg.jpg" />');
	$('#splashImg').fadeIn(1000, function() {
		$('#header').css('background-image','url("' + imgDir + 'templateHeader.png")').fadeIn(3000);
		$('#content').css('background-image','url("' + imgDir + 'templateContent.png")').fadeIn(3000, function (){
			// remove splash image and setup page defaults
			$('#splashImg').fadeOut(1000, function() {
				$('#splashImg').remove();
				$('#title').fadeIn(2000, function() {
					// run the slideshow
					runShow();
				});
			});
		});
	});
	//	
}

// run the slideshow
function runShow() {
	$.ajax({
		url: "nextSlide.php",
		type: "POST",
		data: {screen:screenID, slide:currSlide, playlist:currPlaylist},
		dataType: "json",
		success: function(data) {
			if (data.action && data.action =='reload') {
				$('.hidden').fadeOut(1000, function() {
					location.reload();
				});
			}
			// remove any existing content
			$('.hidden').fadeOut(1000, function() {
				$(this).remove();
			});
			// update defaults
			currSlide = data.slide;
			currPlaylist = data.playlist;
			// show the slide
			setTimeout(function (){
				showSlide(data);
			}, 1500);
		},
		complete: function () {
			setTimeout(runShow, 9000);
		}
	});
};

// display next slide
function showSlide(slideData) {

	// attach new content
	switch (slideData.template) {
		case "1":
			$('body').append('<img id="fullscreen" src="' + imgDir + slideData.content.img + '" class="hidden" width="100%" height="100%" />'); 
			break;
		case "2":
			$('#content').append('<div id="textOnlyVisBlockSmall" class="hidden"></div>');
			$('#content').append('<div id="textOnlyVisBlock" class="hidden"><p>' + slideData.content.head + '</p>');
			$('#textOnlyVisBlock').append('<p>' + slideData.content.sub + '</p>');
			break;
		case "3":
			$('#content').append('<div id="textLeftVisBlock" class="hidden"></div><p id="textLeft" class="hidden">' + slideData.content.head + '</p><ul class="hidden textList" id="textListLeft"></ul>');
			$.each(slideData.content.sub, function(i, text) {
				$('#textListLeft').append('<li>' + text + '</li>');
			});
			$('#content').append('<div id="imgRight" class="hidden"><img src="' + imgDir + slideData.content.img + '" /></div>');
			$('#content').append('<div id="textLeftVisBlockSmall" class="hidden"></div');
			break;
		case "4":
			$('#content').append('<div id="textRightVisBlock" class="hidden"></div><p id="textRight" class="hidden">' + slideData.content.head + '</p><ul class="hidden textList" id="textListRight"></ul>');
			$.each(slideData.content.sub, function(i, item) {
				$('#textListRight').append('<li>' + item + '</li>');
			});
			$('#content').append('<div id="imgLeft" class="hidden"><img src="' + imgDir + slideData.content.img + '" /></div>');
			$('#content').append('<div id="textRightVisBlockSmall" class="hidden"></div');
			break;
	}
	// show new content
	$('.hidden').fadeIn(1000);
}

// Get URL parameters
function getURLParameter(name) {
	return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)','i').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
}