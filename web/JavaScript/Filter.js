﻿//Story 40a
var viewModel = {
    results: ko.observableArray(),
    currentJSONRequest: null,
    getResults: function () {
        if (viewModel.currentJSONRequest != null)
        {
            viewModel.currentJSONRequest.abort();
        }
        // Figure out which json page to go to (this is passed in from the twig)
        var page = './jsonfilter/';
        // Get the search box text
        var filterText = $('#form_filter_list').val();
        // Put the search box text after the page url
        page = page + filterText;
		
        // do a json call to the server to get the results
        viewModel.currentJSONRequest = $.getJSON("./jsonfilter/", {}, function (jsonResults) {
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

            // Register event handler for the select links, but ONLY if it is a popup box
            // Note this has to be here, otherwise jquery can't bind to an element that doesn't exist yet
            if ($('.js-isPopup').data('ispopup') == 1) {
                $('.popupSelectButton').click(postValue);
            }
        });
    }
};

var timeOutInst = null;
var onLoad = function () {
    // apply the bindings
    ko.applyBindings(viewModel);

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
 * Story 40a
 * Sends the chosen entity back to the parent page
 */
function postValue()
{
    // Get the id of the selected item
    var truckId = $(event.target).data('truckId');
    // Send the information back to the parent page
    opener.receiveSelection(truckId);
    // Close the window
    // This beautiful delay is just long enough to make Mink not crash when it clicks the link, but the user won't notice it :)
    setTimeout(function () { window.close(); }, 10);
    //window.close();
}

$(onLoad);