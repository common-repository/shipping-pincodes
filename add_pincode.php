<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



function addModifyPincode()
{
	global $table_prefix, $wpdb;
	$plugin_dir_url =  plugin_dir_url( __FILE__ );
	$data=array();
	$pincode = sanitize_text_field( $_POST['pincode'] );
	$city = sanitize_text_field( $_POST['city'] );
	$state = sanitize_text_field( $_POST['state'] );
	$dod = sanitize_text_field( $_POST['dod'] );
	$safe_zipcode =  sanitize_text_field($pincode);
	$safe_dod = intval( $dod );
	$action = !empty( $_GET['action'] ) ? sanitize_text_field( $_GET['action']) :'';
	$id = !empty( $_GET['id'] ) ? sanitize_text_field( $_GET['id']) :'';
	
	if (  $safe_zipcode && $safe_dod )
	{	

		if($action == 'edit' && !empty($id))
		{
			$wpdb->query( $wpdb->prepare( "UPDATE `".$table_prefix."spwf_pincodes` SET `pincode`='%s', `city`='%s', `state`='%s', `dod`='%d' where `id` = %d", $pincode,$city,$state,$dod,$id) );
			$data['status']='success';
			$data['description']='Edited Successfully.';
		}
		else //new records
		{
			$num_rows = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".$table_prefix."spwf_pincodes` where `pincode` = %s", $pincode ) );

			if($num_rows == 0)

			{

				$result = $wpdb->query( $wpdb->prepare( "INSERT INTO `".$table_prefix."spwf_pincodes` SET `pincode` = %s , `city` = %s , `state` = %s , `dod` = %d ", $pincode, $city, $state, $dod ) );
				
				if($result == 1)
				{
					$data['status']='success';
					$data['description']='Added Successfully.';
				}
				else
				{
					$data['status']='error';
					$data['description']='Something Went Wrong Please Try Again With Valid Data.';
				}
			}
			else
			{
				$data['status']='error';
				$data['description']='This Pincode Already Exists.';
			}
		}
	}
		
	return $data;
}

function SPFW_add_pincodes_f()
{
	global $table_prefix, $wpdb;
	if( !empty( $_POST['submit'] ) && sanitize_text_field( $_POST['submit'] ) && current_user_can( 'manage_options' ) )
	{
		$nonce_check = sanitize_text_field( $_POST['_wpnonce_spwf_add_pincode_form'] );
		if ( ! wp_verify_nonce( $nonce_check, 'spwf_add_pincode_form' ) ) 
		{
			die(  'Security check failed'  ); 
			
		}
		$id = !empty( $_GET['id'] ) ? sanitize_text_field( $_GET['id']) :'';
		
		$output = addModifyPincode();
		
		if($output['status']=='success')
		{
			echo '<div class="updated below-h2" id="message"><p>'.$output['description'].'</p></div>';
		}
		else
		{
			echo '<div class="error below-h2" id="message"><p>'.$output['description'].'</p></div>';
		}
	}
	
	
	
	?>
	<div class="wrap">
			<div id="icon-users" class="icon32"><br/></div>
			<?php $tab = isset($_GET['tab']) ? sanitize_text_field( $_GET['tab']) : '';?>
			<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
			<?php include('includes/tabs.php');?>			
<?php
if($tab == 'add' || $tab == '')
{
	
		$action = !empty( $_GET['action'] ) ? sanitize_text_field( $_GET['action']) :'';
		$id = !empty( $_GET['id'] ) ? sanitize_text_field( $_GET['id']) :'';
		$btn_val = ($action == 'edit') ?  'Update' : 'Add';
		$page_title = ($action == 'edit') ?  'Update Zip Code' : 'Add Zip Code'; 
		$pincode = $city = $state = '';
		$dod = 1;
		if($action == 'edit' && !empty($id)) 
		{
			$qry22 = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `".$table_prefix."spwf_pincodes` where `id` = %d" ,$id) ,ARRAY_A);
			$pincode_data = array_shift($qry22);
			$pincode = $pincode_data['pincode'];
			$city = $pincode_data['city'];
			$state = $pincode_data['state'];
			$dod = $pincode_data['dod'];
		}
?>
			<h2><?php esc_html_e($page_title,'spfw-pincode-woocommerce'); ?></h2>
			
				<form action="" method="post" id="azip_form" name="azip_form">
				
				<?php $nonce = wp_create_nonce( 'spwf_add_pincode_form' ); ?>
							
				<input type="hidden" value="<?php echo $nonce; ?>" name="_wpnonce_spwf_add_pincode_form" id="_wpnonce_spwf_add_pincode_form" />

					<table class="form-table">

					<tbody>

						<tr class="user-user-login-wrap">

							<th><label for="user_login"><?php esc_html_e('Pincode','spfw-pincode-woocommerce'); ?></label></th>

							<td><input type="text"  pattern="[a-zA-Z0-9\s]+" required="required" class="regular-text" id="pincode" name="pincode" value="<?php echo $pincode ;?>"></td>

						</tr>

						<tr class="user-first-name-wrap">

							<th><label for="first_name"><?php esc_html_e('City','spfw-pincode-woocommerce'); ?></label></th>

							<td><input type="text" required="required" value="<?php echo $city ;?>" class="regular-text" id="city" name="city"></td>

						</tr>

						<tr class="user-last-name-wrap">

							<th><label for="last_name"><?php esc_html_e('State','spfw-pincode-woocommerce'); ?></label></th>

							<td><input type="text" required="required" class="regular-text" id="state" value="<?php echo $state ;?>" name="state"></td>

						</tr>

						<tr class="user-nickname-wrap">

							<th><label for="nickname"><?php esc_html_e('Delivery within days','spfw-pincode-woocommerce'); ?></label></th>

							<td><input type="number" min="1" max="365" step="1" class="regular-text" id="dod" name="dod" value="<?php echo $dod ;?>"></td>

						</tr>

					</tbody>

				</table>
					
					<p class="submit"><input type="submit" value="<?php echo $btn_val;?>" class="button button-primary" id="submit" name="submit"></p>

			</form>
			
<?php
}

?>			
</div>


<script>
	jQuery('.id-select-all-1').click(function() {

		if (jQuery(this).is(':checked')) {

			jQuery('div input').attr('checked', true);

		} else {

			jQuery('div input').attr('checked', false);

		}

	});

</script>
<?php
}
?>