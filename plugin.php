<?php
/*
Plugin Name: CAHNRS Content Converter
Plugin URI:	http://cahnrs.wsu.edu/communications/
Description: Converts Site Content.
Author:	CAHNRS, danialbleile
Version: 0.0.1
*/

class CAHNRSWP_Content_Converter {
	
	
	public function __construct(){
		
		add_action( 'admin_menu', array( $this, 'add_settings_page') );
		
		if ( isset( $_GET['cpb_converter_update_post'] ) ){
		
			add_filter( 'template_include', array( $this, 'do_ajax' ) );
			
		} // End if
		
		add_filter( 'the_content', array( $this , 'turn_off_pagebuilder' ), 1 ); 
		
	} // End __construct
	
	
	public function turn_off_pagebuilder( $content ){
		
		if ( strpos( $content, '[row'  ) !== false ){
			
			global $in_loop;
			
			$in_loop = true;
			
		} // End if
		
		return $content;
		
	} 
	
	
	public function do_ajax( $template ){
		
		return dirname( __FILE__ ) . '/ajax.php';
		
	} // End do_ajax
	
	
	public function add_settings_page(){
		
		add_options_page(
			'Content Converter',
			'Content Converter',
			'manage_options',
			'content_converter',
			array(
				$this,
				'the_settings_page'
			)
		);
		
	} // End add_settings_page
	
	
	public function the_settings_page(){
		
		$unsupported_posts = array();
		
		$supported_posts = array();
		
		$version = ( isset( $_GET['cpb-version'] ) ) ? $_GET['cpb-version'] : false;
		
		$converter = false;
		
		switch( $version ){
				
			case '1':
				include_once 'classes/class-cpb-blue.php';
				$converter = new CPB_Blue();
				
		} // End swtich
		
		if ( $converter ){
			
			$unsupported_posts = $converter->get_unsupported_posts();
		
			$supported_posts = $converter->get_supported_posts();
			
		} // End if
		
		include_once 'includes/settings-form.php';
		
		echo 'convert content';
		
	} // End the_settings_page
	
} // End CAHNRSWP_Content_converter

$cahnrs_content_converter = new CAHNRSWP_Content_Converter();