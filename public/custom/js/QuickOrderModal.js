// Function using jQuery to call the modal
$('#quickOrderButton').click(function() {
    $('#modal-form-tables').modal('show');
});

// Or, if you are using pure (vanilla) JavaScript
document.getElementById('quickOrderButton').addEventListener('click', function() {
    var modal = document.getElementById('modal-form-tables');
    var bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
});