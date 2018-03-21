/**
    Simple functions that are loaded on every page.
*/
$(function () {
    // Everytime the #menuBtn button is clicked the sidebar will be toggled on or off.
    $("#menuBtn").click(function () {
        // Jquery selector for the sidebar
        $('.ui.sidebar')
            // Sets how the sidebar will be displayed
            .sidebar('setting', 'transition', 'push')
            .sidebar('toggle');
    });

    // selects all ui elements with the sticky class
    // The sticky() function will have the stickied element follow the page
    // until it hits the .footer element. (the bottom of the page)
    $('.ui.sticky').sticky({ context: '.footer' });

    //This will enable radio buttons on any page to be clicked and selected (otherwise clicking them does nothing)
    $('.ui.checkbox').checkbox();

    //This will enable javascript on any semantic select box on the page (or else it gets gross styles)
    $(".ui.dropdown").dropdown();
});