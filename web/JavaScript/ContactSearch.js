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
        $.getJSON(page, function () {
            // Callback function 

        });
    }
};


var onLoad = function () {

};