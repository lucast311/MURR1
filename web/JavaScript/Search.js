var viewModel = {
    results: ko.observableArray(),
    getResults: function () {
        // Figure out which json page to go to (this is passed in from the twig)
        var page = $('.js-jsonpage').data('jsonpage');
        // Get the search box text
        var searchText = $('#searchBox').val();
        // Put the search box text after the page url
        page = page + "/" + searchText;
        // do a json call to the server to get the results
        $.getJSON(page, function (jsonResults) {
            // Callback function

            // Loop throup each result returned fromn the getJSON call
            for (var i = 0; i < jsonResults.length; i++)
            {
                // push each result onto the observableArray
                results.push(jsonResults[i]);
            }
        });
    }
};

var onLoad = function () {
    // get the results
    viewModel.getResults();

    // apply the bindings
    ko.applyBindings(viewModel);

    // add an evnt handler to the search field
    $('#searchBox').change(viewModel.getResults);
};

$(onLoad);