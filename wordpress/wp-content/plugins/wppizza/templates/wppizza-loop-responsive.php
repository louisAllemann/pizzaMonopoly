<?php
 /****************************************************************************************
 *
 *
 *	WPPizza - Category Loop Template - RESPONSIVE
 *	IF YOU MUST EDIT THIS, READ THE COMMENTS
 *
 *
 ****************************************************************************************/
	/******************************************
	if we are trying to get the loop from a shortcode/widget in another page
	not related to the wppizza custom post type we will have a different post type,
	so we explicitly set it here to generate the right classes etc
	*******************************************/
	$post_type=WPPIZZA_POST_TYPE;

	/*********** plugin options **************/
	$currency=$options['order']['currency_symbol'];//put currency into a shorter variable. just easier to deal with further down
	$optionsDecimals=$options['layout']['hide_decimals'];
	$txt=$options['localization'];/**put localization vars into a shorter variable

	/**check if we have set the headers to be suppressed in wppizza->settings->layout**/
	if($options['layout']['suppress_loop_headers']){
		$noheader=1;
	}
	/*check if we are showing prices*/
	if($options['layout']['hide_prices']){$hidePrices=1;}
	/*check currency to left of price*/
	if($options['layout']['currency_symbol_left']){$currencyLeft=1;}
	/*check if we are hiding pricetiers if there's only one*/
	if($options['layout']['hide_single_pricetier']){$hidePricetier=1;}
	/*check if we are showing cart icon*/
	if($options['layout']['hide_cart_icon']){$hideCartIcon=' '.$post_type.'-no-cart';}else{$hideCartIcon='';}
	/*check if we are showing currency next to item*/
	if($options['layout']['hide_item_currency_symbol']){$hideCurrencySymbol=1;}
	/*check if online order has been enabled and if so , remove title and classes to disable adding to cart js*/
	if($options['layout']['disable_online_order']){$priceClass=''; $priceTitle='';}else{$priceClass=' '.$post_type.'-add-to-cart'; $priceTitle=' title="'.$txt['add_to_cart']['lbl'].'"';}
	/**add trigger adding to cart from title (h2) when there's only one pricetier***/
	if($options['layout']['add_to_cart_on_title_click']){$clickTrigger=1;}


	/****************************************************************
	also, if we are retrieving the loop via a shortcode/widget
	not on a page with this custom post type
	we will have set set the query variable explicitly to this category
	otherwise just get the one we have
	*****************************************************************/
	if(isset($query_var)){
		$termSlug=$query_var;
	}else{
		$termSlug=get_query_var( WPPIZZA_TAXONOMY );
	}

	/*************************************************************
	now lets get term descriptions , names etc only needs to run
	when noheader is not set or we want to add categories to emails etc
	*************************************************************/
	if(!isset($noheader) || isset($getSlugDetails)){
		$termDetails = get_term_by( 'slug', $termSlug, WPPIZZA_TAXONOMY);
	}
	/**************************************************************************
	 add a cat id to class so we can identify which category we are currently
	 in when adding an item (if required)added as hidden input instead of class
	 somewhere as we can otherwise break all sorts of other things
	**************************************************************************/
	$dataCatId=0;
	if(isset($getSlugDetails)){
		$dataCatId=$termDetails->term_id;
	}
	/*************************************************************
	build and run the query
	*************************************************************/
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$args = array(
		'post_type' => ''.WPPIZZA_POST_TYPE.'',
		'posts_per_page' => $options['layout']['items_per_loop'],
		'paged' => $paged ,
		'post__not_in' => !empty($exclude) ? $exclude : '' , /*ADDED in v.2.7.3*/
		'post__in' => !empty($include) ? $include : '' , /*ADDED in v.2.8.9.7*/
		'tax_query' => array(
			array(
				'taxonomy' => ''.WPPIZZA_TAXONOMY.'',
				'field' => 'slug',
				'terms' => ''.$termSlug.'',
				'include_children' => false
			)
		),
		'orderby'=>'menu_order',
		'order'=>'ASC'
	);
	/**new in version 2.5. currenly used to display single posts***/
	$args = apply_filters('wppizza_filter_loop', $args);
	/**execute query**/	
	$the_query = new WP_Query( $args );
?>
<?php
/********************************************
 *
 *	[OUTPUT HEADER]
 *	[edit or even delete if you want]
 *	[alternatively - and better - set "noheader" in shortcode, "Suppress Category Header" in widget
 *	or just suppress all headers above looped items  in wppizza settings->layout]
 *
 ********************************************/
?>
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_outside_start',$the_query);
?>
<?php if(!isset($noheader) && $termDetails){ /*exclude header if set*/?>
	<header class="page-header entry-header <?php echo $post_type ?>-header">
		<h1 class="page-title entry-title <?php echo $post_type ?>-title"><?php echo $termDetails->name ?></h1>
		<?php if ( $termDetails->description!='' ) :?>
		<div class="entry-meta <?php echo $post_type ?>-header-meta"><?php echo $termDetails->description; ?></div>
		<?php endif; ?>
	</header>
<?php } ?>
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_outside_before',$the_query);
?>
<?php
/********************************************
*
*
*	[OUTPUT LOOP - edit if you want/must]
*	WARNING: be careful not to change or delete any classes or id's (especially in the prices section), as the ajax functionality when adding items to cart depends on them
*	Furthermore, the used CSS has - obviously - been setup with those in min.
*	You should be able to add additional classes though. Make sure you test things.
*
*
 ********************************************/
	/* Start the Loop */
	while ( $the_query->have_posts() ) : $the_query->the_post();
	/**changed / added in 2.5***/
	/**changed to not run function multiple times unnecessarily -> replaced all other get_the_ID() further down**/
	$postId=get_the_ID();
	/***new in 2.5.6 ->prettyPhoto (store get_the_title() in var so we can use it multiple times without running function more than once **/
	$postTitle=get_the_title();	
	/**get permalink*****/
	$permalink = get_permalink( $postId );
	/*get meta data for this post**/
	$meta=get_post_meta($postId, $post_type, true );

	/**added in 2.5 to enable messing around with output below if required***/
	$meta = apply_filters('wppizza_filter_loop_meta', $meta, $postId);

	/***********************************************************
	*
	*	if you want to display categories for example , uncomment
	*	the following and put it in the loop where required
	*
	************************************************************/
//		$terms = get_the_terms($postId, WPPIZZA_TAXONOMY);
//		/*example what to do with it. edit as required***/
//		$categoryNames='';
//		if ($terms && ! is_wp_error($terms)){
//			$term_category=array();
//			foreach ($terms as $term) {
//				$term_category[]= $term->name;
//			}
//			$categoryNames = implode(" / ",$term_category);
//		}
//		/*now output $categoryNames somewhere***/

	/**end changed / added in 2.5***/
	$numberOfSizes=count($options['sizes'][$meta['sizes']]);
	/**if selected in admin, make click on title add to cart or
	show alert when there are more than one size**/
	$clickTriggerClass='';
	$clickTriggerId='';
	if(isset($clickTrigger)){
	 	/*trigger add to cart**/
	 	if($numberOfSizes==1){
			$clickTriggerClass=' '.$post_type.'-trigger-click';
			$clickTriggerId=' id="'.$post_type.'-article-'.$postId.'-'.$meta['sizes'].'-0"';
	 	}
	 	/*more than one size available, show alert**/
	 	if($numberOfSizes>1){$clickTriggerClass=' '.$post_type.'-trigger-choose';}
	}
?>
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_inside_before_article',$postId);
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(array(''.$post_type.'-article','entry-content')); ?>>
<?php
	/*Selected category ADDED IN VERSION 2.8.9.4*/
	if($dataCatId>0){?>
	<input type="hidden" id="wppizza-category-<?php the_ID(); ?>" value="<?php echo $dataCatId ?>" />
<?php } ?>	
<?php
/*********************************************
	[single items entry content end]
	[lets attempt to keep single items (if used)
	somewhat consistant]
**********************************************/
if(is_single()){
	print"<div class='entry-content'>";
}
?>
<?php
/*************************************************
		[title, additives info]
**************************************************/
?>
			<h2<?php echo $clickTriggerId ?> class="<?php echo $post_type ?>-article-title<?php echo $clickTriggerClass ?>">
			<?php echo $postTitle ?>
			<?php if(count($meta['additives'])>0){?>
				<sup class='<?php echo $post_type ?>-article-additives' title='<?php echo $txt['contains_additives']['lbl'] ?>'>*
	    		<?php foreach($meta['additives'] as $k=>$v){ $additivesOnPage=true; ?>
	    			<span id="wppizza-loop-additive-<?php echo $postId ?>-<?php echo $k ?>" class="wppizza-loop-additive wppizza-loop-additive-<?php echo $k ?>">(<?php echo $k ?>)</span>
	    		<?php } ?>
				</sup>
			<?php } ?>
		</h2>
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_inside_after_title',$postId);
?>		
<?php
/*********************************************
			[thumbnails]
**********************************************/
?>
		<?php if(has_post_thumbnail()) {?>
			<div class="<?php echo $post_type ?>-article-img">
			<?php
			/**new in 2.5.6 ->prettyPhoto if enabled**/
			if($options['layout']['prettyPhoto']){
				$full_image_data = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full' );
				print'<a href="'.$full_image_data[0].'" rel="wppizzaPrettyPhoto" title="'.$postTitle.'">';
			}
			?>			
			<?php the_post_thumbnail( 'thumbnail', array('class' => ''.$post_type.'-article-img-thumb', 'title'=>$postTitle)); ?>
			<?php
			/**new in 2.5.6 ->prettyPhoto if enabled**/
			if($options['layout']['prettyPhoto']){
				print"</a>";
			}
			?>			
			</div>
		<?php
			}else{
				if($options['layout']['placeholder_img']){//display placeholder
		?>
			<div class="<?php echo $post_type ?>-article-img">
				<div class="<?php echo $post_type ?>-article-img-placeholder"></div>
			</div>
		<?php } } ?>
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_inside_after_thumbnails',$postId);
?>	
<?php
/*********************************************
		[prices and info]
**********************************************/
?>
	<div>
<?php
	if(!isset($hidePrices)){
?>
		<div id="<?php echo $post_type ?>-article-tiers-<?php echo $postId ?>" class="<?php echo $post_type ?>-article-tiers">

	   	<?php if(!isset($hideCurrencySymbol) && isset($currencyLeft)){?>
	   		<span class='<?php echo $post_type ?>-article-price-currency <?php echo $post_type ?>-article-currency-left'><?php echo $currency ?></span>
	   	<?php } ?>


	   	<?php foreach($options['sizes'][$meta['sizes']] as $k=>$v){?>
	   		<span id='<?php echo $post_type."-".$postId."-".$meta['sizes']."-".$k ?>' class='<?php echo $post_type ?>-article-price <?php echo $priceClass ?>' <?php echo $priceTitle ?>>
	    		<span><?php if($options['layout']['show_currency_with_price']==1){echo $currency." ";} ?><?php echo wppizza_output_format_price($meta['prices'][$k],$optionsDecimals)?><?php if($options['layout']['show_currency_with_price']==2){echo " ".$currency;} ?></span>
	    		<?php if(!isset($hidePricetier) || count($options['sizes'][$meta['sizes']])>1){ ?>
	    		<div class='<?php echo $post_type ?>-article-price-lbl<?php echo $hideCartIcon?>'><?php echo $v['lbl']?></div>
	   			<?php } ?>
	   		</span>
	   	<?php } ?>

	   	<?php if(!isset($hideCurrencySymbol) && !isset($currencyLeft)){?>
	   		<span class='<?php echo $post_type ?>-article-price-currency'><?php echo $currency ?></span>
	   	<?php } ?>
		</div>
<?php } ?>
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_inside_after_prices',$postId);
?>
<?php
/*********************************************
		[description]
**********************************************/
?>
		<div class="<?php echo $post_type ?>-article-info">
		<?php
			the_content();
		?>
		</div>
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_inside_after_content',$postId);
?>
	</div>
<?php
/*********************************************
	[single items entry content]
	[lets attempt to keep single items (if used)
	somewhat consistant. depends on theme though]
**********************************************/
if(is_single()){
	print"<div class='entry-content'>";
}
?>
<?php
/*********************************************
		[article end]
**********************************************/
?>
	</article>
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_inside_after_article',$postId);
?>	
<?php
/*************************************************
	[comments box - if single item view and enabled of course]
**************************************************/
if(is_single()){	
	comments_template( '', true ); 
}
?>	
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_inside_after_comments',$postId);
?>
<?php endwhile;	?>
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_outside_after',$the_query);
?>
<?php
/********************************************
 *
 *	[if any of the items have additives, display idents here]
 *	[if showadditives is distinctly set force/hide display]
 *
 ********************************************/
if(!isset($showadditives) || $showadditives!=0){
if(isset($additivesOnPage) || (isset($showadditives) && $showadditives==1)){
?>
	<div class='<?php echo $post_type ?>-contains-additives'>
	<?php foreach($options['additives'] as $k=>$v){?>
		<span id="wppizza-additive-<?php echo $k ?>" class="wppizza-additive"><sup>(<?php echo $k ?>)</sup><?php echo $v ?></span>
	<?php } ?>
	</div>
<?php }} ?>
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_outside_after_additives',$the_query);
?>
<?php
/*************************************************
	[pagination - no need to display empty divs]
**************************************************/
if(!is_single() && $the_query->max_num_pages>1){
?>
<div class="navigation">
  <div class="alignleft"><?php previous_posts_link(''.$txt['previous']['lbl'].'') ?></div>
  <div class="alignright"><?php next_posts_link(''.$txt['next']['lbl'].'',$the_query->max_num_pages) ?></div>
</div>
<?php } ?>
<?php
	/*ADDED IN VERSION 2.8.5*/
	do_action('wppizza_loop_outside_end',$the_query);
?>