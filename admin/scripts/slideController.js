/**
 * Created by Raphael Jenni on 21/10/2016.
 */

var vm = this;
vm.activeElement = null;
var converter = new showdown.Converter({tables: true, headerLevelStart: 2, strikethrough: true});

vm.addCss = function (fileName) {
    var link = '<link rel="stylesheet" type="text/css" href="' + fileName + '">'
    $('head').append(link)
};

vm.loadHtml = function (path) {
    $.get(path, function (content) {
        var template = $('#template-container');
        template.empty();
        template.append(content);
    }).then(function () {
        vm.updatePreview();
    })
};

vm.updatePreview = function () {
    var titleValue = $(".template-editor #inputTitle").val();
    if (titleValue == "") {
        titleValue = "Title"
    }
    $('#template-container').ready(function(){
        console.log($('#template-container').contents());
        $('#template-container').contents().find('h1#title').html('Hey, i`ve changed content of <body>! Yay!!!');
    });


    var textValue = $(".template-editor #inputText").val();
    if (textValue == "") {
        textValue = "Text"
    }

    var preview = $("iframe #text");
    preview.empty();
    preview.append(converter.makeHtml(textValue));

    var imageWrapper = $("#imageWrapper");
    imageWrapper.empty();
    imageWrapper.append("<img src='../upload_files/" + $("#imageURL").val() + "'/>");
};

function selectTemplate(baseDir, dir) {
    vm.loadHtml(baseDir + dir + "/template.html");
    vm.addCss(baseDir + dir + "/style.css");
    if (vm.activeElement) {
        vm.activeElement.removeClass("active-template")
    }
    vm.activeElement = $("#" + dir);
    vm.activeElement.addClass("active-template");
    $("#templateName").val(dir);
}

function updateImage() {
    var image = $("#imageURL");
    image.val($("#uploaded_image_name").val());

    vm.updatePreview();
}


$(document).ready(function () {
    var templateName = $("#templateName").val();
    console.log(templateName);
    // selectTemplate("../templates/", (templateName == "") ? "left_text-templ" : templateName);
    vm.updatePreview();

    $('#submitbtn').click(function () {
        $("#viewimage").html('');
        $(".uploadform").ajaxForm({
            target: '#viewimage'
        }).submit();
    });
});