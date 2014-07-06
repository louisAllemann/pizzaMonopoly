<?php
/********************************************************************************************************
*
*
*	[WPPizza - Main Wrapper Template]
*
*	you will probably want to make a copy of the archive.php, page.php or index.php
*	 file in your theme folder, rename it to wppizza-wrapper.php and copy the bit marked below into that 
*	 copied page, REPLACING the WHOLE loop . ie the bit that will look something like :
*	------------- 	
*	while ( have_posts() ) : the_post(); 
*		
*		//stuff inside the loop
*		
*	endwhile; // end of the loop.
*	------------------
*	 to make the thing play nice with your theme......
*
********************************************************************************************************/
get_header(); 

?>
	<div id="primary" class="site-content">
		<div id="content" role="main">
<?php 
/******************************************************************************
	[copy from here .....]
*****************************************************************************/		

		/****************************************************************
		if the loop template has been copied to the theme folder we get template part
		otherwise, include from plugin template directory
		if using a wppizza subdirectory, change array ('wppizza-loop.php') to array ('wppizza/wppizza-loop.php' )
		****************************************************************/
		if ($template_file = locate_template( array ('wppizza-loop.php' ))){
			include_once($template_file);
		}else{
			include_once(''.WPPIZZA_PATH.'templates/wppizza-loop.php');			
		}

/******************************************************************************
	[...........copy to here]
*****************************************************************************/
?>
		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>