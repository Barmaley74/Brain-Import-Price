<ul class="subsubsub">
    <li><a href="#price-creating"><?php _e( 'Price Creating', 'woo_bip' ); ?></a></li> |</li>
    <li><a href="#discount-settings"><?php _e( 'Discounts', 'woo_bip' ); ?></a> </li>
</ul>
<!-- .subsubsub -->
<br class="clear" />

<form enctype="multipart/form-data" method="post">
    <table class="form-table">
        <tbody>

        <?php do_action( 'woo_bip_export_settings_before' ); ?>

        <tr id="price-creating">
            <td colspan="2" style="padding:0;">
                <hr />
                <h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<?php _e( 'Price Creating', 'woo_bip' ); ?></h3>
            </td>
        </tr>
                <table class="form-table">

                    <tr>
                        <th>
                            <label for="rate"><?php _e( 'Rate', 'woo_bip' ); ?></label>
                        </th>
                        <td>
                            <input type="text" size="3" id="rate" name="rate" value="<?php echo $rate; ?>" size="1" class="text" />
                            <p class="description"><?php _e( 'Enter currency rate for calculating price', 'woo_bip' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th>
                            <label for="trade_margin"><?php _e( 'Trade Margin', 'woo_bip' ); ?></label>
                        </th>
                        <td>
                            <input type="text" size="3" id="trade_margin" name="trade_margin" value="<?php echo $trade_margin; ?>" size="1" class="text" />
                            <p class="description"><?php _e( 'The margin on which allegedly increased the price of the supplier', 'woo_bip' ); ?></p>
                        </td>
                    </tr>

                </table>

        <tr id="discounts">
            <td colspan="2" style="padding:0;">
                <hr />
                <h3><div class="dashicons dashicons-media-spreadsheet"></div>&nbsp;<?php _e( 'Discounts', 'woo_bip' ); ?></h3>
            </td>
        </tr>
        <table class="form-table">

            <tr>
                <label><input type="checkbox" name="enable_discount"<?php checked( $enable_discount, 1 ); ?> value="1" />&nbsp;<?php _e( 'Enable Discounts', 'woo_bip' ); ?></label>
                <p class="description"><?php _e( 'It enables the calculation of discounts for randomly selected products', 'woo_bip' ); ?></p>
            </tr>
            <tr>
                <th>
                    <label for="count_discount"><?php _e( 'Count discount Products', 'woo_bip' ); ?></label>
                </th>
                <td>
                    <input type="text" size="3" id="count_discount" name="count_discount" value="<?php echo $count_discount; ?>" size="1" class="text" />
                    <p class="description"><?php _e( 'Number of Products for which you need to get discounts', 'woo_bip' ); ?></p>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="discount_margin"><?php _e( 'Discount Margin', 'woo_bip' ); ?></label>
                </th>
                <td>
                    <input type="text" size="3" id="discount_margin" name="discount_margin" value="<?php echo $discount_margin; ?>" size="1" class="text" />
                    <p class="description"><?php _e( 'The margin on which allegedly increased the price of the supplier. Must be smaller than the trading margin', 'woo_bip' ); ?></p>
                </td>
            </tr>
        </table>



        </tbody>
    </table>
    <!-- .form-table -->
    <p class="submit">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Calculate the price', 'woo_bip' ); ?>" />
    </p>
    <input type="hidden" name="action" value="price" />
</form>
<?php do_action( 'woo_bip_export_price_bottom' ); ?>