//S40C
var handleRouteAndTemplateSearchResults;
//onload
$(function(){
    //$('.top.menu .item').tab();
    $('.top.menu .item').tab()[0].click();

    //NEEDS "Search.js"
    hideResultsAndMessage = function()
    {
        $(".routeResults table").hide();
        $(".routeResults .message").text("Loading...");

        $(".templateResults table").hide();        
        $(".templateResults .message").text("Loading...");
    }    
    hideResultsAndMessage();
    
    handleRouteAndTemplateSearchResults = function(jsonResults)
    {
        var resultsHaveRoute    = false;
        var resultsHaveTemplate = false;
        var weekdays = 'Mon Tue Wed Thu Fri Sat Sun'.split()
        for(var i = 0; i < jsonResults.length; i++)
        {
            //if (jsonResults[i].startDate != null) jsonResults[i].startDate = new Date(Date(jsonResults[i].startDate)).toLocaleDateString();
            
            //if (jsonResults[i].endDate != null) jsonResults[i].endDate = new Date(Date(jsonResults[i].endDate)).toLocaleDateString();

            if(jsonResults[i].template) resultsHaveTemplate = true; 
            else resultsHaveRoute = true;
                        
            if(resultsHaveRoute && resultsHaveTemplate) i = jsonResults.length
        }

        //Routes
        if(resultsHaveRoute){
            $(".routeResults table").show();
            $(".routeResults .message").text("");
        }else{
            $(".routeResults table").hide();
            $(".routeResults .message").text("No results found");
        }

        //Route Templates
        if(resultsHaveTemplate){
            $(".templateResults table").show();
            $(".templateResults .message").text("");
        }else{
            $(".templateResults table").hide();
            $(".templateResults .message").text("No results found");
        }
    }

    $('#newRouteOptions').click(function () {
        $('.ui.new.route.options.modal').modal('show');
    });
});