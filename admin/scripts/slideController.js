/**
 * Created by Raphael Jenni
 * 21/10/2016.
 */
'use strict';

//noinspection ThisExpressionReferencesGlobalObjectJS
var vm = this;
vm.activeElement = null;
var converter = new showdown.Converter({tables: true, headerLevelStart: 2, strikethrough: true});

vm.loadCss = function () {
    var iframe = document.getElementById('template-container');
    var innerDoc = iframe.contentDocument || iframe.contentWindow.document;

    var head = innerDoc.getElementsByTagName("head");

    var localStylesheet = document.createElement("link");
    localStylesheet.href = "style.css";
    localStylesheet.rel = "stylesheet";

    var bootstrapStylesheet = document.createElement("link");
    bootstrapStylesheet.href = "/css/bootstrap.min.css";
    bootstrapStylesheet.rel = "stylesheet";

    head[0].appendChild(localStylesheet);
    head[0].appendChild(bootstrapStylesheet);
};

vm.loadHtml = function (base) {
    var iframe = document.getElementById('template-container');
    iframe.src = base + "/template.html";
};

function selectTemplate(baseDir, dir) {
    vm.loadHtml(baseDir + dir);
    if (vm.activeElement) {
        vm.activeElement.removeClass("active-template")
    }
    vm.activeElement = $("#" + dir);
    vm.activeElement.addClass("active-template");
    $("#templateName").val(dir);
}

vm.updatePreview = function () {
    var iframe = document.getElementById('template-container');
    var innerDoc = iframe.contentDocument || iframe.contentWindow.document;

    /*==========Title==========*/
    var titleValue = $(".template-editor #inputTitle").val();
    if (titleValue == "") {
        titleValue = "Title"
    }

    var title = innerDoc.getElementById("title");
    if (title) {
        title.innerHTML = titleValue;
    }

    /*==========Text==========*/
    var textValue = $(".template-editor #inputText").val();
    if (textValue == "") {
        textValue = "Text"
    }

    var text = innerDoc.getElementById("text");
    if (text) {
        var isMarkdownEnabled = document.getElementById("enableMarkdown").checked;
        var htmlText = isMarkdownEnabled ? converter.makeHtml(textValue) : textValue;
        htmlText = htmlText.replace(/\<table/mg, "<table class='table' ");
        text.innerHTML = htmlText;
    }

    iframe.className = iframe.className.replace(" hidden", "");

};

vm.updateImage = function () {
    var iframe = document.getElementById('template-container');
    var innerDoc = iframe.contentDocument || iframe.contentWindow.document;

    /*==========Image==========*/
    var imageWrapper = innerDoc.getElementById("imageWrapper");
    var imageUrl = $("#imageURL").val();
    if (imageWrapper && imageUrl != "") {
        var images = imageWrapper.getElementsByTagName("img");
        if (images.length > 0) {
            for (var i = 0; i < images.length; i++) {
                images[i].src = '../../upload_files/' + imageUrl;
            }
        } else {
            imageWrapper.innerHTML = "<img src='../../upload_files/" + imageUrl + "'/>";
        }
    }
};

//noinspection JSUnusedGlobalSymbols
function updateImagePath() {
    var image = $("#imageURL");
    image.val($("#uploaded_image_name").val());
    vm.updateImage();
}


$(document).ready(function () {
    var templateName = $("#templateName").val();
    selectTemplate('../templates/', templateName);

    var myIframe = document.getElementById('template-container');
    myIframe.className += ' hidden';
    myIframe.addEventListener("load", function () {
        vm.loadCss();
        vm.updateImage();
        vm.updatePreview();
    });

    $('#submitbtn').click(function () {
        $("#viewimage").html('');
        $(".uploadform").ajaxForm({
            target: '#viewimage'
        }).submit();
    });
});