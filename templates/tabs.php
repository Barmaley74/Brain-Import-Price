<div id="content">

	<h2 class="nav-tab-wrapper">
		<a data-tab-id="overview" class="nav-tab<?php woo_bip_admin_active_tab( 'overview' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_bip', 'tab' => 'overview' ), 'admin.php' ); ?>"><?php _e( 'Overview', 'woo_bip' ); ?></a>
		<a data-tab-id="export" class="nav-tab<?php woo_bip_admin_active_tab( 'import' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_bip', 'tab' => 'import' ), 'admin.php' ); ?>"><?php _e( 'Import', 'woo_bip' ); ?></a>
		<a data-tab-id="settings" class="nav-tab<?php woo_bip_admin_active_tab( 'settings' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_bip', 'tab' => 'settings' ), 'admin.php' ); ?>"><?php _e( 'Settings', 'woo_bip' ); ?></a>
        <a data-tab-id="price" class="nav-tab<?php woo_bip_admin_active_tab( 'price' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_bip', 'tab' => 'price' ), 'admin.php' ); ?>"><?php _e( 'Prices', 'woo_bip' ); ?></a>
	</h2>
	<?php woo_bip_tab_template( $tab ); ?>

</div>
<!-- #content -->