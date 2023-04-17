jQuery(document).ready(function($) {
    $('#very-simple-crm-form').on('submit', function(event) {
        // Prevent form submission
        event.preventDefault();

        /*var form = $(this);
        var formData = form.serialize();

        console.log(formData);

        $.ajax({
            url: form.attr('action'),
            //url: very_simple_crm_params.ajax_url,
            type: 'post',
            data: formData,
            success: function(response) {
                // Update DOM with success message
                form.find('.customer-success').remove();
                form.append('<p class="customer-success">Thank you for your submission!</p>');
                form[0].reset();
            }
        });*/

        var form = $(this);
        var formData = form.serialize();
        var dataPosts = {
            'action' : 'customer_submission_ajax_handler'
        };

        $.ajax({
            url : very_simple_crm_params.ajaxurl,
            data : dataPosts,
            dataType: "text",
            type : 'POST',
            success : function(data){
                var json = $.parseJSON(data);
                form.find('.customer-success').remove();
                form.append(json.message);
                form[0].reset();
            }
        });
    });

    $('#customer-form-clear').on('click', function() {
        // Reset form fields
        $('#very-simple-crm-form')[0].reset();
    });
});
