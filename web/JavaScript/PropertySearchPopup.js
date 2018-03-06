

/**
 * Story 4e
 * The onLoad function that loads the select2 library 
 */
function onLoad()
{
    // Load the select2 library (for searching in the dropdown) on the select box
    $("#communication_property, #appbundle_container_property").select2(); 
    // Insert an advanced search button beside the dropdown. This is so it's easy to insert instead of modifying the form everywhere it's needed.
    $("#communication_property, #appbundle_container_property").next().after("<input id='advanced_property_search_popup' value='Advanced Search' type='button'/>"); 

    // Register an event handler for clicking the button
    $("#advanced_property_search_popup").click(function () { advancedSearch() });

}

/**
 * Story 4e
 * handler for the advanced search button to trigger a popup
 */
function advancedSearch()
{
    //Way to get the dynamically generated path from symfony
    path = $(".popupjs").attr("data-path");

    // Open a popup window to the search page
    window.open(path, "_blank","width=800, height=600");
}

/**
 * Story 4e
 * This is called by the popup to tell this page which item was picked. This page updates the select box with the picked value.
 * @param {any} id the id of the selected item
 */
function receiveSelection(id)
{
    // Obtain the select box and set it's value to be the recieved id. Need to trigger change for Select2 to update itself.
    $("#communication_property, #appbundle_container_property").val(id).trigger('change');
}

$(onLoad);//when the page is done loading, run the onLoad function