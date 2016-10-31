/**
 * Created by Raphael Jenni on 21/10/2016.
 */

var vm = this;
vm.activeElement = null;
var converter = new showdown.Converter({tables: true, headerLevelStart: 2, strikethrough: true});

vm.loadCss = function () {
    var iframe = document.getElementById('template-container');
    var innerDoc = iframe.contentDocument || iframe.contentWindow.document;

    var head = innerDoc.getElementsByTagName("head");
    var element = document.createElement("link");
    element.href = "style.css";
    element.rel = "stylesheet";
    head[0].appendChild(element)
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

    var text = innerDoc.getElementById("content");
    if (text) {
        text.innerHTML = converter.makeHtml(textValue);
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
        imageWrapper.innerHTML = "<img src='../../upload_files/" + imageUrl + "'/>";
    }
};

function updateImagePath() {
    var image = $("#imageURL");
    image.val($("#uploaded_image_name").val());
}


$(document).ready(function () {
    var templateName = $("#templateName").val();
    console.log(templateName);

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