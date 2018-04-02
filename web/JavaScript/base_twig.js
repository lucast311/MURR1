/**
    Simple functions that are loaded on every page.
*/
$(function () {
    sideNav = $("#sideNav");

    function mediaQuery(x) {
        //javascript to run if the view is mobile
        if (x.matches) { 
            sideNav.removeClass("visible");
        }
        //javscript to run if the view is desktop
        else {
            sideNav.addClass("visible");
        }
    }

    var x = window.matchMedia("(max-width: 767px)")
    mediaQuery(x) // Call listener function at run time
    x.addListener(mediaQuery) // Attach listener function on state changes

    $("#openMenu").click(function () {
        sideNav
            .sidebar('show');
    })

    //// Everytime the #menuBtn button is clicked the sidebar will be toggled on or off.
    //$("#menuBtn").click(function () {
    //    // Jquery selector for the sidebar
    //    $('.ui.sidebar')
    //        // Sets how the sidebar will be displayed
    //        .sidebar('setting', 'transition', 'push')
    //        .sidebar('toggle');
    //});

    //Makes the horizontal navigation menu sticky based on the context of the page content (will only work on mobile
    horizontalNav = $('#horizontalNav')
    horizontalNav.sticky({
        context: "#pageContent"
    });
    //this will reset the sticky any time page content changes, because otherwise the sticky will not adjust to the changes
    $('#pageContent').bind('DOMSubtreeModified', function () {
        horizontalNav.sticky("refresh");
    });

    //This will enable radio buttons on any page to be clicked and selected (otherwise clicking them does nothing)
    $('.ui.checkbox').checkbox();

    //This will enable javascript on any semantic select box on the page (or else it gets gross styles)
    $(".ui.dropdown").dropdown();


});