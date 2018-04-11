//Story 40c
var viewModel = {
    results: ko.observableArray(),
    currentJSONRequest: null,
    getResults: function (id, sortOnField, direction) {
        if (!(sortOnField > -1)) sortOnField = 0;
        if (!(direction > -1)) direction = 0;

        $("#listInfo").removeClass("hidden");
        $("#listInfo").show();

        loadingInfo.show();
        listInfoMessage.hide();

        $("#tPickupListBody").hide();

        if (viewModel.currentJSONRequest != null) {
            viewModel.currentJSONRequest.abort();
            viewModel.currentJSONRequest = null;
        }
        // Figure out which json page to go to (this is passed from the controller to the twig and accessed like this)

        var page = './jsonpickups/'+id;

        // do a json call to the server to get the results
        viewModel.currentJSONRequest = $.getJSON(page, {}, function (jsonResults) {
            // Callback function
            //hide loading thing
            loadingInfo.hide();
            listInfoMessage.show();

            // If no results came back, hide table and display message instead
            if (jsonResults.length === 0) {
                listInfoMessage.text("No results found");
                $("#tPickupListBody").hide();
                //$("#listInfo").show();
                $("#listInfo").removeClass("hidden");
                $("#listInfo").show();
            }
            else {
                //$("#listInfo").hide();
                $("#listInfo").addClass("hidden");
                $("#listInfo").hide();

                //$("#listInfo").css("display") = "none !important";
                $("#tPickupListBody").show();
            }

            // Set the results to be the returned results
            viewModel.results(jsonResults);
        });
    }
};

var loadingInfo;
var listInfoMessage;
var timeOutInst = null;


$(document).ready(function ()
{
    $(".templatePickups").hide();
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

    $("#listInfo").addClass("hidden");
    $("#listInfo").hide();

    $("#appbundle_route_startDate").parent().calendar({
        type: 'date', formatter: {
            date: function (date, settings) {
                var d = new Date(date),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2) month = '0' + month;
                if (day.length < 2) day = '0' + day;

                return [year, month, day].join('-');
            }
        }
    });

    $(".search.template.dropdown .menu .item").click(function () {
        templateId   = $(this).attr('data-value');
        templateName = $(this).text();
        console.log(templateId, templateName);
        $(".templatePickups").show();
        $(".templatePickups .header").text('"'+templateName+'"' + " Pickups");
        showTemplateData(templateId);
    });

    var showTemplateData = function (id) {
        viewModel.getResults(id);
    }
});


