

/**
 * The onLoad function that loads the select2 library 
 */
function onLoad()
{
    // Load the select2 library (for searching in the dropdown) on the select box
    $("#communication_property").select2(); 
    // Insert an advanced search button beside the dropdown. This is so it's easy to insert instead of modifying the form everywhere it's needed.
    $("#communication_property").next().after("<input id='advanced_property_search_popup' value='Advanced Search' type='button'/>"); 

    // Register an event handler for clicking the button
    $("#advanced_property_search_popup").click(advancedSearch);

}

/**
 * handler for the advanced search button and handles information passed back 
 */
function advancedSearch()
{
    // Open a popup window to the search page
    window.open('/property/search?isPopup=true', "_blank","width=800, height=600");
}

function receiveSelection(id)
{
    alert("YOU PICKED " + id);
}

$(onLoad);//when the page is done loading, run the onLoad function