

/**
 * The onLoad function that loads the select2 library 
 */
function onLoad()
{
    $("#communication_property").select2(); 
    $("#communication_property").next().after("<input id='advanced_property_search_popup' value='Advanced Search' type='button'/>"); 
}

/**
 * handler for the advanced search button and handles information passed back 
 */
function advancedSearch()
{

}

$(onLoad);//when the page is done loading, run the onLoad function