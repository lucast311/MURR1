//Story 40a
var viewModel = {
    results: ko.observableArray(),
    currentJSONRequest: null,
    getResults: function (sortOnField, direction)
    {
        if (!(sortOnField > -1)) sortOnField = 0;
        if (!(direction > -1)) direction = 0;

        //show loading,hidedata
        //$("#listInfo").show();
        $("#listInfo").removeClass("hidden");
        $("#listInfo").show();

        loadingInfo.show();
        listInfoMessage.hide();

        $("#filteredTruckListBody").hide();
        
        if (viewModel.currentJSONRequest != null)
        {
            viewModel.currentJSONRequest.abort();
            viewModel.currentJSONRequest = null;
        }
        // Figure out which json page to go to (this is passed from the controller to the twig and accessed like this)

        var page = './jsonfilter/';
        // Get the filter box text
        var filterText = $('#truckFilterBox').val();

        if ($(".spinner.loading.icon").length === 0) {
            $("#btnClear").after('<i class="spinner loading icon"></i>');
        }

        // do a json call to the server to get the results
        viewModel.currentJSONRequest = $.getJSON("./jsonfilter/" + filterText, {}, function (jsonResults) {
            // Callback function
            //hide loading thing
            loadingInfo.hide();
            $(".spinner.loading.icon").remove();
            listInfoMessage.show();

            // If no results came back, hide table and display message instead
            if (jsonResults.length === 0)
            {
                listInfoMessage.text("No results found");
                $("#filteredTruckListBody").hide();
                //$("#listInfo").show();
                $("#listInfo").removeClass("hidden");
                $("#listInfo").show();
            }
            else
            {
                //$("#listInfo").hide();
                $("#listInfo").addClass("hidden");
                $("#listInfo").hide();

                //$("#listInfo").css("display") = "none !important";
                $("#filteredTruckListBody").show();
            }

            // Set the results to be the returned results
            viewModel.results(jsonResults);
            setupRemoveModals();
        });
    }
};

var loadingInfo;
var listInfoMessage;
var timeOutInst = null;
var onLoad = function () {
    // apply the bindings
    ko.applyBindings(viewModel);

    listInfoMessage = $('<h2 class="listError">');
    listInfoMessage.text("No results found");
    listInfoMessage.appendTo("#listInfoContent");

    loadingInfo = $('<div class="ui big text active centered inline indeterminate loader">');//Loading...</div>
    //loadingImage = $('<img class="loadingGIF">'); //Equivalent: $(document.createElement('img'))
    //loadingImage.attr('src', 'https://media.giphy.com/media/ySeqU9tC1eFjy/200.gif');
    loadingInfo.text("Loading...");
    loadingInfo.appendTo("#listInfoContent");
    loadingInfo.hide();

    /*
        Every time a key is pressed in the filter box this event will check if timeOutInst is set.
        If it is set then we call clearTimeout to cancel the timeout function and set it to be null
        After this we call the setTimeout function to send an ajax call in 400 ms.
    */

    // Register a click handler for the clear button
    $('#btnClear').click(function () {
        // Clear the filter value
        if ($('#truckFilterBox').val() != "") {
            $('#truckFilterBox').val("");
            viewModel.getResults();
        }
    });

    // Register a click handler for typing a filter
    $('#truckFilterBox').keyup(function () {
        listInfoMessage.hide();
        loadingInfo.show();

        if (timeOutInst != null) {
            clearTimeout(timeOutInst);
            timeOutInst = null;
        }

        timeOutInst = setTimeout(function () { viewModel.getResults();}, 400);
    });//viewModel.getResults);

    viewModel.results($('.js-inittrucks').data('inittrucks'));
    $("#listInfo").addClass("hidden");
    $("#listInfo").hide();

    $('.js-inittrucks').remove();





    initModals();
}

function initModals()
{
    /*!WARNING!_CALLING addModal() MORE THAN ONCE WILL 
       GLITCH OUT MODALS DUE TO POOR IMPLEMENTATION!*/
    addModal();

    setupRemoveModals();
}

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

/**
 * STORY40a
 */
function setupRemoveModals()
{
    $('.removeButton').click(function () {
        var parent = $($(this).parent("form").get(0));
        showModal(parent.data('message'), parent);
    });

    $(".ui.dropdown").dropdown();

    enableRemoveModalButtons();
}

/**
 * STORY40a
 */
function enableRemoveModalButtons() {
    //set an event handler on the remove buttons that will call the custom method on the DeletePrompt.js Script
    $('.rmb').click(function (event) {
        clickedBtn = $(event.target);

        //if the i (changed to svg and path tags from font-awesome) tag inside the button was clicked, 
        //consider the button as clicked (because event.target sometimes gives you the i tag)
        //if the path was clicked these if statements will cascade up to the button tag
        if (clickedBtn.is('path')) clickedBtn = clickedBtn.parent();
        if (clickedBtn.is('svg')) clickedBtn = clickedBtn.parent();

        //get the ID that is stored in the form that this current button is in
        //This is the ID of the routePickup that was clicked
        id = clickedBtn.parent().attr('data-id');

        //call the prompt delete and pass in the necessary html things from the page
        promptDelete($("#rmmsg" + id), "Are you sure?", clickedBtn,
            $("#rmba" + id), $("#rmbc" + id));
    });

    //set an event handler on all the cancel buttons that will call the cancel prompt on the DeletePrompt.js script
    $('.rmbc').click(function (event) {
        clickedBtn = $(event.target);

    //if the i (changed to svg and path tags from font-awesome) tag inside the button was clicked, 
    //consider the button as clicked (because event.target sometimes gives you the i tag)
    //if the path was clicked these if statements will cascade up to the button tag
    if (clickedBtn.is('path')) clickedBtn = clickedBtn.parent();
        if (clickedBtn.is('svg')) clickedBtn = clickedBtn.parent();

        //get the stored ID from the form this button is in
        id = clickedBtn.parent().attr('data-id');

        //call the cancel prompt from the DeletePrompt.js script and pass in the necessery jquery objects
        cancelPrompt($("#rmmsg" + id), $('#rmb' + id), $('#rmba' + id), clickedBtn);
    });
}

$(onLoad);