/**
 * Story 4k
 * Handles showing a modal used for deletions. Shows the passed in message and submits the passed in form
 * @param {any} message String message to be displayed
 * @param {any} formToSubmit JQuery object for the form to be submitted
 */
function showModal(message, formToSubmit)
{
    $(document.body).append(`
<div class="ui basic modal" id="removeModal">
    <i class="trash alternate outline icon"></i>
    <p>` + message + `</p >
    <button class="ui button inverted red" id="btnDecline"><i class="close icon"></i>No</button>
    <button class="ui button inverted green"><i class="check icon" id="btnAccept"></i>Yes</button>
</div>`);

    var modal = $('#removeModal');

    modal.modal('show');

    $('#btnDecline').click(function () {
        modal.modal('hide');
    });

    $('#btnAccept').click(function () {
        modal.modal('hide');
    });
}