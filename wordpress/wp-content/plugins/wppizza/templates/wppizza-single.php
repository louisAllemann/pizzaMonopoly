/********************************************************


	WPPIZZA - Single Post Template Instructions
	
	NOTE: THIS FILE DOES NOT DO ANYTHING BY ITSELF, PLEASE FOLLOW THE INSTRUCTIONS BELOW !!!

	Howto:
	a) locate the single.php file in your template directory and copy it to the same directory as single-wppizza.php
	b) the file will probably have something like  while ( have_posts() ) : the_post(); .....somes stuff ....endwhile; in it
	c) REPLACE this with the code below and you should now have the same layout for a single item as you have when displaying categories (see below)
	d) IMPORTANT if you have a customised version of wppizza-loop.php in your template directory from versions prior to 2.5, please add
			$args = apply_filters('wppizza_filter_loop', $args);
		right above
			$the_query = new WP_Query( $args );
		otherwsie this will not work
	e) if you want to have a link to a single post from the category loop add the snippet below to the loop
***************************************************************************************************************/


/***********************************************************************************
	Re c) : 
		the code to replace the whole loop with
		(THIS MIGHT BE SOMEWHAT DIFFERENT IN YOUR THEME)
************************************************************************************/
<?php
	while ( have_posts() ) : the_post(); 
	
	if(is_single()){/*the is_single() is probably overkill as it should really be a single anyway, oh well...**/
		
		if ($template_file = locate_template( array ('wppizza-loop.php' ))){ /*or use wppizza-loop-responsive.php, depending what you use. furthermore, if using a subdirectory called wppizza, use array ('wppizza/wppizza-loop.php' )*/
			//get_template_part( WPPIZZA_POST_TYPE, 'loop' );
			include_once($template_file);
		}else{
			include_once(''.WPPIZZA_PATH.'templates/wppizza-loop.php');	/*or use wppizza-loop-responsive.php, depending what you use*/
		}
	
	}	
	endwhile; // end of the loop.
?>
/************************************************************
	to display the link to the single page add the following somewhere in the wppizza-loop.php
************************************************************/
<?php
	if(!is_single()){/*no point displaying it whan already on single page i would have tought**/
		echo"<a href='".$permalink."'>[whatever you want linked]</a>";
	}
?>
/***********************************************************************************
	Re d) : 
	if you have a customised version of wppizza-loop.php in your template directory 
	from versions PRIOR TO 2.5, please add
************************************************************************************/
<?php
	$args = apply_filters('wppizza_filter_loop', $args);
?>
	RIGHT ABOVE
	
	$the_query = new WP_Query( $args );



/***********************************************************************************

	Re e) : 
	get link to single post inside category loop
		
************************************************************************************/


/******************************
IF ITS NOT ALREADY THERE (customised versions <2.5) add 
******************************/
<?php
	$permalink = get_permalink( $postId );
?>

AFTER
		
	while ( $the_query->have_posts() ) : $the_query->the_post();
	$postId=get_the_ID();
