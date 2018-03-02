<?php

class CPB_Content_Converter_AJAX {
	
	
	public function __construct(){
		
		$converter = false;
		
		if ( isset( $_GET['cpb_version'])) {
			
			switch( $_GET['cpb_version'] ){
					
				case '1':
					include_once 'classes/class-cpb-blue.php';
					$converter = new CPB_Blue();
					
			} // End Switch
			
			if ( isset( $_GET['cpb_converter_update_post'] ) && $converter ) {
				
				$post_id = $_GET['cpb_converter_update_post'];
				
				$converter->update_post( $post_id );
				
			} // End if
			
		} // End if
		
	} // End __construct
	
	
	
} // End CPB_Content_Converter_AJAX

$cpb_converter_ajax = new CPB_Content_Converter_AJAX();