<?php
/*******************************************************************
*
*	[helper function to make and return a hash and original string to check against ]
*
*******************************************************************/
	function wppizza_mkHash($array){
		$tohash=serialize($array);
		/*try sha256 first if that's an error, use md5*/
		$hash=''.hash("sha256","".AUTH_SALT."".$tohash."".NONCE_SALT."").'';
		if(!$hash || $hash==false || strlen($hash)<64){
		$hash='['.md5("".AUTH_SALT."".$tohash."".NONCE_SALT."").']';	
		}
		$ret['hash']=$hash;
		$ret['order_ini']=$tohash;
	return $ret;
	}

/*******************************************************************
*
*	[always round up - $precision => no of decimals]
*
*******************************************************************/	
function wppizza_round_up ( $value, $precision ) { 
	/**make sure we round up to full if hide decimals**/
	if (class_exists( 'WPPIZZA' ) ) {
		$wpp=new WPPIZZA();
		if($wpp->pluginOptions['layout']['hide_decimals']){
			$precision=0;		
		}
	}
    $pow = pow ( 10, $precision ); 
    return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow; 
} 	
	
/*******************************************************************
*
*	[find serialization errors]
*
*******************************************************************/	
function wppizza_serialization_errors($data1) {
    $output='';
    //echo "<pre>";
    $data2 = preg_replace ( '!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'",$data1 );
    $max = (strlen ( $data1 ) > strlen ( $data2 )) ? strlen ( $data1 ) : strlen ( $data2 );

    $output.= $data1 . PHP_EOL;
    $output.= $data2 . PHP_EOL;

    for($i = 0; $i < $max; $i ++) {

        if (@$data1 {$i} !== @$data2 {$i}) {

            $output.= "Diffrence ". @$data1 {$i}. " != ". @$data2 {$i}. PHP_EOL;
            $output.= "\t-> ORD number ". ord ( @$data1 {$i} ). " != ". ord ( @$data2 {$i} ). PHP_EOL;
            $output.= "\t-> Line Number = $i" . PHP_EOL;

            $start = ($i - 20);
            $start = ($start < 0) ? 0 : $start;
            $length = 40;

            $point = $max - $i;
            if ($point < 20) {
                $rlength = 1;
                $rpoint = - $point;
            } else {
                $rpoint = $length - 20;
                $rlength = 1;
            }

            $output.= "\t-> Section Data1  = ". substr_replace ( substr ( $data1, $start, $length ). "<b style=\"color:green\">{$data1 {$i}}</b>", $rpoint, $rlength ). PHP_EOL;
            $output.= "\t-> Section Data2  = ". substr_replace ( substr ( $data2, $start, $length ). "<b style=\"color:red\">{$data2 {$i}}</b>", $rpoint, $rlength ). PHP_EOL;
        }

    }

	return $output;
}

/******************************* find path to wp-load.php or any other wp file above current directory under document root****************************/
/** this is probably not very useful ever as one will need to find the path before this has been loaded****/
function wppizza_get_wp_config_path($file){
    $base = dirname(__FILE__);
    $base = str_replace("\\", "/", $base);
    $docRoot = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
	$httpRoot= str_replace($docRoot, "", $base);
	$chunkPath=explode("/",$httpRoot);
	$wpbase=$docRoot;
	foreach($chunkPath as $k=>$v){
		$filePath=$wpbase.''.$file.'';
		if(file_exists($filePath)){
			return $filePath;
		}		
	}
}	
?>