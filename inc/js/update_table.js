function updateDatabase(editableObj, tablename, tablekey, column, id) {
	//**Ref: phppot.com/php/php-mysql-inline-editing-using-jquery-ajax ***/
	if ( editableObj.getElementsByTagName('select').length > 0 ) {
		var val = jQuery(editableObj).find("select option:selected").val();
	} else {
		var val = editableObj.innerHTML;
	}
	var send = {
		'security': wp_ajax_object_tt_update_table.security,
		'action': 'tt_update_table',
		'field': column,
		'value': val,
		'id': id,
		'table': tablename,
		'id_field': tablekey
	};
	jQuery.ajax({
	  action: 'tt_update_table',
	  url: wp_ajax_object_tt_update_table.ajax_url,
	  type: 'POST',
	  data: send,
	  success: function(response){
		  if (response.success) {
			//editableObj.css("background","#FDFDFD");
		  } else {
			  console.log('Database update failed. Attempt' + response.details + '. Error: ' + response.message);
		  }
	  }        
	});
  }