jQuery(document).ready(function($) {
    // Reveal variations based on selected bundle
    $('input[name="cod_bundle"]').on('change', function() {
        var bundleId = $(this).val();
        
        // Hide all variation rows
        $('.bundle-variations-box').slideUp();
        
        // Remove active class from all bundles
        $('.bundle-option-box').removeClass('active-bundle');
        
        // Show variations for selected bundle
        $('#bundle_variations_' + bundleId).slideDown();
        $(this).closest('.bundle-option-box').addClass('active-bundle');
    });

    // Handle Color Swatch Selection
    $('.color-swatch').on('click', function() {
        $(this).siblings().removeClass('selected');
        $(this).addClass('selected');
        var color = $(this).data('color');
        // Save to hidden input
        $(this).closest('.variation-row').find('.selected-color').val(color);
    });

    // Handle form submission via AJAX
    $('#custom_cod_checkout_form').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var submitBtn = form.find('.submit-cod-btn');
        var originalText = submitBtn.text();
        
        // Basic Validation
        var name = form.find('input[name="cod_name"]').val();
        var phone = form.find('input[name="cod_phone"]').val();
        var state = form.find('input[name="cod_state"]').val();
        if (!name || !phone || !state) {
            alert('يرجى تعبئة الحقول الأساسية (الاسم، الهاتف، الولاية)');
            return;
        }

        submitBtn.text('جاري المعالجة...').prop('disabled', true);

        // Gather Data
        var formData = new FormData(this);
        formData.append('action', 'submit_cod_order');
        formData.append('security', cod_ajax.nonce);
        
        $.ajax({
            url: cod_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    window.location.href = response.data.redirect_url;
                } else {
                    alert('حدث خطأ: ' + response.data.message);
                    submitBtn.text(originalText).prop('disabled', false);
                }
            },
            error: function() {
                alert('حدث خطأ بالاتصال، يرجى المحاولة مرة أخرى.');
                submitBtn.text(originalText).prop('disabled', false);
            }
        });
    });
});
