<style>
	.converter-field {
		padding: 4px 0;
	}
	.converter-field.cc-updated label {
		color: #1A5B10;
		font-weight: bold;
	}
	.converter-field > span {
		display: none;
		font-size: 10px;
		text-transform: uppercase;
		padding: 4px;
		background-color: #1A5B10;
		border-radius: 4px;
		color: #fff !important;
		margin-right: 4px;
	}
	
	.converter-field label {
		font-size: 14px;
		display: inline-block;
		margin-right: 12px;
	}
	
	.converter-field.cc-updated > span {
		display: inline-block;
	}
	.converter-field a {
		display: inline-block;
		font-size: 10px;
		text-transform: uppercase;
		padding: 2px 4px;
		border-radius: 4px;
		text-decoration: none;
		font-weight: bold;
		letter-spacing: 1px;
	}
</style>
<h1>CAHNRS Content Converter</h1>
<form method="get">
	<input type="hidden" value="content_converter" name="page" />
	<fieldset>
		<h2>Converter Settings</h2>
		<div class="converter-field">
			<label>Pagebuilder Version</label>
			<select name="cpb-version" class="cpb-version">
				<option value="">Not Set</option>
				<option value="1" <?php selected( $version, 1 );?>>V.1 (Blue)</option>
			</select>
			<input type="submit" value="Go" />
		</div>
	</fieldset>
	<fieldset>
		<h2>Posts: Not Supported</h2>
		<?php foreach( $unsupported_posts as $us_post ):?>
		<div class="converter-field" data-postid="<?php echo $us_post->ID;?>">
			<span>Updated</span>
			<input class="post-update-item" id="post-<?php echo $us_post->ID;?>" type="checkbox" value="1" />
			<label for="post-<?php echo $us_post->ID;?>"><?php echo $us_post->post_title;?></label>
			<a href="<?php echo get_permalink( $us_post->ID );?>">View</a>
			<a class="coverter-update-post" href="#">Update</a>
			<a href="<?php echo get_bloginfo('url');?>?cpb_converter_update_post=<?php echo $us_post->ID;?>&cpb_version=<?php echo $version;?>&cc_preview=true">Preview</a>
		</div>
		<?php endforeach;?>
		<div class="converter-field">
			<input type="button" value="Select All" class="select-all" />
		</div>
		<div class="converter-field">
			<input type="button" value="Update Selected" class="update-select" />
		</div>
	</fieldset>
	<fieldset>
		<h2>Posts: Supported</h2>
		<?php foreach( $supported_posts as $post ):?>
		<div class="converter-field" data-postid="<?php echo $post->ID;?>">
			<span>Updated</span>
			<input class="post-update-item" id="post-<?php echo $post->ID;?>" type="checkbox" value="1" />
			<label for="post-<?php echo $post->ID;?>"><?php echo $post->post_title;?></label>
			<a href="<?php echo get_permalink( $post->ID );?>">View</a>
			<a class="coverter-update-post" href="#">Update</a>
			<a href="<?php echo get_bloginfo('url');?>?cpb_converter_update_post=<?php echo $post->ID;?>&cpb_version=<?php echo $version;?>&cc_preview=true">Preview</a>
		</div>
		<?php endforeach;?>
		<div class="converter-field">
			<input type="button" value="Select All" class="select-all" />
		</div>
		<div class="converter-field">
			<input type="button" value="Update Selected" class="update-select" />
		</div>
	</fieldset>
</form>
<script>
	
	jQuery('body').on(
		'click',
		'.select-all',
		function( event ){
			event.preventDefault();
			var parent = jQuery( this ).closest('fieldset');
			cc_select_all( parent );
		}
	);
	
	jQuery('body').on(
		'click',
		'.update-select',
		function( event ){
			event.preventDefault();
			var parent = jQuery( this ).closest('fieldset');
			var item = parent.find('.post-update-item:checked').first().closest('.converter-field');
			cc_update_item( item );
		}
	);
	
	jQuery('body').on(
		'click',
		'.coverter-update-post',
		function( event ){
			event.preventDefault();
			var item = jQuery( this ).closest('.converter-field');
			cc_update_item( item );
		}
	);
	
	function cc_update_item( item ){
		
		var version = jQuery('select.cpb-version').val();
		
		var request_url = '<?php echo get_bloginfo('url');?>';
		
		var data = { postid: item.data('postid'), cpb_converter_update_post:item.data('postid'), cpb_version: version };
		
		console.log( data );
		
		jQuery.get( 
			request_url,
			data,
			function( data ) {
				
				if ('success' === data ){
					
					item.find('input').prop('checked', false);
					item.addClass('cc-updated');
					var parent = item.closest('fieldset');
					var next_item = parent.find('.post-update-item:checked').first().closest('.converter-field');
					if ( next_item.length ){
						
						cc_update_item( next_item )
						
					} // End if
					
				} else {
					
					alert( 'Operation Failed' );
					
				}// End if
				
			});
		
	} // End cc_update_item
	
	function cc_select_all( wrapper ){
		
		console.log(wrapper);
		
		wrapper.find('.post-update-item').each( function(){
			
			jQuery( this ).prop('checked', true);
			
		})
		
	} // End cc_select_all  
	
</script>