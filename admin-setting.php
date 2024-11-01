<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb,$table_prefix;

$plugin_dir_url =  plugin_dir_url( __FILE__ );

wp_enqueue_script('wp-color-picker'); //for color picker scripts

wp_enqueue_style( 'wp-color-picker' );

wp_enqueue_media();  //for upload media scripts

?>

<div id="profile-page" class="wrap">
<?php $tab = isset($_GET['tab']) ? sanitize_text_field( $_GET['tab']) : '';?>
<?php include('includes/tabs.php');?>
<?php
if($tab == 'set' || $tab == '')
{
?>
<h2><?php esc_html_e('WooCommerce Pincode Check - Plugin Options','spfw-pincode-woocommerce'); ?></h2>

<form novalidate="novalidate" method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" >
<h3><?php esc_html_e('Manual Settings','spfw-pincode-woocommerce'); ?></h3>
<?php $nonce = wp_create_nonce( 'spfw_check_pincode_setting' ); ?>
		
<input type="hidden" value="<?php echo $nonce; ?>" name="_wpnonce_spfw_check_pincode_setting" id="_wpnonce_spfw_check_pincode_setting" />


<table class="form-table">

	<tbody>

		<tr class="user-user-login-wrap">

			<th><label for="del_help_text"><?php esc_html_e('Delivery Date Help Text','spfw-pincode-woocommerce'); ?></label></th>
			
			<td><textarea class="regular-text" id="del_help_text" name="spwf_general[del_help_text]"><?php echo spwf_get_option('del_help_text','spwf_general','We can deliver quickly'); ?></textarea></td>

		</tr>

		

	</tbody>

</table>

<table class="form-table">

	<tbody>

		<h3><?php esc_html_e('Enable Help Text','spfw-pincode-woocommerce'); ?></h3>

		<tr class="user-nickname-wrap">

			<th><label for="del_date"><?php esc_html_e('Delivery Date','spfw-pincode-woocommerce'); ?></label></th>

			<td><label for="del_date"><input type="radio" <?php if(spwf_get_option('del_date','spwf_general',1) == 1) { ?> checked <?php } ?> name="spwf_general[del_date]" value="1"><?php esc_html_e('ON','spfw-pincode-woocommerce'); ?></label>

			<label for="del_date"><input type="radio" <?php if(spwf_get_option('del_date','spwf_general',1) == 0) { ?> checked <?php } ?> name="spwf_general[del_date]" value="0"><?php esc_html_e('OFF','spfw-pincode-woocommerce'); ?></label></td>

		</tr>

	</tbody>

</table>

<table class="form-table">

<tbody>

<h3><?php esc_html_e('Styling of Check Pincode Functionality on Product Page','spfw-pincode-woocommerce'); ?></h3>


	<tr class="user-user-login-wrap">

			<th><label for="bgcolor"><?php esc_html_e('Box Background color','spfw-pincode-woocommerce'); ?></label></th>

			<td><input type="text" class="regular-text" value="<?php echo spwf_get_option('bgcolor','spwf_general','#ffffff'); ?>" id="bgcolor" name="spwf_general[bgcolor]"></td>

		</tr>


		<tr class="user-first-name-wrap">

			<th><label for="textcolor"><?php esc_html_e('Check Pincode Label Text Color','spfw-pincode-woocommerce'); ?></label></th>

			<td><input type="text" class="regular-text" value="<?php echo spwf_get_option('textcolor','spwf_general','#000000'); ?>" id="textcolor" name="spwf_general[textcolor]"></td>

		</tr>


		<tr class="user-last-name-wrap">

			<th><label for="buttoncolor"><?php esc_html_e('"Check" Button Color','spfw-pincode-woocommerce'); ?></label></th>

			<td><input type="text" class="regular-text" value="<?php echo spwf_get_option('buttoncolor','spwf_general','#1e73be'); ?>" id="buttoncolor" name="spwf_general[buttoncolor]"></td>

		</tr>
		
		
		<tr class="user-last-name-wrap">

			<th><label for="buttontcolor"><?php esc_html_e('"Check" Button Text Color','spfw-pincode-woocommerce'); ?></label></th>

			<td><input type="text" class="regular-text" value="<?php echo spwf_get_option('buttontcolor','spwf_general','#ffffff'); ?>" id="buttontcolor" name="spwf_general[buttontcolor]"></td>

		</tr>
		

</tbody>

</table>		

<input type="hidden" value="spwf_save_settings" name="action">
<p class="submit"><input type="submit" value="Save" class="button button-primary" id="submit" name="submit"></p>

</form>

<?php
}

?>			
</div>

<script>

jQuery(document).ready(function($) {

	jQuery("#bgcolor").wpColorPicker();

	jQuery("#textcolor").wpColorPicker();

	jQuery("#buttoncolor").wpColorPicker();
	
	jQuery("#buttontcolor").wpColorPicker();
	
});

</script>
<style>
.form-table th {
    width: 270px;
	padding: 25px;
}
.form-table td {
	
    padding: 20px 10px;
}
.form-table {
	background-color: #fff;
}
h3 {
    padding: 10px;
}
</style>