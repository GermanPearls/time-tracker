function trigger_table_cell_blur_event(elem) {
    jQuery(elem).parents().closest('td').dispatchEvent('onBlur');
}