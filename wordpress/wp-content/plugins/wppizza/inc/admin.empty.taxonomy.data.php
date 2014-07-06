<?php
$terms = get_terms(''.$this->pluginSlugCategoryTaxonomy.'', array('hide_empty' => false));

  if(count($terms)>0){
	/*************************************************************************************************
	*
	*	[first get all posts and make an array of all post we have to use in attachment/post delete]
	*
	*************************************************************************************************/
	$postids=array();
	$args = array('post_type'=> $this->pluginSlug,'posts_per_page'=>-1);  
	$the_query = new WP_Query( $args );
	if($the_query->have_posts()) {
		$posts=$the_query->posts;	
	}
	if(isset($posts) && is_array($posts)){
	foreach($posts as $k=>$v){
		$postids[]=$v->ID;
	}}
	/*************************************************************************************************
	*
	*	[as attachments parents get set to 0 when a post is deleted , delete attachments first (ifset)]
	*
	*************************************************************************************************/
	if($deleteAttachments){
		if(isset($postids) && is_array($postids)){
		foreach($postids as $k=>$v){
			$args = array(
			'post_parent' => $v,
			'post_status' => null,
			'post_type' => 'attachment'
			);
			$attachments = get_children( $args );
			foreach($attachments as $attachment){
				wp_delete_attachment( $attachment->ID,true );
			}
		}}
	}
	/*************************************************************************************************
	*
	*	[now lets delete all posts]
	*
	*************************************************************************************************/
	if(isset($postids) && is_array($postids)){
	foreach($postids as $k=>$v){
		wp_delete_post( $v, true );
	}}
	
	/*************************************************************************************************
	*
	*	[now lets delete all terms]
	*
	*************************************************************************************************/
	foreach( $terms as $term ){
		wp_delete_term( $term->term_id, $this->pluginSlugCategoryTaxonomy );
	}
	
	/*************************************************************************************************
	*
	*	[if set , truncate order table]
	*
	*************************************************************************************************/
	if($truncateOrders){
		global $wpdb;
		/*no backticks or apostrophies please**/
		/** see http://codex.wordpress.org/Creating_Tables_with_Plugins **/		
		$sql="TRUNCATE ".$wpdb->prefix . $this->pluginOrderTable."";
		$e = $wpdb->query($sql); 
		//die(var_dump($e));
	}
  }
?>