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
        var template = $('#template-div');
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
    $(".preview #title").text(titleValue);
    var textValue = $(".template-editor #inputText").val();
    if (textValue == "") {
        textValue = "Text"
    }

    var preview = $(".preview #text");
    preview.empty();
    preview.append(converter.makeHtml(textValue));
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


$(document).ready(function () {
    selectTemplate("../templates/", "2-templ");
});
