/**
 * This method is responsible for maniuplating an HTML page to prompt for a deletion
 * @param {any} messageTag Jquery object for the tag holding the prompt message
 * @param {any} message Message to show in the message tag
 * @param {any} deleteBtn Jquery object of the original delete button (to hide)
 * @param {any} acceptBtn Jquery object of the accept button (to show)
 * @param {any} CancelBtn Jquery object of the cancel button (to show)
 */
function promptDelete(messageTag, message, deleteBtn, acceptBtn, cancelBtn) {
    //give the message tag the desired message
    messageTag.html(message);

    //hide the original delete button
    deleteBtn.attr('hidden', 'hidden');

    //unhide the accept and cancel buttons
    acceptBtn.removeAttr('hidden');
    cancelBtn.removeAttr('hidden');
}

/**
 * This method is responsible for manipulating an HTML page to cancel the deletion prompt (created by the above method)
 * @param {any} messageTag Jquery object of the tag holding the prompt message (to now hide)
 * @param {any} deleteBtn Jquery object of the delete button (to now re-appear)
 * @param {any} acceptBrn Jquery object of the accept button (to now hide)
 * @param {any} cancelBtn Jquery object of the cancel button (to now hide)
 */
function cancelPrompt(messageTag, deleteBtn, acceptBtn, cancelBtn) {
    //Empty the message tag
    messageTag.html("");

    //show the delete button again
    deleteBtn.removeAttr('hidden');

    //hide the accept and cancel buttons
    acceptBtn.attr('hidden', 'hidden');
    cancelBtn.attr('hidden', 'hidden');
}