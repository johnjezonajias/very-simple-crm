jQuery(document).ready(function($) {
    $('#very-simple-crm-form').on('submit', function(event) {
        // Prevent form submission
        event.preventDefault();

        var form = $(this);
        var name = $('input#customer_name').val();
        var email = $('input#customer_email').val();

        $.ajax({
            url: crm.ajaxurl,
            type: 'post',
            data: {
                action: 'customer_submission',
                name: name,
                email: email,
            },
            success: function(response) {
                // Update DOM with success message
                form.find('.customer-success').remove();
                form.append('<p class="customer-success">Thank you for your submission.</p>');
                form[0].reset();
            },
            error: function(response) {
                // Handle error
                console.error('Error:', response.responseText);
                // Update UI, show error message, etc.
            }
        });
    });

    $('#customer-form-clear').on('click', function() {
        // Reset form fields
        $('#very-simple-crm-form')[0].reset();
    });
});
