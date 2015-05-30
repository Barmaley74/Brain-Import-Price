<div id="content" class="woo_bip_page_options">
	<?php woo_bip_total_columns(); ?>
	<p><?php _e( 'Using the drop down menu match each column in your import file to a Product detail, then click Upload file and import.', 'woo_bip' ); ?></p>
	<form enctype="multipart/form-data" method="post" action="<?php echo add_query_arg( 'action', null ); ?>" class="options">
		<table class="widefat page fixed">
			<thead>
				<tr>
					<th class="manage-column column-row text-align-right"><?php _e( 'CSV', 'woo_bip' ); ?> &raquo; <?php _e( 'WooCommerce', 'woo_bip' ); ?></th>
					<th class="manage-column column-equals">&nbsp;</th>
					<th class="manage-column"><?php _e( 'Value', 'woo_bip' ); ?></th>
				</tr>
			</thead>
			<tbody>
            <?php foreach( $import->options as $option ) { ?>
				<tr>
					<th class="vertical-align-middle text-align-right" valign="top">
						<code><?php echo $option['label']; ?></code>
					</th>

					<td class="vertical-align-middle text-align-center column-equals"><strong>=</strong></td>
					<td class="vertical-align-middle">
						<select name="value_name[]">
                            <?php $k = -1;?>
                            <option></option>
                        <?php foreach( $first_row as $key => $cell ) { ?>
                            <?php $k++;?>
                            <option value="<?php echo $key; ?>"<?php selected(woo_bip_get_option($option['name']),$k ); ?>><?php echo $cell.': '. $second_row[$key]; ?></option>
                    	<?php } ?>
						</select>
				</tr>
            <?php } ?>
			</tbody>
		</table>
		<p class="description"><?php _e( '<small>(*)</small> If your CSV contains special characters - such as &aelig;, &szlig;, &eacute;, etc. - they may display weird under the First and Second Row above, please continue regardless.', 'woo_bip' ); ?></p>

		<div id="poststuff">
			<div class="postbox">
				<h3 class="hndle"><?php _e( 'Import Options', 'woo_bip' ); ?></h3>
				<div class="inside">
					<table class="form-table">

						<tr>
							<td>
								<ul>
                                    <li>
                                        <label><input type="checkbox" name="skip_first"<?php checked( $import->skip_first, 1 ); ?> value="1" />&nbsp;<?php _e( 'First row skip', 'woo_bip' ); ?></label>
                                        <p class="description"><?php _e( 'Skip the first row of the import file if it contains column headers. Brain Import Price detects columns headers at upload time and toggles this option if neccesary.', 'woo_bip' ); ?></p>
                                    </li>
									<li>
										<label><input type="checkbox" name="advanced_log"<?php checked( $import->advanced_log, 1 ); ?> value="1" />&nbsp;<?php _e( 'Advanced import reporting', 'woo_bip' ); ?></label>
										<p class="description"><?php _e( 'This option will provide a more detailed import log but comes at the expense of a slower import process. Default is off.', 'woo_bip' ); ?></p>
									</li>
                                    <li>
                                        <label><input type="checkbox" name="only_price"<?php checked( $import->only_price, 1 ); ?> value="1" />&nbsp;<?php _e( 'Only price update', 'woo_bip' ); ?></label>
                                        <p class="description"><?php _e( 'Updates only supplier price for existing products.', 'woo_bip' ); ?></p>
                                    </li>
                                    <li>
<?php if( !ini_get( 'safe_mode' ) ) { ?>
									<li>
										<label for="timeout"><?php _e( 'Script timeout', 'woo_bip' ); ?>: </label>
										<select id="timeout" name="timeout">
											<option value="600"<?php selected( $import->timeout, 600 ); ?>><?php printf( __( '%d minutes', 'woo_bip' ), 10 ); ?></option>
											<option value="1800"<?php selected( $import->timeout, 1800 ); ?>><?php printf( __( '%d minutes', 'woo_bip' ), 30 ); ?></option>
											<option value="3600"<?php selected( $import->timeout, 3600 ); ?>><?php printf( __( '%d hour', 'woo_bip' ), 1 ); ?></option>
											<option value="0"<?php selected( $import->timeout, 0 ); ?>><?php _e( 'Unlimited', 'woo_bip' ); ?></option>
										</select>
										<p class="description"><?php _e( 'Script timeout defines how long Brain Import Price is \'allowed\' to process your CSV file, once the time limit is reached the import process halts. Default is 10 minutes.', 'woo_bip' ); ?></p>
									</li>
<?php } ?>
								</ul>
							</td>
						</tr>

					</table>
					<!-- .form-table -->
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->
		</div>
		<!-- #poststuff -->
		<?php wp_nonce_field( 'update-options' ); ?>
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="delimiter" value="<?php echo $import->delimiter; ?>" />
		<p class="submit">
			<input type="submit" value="<?php _e( 'Upload file and import', 'woo_bip' ); ?>" class="button-primary" />
		</p>
		<p><?php printf( __( '<strong>Note</strong>: If the following screen goes blank simply hit your browser\'s Refresh (F5) button to continue the import process. If this fails please consult the <a href="%s" target="_blank">Usage page</a> of this Plugin for further assistance.', 'woo_bip' ), $troubleshooting_url ); ?></p>
	</form>
</div>