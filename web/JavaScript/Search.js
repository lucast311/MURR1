// This variable holds modified results from the search JSON that is reformatted in a way that semantic's autocomplete enjoys.
var autocompleteValues = [];

var viewModel = {
    results: ko.observableArray(),
    currentJSONRequest: null,
    getResults: function (reQuery) {

        if (reQuery === undefined)
        {
            reQuery = false;
        }

        if (viewModel.currentJSONRequest != null)
        {
            viewModel.currentJSONRequest.abort();
        }
        // Figure out which json page to go to (this is passed in from the twig)
        var page = $('.js-jsonpage').data('jsonpage');
        // Get the search box text
        var searchText = $('#searchBox').val();
        // Put the search box text after the page url
        page = page + searchText;

        // Start a spinny thingy to tell user search is occuring
        // BUT ONLY IF ITS NOT ALREADY THERE
        if ($(".spinner.loading.icon").length === 0)
        {
            $("#btnClear").after('<i class="spinner loading icon"></i>');
        }
        
		
        // do a json call to the server to get the results
        viewModel.currentJSONRequest = $.getJSON(page, {}, function (jsonResults) {
            // Callback function

            // If no results came back, hide table and display message instead
            if (jsonResults.length === 0)
            {
                $("table").hide();
                $("#message").text("No results found");
            }
            else
            {
                $("table").show();
                $("#message").text("");
            }

            // Set the results to be the returned results
            viewModel.results(jsonResults);

            // Remove the spinny thingy
            $(".spinner.loading.icon").remove();

            // Only proceed with the array manipulation if this isn't a requery
            // This is to prevent glitches that may occur if you try to autocomplete on an already selected autocomplete result.
            if (!reQuery)
            {
                // Manipulate the results to fit in the autocomplete array in a way that semantic likes
                // Loop through all the results from the json
                for (var i = 0; i < jsonResults.length; i++) {
                    // Each result is an object. Loop through all the properties of that object.
                    for (var resultProp in jsonResults[i]) {
                        // Get the value associated with the result
                        var resultVal = jsonResults[i][resultProp];
                        // check if it isn't already in the array or null, if so, put it in the results for the autocomplete. Also ignore the id field.
                        if (autocompleteValues.map(function (e) { return e.title }).indexOf(resultVal) == -1 && resultVal != null && resultProp != "id") {
                            // Turn it into an object that semantic likes for the autocomplete
                            resultObj = { title: resultVal };
                            // Put it into the array
                            autocompleteValues.push(resultObj);
                        }
                    }
                }

                // Re-call autocomplete to cause it to update itself with the new array
                autoComplete();
            }


            if (window.location.href.indexOf("search") > -1) //click handler for a dedicated search page
            {
                // Register a click handler for the row of the result (instead of a view button)
                // Note this also has to be done after updating the rows, otherwise new rows won't be affected.
                $('tbody tr').click(function () {
                    // Get the id of the item from the bound data-id property of the row
                    var id = $(event.target).parent().data('id');
                    // Go to the URL
                    window.location = './' + id;
                });
            }
            else // otherwise register handler for normal click
            {
                if ($('#searchTable').data() != null) {
                    $('tbody tr').click(postValue);
                }
                else {
                    $('.ui.celled.table.selectable tbody tr').click(function () {
                        //console.log($(event.target).parent().data('entity'));

                        var id = $(event.target).parent().data('id');
                        var entity = $(event.target).parent().data('entity');
                        // Go to the URL
                        window.location = '/' + entity + '/' + id;
                    });
                }
            }
            
        });
    }
};

var timeOutInst = null;
var onLoad = function () {
    // apply the bindings
    ko.applyBindings(viewModel);

    // Run the code to make autocomplete work
    autoComplete();

    if ($('#searchBox').val() == "") {
        viewModel.getResults();
    }

    // get results if there is any text in the searchbox on load
    //fixes issue where it wouldn't get the data when you went back to the page
    if ($('#searchBox').val() != "")
    {
        viewModel.getResults();
    }

    // Register a click handler for the clear button
    $('#btnClear').click(function () {
        // Clear the searchbox value
        var searchText = $('#searchBox').val("");
        // clear the results
        viewModel.results([]);
        // Do some aesthetic fixes
        $("table").hide();
        $("#message").text("");

        viewModel.getResults();
    });

    /*
        Every time a key is pressed in the search box this event will check if timeOutInst is set.
        If it is set then we call clearTimeout to cancel the timeout function and set it to be null
        After this we call the setTimeout function to send an ajax call in 400 ms.
    */
    $('#searchBox').keyup(function () {
        lastVal = $('#searchBox').val();
        console.log(lastVal);
        if (timeOutInst != null) {
            clearTimeout(timeOutInst);
            timeOutInst = null;
        }

        timeOutInst = setTimeout(function () { viewModel.getResults();}, 400);
    });//viewModel.getResults);
};

/**
 * Story 12e
 * Implemetation of the auto complete functionality on the search box
 */
var autoComplete = function ()
{
    // Get the search box and apply the semantic search functionality
    // It will search the array for objects with title 'result'
    // This array is generated in getResults
    $(".ui.search").search({
        source: autocompleteValues,
        searchFields: ['title'],
        cache: false,
        fullTextSearch: "true",
        showNoResults: false,
        // The handler for when someone clicks a result.
        onSelect: function () { setTimeout(function () { viewModel.getResults(true); }, 110) }
    });

    // Force a re-query of the search box (since the array has likely changed since the user finished typing);
    $("#propertySearch").search('query');
    $("#contactSearch").search('query');
    //$(".ui.search").search('query');
}

/**
 * Story 4e
 * Sends the chosen entity back to the parent page
 */
function postValue() {
    // Get the id of the selected item
    var id = $($(this).get(0)).data('id');

    // Send the information back to the parent page
    receiveSelection(id);

    // Close the modal
    $('#propertyModal').modal('hide');
}

/**
 * Story 4e
 * This is called by the popup to tell this page which item was picked. This page updates the select box with the picked value.
 * @param {any} id the id of the selected item
 */
function receiveSelection(id)
{
    $('#appbundle_communication_property').dropdown('set selected', id);
}

$(onLoad);