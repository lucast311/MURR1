var searchSelectBox;

/**
 * Story 4k
 * The onLoad function that loads the select2 library 
 */
function initialize(selectbox)
{

    searchSelectBox = selectbox;

    // Insert an advanced search button beside the dropdown. This is so it's easy to insert instead of modifying the form everywhere it's needed.
    //selectbox.after("<input class='ui button' id='advanced_property_search_popup' value='Advanced Search' type='button'/>"); 



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
    if ($(searchSelectBox).hasClass('ui dropdown'))
    {
        $(searchSelectBox).val(id);
    }
    else {
        // Obtain the select box and set it's value to be the recieved id. Need to trigger change for Select2 to update itself.
        //searchSelectBox.val(id);
        $(searchSelectBox).dropdown('set selected', id);
    }
}