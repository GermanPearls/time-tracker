function updateDatabase(editableObj, tablename, tablekey, column, id) {
	//**Ref: https://phppot.com/php/php-mysql-inline-editing-using-jquery-ajax/***/
	jQuery.ajax({
	  url: getDirectory.pluginURL + "/function-tt-update-table.php",
	  type: "POST",
	  data:'field='+column+'&value='+editableObj.innerHTML+'&id='+id+'&table='+tablename+'&id_field='+tablekey,
	  success: function(data){
		//editableObj.css("background","#FDFDFD");
	  }        
	});
  }