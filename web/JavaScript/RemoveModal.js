/**
 * Story 4k
 * Handles showing a modal used for deletions. Shows the passed in message and submits the passed in form
 * @param {any} message String message to be displayed
 * @param {any} formToSubmit JQuery object for the form to be submitted
 */
function showModal(message, formToSubmit)
{
    //get the modal
    var modal = $('#removeModal');

    //set the message in the modal
    $("#removeModalMessage").html(message);

    //show the modal
    modal.modal('show');

    //clicking the decline button
    $('#btnDecline').click(function () {
        modal.modal('hide');
    });

    //clicking the accept button
    $('#btnAccept').click(function () {
        modal.modal('hide');
        //submit the form
        form.submit();
    });
}

/**
 * Adds a removal modal the the page
 */
function addModal()
{
    $(document.body).append(`
    <div class="ui basic modal" id="removeModal">
        <i class="trash alternate outline icon"></i>
        <p id="removeModalMessage"></p>
        <button class="ui button inverted" id="btnDecline"><i class="close icon"></i>Cancel</button>
        <button class="ui button inverted red" id="btnAccept"><i class="check icon"></i>Remove</button>
    </div>`);
}