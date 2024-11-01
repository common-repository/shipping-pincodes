<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a class="nav-tab <?php if($tab == 'set' || $tab == ''){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=pincodes_setting&amp;tab=set"><?php esc_html_e('Settings','spfw-pincode-woocommerce'); ?></a>
		
		<a class="nav-tab <?php if($tab == 'add'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=add_pincode&amp;tab=add"><?php esc_html_e('Add Zip Code','spfw-pincode-woocommerce'); ?></a>
		
		<a class="nav-tab <?php if($tab == 'list'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=list_pincodes&amp;tab=list"><?php esc_html_e('Zip Code List','spfw-pincode-woocommerce'); ?></a>
</h2>
