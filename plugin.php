<?php
/*
Plugin Name: Shipping Pincodes for Woocommerce
Description: Advance Check Pin Code is a solution that allows users to set delivery dates based on the pin codes.
Version: 1.0.0
Author: Cozy Vision Technologies Pvt. Ltd.
Text Domain: cvtech-wc-pincode-for-shipping
WC requires at least: 2.6.0
WC tested up to: 3.6.2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**

** Check if WooCommerce is active
 
**/

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
{
	//add_action('admin_init', 'spwf_save');
	add_action( 'admin_post_spwf_save_settings','spwf_save');
	add_action( 'admin_notices', 'spwf_show_admin_notice__success' );   
			
	/* Form Post Data */
	
	function spwf_show_admin_notice__success() {
		if(!empty($_GET['spwf_status']))
		{ 
			if($_GET['spwf_status']!=1){
				return false;
			}
			else
			{
				echo '<div class="notice notice-success is-dismissible"><p>Settings saved successfully.</p></div>';
			}
		}
		
	}	
	
	function spwf_get_option( $option, $section, $default = '' ) {
		$options = get_option( $section );

		if ( isset( $options[$option] ) ) {
			return $options[$option];
		}
		return $default;
	}

	function spwf_sanitize_array($arr) 
	{
		  $result = array();
		  foreach ($arr as $key => $val)
		  {
			  $result[$key] = is_array($val) ? spwf_sanitize_array($val) : sanitize_text_field($val);
		  }
		  return $result;
	}

	function spwf_save() 
	{
		$_POST = spwf_sanitize_array($_POST);
		save_settings($_POST);
	}

	function save_settings($options)
	{
		if(empty($_POST)){return false;}
			
			$defaults = array( 
					'spwf_general'       => array(
						'del_help_text'=>'We can deliver quickly',
						'del_date'=>'1',
						'bgcolor'=>'#ffffff',
						'textcolor'=>'#000000',
						'buttoncolor'=>'#1e73be',
						'buttontcolor'=>'#ffffff',
						'date_time'=>date('Y-m-d H:i:s'),
					),
			);
				
			$options=array_replace_recursive($defaults, array_intersect_key( $_POST, $defaults)); 
			foreach($options as $name => $value)
			   {
				   if(is_array($value))
				   {
					   foreach($value as $k => $v)
					   {
						   if(!is_array($v))
						   {
								$value[$k] = stripcslashes($v);
						   }
					   }
				   }
					update_option( $name, $value );
			   }
			   //return true;
			wp_redirect(admin_url( 'admin.php?page=pincodes_setting&spwf_status=1' ) );
			exit; 
	}
	/* Form Post Data */
	function SPFW_pincodes_settings_link($links) {
	
		  $settings_link = '<a href="admin.php?page=pincodes_setting">Settings</a>'; 
		  
		  array_unshift($links, $settings_link); 
		  
		  return $links; 
		  
	}
		 
	$plugin = plugin_basename(__FILE__);

	add_filter("plugin_action_links_$plugin", 'SPFW_pincodes_settings_link' ); //for plugin setting link

	function SPFW_pincodes_setting() {
		require_once(dirname(__FILE__).'/admin-setting.php');
	}
 
	 
	function SPFW_adpanel_style3() {

		?>
			<script>
				var blog_title = '<?php echo plugin_dir_url(__FILE__); ?>';
				var usejs = 0;
			</script>
		<?php
	
		wp_enqueue_style( 'picodecheck-css', plugin_dir_url( __FILE__ ) . 'assets/css/style.css' );
		
		// embed the javascript file that makes the AJAX request
		
		wp_enqueue_script( 'picodecheck-ajax-request', plugin_dir_url( __FILE__ ) . '/assets/js/custom.js', array( 'jquery' ) );

		// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
		
		wp_localize_script( 'picodecheck-ajax-request', 'pincode_check', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	}

	add_action('wp_head', 'SPFW_adpanel_style3'); //for adding assets/js/css in wp head
	
	function SPFW_adpanel_style4() {
	
		wp_enqueue_style( 'picodecheck-css', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css' );
		
		wp_enqueue_script( 'picodecheck-ajax-request', plugin_dir_url( __FILE__ ) . 'assets/js/custom.js', array( 'jquery' ) );
		
		?>
		
			<script>
			
				var usejs = 0;
				
			</script>
			
		<?php
		
	}

	add_action('admin_head', 'SPFW_adpanel_style4'); //for adding assets/js/css in wp head

	//Activation Code of table in wordpress
	register_activation_hook(__FILE__, 'spwf_pincode_plugin_activation');

	function spwf_pincode_plugin_activation() {

		global $table_prefix, $wpdb;

		$tblname = 'spwf_pincodes';

		$wp_track_members_table = $table_prefix . "$tblname";

		#Check to see if the table exists already, if not, then create it

		if($wpdb->get_var( "show tables like '$wp_track_members_table'" ) != $wp_track_members_table) 
		{

			$sql0  = "CREATE TABLE `". $wp_track_members_table . "` ( ";

			$sql0 .= "  `id`  int(11)   NOT NULL auto_increment, ";

			$sql0 .= "  `pincode`  varchar(250)   NOT NULL, ";

			$sql0 .= "  `city`  varchar(250)   NOT NULL, ";

			$sql0 .= "  `state`  varchar(250)   NOT NULL, ";

			$sql0 .= "  `dod`  int(11)   NOT NULL, ";

			$sql0 .= "  `cod`  varchar(250)   NOT NULL DEFAULT 'no', ";

			$sql0 .= "  PRIMARY KEY `order_id` (`id`) "; 

			$sql0 .= ") ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ; ";

			#We need to include this file so we have access to the dbDelta function below (which is used to create the table)

			require_once(ABSPATH . '/wp-admin/upgrade-functions.php');

			dbDelta($sql0);

		}
	}
	
	require_once(dirname(__FILE__).'/list_pincodes.php');
	require_once(dirname(__FILE__).'/add_pincode.php');
	
	add_action( 'admin_menu', 'SPFW_menu_page' ); //for admin menu

	function SPFW_menu_page() {
        
        $plugin_dir_url =  plugin_dir_url( __FILE__ );

		add_menu_page(__('Zip codes','disp-test'), __('Zip codes','disp-test'), 'manage_options' , 'add_pincode' , '' , "$plugin_dir_url/assets/img/page_white_zip.png" , '6');

		add_submenu_page('add_pincode', __('Add Zip Code','displ-test'), __('Add Zip Code','displ-test'), 'manage_options', 'add_pincode', 'SPFW_add_pincodes_f');

		add_submenu_page('add_pincode', __('Zip Code List','displ-test'), __('Zip Code List','displ-test'), 'manage_options', 'list_pincodes', 'SPFW_list_pincodes_f');

		add_submenu_page('add_pincode', __('Setting','displ-test'), __('Settings','displ-test'), 'manage_options', 'pincodes_setting', 'SPFW_pincodes_setting');
		
	}

	add_action( 'woocommerce_before_add_to_cart_button', 'SPFW_pincode_field' ); //for pincode field on product page
	
	function SPFW_pincode_field( $product ) {
		
		global $table_prefix, $wpdb,$woocommerce;
		
		$pro_id = get_the_ID();
		
		$_pf = new WC_Product_Factory();  

		$_product = $_pf->get_product($pro_id);
		
		$product_type =  $_product->get_type();
		
		$blog_title = site_url();
		
		if($product_type != 'external' && $_product->is_downloadable('yes') != 1 && $_product->is_virtual ('yes') != 1) 
		{
			?>
				<script>
					var usejs = 1;
				</script>
			<?php
			
			$plugin_dir_url =  plugin_dir_url( __FILE__ );
			
			//echo '<script src="'.$plugin_dir_url.'/assets/js/custom.js"></script>';
			
			if( isset( $_COOKIE['valid_pincode'] ) ) {
				
				$cookie_pin = isset($_COOKIE['valid_pincode'])?sanitize_text_field( $_COOKIE['valid_pincode'] ):'';
				
			}
			else
			{
				
				$cookie_pin = '';
				
			}
			
			$num_rows = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM `".$table_prefix."spwf_pincodes` where `pincode` = %s" , $cookie_pin ) );
	
			if($num_rows == 0)
			{

				$cookie_pin = '';

			}


			if(isset($cookie_pin) && $cookie_pin != '') {

				$query = " SELECT * FROM `".$table_prefix."spwf_pincodes` where `pincode` = '$cookie_pin' ";

				$getdata = $wpdb->get_results( $query );

				foreach($getdata as $data){

				$dod =  $data->dod;

				}


				$delivery_date = date("D, jS M", strtotime("+ $dod day"));

				$customer = new WC_Customer();

				$customer->set_shipping_postcode($cookie_pin);
				
				$user_ID = get_current_user_id();
				
				if(isset($user_ID) && $user_ID != 0) {
					
					update_user_meta($user_ID, 'shipping_postcode', $cookie_pin); //for setting shipping postcode
					
				}

				?>


				<div style="clear:both;font-size:14px;" class="wc-delivery-time-response">
					
				<span class='avlpin' id='avlpin'><p><?php esc_html_e('Available at','spfw-pincode-woocommerce'); ?> <?php echo esc_html( $cookie_pin ); ?></p><a class="button" id='change_pin'><?php esc_html_e('change','spfw-pincode-woocommerce'); ?></a></span>

				<div class="pin_div" id="my_custom_checkout_field2" style="display:none;">

						<div class="error_pin" id="error_pin" style="display:none"><?php esc_html_e('Oops! We are not currently servicing your area.','spfw-pincode-woocommerce'); ?></div>

						<p id="pincode_field_idp" class="form-row my-field-class form-row-wide">

							<label class="" for="pincode_field_id"><?php esc_html_e('Check Availability At','spfw-pincode-woocommerce'); ?></label>

							<input type="text" required="required" value="<?php echo esc_html( $cookie_pin ); ?>" placeholder="Enter Your Pincode" id="pincode_field_id" name="pincode_field" class="input-text" />

							<a class="button" id="checkpin"><?php esc_html_e('Check','spfw-pincode-woocommerce'); ?></a>

							<span id="chkpin_loader" style="display:none">

							<img src="<?php echo esc_url( $plugin_dir_url ); ?>/assets/img/ajax-loader.gif"/>

							</span>
						</p>
				</div>
				
				
				<div class="delivery-info-wrap">

					<div class="delivery-info">

							<div class="header">

								<span><h6><?php esc_html_e('Delivered By','spfw-pincode-woocommerce'); ?></h6></span>
								
								<?php
								
									if(spwf_get_option('del_date','spwf_general')==1)
									{
										?>
										<a id="delivery_help_a" class="delivery-help-icon">?</a>
										
										<div class="delivery_help_text_main width_class" style="display:none">
										
											<a id="delivery_help_x" class="delivery-help-cross">x</a>
												
											<div class="delivery_help_text width_class" >
																	
																			
												<?php
												
													echo esc_html(spwf_get_option('del_help_text','spwf_general'));
												
												?>
											
											</div>
										
										</div>
										<?php
									}
								?>
														
								<div class="delivery">
		
									<ul class="ul-disc">
		
										<li>
		
											<?php echo esc_html( $delivery_date ); ?>
		
										</li>
		
									</ul>
		
								</div>
							
							</div>

					</div>

				 </div>

				</div>

				<?php

			}
			else
			{
				?>

				<div class="pin_div" id="my_custom_checkout_field">
					
						<div class="error_pin" id="error_pin" style="display:none"><?php esc_html_e('Oops! We are not currently servicing your area.','spfw-pincode-woocommerce'); ?></div>

						<p id="pincode_field_idp" class="form-row my-field-class form-row-wide">

							<label class="" for="pincode_field_id"><?php esc_html_e('Check Availability At','spfw-pincode-woocommerce'); ?></label>

							<input type="text" required="required" value="" placeholder="Enter Your Pincode" id="pincode_field_id" name="pincode_field" class="input-text" />

							<a class="button" id="checkpin"><?php esc_html_e('Check','spfw-pincode-woocommerce'); ?></a>

								<span id="chkpin_loader" style="display:none">

									<img src="<?php echo esc_url( $plugin_dir_url ); ?>/assets/img/ajax-loader.gif"/>

								</span>
						</p>

				</div>

				<?php

			}
		}
		else
		{
			?>
				<script>
					var usejs = 0;
				</script>
			<?php
		}

	}

	add_action( 'woocommerce_after_order_notes', 'SPFW_checkout_page_function' ); //for checkout page functionality

	function SPFW_checkout_page_function() {
		
		global $table_prefix, $wpdb, $woocommerce;
		
		$blog_title = site_url();
		
		if( isset( $_COOKIE['valid_pincode'] ) ) {
			$cookie_pin = isset($_COOKIE['valid_pincode'])?sanitize_text_field( $_COOKIE['valid_pincode'] ):'';
			// $cookie_pin = $_COOKIE['valid_pincode'];
			
		}
		else
		{
			$cookie_pin = '';
		}

		if(isset($cookie_pin))
		{		
	
			$customer = new WC_Customer();

			$customer->set_shipping_postcode($cookie_pin);
			
			$user_ID = get_current_user_id();
			
			$current_pcode = get_user_meta($user_ID, 'shipping_postcode');
			
			$customer = new WC_Customer();
			
			if(isset($user_ID) && $user_ID != 0)
			{
				update_user_meta($user_ID, 'shipping_postcode', $cookie_pin);
				
				if($current_pcode[0] != $cookie_pin)
				{
					
					header("Refresh:0");
				}
			}
			
			
		}
		
	}
	
	// if both logged in and not logged in users can send this AJAX request,
	// add both of these actions, otherwise add only the appropriate one
	add_action( 'wp_ajax_nopriv_spfw_pincodecheck_ajax_submit', 'spfw_pincodecheck_ajax_submit' );
	add_action( 'wp_ajax_spfw_pincodecheck_ajax_submit', 'spfw_pincodecheck_ajax_submit' );

	function spfw_pincodecheck_ajax_submit() {
		// get the submitted parameters
		global $table_prefix, $wpdb;
		
		$pincode = sanitize_text_field( $_POST['pin_code'] );
		
		$safe_zipcode =  $pincode ;
		
		if($safe_zipcode)
		{
			$table_pin_codes = $table_prefix."spwf_pincodes";
			
			$count = $wpdb->get_var( $wpdb->prepare( "select COUNT(*) from $table_pin_codes where `pincode` = %s" , $pincode ) );
			
			if($count==0)
			{

			   echo "0";  

			}
			else
			{
				setcookie("valid_pincode",$pincode,time() + (10 * 365 * 24 * 60 * 60),"/");
				
				echo "1";
			}
		}
		else
		{
			echo "0";
		}

		// IMPORTANT: don't forget to "exit"
		exit;
	}

    add_action('wp_head','SPFW_hook_css'); //for adding dynamic css in wp head
    
    function SPFW_hook_css() {
		
		global $table_prefix, $wpdb, $woocommerce;
		
		$blog_title = site_url();
		
		$bgcolor =  spwf_get_option('bgcolor','spwf_general');
		
		$textcolor =  spwf_get_option('textcolor','spwf_general');
				
		$buttoncolor = spwf_get_option('buttoncolor','spwf_general');
		
		$buttontcolor = spwf_get_option('buttontcolor','spwf_general');

    ?>
    <style>
	#shade{background: none repeat scroll 0 0 #000000;opacity: 0.5;}
	
	#shade {height: 100%;left: 0;position: fixed;top: 0;width: 100%;z-index: 100;}
    
	form.cart #my_custom_checkout_field #pincode_field_id{width:180px;border: 1px solid #d3d3d3;margin-right: 5px;font-size: 13px;font-family: "Source Sans Pro",Helvetica,sans-serif;}
    
	form.cart #my_custom_checkout_field #pincode_field_idp label{display: inline-block;margin-right: 5px;font-size: 14px;font-family: "Source Sans Pro",Helvetica,sans-serif;color:<?php echo $textcolor; ?>;}
    
    form.cart .wc-delivery-time-response .delivery-info-wrap {margin: 15px 0;}
    
	form.cart .wc-delivery-time-response .delivery-info-wrap .delivery-info {display: inline-block;width: 100%; position: relative;}
    
	form.cart .wc-delivery-time-response .delivery-info-wrap .delivery-info .header {float: left;width: 50%;}
    
	form.cart .wc-delivery-time-response .delivery-info-wrap .delivery-info .cash-on-delivery-info-wrap {float: right;width: 50%;position:relative;}
    
	form.cart .wc-delivery-time-response .delivery-info-wrap .delivery-info .delivery-help-icon{margin-left:5px;cursor:pointer;}
    
    form.cart .wc-delivery-time-response .delivery-info-wrap .delivery-info .header .delivery .ul-disc{margin:0;padding:0;list-style:none;}
   
    form.cart .wc-delivery-time-response .delivery-info-wrap .delivery-info .cash-on-delivery-info-wrap .cash-on-delivery-info .header{float:none;width:100%;}
    
	form.cart .wc-delivery-time-response .delivery-info-wrap .delivery-info .cash-on-delivery-info-wrap .cash-on-delivery-info .header .cash-on-delivery-help-icon{margin-left: 5px;cursor:pointer;}
    
    
    /*-------------------product1-----------------*/
 
    #my_custom_checkout_field2 #pincode_field_idp #pincode_field_id.input-text{border-radius: 0; display: inline-block; padding: 7px; width:180px;border: 1px solid #666666;margin-right: 5px;font-size: 13px;font-family: "Source Sans Pro",Helvetica,sans-serif;color:#999;}
    
    #my_custom_checkout_field2 #pincode_field_idp .button{cursor: pointer; display: inline-block; vertical-align: top; margin-top:0px;padding:7px 10px;float: none;font-size: 14px;font-family: "Source Sans Pro",Helvetica,sans-serif;text-transform: uppercase;  font-weight: normal;}
    
	#my_custom_checkout_field2 #pincode_field_idp .button:hover {box-shadow: none;}
	
	#my_custom_checkout_field #pincode_field_idp #pincode_field_id.input-text{border-radius: 0; display: inline-block; padding: 7px; width:180px;border: 1px solid #666666;margin-right: 5px;font-size: 13px;font-family: "Source Sans Pro",Helvetica,sans-serif;}
    
    #my_custom_checkout_field #pincode_field_idp .button{ margin-top:-3px;padding: 5px 10px;float: none;font-size: 14px;font-family: "Source Sans Pro",Helvetica,sans-serif;text-transform: uppercase;  font-weight: normal;}
    
    
    .delivery_help_text p{font-size: 14px;font-family: "Source Sans Pro",Helvetica,sans-serif;color:<?php echo $textcolor; ?>;}
    
	.delivery_help_text h3{font-size: 16px;font-family: "Source Sans Pro",Helvetica,sans-serif;color:#7d7b6d;}
    
    .header .cash_on_delivery_help_text p{font-size: 14px;font-family: "Source Sans Pro",Helvetica,sans-serif;color:<?php echo $textcolor; ?>;}
    
	.header .cash_on_delivery_help_text h3{font-size: 16px;font-family: "Source Sans Pro",Helvetica,sans-serif;color:#7d7b6d;}
    
	.delivery-help-cross {color: #000 !important;font-size: 17px;font-weight: bold;position: absolute;right: 0px;top: -2px;cursor: pointer;}
    /*-------------------product1-----------------*/
    
    
    .cash_on_delivery_help_text p{font-size: 14px;font-family: "Source Sans Pro",Helvetica,sans-serif;color:<?php echo $textcolor; ?>;}

	/*------------background & border color & EOD message color------------*/
	
	.avlpin{ <?php if($bgcolor == ''){ echo "background:#f4f2f2;"; } else { echo "background:$bgcolor".';'; }  ?> }
	
	.avlpin{  border: 1px solid #e8e7e7; }
	
	.avlpin{ margin:24px 0 12px; padding:20px; text-align:center; min-width:auto; display:inline-block;box-sizing:border-box;}
	
	.pin_div{ <?php if($bgcolor == ''){ echo "background:#f4f2f2;"; } else { echo "background:$bgcolor".';'; }  ?> }
	
	.pin_div{ border: 1px solid #e8e7e7; }
	
	.pin_div{ margin:24px 0 12px; padding:20px; text-align:center; width:100%; display:inline-block; }
	
	/*------------background & border color & EOD message color------------*/
	
	/*------------Text color------------*/
	
	.avlpin p{ color:<?php echo $textcolor; ?>; }
	
	.avlpin p{ display:inline-block; margin-right: 5px; font-size: 14px; font-family: "Source Sans Pro",Helvetica,sans-serif; margin-bottom:0;}
	
	#pincode_field_idp label{color:<?php echo $textcolor; ?>; display: inline-block; margin-right: 5px; font-size:14px; font-family: "Source Sans Pro",Helvetica,sans-serif;}
	
	/*------------Text color------------*/
	
	/*------------Button & Button Text color------------*/
	
	#change_pin.button{ background:<?php echo $buttoncolor; ?>; }
	
	#change_pin.button{ color:<?php echo $buttontcolor; ?>; }
	
	#my_custom_checkout_field2 #pincode_field_idp .button{ color:<?php echo $buttontcolor; ?>; }
	
	#my_custom_checkout_field2 #pincode_field_idp .button{ background:none repeat scroll 0 0 <?php echo $buttoncolor; ?>; }
	
	#my_custom_checkout_field #pincode_field_idp .button{ color:<?php echo $buttontcolor; ?>; }
	
	#my_custom_checkout_field #pincode_field_idp .button{ background:none repeat scroll 0 0 <?php echo $buttoncolor; ?>; }
		
	#change_pin.button{ float:none; font-size: 14px; font-family: "Source Sans Pro",Helvetica,sans-serif; padding:7px 12px; text-transform: uppercase; font-weight:normal;}
	
	/*------------Button & Button Text color------------*/
	
	/*-----Tooltip Border, Tooltip Background & Tooltip Text color-----*/

	.header .delivery_help_text{ background:#EDEDED; }
	
	.header .delivery_help_text{ border:1px solid #e8e7e7; }
	
	.header .delivery_help_text{ width:100%; box-sizing: border-box; overflow:auto; height:200px; position: absolute; z-index:9999; top:25px; left:0; padding:15px; font-size:14px; font-family: "Source Sans Pro",Helvetica,sans-serif;}	
	
	/*-----Tooltip Border, Tooltip Background & Tooltip Text color-----*/
    
	/*------------Delivered by Text color------------*/
	
	.delivery-info h6{ margin:0; display:inline-block; font-size: 16px; font-family: "Source Sans Pro",Helvetica,sans-serif;}
	
	/*------------Delivered by Text color------------*/

	/*------------Date color------------*/
	
	.delivery .ul-disc li{ font-size:14px;font-family: "Source Sans Pro",Helvetica,sans-serif;}
	
	/*------------Date color------------*/
	
	.delivery_help_text_main{ position: relative;width:100%; }
	
	.delivery-info span h6{ color:#484747; }
	
	.width_class {  }
	
    </style>
    <?php
    }
	
}
?>
