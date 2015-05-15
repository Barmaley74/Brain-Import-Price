<ul class="subsubsub">
	<li><a href="#general-settings"><?php _e( 'General Settings', 'woo_bip' ); ?></a> |</li>
	<li><a href="#csv-settings"><?php _e( 'CSV Settings', 'woo_bip' ); ?></a> </li>
</ul>
<!-- .subsubsub -->
<br class="clear" />

<form enctype="multipart/form-data" method="post">
	<table class="form-table">
		<tbody>

			<?php do_action( 'woo_bip_export_settings_before' ); ?>

			<tr id="general-settings">
				<td colspan="2" style="padding:0;">
					<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<?php _e( 'General Settings', 'woo_bip' ); ?></h3>
					<p class="description"><?php _e( 'Manage import options across Brain Import Price from this screen.', 'woo_bip' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="delete_file"><?php _e( 'Enable archives', 'woo_bip' ); ?></label>
				</th>
				<td>
					<select id="delete_file" name="delete_file">
						<option value="0"<?php selected( $delete_file, 0 ); ?>><?php _e( 'Yes', 'woo_bip' ); ?></option>
						<option value="1"<?php selected( $delete_file, 1 ); ?>><?php _e( 'No', 'woo_bip' ); ?></option>
					</select>
					<p class="description"><?php _e( 'Save uploaded files to the WordPress Media for downloading/re-importing later. By default this option is turned on.', 'woo_bip' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="encoding"><?php _e( 'Character encoding', 'woo_bip' ); ?></label>
				</th>
				<td>
<?php if( $file_encodings ) { ?>
					<select id="encoding" name="encoding">
						<option value=""><?php _e( 'System default', 'woo_bip' ); ?></option>
	<?php foreach( $file_encodings as $key => $chr ) { ?>
						<option value="<?php echo $chr; ?>"<?php selected( $chr, $encoding ); ?>><?php echo $chr; ?></option>
	<?php } ?>
					</select>
<?php } else { ?>
					<p class="description"><?php _e( 'Character encoding options are unavailable in PHP 4, contact your hosting provider to update your site install to use PHP 5 or higher.', 'woo_bip' ); ?></p>
<?php } ?>
				</td>
			</tr>
<?php if( !ini_get( 'safe_mode' ) ) { ?>
			<tr>
				<th>
					<label for="timeout"><?php _e( 'Script timeout', 'woo_bip' ); ?></label>
				</th>
				<td>
					<select id="timeout" name="timeout">
						<option value="600"<?php selected( $timeout, 600 ); ?>><?php printf( __( '%s minutes', 'woo_bip' ), 10 ); ?></option>
						<option value="1800"<?php selected( $timeout, 1800 ); ?>><?php printf( __( '%s minutes', 'woo_bip' ), 30 ); ?></option>
						<option value="3600"<?php selected( $timeout, 3600 ); ?>><?php printf( __( '%s hour', 'woo_bip' ), 1 ); ?></option>
						<option value="0"<?php selected( $timeout, 0 ); ?>><?php _e( 'Unlimited', 'woo_bip' ); ?></option>
					</select>
					<p class="description"><?php _e( 'Script timeout defines how long Brain Import Price is \'allowed\' to process your CSV file, once the time limit is reached the import process halts.', 'woo_bip' ); ?></p>
				</td>
			</tr>
<?php } ?>

			<?php do_action( 'woo_bip_export_settings_general' ); ?>

			<tr id="csv-settings">
				<td colspan="2" style="padding:0;">
					<hr />
					<h3><div class="dashicons dashicons-media-spreadsheet"></div>&nbsp;<?php _e( 'CSV Settings', 'woo_bip' ); ?></h3>
				</td>
			</tr>
			<tr>
				<th>
					<label for="delimiter"><?php _e( 'Field delimiter', 'woo_bip' ); ?></label>
				</th>
				<td>
					<input type="text" size="3" id="delimiter" name="delimiter" value="<?php echo $delimiter; ?>" maxlength="1" class="text" />
					<p class="description"><?php _e( 'The field delimiter is the character separating each cell in your CSV. This is typically the \',\' (comma) character.', 'woo_bip' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="category_separator"><?php _e( 'Category separator', 'woo_bip' ); ?></label>
				</th>
				<td>
					<input type="text" size="3" id="category_separator" name="category_separator" value="<?php echo $category_separator; ?>" maxlength="1" class="text" />
					<p class="description"><?php _e( 'The Product Category separator allows you to assign individual Products to multiple Product Categories/Tags/Images at a time. It is suggested to use the \'|\' (vertical pipe) character between each item. For instance: <code>Clothing|Mens|Shirts</code>.', 'woo_bip' ); ?></p>
				</td>
			</tr>
			<tr>
				<th>
					<label for="parent_child_delimiter"><?php _e( 'Product Category heirachy delimiter', 'woo_bip' ); ?></label>
				</th>
				<td>
					<input type="text" size="3" id="parent_child_delimiter" name="parent_child_delimiter" value="<?php echo $parent_child_delimiter; ?>" size="1" class="text" />
					<p class="description"><?php _e( 'The Product Category heirachy delimiter links Products Categories in parent/child relationships. It is suggested to use the \'>\' character between each Product Category. For instance: <code>Clothing>Mens>Shirts</code>', 'woo_bip' ); ?>.</p>
				</td>
			</tr>

		</tbody>
	</table>
	<!-- .form-table -->
	<p class="submit">
		<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'woo_bip' ); ?>" />
	</p>
	<input type="hidden" name="action" value="save-settings" />
</form>
<?php do_action( 'woo_bip_export_settings_bottom' ); ?>