<ul class="subsubsub">
	<li><a href="#price-creating"><?php _e( 'Price Creating', 'woo_bip' ); ?></a></li>
	<li>| <a href="#upload-csv"><?php _e( 'Upload Products', 'woo_bip' ); ?></a></li>
	<li>| <a href="#import-options"><?php _e( 'Import Options', 'woo_bip' ); ?></a></li>
	<li>| <a href="#import-modules"><?php _e( 'Import Modules', 'woo_bip' ); ?></a></li>
</ul>
<!-- .subsubsub -->
<br class="clear" />

<p><strong><?php _e( 'Hello! Upload your Product spreadsheet - formatted as a CSV file - and we\'ll import your Products into WooCommerce.', 'woo_bip' ); ?></strong></p>
<form id="upload_form" enctype="multipart/form-data" method="post">
	<div id="poststuff">

		<?php do_action( 'woo_bip_before_upload' ); ?>

        <div id="price-creating" class="postbox">
            <h3 class="hndle"><?php _e( 'Price Creating', 'woo_bip' ); ?></h3>
            <div class="inside">
                <table class="form-table">

                    <tr>
                        <th>
                            <label for="rate"><?php _e( 'Rate', 'woo_bip' ); ?></label>
                        </th>
                        <td>
                            <input type="text" size="3" id="rate" name="rate" value="<?php echo $import->rate; ?>" size="1" class="text" />
                            <p class="description"><?php _e( 'Enter currency rate for calculating price', 'woo_bip' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <label for="trade_margin"><?php _e( 'Trade Margin', 'woo_bip' ); ?></label>
                        </th>
                        <td>
                            <input type="text" size="3" id="trade_margin" name="trade_margin" value="<?php echo $import->trade_margin; ?>" size="1" class="text" />
                            <p class="description"><?php _e( 'The margin on which allegedly increased the price of the supplier', 'woo_bip' ); ?></p>
                        </td>
                    </tr>

                </table>
            </div>
            <!-- .inside -->
        </div>
        <!-- .postbox -->

        <?php do_action( 'woo_bip_after_upload' ); ?>
        <?php do_action( 'woo_bip_before_options' ); ?>

        <div id="upload-csv" class="postbox">
			<h3 class="hndle"><?php _e( 'Upload Products', 'woo_bip' ); ?></h3>
			<div class="inside">
				<p><?php _e( 'Choose a file from your computer, then click Upload file to import.', 'woo_bip' ); ?></p>

				<div id="import-products-filters-upload" class="upload-method separator">
					<label for="file_upload"><strong><?php _e( 'Choose a file from your computer', 'woo_bip' ); ?></strong>:</label> <input type="file" id="csv_file" name="csv_file" size="25" />
					<p class="description"><?php printf( __( 'Choose your Product Catalogue/CSV (.csv) to upload, maximum size: %sMB.', 'woo_bip' ), $upload_mb ); ?></p>
				</div>

				<p class="submit">
					<input type="submit" value="<?php _e( 'Upload file and import', 'woo_bip' ); ?>" class="button-primary" />
					<input type="reset" value="<?php _e( 'Reset', 'woo_bip' ); ?>" class="button" />
				</p>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->

		<?php do_action( 'woo_bip_after_upload' ); ?>
		<?php do_action( 'woo_bip_before_options' ); ?>

		<div id="import-options" class="postbox">
			<h3 class="hndle"><?php _e( 'Import Options', 'woo_bip' ); ?></h3>
			<div class="inside">
				<table class="form-table">

					<tr>
						<th>
							<label for="delimiter"><?php _e( 'Field delimiter', 'woo_bip' ); ?></label>
						</th>
						<td>
							<input type="text" size="3" id="delimiter" name="delimiter" value="<?php echo $import->delimiter; ?>" size="1" class="text" />
							<p class="description"><?php _e( 'The field delimiter is the character separating each cell in your CSV. This is typically the \',\' (comma) character.', 'woo_bip' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="category_separator"><?php _e( 'Product Category separator', 'woo_bip' ); ?></label>
						</th>
						<td>
							<input type="text" size="3" id="category_separator" name="category_separator" value="<?php echo $import->category_separator; ?>" size="1" class="text" />
							<p class="description"><?php _e( 'The Product Category separator allows you to assign individual Products to multiple Product Categories/Tags/Images at a time. It is suggested to use the \'|\' (vertical pipe) character between each item. For instance: <code>Clothing|Mens|Shirts</code>.', 'woo_bip' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="parent_child_delimiter"><?php _e( 'Product Category heirachy delimiter', 'woo_bip' ); ?></label>
						</th>
						<td>
							<input type="text" size="3" id="parent_child_delimiter" name="parent_child_delimiter" value="<?php echo $import->parent_child_delimiter; ?>" size="1" class="text" />
							<p class="description"><?php _e( 'The Product Category heirachy delimiter links Products Categories in parent/child relationships. It is suggested to use the \'>\' character between each Product Category. For instance: <code>Clothing>Mens>Shirts</code>', 'woo_bip' ); ?>.</p>
						</td>
					</tr>

				</table>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->

		<?php do_action( 'woo_bip_after_options' ); ?>
		<?php do_action( 'woo_bip_before_modules' ); ?>

		<div id="import-modules" class="postbox">
			<h3 class="hndle"><?php _e( 'Import Modules', 'woo_bip' ); ?></h3>
			<div class="inside">
				<p><?php _e( 'Import and merge Product details from other WooCommerce and WordPress Plugins, simply install and activate one of the below Plugins to enable those additional import options.', 'woo_bip' ); ?></p>
<?php if( $modules ) { ?>
				<div class="table table_content">
					<table class="woo_vm_version_table">
	<?php foreach( $modules as $module ) { ?>
						<tr>
							<td class="import_module">
		<?php if( $module['description'] ) { ?>
								<strong><?php echo $module['title']; ?></strong>: <span class="description"><?php echo $module['description']; ?></span>
		<?php } else { ?>
								<strong><?php echo $module['title']; ?></strong>
		<?php } ?>
							</td>
							<td class="status">
								<div class="<?php woo_bip_modules_status_class( $module['status'] ); ?>">
		<?php if( $module['status'] == 'active' ) { ?>
									<div class="dashicons dashicons-yes" style="color:#008000;"></div><?php woo_bip_modules_status_label( $module['status'] ); ?>
		<?php } else { ?>
			<?php if( $module['url'] ) { ?>
									<?php if( isset( $module['slug'] ) ) { echo '<div class="dashicons dashicons-download" style="color:#0074a2;"></div>'; } else { echo '<div class="dashicons dashicons-admin-links"></div>'; } ?>&nbsp;<a href="<?php echo $module['url']; ?>" target="_blank"<?php if( isset( $module['slug'] ) ) { echo ' title="' . __( 'Install via WordPress Plugin Directory', 'woo_bip' ) . '"'; } else { echo ' title="' . __( 'Visit the Plugin website', 'woo_bip' ) . '"'; } ?>><?php woo_bip_modules_status_label( $module['status'] ); ?></a>
			<?php } ?>
		<?php } ?>
								</div>
							</td>
						</tr>
	<?php } ?>
					</table>
				</div>
				<!-- .table -->
<?php } else { ?>
				<p><?php _e( 'No import modules are available at this time.', 'woo_bip' ); ?></p>
<?php } ?>
			</div>
			<!-- .inside -->
		</div>
		<!-- .postbox -->

		<?php do_action( 'woo_bip_after_modules' ); ?>

	</div>
	<!-- #poststuff -->

	<input type="hidden" name="action" value="upload" />
	<input type="hidden" name="page_options" value="csv_file" />
	<?php wp_nonce_field( 'update-options' ); ?>
</form>