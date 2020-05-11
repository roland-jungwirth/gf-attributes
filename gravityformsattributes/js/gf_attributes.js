jQuery(document).ready(function () {
    let field_type = 'gf-attributes';
    let element_selector = field_type + '-options';
    let max_sum = 10;

    // extract the form and field id
    let element_id = jQuery('.' + element_selector).attr('id');

    if (element_id) {
        // gf-attributes-14-24
        let form_id = element_id.substring(element_id.indexOf('-', field_type.length) + 1, element_id.lastIndexOf('-'));
        let field_id = element_id.substring(element_id.lastIndexOf('-') + 1);

        let hidden_field = jQuery('#' + element_id + 'hidden');

        // save the current value of the field on focus
        jQuery('.' + element_selector + ' input').live('focusin', function () {
            jQuery(this).data('val', jQuery(this).val());
        });

        jQuery('.' + element_selector + ' input').live('change', function() {
            let sum = 0;
            let field_value_array = [];
            jQuery('.' + element_selector + ' input').each(function () {
                let val = Number(jQuery(this).val());
                let id = jQuery(this).attr('id');
                sum += val;
                if (val > 0) {
                    field_value_array.push({'id': id.substring(id.indexOf('_') + 1), 'value': val});
                }
            });
            if (sum > max_sum) {
                jQuery('.gf_attributes_result').addClass('error');
                jQuery(this).val(jQuery(this).data('val'));
                // reset the focus
                jQuery(this).focus();
            } else {
                jQuery('#gf-attributes-remainder-' + form_id + '-' + field_id).html(max_sum - sum);
                // update the hidden field's value
                jQuery('#' + field_type + '-' + form_id + '-' + field_id + '-hidden').val(JSON.stringify(field_value_array));
            }
        });
    }
});
