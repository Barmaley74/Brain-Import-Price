<?php
function woo_bip_generate_categories() {

	global $wpdb, $import;

	@ini_set( 'memory_limit', WP_MAX_MEMORY_LIMIT );

	$i = 0;
	$base_categories = array();
	// Check if Categories is empty
	// Проверяем если категории пуcтые
	if( !empty( $import->csv_category ) ) {
		// Check if Categories only contains a single header
		// Проверяем если категории содержат один заголовок
		$size = count( $import->csv_category );
		if( $import->skip_first == 1 && $size == 1 ) {
			$import->log .= "<br />" . __( 'No Categories were provided', 'woo_bip' );
			return;
		}
		for( ; $i < $size; $i++ ) {
				if( isset( $import->csv_category[$i] ) )
					$base_categories[] = $import->csv_category[$i];
		}
		unset( $size );
		if( $import->skip_first == 1)
			$i = 1;
		else
			$i = 0;
		$term_taxonomy = 'product_cat';
		$size = count( $base_categories );
            $include_log = true;
		if( $size > 1000 ) {
			$import->log .= "<br />>>> " . sprintf( __( 'We have just processed and generated so many Product Categories that we couldn\'t actually show you it in real-time, ~%d to be precise', 'woo_bip' ), $size );
			$include_log = false;
		}
		for( ; $i < $size; $i++ ) {
				$category = $base_categories[$i];
            $import->log .= "<br />>>> " .$base_categories[$i].' - '.$category;
						if( $include_log )
							$import->log .= "<br />>>> " . sprintf( __( 'Category: %s', 'woo_bip' ), trim( $category ) );
						if( !term_exists( trim( $category ), $term_taxonomy ) )
							$term = wp_insert_term( htmlspecialchars( trim( $category ) ), $term_taxonomy );
						if( $include_log ) {
							if( isset( $term ) && $term )
								$import->log .= "<br />>>>>>> " . sprintf( __( 'Created Category: %s', 'woo_bip' ), trim( $category ) );
							else
								$import->log .= "<br />>>>>>> " . sprintf( __( 'Duplicate of Category detected: %s', 'woo_bip' ), trim( $category ) );
						}
				unset( $category, $term );
		}
		unset( $size );
		$import->log .= "<br />" . __( 'Categories have been generated', 'woo_bip' );
	} else {
		$import->log .= "<br />" . __( 'No Categories were provided', 'woo_bip' );
	}

}

function woo_bip_process_categories() {

	global $wpdb, $product, $import;

	// Category association
    // Ассоциация с категорей
	$product->category_term_id = array();
	$pid_categories = array();
	if( isset( $product->category ) ) {
		/*if( strpos( $product->category, $import->category_separator ) ) {
			$pid_categories_explode = explode( $import->category_separator, $product->category );
			$size = count( $pid_categories_explode );
			for( $i = 0; $i < $size; $i++ )
				$pid_categories[] = $pid_categories_explode[$i];
			unset( $pid_categories_explode, $size );
		} else {
			$pid_categories[] = trim( $product->category );
		}*/
		$term_taxonomy = 'product_cat';
		// Get a list of Product Categories
		$db_categories_sql = $wpdb->prepare( "SELECT terms.`term_id` FROM `"
            . $wpdb->terms . "` as terms, `" . $wpdb->term_taxonomy . "` as term_taxonomy WHERE terms.`term_id` = term_taxonomy.`term_id`"
            . " AND terms.`name` = %s AND term_taxonomy.`taxonomy` = %s", $product->category, $term_taxonomy );
		$db_category = $wpdb->get_var( $db_categories_sql );
		$wpdb->flush();
        $product->category_term_id[] = $db_category;
        $import->log .= "<br />>> " . $db_category . ' - '.$product->category;
		/*foreach( $pid_categories as $pid_category ) {
			$pid_categorydata = explode( $import->parent_child_delimiter, $pid_category );
			$pid_categorydata_size = count( $pid_categorydata );
			for( $k = 0; $k < $pid_categorydata_size; $k++ ) {
				switch( $k ) {

					case '0':
						foreach( $db_categories as $db_category ) {
							if( ( htmlspecialchars( $pid_categorydata[$k] ) == $db_category->name ) && ( $db_category->category_parent == '0' ) ) {
								$product->category_term_id[] = $db_category->term_id;
								break;
							}
						}
						break;

				}
			}
		}*/
	}

}
?>