﻿// This variable holds modified results from the search JSON that is reformatted in a way that semantic's autocomplete enjoys.
var autocompleteValues = [];

var viewModel = {
    results: ko.observableArray(),
    currentJSONRequest: null,
    getResults: function () {
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

            // Manipulate the results to fit in the autocomplete array in a way that semantic likes
            // Loop through all the results from the json
            for (var i = 0; i < jsonResults.length; i++)
            {
                // Each result is an object. Loop through all the properties of that object.
                for (var resultProp in jsonResults[i])
                {
                    // Get the value associated with the result
                    var resultVal = jsonResults[i][resultProp];
                    // check if it isn't already in the array or null, if so, put it in the results for the autocomplete. Also ignore the id field.
                    if (autocompleteValues.map(function (e) { return e.result }).indexOf(resultVal) == -1 && resultVal != null && resultProp != "id")
                    {
                        // Turn it into an object that semantic likes for the autocomplete
                        resultObj = { result: resultVal };
                        // Put it into the array
                        autocompleteValues.push(resultObj);
                    }
                }
            }

            // Register event handler for the select links, but ONLY if it is a popup box
            // Note this has to be here, otherwise jquery can't bind to an element that doesn't exist yet
            if ($('.js-isPopup').data('ispopup') == 1)
            {
                $('tr').click(postValue);
            }
            else // otherwise register handler for normal click
            {
                // Register a click handler for the row of the result (instead of a view button)
                // Note this also has to be done after updating the rows, otherwise new rows won't be affected.
                $('tr').click(function () {
                    // Get the id of the item from the bound data-id property of the row
                    var id = $(event.target).parent().data('id');
                    // Go to the URL
                    window.location = './' + id;
                });
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
    });

    /*
        Every time a key is pressed in the search box this event will check if timeOutInst is set.
        If it is set then we call clearTimeout to cancel the timeout function and set it to be null
        After this we call the setTimeout function to send an ajax call in 400 ms.
    */
    $('#searchBox').keyup(function () {
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
        searchFields: ['result']
    });

}

/**
 * Story 4e
 * Sends the chosen entity back to the parent page
 */
function postValue()
{
    // Get the id of the selected item
    var id = $(event.target).parent().data('id');
    // Send the information back to the parent page
    opener.receiveSelection(id);
    // Close the window
    // This beautiful delay is just long enough to make Mink not crash when it clicks the link, but the user won't notice it :)
    setTimeout(function () { window.close(); }, 10);
    //window.close();
}

$(onLoad);