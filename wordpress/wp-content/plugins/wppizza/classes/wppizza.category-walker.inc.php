<?php
/********************************************************************************************

	get wppizza categories in hierarchical sortorder

********************************************************************************************/
if (!class_exists( 'WPPizza' ) ) {return ;}

/**get wppizza categories in hierarchical sortorder. returns pipe separated string of category id's in hierarchical order**/
class WPPizzaCategoryHierarchyWalker extends Walker_Category {
    function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		static $c=0;$c++;
		if($c>1){$output .='|';}/*to be used to explode/arrayise*/
		$output .= $category->term_id;
	}
}
?>