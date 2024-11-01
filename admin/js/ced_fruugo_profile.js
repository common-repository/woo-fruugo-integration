
jQuery(document).ready(function(){
	jQuery(document.body).on("click",".ced_fruugo_profile",function(){
		var prodId = jQuery(this).attr("data-proid");
		jQuery(".ced_fruugo_save_profile").attr("data-prodid",prodId);
		jQuery(".ced_fruugo_overlay").show();
	});


	jQuery(document.body).on("click",".ced_fruugo_overlay_cross",function(){
		jQuery(".ced_fruugo_overlay").hide();
	})
	jQuery(document.body).on("click",".umb_remove_profile",function(){

		var proId     = jQuery(this).attr("data-prodid");
		jQuery("#ced_fruugo_marketplace_loader").show();
		var profileId = 0;
		var data  = {
						"action"    : "ced_fruugo_save_profile",
						"proId"     : proId,
						"profileId" : profileId
					}
		jQuery.post(
					profile_action_handler.ajax_url,
					data,
					function(response)
					{
						jQuery("#ced_fruugo_marketplace_loader").hide();

						jQuery(".ced_fruugo_overlay").hide();
						if(response != "success")
						{
							alert("Failed");
						}
						else
						{
							window.location.reload();
						}	
					}
				)
			  .fail(function() {
				  jQuery("#ced_fruugo_marketplace_loader").hide();
				  alert( "Failed" );

			  })
	})
	
	jQuery(document.body).on("click",".ced_fruugo_save_profile",function(){

		var proId     = jQuery(this).attr("data-prodid");
		jQuery("#ced_fruugo_marketplace_loader").show();

		var profileId = jQuery(".ced_fruugo_profile_select option:selected").val();
		var data  = {
						"action"    : "ced_fruugo_save_profile",
						"proId"     : proId,
						"profileId" : profileId
					}
		jQuery.post(
					profile_action_handler.ajax_url,
					data,
					function(response) {
						jQuery("#ced_fruugo_marketplace_loader").hide();

						jQuery(".ced_fruugo_overlay").hide();
						if(response != "success") {
							alert("Failed");
						}
						else {
							window.location.reload();
						}	
					}
				)
			  .fail(function() {
				  jQuery("#ced_fruugo_marketplace_loader").hide();
				  alert( "Failed" );
			  });
	});
});


