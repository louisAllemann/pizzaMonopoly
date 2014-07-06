<?php
/*
	Template for displaying WPPizza Category Navigation
	unless you want to wrap the whole thing in a div or something
	I would suggest you leave it alone......
	
	incidentally, there's also a filter hook you can use for the navigation
	so you can use something like
	
	add_filter('wppizza_filter_navigation', array( $this, 'my_filter'),10);
	function my_filter($args){
		// -> do stuff	
		return $args;
	}
	
*/
?>
<?php
/**set custom sort order**/
add_filter('terms_clauses', array($this,'wppizza_term_filter'), '', 1);
?>
<ul id="<?php echo $post_type ?>-categories" class="<?php echo $post_type ?>-categories">
<?php echo wp_list_categories( $args ); ?>
</ul>