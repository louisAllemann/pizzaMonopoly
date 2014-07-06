<?php
//error_reporting(0);
if(!defined('DOING_AJAX') || !DOING_AJAX){
	header('HTTP/1.0 400 Bad Request', true, 400);
	print"you cannot call this script directly";
  exit; //just for good measure
}
/*****************************************************************************
*
*
*	[set session for selected delivery area]
*
*
*****************************************************************************/

if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='dbp' && (int)$_POST['vars']['dbpid']>=0 ){
	header('Content-type: application/json');

	/**lets not loose the already added form data**/
	if($_POST['vars']['data'] && $_POST['vars']['data'] !=''){
		$WPPIZZAACTIONS=new WPPIZZA_ACTIONS;
		if (method_exists($WPPIZZAACTIONS, 'wppizza_sessionise_userdata')){
			$WPPIZZAACTIONS->wppizza_sessionise_userdata($_POST['vars']['data'],$this->pluginOptions['order_form']);
		}
	}
	

	$dbpid=(int)$_POST['vars']['dbpid'];
	if(isset($_SESSION[$this->dbpSession]['dbp'])){
		unset($_SESSION[$this->dbpSession]['dbp']);
	}
	/*stop tampering with the frontend*/
	if(array_key_exists($dbpid, $this->dbpOptions['delivery_areas'])) {
		$_SESSION[$this->dbpSession]['dbp']=$dbpid;
	}else{
		$_SESSION[$this->dbpSession]['dbp']='';
	}

//print"".json_encode($_POST);
exit();
}
/*****************************************************************************
*
*
*	[check if session has been set to show or not show thickbox]
*
*
*****************************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='dbp-thickbox'){
	header('Content-type: application/json');
	if(isset($_SESSION[$this->dbpSession]['dbp']) && $_SESSION[$this->dbpSession]['dbp']>='0'){
		$output['nothickbox']=true;
	}else{
		$output['nothickbox']=false;
	}

	$output['test']=$_SESSION[$this->dbpSession]['dbp'];

	print"".json_encode($output);
exit();
}
/*****************************************************************************
*
*
*	[get delivery areas for instant search]
*
*
*****************************************************************************/
if(isset($_POST['vars']['type']) && $_POST['vars']['type']=='dbpis'){
	header('Content-type: application/json');
	$validAreas=$this->wppizza_dbp_valid_areas();
	$output['areas']=array();
	foreach($validAreas as $k=>$v){
		$output['areas'][$k]=$v['label'];
	}
	$output['noresults']=$this->dbpOptions['localization']['noresults_instant']['lbl'];
	print"".json_encode($output);
	exit();
}
?>