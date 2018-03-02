<ul>
<?php foreach( $posts as $post ):?>
	<li>
		<a href="<?php echo get_permalink( $post->ID );?>"><?php echo $post->post_title;?></a>
		<?php //var_dump( get_post_meta( $post->ID, '_cahnrs_layout', true ) );?>
		<a href="<?php echo get_bloginfo('url');?>?cpb-version=<?php echo $version;?>&cpb-converter-update-post=<?php echo $post->ID;?>">Update</a>
		<ul>
			<?php if ( isset( $post->items ) ):?>
				<?php foreach( $post->items as $item ):?>
				<li><?php echo $item['type'];?> <?php echo $item['supported'];?></li>
				<?php endforeach;?>
			<?php endif;?>
		</ul>
	</li>
<?php endforeach;?>
</ul>