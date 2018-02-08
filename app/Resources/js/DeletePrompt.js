/**
 * This method is responsible for maniuplating an HTML page to prompt for a deletion
 * @param {any} messageTag Jquery object for the tag holding the prompt message
 * @param {any} message Message to show in the message tag
 * @param {any} deleteBtn the original delete button (to hide)
 * @param {any} acceptBtn the accept button (to show)
 * @param {any} CancelBtn the cancel button (to show)
 */
function promptDelete(messageTag, message, deleteBtn, acceptBtn, CancelBtn) {
    //give the message tag the desired message
    messageTag.html(message);

    //hide the original delete button
    deleteBtn.attr('hidden', 'hidden');

    //unhide the accept and cancel buttons
    acceptBtn.removeAttr('hidden');
    cancelBtn.removeAttr('hidden');
}