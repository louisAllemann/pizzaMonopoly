<?php
	/*no need to run this when no wpml or on first install*/
	if(function_exists('icl_translate') && $this->wpptmOptions!=0) {
		/**localization**/
		foreach($this->wpptmOptions['localization'] as $k=>$val){
			$this->wpptmOptions['localization'][$k] = icl_translate($this->wpptmSlug,''. $k.'', ''.$val.'');
		}
	}
?>