// Story 11d
// Loaded when the property view page is loaded.
// Handles the modal popup for adding a communication through the property view page.
var onLoad = function ()
{
    // Communication modal jQuery Object
    var communicationModal = $("#communicationModal");

    //specify that the modal should not automatically focus the first item
    communicationModal.modal({ autofocus: false });

    // Set a click handler for the new communication button to show the modal0
    $("#newCommunication").click(function ()
    {
        communicationModal.modal('show');
    });


    // Put a click handler on the communication add button so it can submit the form yet be outside of the form tags (due to styling issues)
    $("#communicationSubmit").click(function ()
    {
        // Get form, submit form
        $('#hiddenCommunicationSubmit').click();
    });

    //get the property select box
    propertyDropdown = $("#appbundle_communication_property");

    // Get the current property ID
    propertyId = $('.js-propertyid').data('propertyid');
    //Put the current property ID on the select box
    propertyDropdown.val(propertyId);
    // Apply the semantic styling so it's actually disabled
    propertyDropdown.parent().addClass('disabled');
    //Trigger the search select box to show the proper value
    propertyDropdown.dropdown('set selected', propertyId)

    // If the form was submitted invalid, the controller should signal us. Make the modal reappear so you can see the errors.
    if ($('.js-showcommunicationform').data('showcommunicationform'))
    {
        communicationModal.modal('show');
    }

    // Add click handlers for the tables so that clicking on a row will take you to it's view page
    // Communications table
    $('.communications tbody tr').click(function () {
        // Get the id of the item from the bound data-id property of the row
        var id = $(event.target).parent().data('id');
        // Go to the URL
        window.location = '../communication/' + id;
    });
    // Container table
    $('.containers tbody tr').click(function () {
        // Get the id of the item from the bound data-id property of the row
        var id = $(event.target).parent().data('id');
        // Go to the URL
        window.location = '../container/' + id;
    });
}

$(onLoad);