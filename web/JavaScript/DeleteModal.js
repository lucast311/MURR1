
// Execute when the page loads
var onLoad = function ()
{
    // Register all modals on the page with semantic
    $('.ui.modal').modal();

    // Register a click handler for the delete buttion
    $("#delete").click(function ()
    {
        // show the modal
        $('.ui.modal.delete').modal('show');
    });

     // Register an event handler to clicking the modal delete (the real deal delete)
    $('ui negative right labeled icon button').click(function ()
    {
        $('div.ui.negative.labeled.icon.button form').submit();
    });
}

// Call the onload function
$(onLoad);