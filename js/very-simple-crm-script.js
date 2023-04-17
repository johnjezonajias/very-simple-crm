jQuery(document).ready(function($) {
    $('#very-simple-crm-form').on('submit', function(event) {
        // Prevent form submission
        event.preventDefault();

        var form = $(this);
        var dataPosts = {
            'action'           : 'customer_submission_ajax_handler',
            'customer_name'    : $('#very-simple-crm input[name="customer_name"]').val(),
            'customer_phone'   : $('#very-simple-crm input[name="customer_phone"]').val(),
            'customer_email'   : $('#very-simple-crm input[name="customer_email"]').val(),
            'customer_budget'  : $('#very-simple-crm input[name="customer_budget"]').val(),
            'customer_message' : $('#very-simple-crm textarea#customer_message').val()
        };

        $.ajax({
            url : very_simple_crm_params.ajaxurl,
            data : dataPosts,
            dataType: "text",
            type : 'POST',
            success : function(data){
                form.find('.customer-success').remove();
                form.append('<p class="customer-success">Thank you for your submission!</p>');
                form[0].reset();
            }
        });
    });

    $('#customer-form-clear').on('click', function() {
        // Reset form fields
        $('#very-simple-crm-form')[0].reset();
    });
});
