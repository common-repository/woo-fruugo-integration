
// Common toggle code

jQuery(document).ready(function(){

	setTimeout( function(){ jQuery('.ced_fruugo_current_notice').remove(); }, 5000 );
	jQuery(document).on('click','.ced_fruugo_toggle',function(){
		jQuery(this).next('.ced_fruugo_toggle_div').slideToggle('slow');
	});
	
});
// Market Place JQuery End

//jquery for file status.
jQuery(document).ready(function(){
	jQuery(document).on('change','.ced_fruugo_select_cat_profile',function(){
		jQuery(".umb_current_cat_prof").remove();
		var currentThis = jQuery(this);
		var catId  = jQuery(this).parent('td').attr('data-catId');
		var profId = jQuery(this).find(':selected').val();

		if(catId == null || typeof catId === "undefined" || catId == null || profId == "" || typeof profId === "undefined" || profId == null || profId == "--Select Profile--")
		{
			return;
		}
		jQuery('#ced_fruugo_marketplace_loader').show();
		jQuery.post(
				common_action_handler.ajax_url,
				{
					'action': 'ced_fruugo_select_cat_prof',
					'catId' : catId,
					'profId' : profId,
				},
				function(response)
				{
					jQuery('#ced_fruugo_marketplace_loader').hide();
					response = jQuery.parseJSON(response);
					if(response.status == "success" && response.profile != 'Profile not selected')
					{
						currentThis.parent('td').next('td').text(response.profile);
						var successHtml = '<div class="notice notice-success umb_current_cat_prof ced_fruugo_current_notice"><p>Profile Assigned Successfully.</p></div>';
						jQuery('.ced_fruugo_wrap').find('.ced_fruugo_setting_header').append(successHtml);
						// setTimeout( function(){ jQuery( '.ced_fruugo_current_notice' ).remove(); }, 4000 );
					}
					else{
						currentThis.parent('td').next('td').text(response.profile);
						var errorHtml = '<div class="notice ced_fruugo_current_notice notice-error umb_current_cat_prof"><p>Profile Removed!</p></div>';
						jQuery('.ced_fruugo_wrap').find('.ced_fruugo_setting_header').append(errorHtml);
						// setTimeout( function(){ jQuery( '.ced_fruugo_current_notice' ).remove(); }, 4000 );
					}
				}
		);
	});	
	
	jQuery("#umb_bulk_act_category").change(function(){
		var catid = jQuery(this).val();
		jQuery.post(
				common_action_handler.ajax_url,
				{
					'action': 'ced_fruugo_select_cat_bulk_upload',
					'catId' : catid,
				},
				function(response)
				{
					if(response.result == 'success')
					{
						var product = response.data;
						var preselect = jQuery("#umb_bulk_act_product").val();
						var option = '';
						for(key in product)
						{
							select = '';
							if(preselect)
							{	
								if(preselect.indexOf(key) != -1)
								{
									select='selected="selected"';
								}	
							}
							option += '<option value="'+key+'" '+select+'>'+product[key]+'</option>';
						}	
						jQuery("#umb_bulk_act_product").html(option);
						jQuery("#umb_bulk_act_product").select2();

						jQuery("#umb_bulk_act_product_select").html(option);
						jQuery("#umb_bulk_act_product_select").select2();
					}	
				},
				'json'
			);	
	});
	

});