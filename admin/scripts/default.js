$(document).ready(function () {

    /*Navigation initialization*/
    var navigation = $('#navigation');
    var navShown = false;
    var navController = $('#nav-controller');
    if (navController) {
        navController.on('click', function () {
            if (navShown) {
                navigation.removeClass('show-nav');
                navigation.addClass('hide-nav');
                navShown = false;
            } else {
                navigation.removeClass('hide-nav');
                navigation.addClass('show-nav');
                navShown = true;
            }
        });
    }

    /*Bootstrap Popover initialization*/
    $(function () {
        $('[data-toggle="popover"]').popover()
    });

    /*Verical scorlling*/
    $(function () {
        function scrollHorizontally(e) {
            e = window.event || e;
            var delta = Math.max(-1, Math.min(1, (e.wheelDelta || -e.detail)));
            document.getElementById('horizontal-scrolling').scrollLeft -= (delta * 40); // Multiplied by 40
            e.preventDefault();
        }

        var horizontalScrolling = document.getElementById('horizontal-scrolling');
        if (horizontalScrolling) {
            if (horizontalScrolling.addEventListener) {
                // IE9, Chrome, Safari, Opera
                horizontalScrolling.addEventListener("mousewheel", scrollHorizontally, false);
                // Firefox
                horizontalScrolling.addEventListener("DOMMouseScroll", scrollHorizontally, false);
            } else {
                // IE 6/7/8
                horizontalScrolling.attachEvent("onmousewheel", scrollHorizontally);
            }
        }
    });

    /*File uplad button*/
    document.getElementById("uploadBtn").onchange = function () {
        document.getElementById("uploadFile").value = this.value;
    };
});