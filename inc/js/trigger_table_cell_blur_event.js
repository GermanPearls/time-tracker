function hasOnBlurHandler(element) {
    //Google AI
    //return !!jQuery(element).data('events') && !!jQuery(element).data('events').blur;
    return jQuery(element).on('blur');
  }
  
function trigger_table_cell_blur_event(elem) {
    prnt = jQuery(elem).parents().get(0);
    if (hasOnBlurHandler(prnt)) {
        prnt.onblur();
    } else {
        prnt_td = jQuery(elem).parents().closest('td').get(0);
        if (hasOnBlurHandler(prnt_td)) {
            prnt_td.onblur();
        }
    }
}