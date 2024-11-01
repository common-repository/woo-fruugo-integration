<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

//header file.
require_once CED_FRUUGO_DIRPATH.'admin/pages/header.php';
$arrayOfLinks = array( 
	'bulk_profile_assignment' => __('Bulk Profile Assignment', 'ced-fruugo'),
	'bulk_product_upload' => __('Bulk Product Upload', 'ced-fruugo'),
	);
$counter = 1;
$page = 'umb-fruugo-bulk-action';
(isset($_GET['section'])) ? $section = esc_attr($_GET['section']) : $section = 'bulk_profile_assignment';

echo '<div class="ced_fruugo_wrap">';
echo '<ul class="subsubsub">';
foreach ($arrayOfLinks as $linkKey => $linkName) {
	($section == $linkKey) ? $class = 'current' : $class = '';
	$redirectURL = get_admin_url()."admin.php?page=".$page."&amp;section=".$linkKey;
	echo '<li>';
	echo '<a href="'.$redirectURL.'" class="'.$class.'">'.strtoupper($linkName).'</a>'; 
	if($counter < count($arrayOfLinks) ){ 
		echo '|'; 
	}
	echo '</li>';
	$counter++;
}
echo '</ul>';

global $wpdb;
$product_categories = get_terms( 'product_cat');
$table_name = $wpdb->prefix.CED_FRUUGO_PREFIX.'_fruugoprofiles';
$query = "SELECT `id`, `name` FROM `$table_name` WHERE `active` = 1";
$profiles = $wpdb->get_results($query,'ARRAY_A');
$getSavedvalues = get_option('ced_fruugo_category_profile', false);

?>
<?php
// Bulk Profile Assignment Section
if( $section == 'bulk_profile_assignment' ) {
	?>
	<div id="ced_fruugo_marketplace_loader" class="loading-style-bg" style="display: none;">
		<img src="<?php echo plugin_dir_url(__dir__);?>/images/BigCircleBall.gif">
	</div>
	<h2 class="ced_fruugo_setting_header"><?php _e('Assign Profile To Category','ced-fruugo');?></h2>
	<div class="ced_fruugo_category_profile_mapping wrap">
		<table class="wp-list-table widefat fixed striped ced_fruugo_mps">
			<tr>
				<th><b><?php _e('Category','ced-fruugo');?></b></th>
				<th><b><?php _e('Select Profile','ced-fruugo');?></b></th>
				<th><b><?php _e('Selected Profile','ced-fruugo');?></b></th>
			</tr>
			<?php 
			if(is_array($product_categories) && !empty($product_categories))
			{
				foreach ($product_categories as $key => $product_category)
				{ 
					?>
					<tr>
						<td><?php _e($product_category->name)?></td>
						<td data-catId = '<?php echo $product_category->term_id?>'>
							<select class="ced_fruugo_select_cat_profile">
								<option value="removeProfile"><?php _e('--Select Profile--', 'ced-fruugo')?></option>
								<?php 
								if(is_array($profiles) && !empty($profiles))
									{ ?>

								<?php 
								$selected = '';
								foreach ($profiles as $profile)
								{
									//if( $profile['id'] == $getSavedvalues[$product_category->term_id] )
									//{
									//	$selected= "selected";
									//}
									?>
									<option value="<?php echo $profile['id'];?>" <?php echo $selected; ?>><?php _e($profile['name'], 'ced-fruugo');?></option>
									<?php 
									$selected = '';
								}
							}
							?>
						</select>
					</td>
					<td>
						<?php 
						if( is_array( $getSavedvalues ) && !empty( $getSavedvalues ) ){
							$getSavedvalues = array_filter($getSavedvalues);
						}
						if(is_array($getSavedvalues) && !empty($getSavedvalues))
						{
							$f = 0;
							foreach ($getSavedvalues as $catID => $profID)
							{
								if($catID == $product_category->term_id)
								{
									$f = 1;
									if(is_array($profiles) && !empty($profiles))
									{
										foreach ($profiles as $profile)
										{
											if($profile['id'] == $profID)
											{
												echo $profile['name'];
											}
										}
									}
								}
							}
							if( $f == 0 ){
								echo __( "Profile Not selected", 'ced-fruugo' );
							}
						}
						else {
							echo __( "Profile Not selected", 'ced-fruugo' );
						}
						?>
					</td>
				</tr>
				<?php 
			}
		}?>
	</table>
</div>
<?php 
}
// Bulk Product Upload Section
if( $section == 'bulk_product_upload' && current_user_can('administrator') ) {
	
	$notices = array();
	if( isset($_POST['ced_fruugo_	bulk_upload_submit']) || isset($_POST['save_bulk_action']) ) {
		$bulk = array();
		if(isset($_POST['umb_bulk_act_category']) && is_array($_POST['umb_bulk_act_category'])) {
			if( in_array("all", $_POST['umb_bulk_act_category'])) {
				$all_cat = get_terms('product_cat',array('hide_empty'=>0));
				$cat_ids_array = array();
				foreach ($all_cat as $key => $cat) {
					$cat_ids_array[] = $cat->term_id;
				}
				$bulk['cat'] = $cat_ids_array;
			}
			else {
				if(isset($_POST['umb_bulk_act_category']) && is_array($_POST['umb_bulk_act_category'])){
					$ced_cat = array();
					foreach ($_POST['umb_bulk_act_category'] as $key => $value) {
						$ced_cat[] = sanitize_text_field($value);
						$bulk['cat'] = $ced_cat;
					}
				}
			}
		}
		if(isset($_POST['umb_bulk_act_product'])) {
			$bulk['ex_product'] = sanitize_text_field($_POST['umb_bulk_act_product']);
		}
		if(isset($_POST['umb_bulk_act_product_select'])) {
			$bulk['select_product'] = sanitize_text_field($_POST['umb_bulk_act_product_select']);
		}
		update_option('ced_fruugo_cat_bulk', $bulk);
		if(isset($_POST['save_bulk_action'])){
			$notices['message'] = 'Setting Saved.';
			$notices['classes'] = 'notice notice-success is-dismissable';
		}

		if(isset($notices) && count($notices))
		{
			$message = isset($notices['message']) ? esc_html($notices['message']) : '';
			$classes = isset($notices['classes']) ? esc_attr($notices['classes']) : 'error is-dismissable';
			if(!empty($message))
				{?>
			<div class="<?php echo $classes;?>">
				<?php echo $message;?>
			</div>
			<?php 	
		}
		
		unset($notices);
	}
}	

if(isset($_POST['ced_fruugo_bulk_upload_submit']) && current_user_can('administrator')) {

	$assigndCatprofiles = get_option('ced_fruugo_category_profile', false);

	$marketPlace = sanitize_text_field($_POST['ced_fruugo_bulk_upload_marketplace']);
	if( !empty( $marketPlace ) ) {
		$prodetail   = 	get_option('ced_fruugo_cat_bulk', false);
		$selectedcategories = '';
		if(isset($prodetail['cat']))
			$selectedcategories = $prodetail['cat'];
		$proArraytoUpload =array();

		$select_product = isset($prodetail['select_product']) && is_array($prodetail['select_product']) ? $prodetail['select_product'] : array();
		if( !empty($select_product) ) {
			$proArraytoUpload = $select_product;
		}
		else {
			$tax_query['taxonomy'] = 'product_cat';
			$tax_query['field'] = 'id';
			$tax_query['terms'] = $selectedcategories;
			$tax_queries[] = $tax_query;
			$args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'tax_query' => $tax_queries, 'orderby' => 'rand' );

			$loop = new WP_Query( $args );
			while ( $loop->have_posts() ) { 
				$loop->the_post(); 
				global $product;
				$product_id = $loop->post->ID;
				$excludedArray = isset($prodetail['ex_product']) && is_array($prodetail['ex_product']) ? $prodetail['ex_product'] : array();

				if(!in_array($product_id, $excludedArray))
				{
					$product_title = $loop->post->post_title;
					$products[$product_id] = $product_title;
					$proArraytoUpload[] = $product_id;
					$terms = get_the_terms( $product_id, 'product_cat' );
					if(isset($terms) && !empty($terms))
					{	
						foreach ($terms as $term)
						{
							$termId = $term->term_id;
							if(is_array($assigndCatprofiles)){
								
								foreach ($assigndCatprofiles as $key => $value)
								{
									if($termId == $key)
									{
										update_post_meta($product_id, "ced_fruugo_profile", $assigndCatprofiles[$key]);
									}
								}
							}
						}
					}
				}
			}
		}

		if( $marketPlace && $marketPlace != "null" ) {
			require_once CED_FRUUGO_DIRPATH."/marketplaces/$marketPlace/class-fruugo.php";
			$classname = "CED_FRUUGO_manager";
			$marketPlacemanager = new  $classname;
			$response = $marketPlacemanager->upload($proArraytoUpload);
			$notice = $response;
			$notice_array = json_decode($notice,true);
			if(is_array($notice_array)){
				$message = isset($notice_array['message']) ? $notice_array['message'] : '' ;
				$classes = isset($notice_array['classes']) ? $notice_array['classes'] : 'error is-dismissable';
				$notices[] = array('message'=>$message, 'classes'=>$classes);
			}else{

				$message = __('Unexpected error encountered, please try again!','ced-fruugo');
				$classes = "error is-dismissable";
				$notices[] = array('message'=>$message, 'classes'=>$classes);
			}
		}
	}
}

$bulk = get_option('ced_fruugo_cat_bulk', false);
$selected_cat = array();
$selected_pro = array();
if(isset($bulk['cat'])) {	
	$selected_cat = $bulk['cat'];
}
if(isset($bulk['ex_product'])) {
	$selected_pro = $bulk['ex_product'];
}
$select_product= '';
if(isset($bulk['select_product'])) {
	$select_product = $bulk['select_product'];
}	

$products = array();
if(isset($selected_cat) && !empty($selected_cat)) {
	$tax_query['taxonomy'] = 'product_cat';
	$tax_query['field'] = 'id';
	$tax_query['terms'] = $selected_cat;
	$tax_queries[] = $tax_query;
	$args = array( 'post_type' => 'product', 'posts_per_page' => -1, 'tax_query' => $tax_queries, 'orderby' => 'rand' );
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : 
		$loop->the_post(); 
	$product_id = $loop->post->ID;
	$product_title = $loop->post->post_title;
	$products[$product_id] = $product_title;
	endwhile;
}

/* get all profiles */
global $wpdb;
$prefix = $wpdb->prefix . CED_FRUUGO_PREFIX;
$tableName = $prefix.'_fruugoprofiles';
$sql = "SELECT `id`,`name`,`active`,`marketplace` FROM `$tableName` ORDER BY `id` DESC";
$result = $wpdb->get_results($sql,'ARRAY_A');
$profiles_array = array();
foreach ($result as $key => $value) {
	$profiles_array[$value['id']] = $value['name'];
}

$selected_profiles = array();
if(isset($notices) && count($notices))
{
	foreach($notices as $notice_array)
	{
		$message = isset($notice_array['message']) ? esc_html($notice_array['message']) : '';
		$classes = isset($notice_array['classes']) ? esc_attr($notice_array['classes']) : 'error is-dismissable';
		if(!empty($message))
			{?>
		<div class="<?php echo $classes;?>">
			<?php echo $message;?>
		</div>
		<?php 	
	}
}
unset($notices);
}

?>

<h2 class="ced_fruugo_setting_header ced_fruugo_bottom_margin"><?php _e('Bulk Upload', 'ced-fruugo')?></h2>
<form method="post">
	<table class="wp-list-table widefat fixed striped ced_fruugo_bulk_action" >
		<tbody>

			<tr class="ced_fruugo_select_categories">
				<th><?php _e('Select Categories', 'ced-fruugo');?></th>
				<td>
					<?php 
					/* get woocommerce categories */
					$cat_name = ARRAY();
					$all_cat = get_terms('product_cat',array('hide_empty'=>0));
					if($all_cat)
					{
						$cat_name['all'] = __("Select All", 'ced-fruugo');
						foreach ($all_cat as $cat)
						{
							$cat_name[$cat->term_id] = $cat->name;
						}
					}
					else 
					{
						$cat_name = ARRAY();
						$cat_name[] = __('No categories', 'ced-fruugo');
					}
					?>
					<select name="umb_bulk_act_category[]" id="umb_bulk_act_category" multiple>
						<?php 
						if(isset($cat_name) && !empty($cat_name) && is_array($selected_cat))
						{
							foreach($cat_name as $k=>$val)
							{	
								$select = "";
								if(in_array($k, $selected_cat))
								{
									$select = 'selected="selected"';
								}
								?>
								<option value="<?php echo $k?>" <?php echo $select;?>><?php echo $val;?></option>
								<?php 
							}
						}
						?>	
					</select>
				</td>
			</tr>

			<tr class="ced_fruugo_select_products">
				<th><?php _e('Select Products', 'ced-fruugo');?></th>
				<td>
					<select name="umb_bulk_act_product_select[]" id="umb_bulk_act_product_select" multiple>
						<?php
						if(isset($products) && !empty($products) && is_array($select_product))
						{ 
							foreach($products as $k=>$val)
							{	
								$select = "";
								if(in_array($k, $select_product))
								{
									$select = 'selected="selected"';
								}
								?>
								<option value="<?php echo $k?>" <?php echo $select;?>><?php echo $val;?></option>
								<?php 
							}
						}
						?>	
					</select>
				</td>
			</tr>

			<tr class="ced_fruugo_exclude_products">
				<th><?php _e('Exclude Products', 'ced-fruugo');?></th>
				<td>
					<select name="umb_bulk_act_product[]" id="umb_bulk_act_product" multiple>
						<?php
						if(isset($products) && !empty($products))
						{ 
							foreach($products as $k=>$val)
							{	
								$select = "";
								if(in_array($k, $selected_pro))
								{
									$select = 'selected="selected"';
								}
								?>
								<option value="<?php echo $k?>" <?php echo $select;?>><?php echo $val;?></option>
								<?php 
							}
						}
						?>	
					</select>
				</td>
			</tr>

		</tbody>
	</table>
	<p class="ced_fruugo_button_right">
		<?php 
		$activeMarketplaces= fruugoget_enabled_marketplaces();
		?>	
		<select name = "ced_fruugo_bulk_upload_marketplace" class="ced_fruugo_bulk_upload_marketplace">
			<option value = ""><?php _e('-- Select --','ced-fruugo');?></option>
			<?php 
			foreach ($activeMarketplaces as $activeMarketplace)
				{ ?>
			<option value = "<?php echo $activeMarketplace?>" selected>
				<?php echo $activeMarketplace;?>
			</option>
			<?php 	
		}
		?>
	</select>
	<input type = "submit" name = "ced_fruugo_bulk_upload_submit" class="button button-ced_fruggo ced_fruugo_bulk_upload_submit" value ="<?php _e('Upload','ced-fruugo');?>">
	<input type="submit" value="<?php _e('Save changes','ced-fruugo');?>" class="button button-ced_fruggo" name="save_bulk_action">
</p>		
</form>

<?php 
}?>	