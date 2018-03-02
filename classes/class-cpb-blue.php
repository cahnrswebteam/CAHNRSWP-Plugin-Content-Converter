<?php

class CPB_Blue {
	
	
	public function display_posts( $count = 2 ){
		
		$posts = $this->get_posts( -1 );
		
		$version = '1';
		
		$this->display_unsupported( $posts );
		
		$this->display_supported( $posts );
		//var_dump( $posts );
		
	} // End display_posts
	
	public function get_unsupported_posts( $limit = '100' ){
		
		$posts = $this->get_posts( $limit );
		
		$posts = $this->get_filter_posts('unsupported', $posts );
		
		return $posts;
		
	} // End get_unsupported_posts
	
	
	public function get_supported_posts( $limit = '100' ){
		
		$posts = $this->get_posts( $limit );
		
		$posts = $this->get_filter_posts('supported', $posts );
		
		return $posts;
		
	} // End get_unsupported_posts
	
	
	protected function display_unsupported( $posts ){
		
		$version = '1';
		
		echo '<h2>Unsupported Posts</h2>';
		
		$posts = $this->get_filter_posts('unsupported', $posts );
		
		include dirname( dirname( __FILE__ ) ) . '/displays/posts/post-list.php';
		
	} // End display_supported
	
	
	protected function display_supported( $posts ){
		
		$version = '1';
		
		echo '<h2>Supported Posts</h2>';
		
		$posts = $this->get_filter_posts('supported', $posts );
		
		include dirname( dirname( __FILE__ ) ) . '/displays/posts/post-list.php';
		
	} // End display_supported
	
	
	public function get_posts( $count = 2 ){
		
		$args = array(
			'posts_per_page'   	=> $count,
			'post_status'      	=> 'any',
			'post_type' 		=> 'any',
			'meta_query' 		=> array(
				array(
					'key' => '_cahnrs_layout',
					'compare' => 'EXISTS', //or "NOT EXISTS", for non-existance of this key
				)
			),
		);
		
		
		$posts = get_posts( $args );
		
		foreach( $posts as $index => $post ){
			
			$items = $this->get_items( $post );
			
			$posts[ $index ]->items = $items;
			
		} // End foreach
		
		return $posts;
		
	} // End get_posts
	
	
	public function update_post( $post_id ){
		
		$html = '';
		
		$layout = get_post_meta( $post_id, '_cahnrs_layout', true );
		
		if ( isset( $_GET['cc_preview'] ) ) {

			var_dump( $layout );

		}
		
		if ( ! empty( $layout ) ){
		
			if ( is_array( $layout ) ){

				foreach( $layout as $index => $row ){

					$html .= $this->get_row_html( $row, $post_id );

				} // End foreach

			} // End if
			
		} else {
			
			$content_post = get_post( $post_id ); 
		
			$content = $content_post->post_content;

			$html = str_replace('<!-- PRIMARY CONTENT -->','', $content );
			
		}// End if 
		
		
		$the_post = array(
			'ID'           => $post_id,
			'post_content' => $html,
		);

		if ( isset( $_GET['cc_preview'] ) ) {

			var_dump( $the_post );

		} else {

			wp_update_post( $the_post );

			delete_post_meta( $post_id, '_cahnrs_layout' );

		}

		echo 'success';
		
	} // End update_posts
	
	
	public function update_posts(){
		
		
		
	} // End update_posts
	
	
	protected function get_row_html( $row, $post_id ){
		
		if ( isset( $_GET['cc_preview'] ) ) {

			var_dump( $row );

		}
		
		$html = '';
		
		if ( ! empty( $row['columns'] ) ){
			
			if ( 'pagbuilder-layout-aside' === $row['layout'] && ( count( $row['columns'] ) < 2 ) ){
				
				$layout = 'single';
				
			} else {
				
				$layout = $this->get_layout( $row );
				
			} // end if
			
			$html .= '[row layout="' . $layout . '" csshook="' . $row['class'] . '"]';
			
			$columns = $row['columns'];
			
			ksort( $columns );
			
			foreach( $columns as $column ){
				
				if ( isset( $_GET['cc_preview'] ) ) {

					var_dump( $column );

				}
				
				$html .= '[column]';
				
				foreach( $column['items'] as $item ){
					
					if ( isset( $_GET['cc_preview'] ) ) {

						var_dump( $item );

					}
					
					$html .= $this->get_item( $item, $post_id );
					
				} // End foreach
				
				$html .= '[/column]';
				
			} // End foreach
			
			//var_dump( $layout );
			
			//var_dump( $row );
			
			$html .= '[/row]';
			
			return $html;
			
		} // End if
		
		return $html;
		
	} // End get_row_html
	
	
	protected function get_item( $item, $post_id ){
		
		$html = '';
		
		$method_name = 'get_item_' . $item['id'];
		
		if ( method_exists( $this, $method_name ) ){
			
			$html .= $this->$method_name( $item, $post_id );
			
		} // End if
		
		return $html;
		
	} // End get_item
	
	
	protected function get_layout( $row ){
		
		$layouts = array(
			'pagbuilder-layout-full' 		=> 'single',
			'pagbuilder-layout-aside-empty' => 'single',
			'pagbuilder-layout-aside' 		=> 'side-right',
			'pagbuilder-layout-half' 		=> 'halves',
			'pagbuilder-layout-third-left' 	=> 'side-left',
			'pagbuilder-layout-third-right' => 'side-right',
			'pagbuilder-layout-thirds' 		=> 'thirds',
			'pagbuilder-layout-fourths' 	=> 'quarters',
		);
		
		return ( array_key_exists( $row['layout'], $layouts ) ) ? $layouts[ $row['layout'] ] : 'single';
		
	} // End get_layout
	
	
	protected function get_item_page_content( $item, $post_id ){
		
		$content_blocks = get_post_meta( $post_id, '_pagebuilder_editor', true );
		
		$content = $content_blocks['primary_content'];
		
		$content = str_replace('<!-- PRIMARY CONTENT -->','', $content );
		
		$html = '[textblock]' . $content . '[/textblock]';
		
		return $html;
		
	} // End get_item_page_content
	
	
	protected function get_item_content_block( $item, $post_id ){
		
		$content_blocks = get_post_meta( $post_id, '_pagebuilder_editor', true );
		
		$key = 'content_block-' . $item['instance'];
		
		if ( array_key_exists( $key, $content_blocks ) ){
			
			$content = $content_blocks[ $key ];
			
			$content = str_replace('<!-- PRIMARY CONTENT -->','', $content );
		
			$html = '[textblock]' . $content . '[/textblock]';
			
		} // End if		
		
		return $html;
		
	} // End get_item_page_content
	
	
	protected function get_item_cahnrs_action_item( $item, $post_id ){
		
		$html = '';
		
		for( $i = 1;$i < 4; $i++ ){
			
			$name_key = 'name_' . $i;
			
			$url_key = 'url_' . $i;
			
			if ( ! empty( $item['settings'][ $url_key ] ) ){
				
				$html .= '[action css_hook="' . $item['settings']['css_hook'] . '" label="' . $item['settings'][ $name_key ] . '" link="' . $item['settings'][ $url_key ] . '"][/action]';
				
			} // End if
			
		} // End for	
		
		return $html;
		
	} // End get_item_page_content
	
	
	protected function get_item_subtitle( $item, $post_id ){
		
		$html = '[subtitle tag="h2" title="' . $item['settings']['title'] . '" style="' . $item['settings']['css_hook'] . '"][/subtitle]';
		
		return $html;
		
	} // End get_item_page_content
	
	
	protected function get_item_cahnrs_iframe( $item, $post_id ){
		
		$html = '[iframe width="' . $item['settings']['width'] . '" src="' . $item['settings']['src'] . '" height="' . $item['settings']['height'] . '"][/iframe]';
		
		return $html;
		
	} // End get_item_page_content
	
	
	protected function get_item_CAHNRS_feed_widget( $item, $post_id ){
		
		$html = '[promo ';
		
		 if ( $item['settings']['feed_type'] === 'select' ){
			
			$params[] = 'post_ids="' . $item['settings']['selected_item'] . '"';
			 
			 $params[] = 'promo_type="select"';
			
		} else {
		
			$tax_operator = ( $item['settings']['terms'] )? 'AND' : '';

			$params = array(
				'promo_type="feed"',
				'post_type="' . $item['settings']['post_type'] . '"',
				'taxonomy="' . $item['settings']['taxonomy'] . '"',
				'terms="' . $item['settings']['terms'] . '"',
				'count="' . $item['settings']['count'] . '"',
				'offset="' . $item['settings']['skip'] . '"',
				'tax_operator="' . $tax_operator . '"',
				'order_by="' . $item['settings']['order_by'] . '"',
				'css_hook="' . $item['settings']['css_hook'] . '"',
			);
			
		}// End If
		
		if ( '0' == $item['settings']['display_title'] ) $params[] = 'unset_title="1"';
		
		if ( '0' == $item['settings']['display_excerpt'] ) $params[] = 'unset_excerpt="1"';
		
		if ( '0' == $item['settings']['display_link'] ) $params[] = 'unset_link="1"';
		
		if ( '0' == $item['settings']['display_image'] ) $params[] = 'unset_img="1"';
		
		$html .= implode( ' ', $params );
		
		$html .= '][/promo]';
		
		return $html;
		
	} // End get_item_page_content
	
	
	protected function get_item_cahnrs_custom_gallery_widget( $item, $post_id ){
		
		$html = '[postgallery ';
		
		$tax_operator = ( $item['settings']['terms'] )? 'AND' : '';
		
		$params = array(
			'source_type="feed"',
			'post_type="' . $item['settings']['post_type'] . '"',
			'taxonomy="' . $item['settings']['taxonomy'] . '"',
			'terms="' . $item['settings']['terms'] . '"',
			'count="' . $item['settings']['count'] . '"',
			'offset="' . $item['settings']['skip'] . '"',
			'tax_operator="' . $tax_operator . '"',
			'order_by="' . $item['settings']['order_by'] . '"',
			'css_hook="' . $item['settings']['css_hook'] . '"',
			'columns="' . $item['settings']['columns'] . '"',
		);
		
		if ( '0' == $item['settings']['display_title'] ) $params[] = 'unset_title="1"';
		
		if ( '0' == $item['settings']['display_excerpt'] ) $params[] = 'unset_excerpt="1"';
		
		if ( '0' == $item['settings']['display_link'] ) $params[] = 'unset_link="1"';
		
		if ( '0' == $item['settings']['display_image'] ) $params[] = 'unset_img="1"';
		
		$html .= implode( ' ', $params );
		
		$html .= '][/postgallery]';
		
		return $html;
		
	} // End get_item_page_content 
	
	
	protected function get_item_cahnrs_faqs( $item, $post_id ){
		
		$html = '[content_feed ';
		
		$tax_operator = ( $item['settings']['terms'] )? 'AND' : '';
		
		$params = array(
			'feed_type="feed"',
			'display="accordion"',
			'post_type="' . $item['settings']['post_type'] . '"',
			'taxonomy="' . $item['settings']['taxonomy'] . '"',
			'terms="' . $item['settings']['terms'] . '"',
			'count="' . $item['settings']['count'] . '"',
			'offset="' . $item['settings']['skip'] . '"',
			'tax_operator="' . $tax_operator . '"',
			'order_by="' . $item['settings']['order_by'] . '"',
			'css_hook="' . $item['settings']['css_hook'] . '"',
			'columns="' . $item['settings']['columns'] . '"',
		);
		
		if ( '0' == $item['settings']['display_title'] ) $params[] = 'unset_title="1"';
		
		if ( '0' == $item['settings']['display_excerpt'] ) $params[] = 'unset_excerpt="1"';
		
		if ( '0' == $item['settings']['display_link'] ) $params[] = 'unset_link="1"';
		
		if ( '0' == $item['settings']['display_image'] ) $params[] = 'unset_img="1"';
		
		$html .= implode( ' ', $params );
		
		$html .= '][/postgallery]';
		
		return $html;
		
	} // End get_item_page_content cahnrs_faqs
	
	
	protected function get_item_TribeEventsFeatureWidget( $item, $post_id ){
		
		$html = '<h2>' . $item['settings']['title'] . '</h2>[tribe_events_list  category="' . $item['settings']['category'] . '" limit="5"]';
		
		return $html;
		
	} // End get_item_page_content
	
	
	protected function get_items( $post ){
		
		$items = array();
		
		$layout = get_post_meta( $post->ID, '_cahnrs_layout', true );
		
		if ( is_array( $layout ) ){
			
			foreach( $layout as $index => $row ){
				
				if ( ! empty( $row['columns'] ) ){
				
					foreach( $row['columns'] as $column ){

						foreach( $column['items'] as $item ){

							$item = array(
								'type' 		=> $item['id'],
								'supported' => $this->check_supported( $item ),
							);
							
							$items[] = $item;

						} // End foreach

					} // End foreach
					
				} // End if
				
			} // End foreach
			
		} // End if
		
		return $items;
		
	} // End get_items
	
	
	protected function check_supported( $item ){
		
		$supported = false;
		
		$type = $item['id'];
		
		switch( $type ){
				
			case 'page_content':
			case 'subtilte':
			case 'content_block':
			case 'cahnrs_action_item':
			case 'cahnrs_iframe';
			case 'TribeEventsFeatureWidget':
				$supported = true;
				break;
			case 'CAHNRS_feed_widget':
			case 'cahnrs_custom_gallery_widget':
			case 'cahnrs_faqs':
				$supported = ( ( 'basic' === $item['settings']['feed_type'] ) || ( 'select' === $item['settings']['feed_type'] ) ) ? true : false;
				break;
				
		} // End switch
		
		return $supported;
		
	} // End check_supported
	
	
	protected function get_filter_posts( $filter, $posts ){
		
		$the_posts = array();
			
			foreach( $posts as $post ){
				
				if ( isset( $post->items ) ){
					
					$supported = true;
					
					foreach( $post->items as $item ){
						
						if ( ! $item['supported'] ){
							
							$supported = false;
							
						} // End if
						
					} // End
					
					if ( 'supported' === $filter ){
					
						if ( $supported ){

							$the_posts[] = $post;

						} // End if
						
					} else {
						
						if ( ! $supported ){

							$the_posts[] = $post;

						} // End if
						
					} // End if
					
				} // End if
				
			} // End forach
		
		return $the_posts;
		
	} // End get_filter_posts
	
	
} // End CPB_Blue