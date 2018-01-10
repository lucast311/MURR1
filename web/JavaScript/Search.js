var viewModel = {
    results: ko.observableArray(),
    getResults: function () {
        // Figure out which json page to go to (this is passed in from the twig)
        var page = $('.js-jsonpage').data('jsonpage');
        // Get the search box text
        var searchText = $('#searchBox').val();
        // Put the search box text after the page url
        page = page + searchText;
		
        // do a json call to the server to get the results
        $.getJSON(page, {}, function (jsonResults) {
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
        });
    }
};

var onLoad = function () {
    // apply the bindings
    ko.applyBindings(viewModel);

    // add an event handler to the search field
    $('#searchBox').keyup(viewModel.getResults);
};

$(onLoad);