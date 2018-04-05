$(function () {
    initModals();
});

function initModals(){
    /*!WARNING!_CALLING addModal() MORE THAN ONCE WILL 
       GLITCH OUT MODALS DUE TO POOR IMPLEMENTATION!*/
    addModal();
    setupRemoveModals();
}

/**
 * Story 40b
 * Sends the chosen entity back to the parent page
 */
function postValue(){
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
 * STORY40b
 */
function setupRemoveModals(){
    $('.removeButton').click(function () {
        var parent = $($(this).parent("form").get(0));
        showModal(parent.data('message'), parent);
    });

    $(".ui.dropdown").dropdown();

    enableRemoveModalButtons();
}

/**
 * STORY40b
 */
function enableRemoveModalButtons() {
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