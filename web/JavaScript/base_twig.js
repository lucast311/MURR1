/**
    Simple functions that are loaded on every page.
*/
$(function () {

    sideNav = $("#sideNav");

    /*
     * This is a callback function used whenever the page resizes to run javascript
     */
    function mediaQuery(x)
    {
        //javascript to run if the view is mobile
        if (x.matches)
        {
            sideNav.removeClass("visible");
        }
        //javascript to run if the view is destkop
        else
        {
            sideNav.addClass("visible");
        }
    }

    //Specify that mobile is a window smaller than 767px
    var x = window.matchMedia("(max-width: 767px)");
    //call the function initially on page load
    mediaQuery(x);
    //add the media query listener for whenever the page resizes
    x.addListener(mediaQuery);


    //Specify a click handler on the hamburger button to open up the sidebar navigation
    $("#openMenu").click(function () {
        sideNav.sidebar('show');
    });

    //find the horizontal navigation bar
    horizontalNav = $('horizontalNav');
    //make it sticky based on the context of the page content
    //Note that this only sticks on mobile
    horizontalNav.sticky({ context: "#pageContent" });

    //This will refresh the sticky any time the page content changes (because semantic event handlers aren't always smart enough)
    $("#pageContent").bind("DOMSubtreeModified", function () {
        horizontalNav.sticky("refresh");
    });

    //This will enable radio buttons on any page to be clicked and selected (otherwise clicking them does nothing)
    $('.ui.checkbox').checkbox();

    //This will enable javascript on any semantic select box on the page (or else it gets gross styles)
    $(".ui.dropdown").dropdown();


});