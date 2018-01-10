﻿var viewModel = {
    results: ko.observableArray(),
    getResults: function () {
        // Figure out which json page to go to (this is passed in from the twig)
        var page = $('.js-jsonpage').data('jsonpage');
        // Get the search box text
        var searchText = $('#searchBox').val();
        // Put the search box text after the page url
        page = page + "/" + searchText;
		
		alert("hi");
        // do a json call to the server to get the results
        $.getJSON(page, {}, function (jsonResults) {
            // Callback function

            //// Loop throup each result returned fromn the getJSON call
            //for (var i = 0; i < jsonResults.length; i++)
            //{
            //    // push each result onto the observableArray
            //    results.push(jsonResults[i]);
            //}
			alert(jsonResults);
			
            viewModel.results = ko.observableArray(jsonResults);
            //[{ "id": 1, "firstName": "Jim", "lastName": "Jimson", "role": "Property Manager", "companyName": "123-456-7890", "primaryPhone": null, "phoneExtension": null, "secondaryPhone": null, "emailAddress": null, "fax": "SIAST", "address": 1 }]
        });
    }
};

var onLoad = function () {
    // get the results - don't actaully off the bat or it will call the wrong url
    //viewModel.getResults();

    // apply the bindings
    ko.applyBindings(viewModel);

    // add an event handler to the search field
    $('#searchBox').keyup(viewModel.getResults);
};

$(onLoad);