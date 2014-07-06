<?php
/*****************************************************
* Validates integer
* @str the input to check
******************************************************/
	function wppizza_validate_int_only($str){
		$str=(int)(preg_replace("/[^0-9]/","",$str));
		return $str;
	}
	
/*****************************************************
* Validates boolean
* @str the input to check
******************************************************/
	function wppizza_validate_boolean($inp){
		$bool=filter_var($inp, FILTER_VALIDATE_BOOLEAN);
		return $bool;
	}	
/*****************************************************
* Validates url
* @str the input to check
******************************************************/
	function wppizza_validate_url($inp){
		$url=filter_var($inp, FILTER_VALIDATE_URL);
		return $url;
	}	
/*****************************************************
* Validates float [no negatives]
* @str the input to check, @round [int] to round
* save as float, regardless of what seperators/locale were used
* (also mainly to make it work with legacy versions of plugin)
******************************************************/
	function wppizza_validate_float_only($str,$round='',$omitDecimals=false){
		$str=preg_replace('/[^0-9.,]*/','',$str);/*first get  rid of all chrs that should definitely not be in there*/
		$str=str_replace(array('.',','),'#',$str);/*make string we can explode*/
		$floatArray=explode('#',$str);/*explode so we know the last bit might be decimals*/
		$exLength=count($floatArray);
		
		/**************************************************************************************************
			a bit of a hack to find out if the last part IS actually decimals (as we might be omitting them)
			if it is not decimals (ie 1.300 or 1,300 depending on locale), it will be strlen==3
		**************************************************************************************************/
		if($exLength>0 && strlen($floatArray[$exLength-1])==3){
			$omitDecimals=true;	
		}
		
		$str='';
		for($i=0;$i<$exLength;$i++){
			if($i>0 && $i==($exLength-1) && !$omitDecimals){
			$str.='.';//add decimal point if needed
			}
			$str.=''.$floatArray[$i].'';
		}
		$str=(float)$str;
		if(is_int($round)){$str=round($str,$round);}
		return $str;
	}
	
	
	/*** currently this is just a fix to deal with percentages/sales tax that have 3 decimals as otherwsie it would be recognised with the function above as being 8625% instead of 8.625% ***/
	/*** i need to write something else to take care of all these scenarios (i.e also when people choose to not display decimals etc)***/
	/*** for now , the below will have to do for the salestax**/
	function wppizza_validate_float_pc($str,$round=5){
		$str=preg_replace('/[^0-9.,]*/','',$str);/*first get  rid of all chrs that should definitely not be in there*/	
		$str=str_replace(array('.',','),'#',$str);/*make string we can explode*/
		$floatArray=explode('#',$str);/*explode so we know the last bit might be decimals*/
		$exLength=count($floatArray);
		$str='';
		for($i=0;$i<$exLength;$i++){
			if($i>0 && $i==($exLength-1)){
				$str.='.';//add decimal point if needed
			}
			$str.=''.$floatArray[$i].'';
		}
		$str=(float)$str;
		if(is_int($round)){$str=round($str,$round);}		
	return $str;	
	}
/*****************************************************
* Validates a-zA_Z
* @str the input to check, @limit to limit length of output
******************************************************/
	function wppizza_validate_letters_only($str,$limit=''){
		$str=preg_replace("/[^a-zA-Z]/","",$str);
		if($limit>0){$str=substr($str,0,$limit);}
		return $str;
	}
/*****************************************************
* Validates a-zA-Z0-9\-_
* @str the input to check
******************************************************/
	function wppizza_validate_alpha_only($str){
		$str=(preg_replace("/[^a-zA-Z0-9\-_]/","",$str));
		return $str;
	}
/*****************************************************
* Validates css declarations #a-zA-Z0-9% no spaces or commas etc
* @str the input to check
******************************************************/
	function wppizza_validate_css($str){
		$str=(preg_replace("/[^a-zA-Z0-9#%]/","",$str));
		$str=strtolower($str);
		return $str;
	}	
/*****************************************************
* Validate and returns 24 hour time (02:55)
* @str the input to check
******************************************************/
	function wppizza_validate_24hourtime($str){
		$t=explode(":",$str);
		/**first make them abs int*/
		$hr=(int)abs($t[0]);
		$min=(int)abs($t[1]);
		/*make sure we dont have an hour above 24*/
		if($hr>24){$hr=23;}
		/*make sure we dont have a minute above 59*/
		if($min>59){$min=59;}
		/**output format**/
		$str=''.sprintf('%02d',$hr).':'.sprintf('%02d',$min).'';
		return $str;
	}
/*****************************************************
* Validate and returns a date according to format
* @str the input to check, @format what date format
******************************************************/
	function wppizza_validate_date($str,$format){
		$str=date($format,strtotime($str));
		return $str;
	}
/*****************************************************
* return comma seperated string as array
* @str the input to check
******************************************************/
	function wppizza_strtoarray($str){
		$str=explode(",",$str);
		$array=array();
		foreach($str as $s){
			$array[]=wppizza_validate_string($s);
		}
		return $array;
	}
/*****************************************************
* return pipe, colon seperated string as array
* left of colon=>key, right of colon=>value
* @str the input to check
* CURRENTLY NOT IN USE
******************************************************/
	function wppizza_surchargestoarray($str){
		$str=explode("|",$str);
		$array=array();
		foreach($str as $s){
			$keyVal=explode(":",$s);
			$key=wppizza_validate_string($keyVal[0]);
			/**this should definitely be a float/number**/
			$val='0';
			if(isset($keyVal[1])){
				$val=wppizza_validate_float_only($keyVal[1]);
				/**add percentage sign if required**/
				$hasPc = strpos($keyVal[1], '%');
				if ($hasPc !== false) {
					$val.='%';
				}

			}
			$array[$key]=$val;
		}
		return $array;
	}
/*****************************************************
* return array
* @arr the input array to validate
* @callback the function to use for validating each arr item
******************************************************/
	function wppizza_validate_array($arr=array(),$callback='wppizza_validate_alpha_only'){
		$array=array();
		foreach($arr as $k=>$s){
			$array[''.$callback($k).'']=''.$callback($s).'';
		}
		return $array;
	}
/*****************************************************
* check and return comma seperated string of EMAILS as array
* @str the input to check
******************************************************/
	function wppizza_validate_email_array($str){
		$str=explode(",",$str);
		$email=array();
		foreach($str as $s){
			$s=trim($s);
			if(wppizza_validEmail($s)){
				$email[]=$s;
			}
		}
		return $email;
	}
/*****************************************************
* check format of email
* @email the email to check
******************************************************/
	function wppizza_validEmail($email){
	   $isValid = true;
	   $atIndex = strrpos($email, "@");
	   if (is_bool($atIndex) && !$atIndex){
	      $isValid = false;
	   }else{
	      $domain = substr($email, $atIndex+1);
	      $local = substr($email, 0, $atIndex);
	      $localLen = strlen($local);
	      $domainLen = strlen($domain);
	      if ($localLen < 1 || $localLen > 64){
	         $isValid = false;	         // local part length exceeded
	      }
	      else if ($domainLen < 1 || $domainLen > 255){
	         $isValid = false;	         // domain part length exceeded
	      }
	      else if ($local[0] == '.' || $local[$localLen-1] == '.'){
	         $isValid = false;	         // local part starts or ends with '.'
	      }
	      else if (preg_match('/\\.\\./', $local)){
	         $isValid = false;	         // local part has two consecutive dots
	      }
	      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)){
	         $isValid = false;	         // character not valid in domain part
	      }
	      else if (preg_match('/\\.\\./', $domain)){
	         $isValid = false;	         // domain part has two consecutive dots
	      }
	      else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local))){
	         // character not valid in local part unless
	         // local part is quoted
	         if (!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local))){
	            $isValid = false;
	         }
	      }
	   }
	   return $isValid;
	}

/*****************************************************
* sanitize all costomer order page post vars
* returns serialized value no html etc
******************************************************/
	function wppizza_sanitize_post_vars_recursive(&$str) {
		$str=stripslashes($str);
		$str=wppizza_email_decode_entities($str,get_bloginfo('charset'));
		$str=wp_kses($str,array());
		$str=wppizza_email_html_entities($str);
	}

	function wppizza_sanitize_post_vars($arr){
		if(is_array($arr)){
			array_walk_recursive($arr,'wppizza_sanitize_post_vars_recursive');
		}
		return esc_sql(serialize($arr));
	}



/*****************************************************
* validate and convert characters in string  using internal wordpress functions
* @str the string to check, @htmlAllowed  whether or not html should be stripped
******************************************************/
	function wppizza_validate_string($str,$htmlAllowed=false) {
		$str=convert_chars($str);
		if(!$htmlAllowed){
		$str=esc_html($str);
		}
		return $str;
	}


/*****************************************************
* return new default options when updating plugin
* compares options in option table with default and returns array
* of options that are not yet in option table or are not used anymore
* used on plugin update
* @a1=>comparison array 1 , @a2=>comparison array 2
******************************************************/
function wppizza_compare_options ($a1, $a2) {
    $r = array();
    if(is_array(($a1))){
        foreach($a1 as $k => $v){
            if(isset($a2[$k])){
                $diff = wppizza_compare_options($a1[$k], $a2[$k]);
                if (!empty($diff)){
                    $r[$k] = $diff;
                }
            }else{
                $r[$k] = $v;
            }
        }
    }
    return $r;
}

/*****************************************************
*
* merge current with new options
*
******************************************************/
function wppizza_merge_options ($list1, $list2) {
  $final_array = array();
  $final_array = wppizza_traverse_list($list1, $final_array);
  $final_array = wppizza_traverse_list($list2, $final_array);
  return $final_array;
}

function wppizza_traverse_list($list,$output){
  foreach (array_keys($list) as $key){
    if (array_key_exists($key, $output)){
      foreach ($list[$key] as $k=>$item ){
        $output[$key][$k] = $item;
      }
      arsort($output[$key]);
    }
    else{
      $output[$key] = $list[$key];
    }
  }
  return $output;
}
/**************************************************************
*
* [flatten and inflate multidimensional array to compare]
*
**************************************************************/
function wppizza_flatten($arr, $base = "", $divider_char = "/") {
    $ret = array();
    if(is_array($arr)) {
        foreach($arr as $k => $v) {
            if(is_array($v)) {
                $tmp_array = wppizza_flatten($v, $base.$k.$divider_char, $divider_char);
                $ret = array_merge($ret, $tmp_array);
            } else {
                $ret[$base.$k] = $v;
            }
        }
    }
    return $ret;
}

function wppizza_inflate($arr, $divider_char = "/") {
    if(!is_array($arr)) {
        return false;
    }

    $split = '/' . preg_quote($divider_char, '/') . '/';

    $ret = array();
    foreach ($arr as $key => $val) {
        $parts = preg_split($split, $key, -1, PREG_SPLIT_NO_EMPTY);
        $leafpart = array_pop($parts);
        $parent = &$ret;
        foreach ($parts as $part) {
            if (!isset($parent[$part])) {
                $parent[$part] = array();
            } elseif (!is_array($parent[$part])) {
                $parent[$part] = array();
            }
            $parent = &$parent[$part];
        }

        if (empty($parent[$leafpart])) {
            $parent[$leafpart] = $val;
        }
    }
    return $ret;
}
/**for legacy reasons in paypal gateway, we also have this here.*/
/**either we move the one from actions here or we move this one into actions we'll see*/
/**or use the wppizza_sanitize_post_vars above*****/
	function wppizza_filter_sanitize_post_vars_recursive(&$val,$key){
		$val=stripslashes($val);
		/**let's first decode all already encode ones to not double encode**/
		$val=wppizza_email_decode_entities($val,get_bloginfo('charset'));
		/**strip things**/
		$val=wp_kses($val,array());
		/*now entitize the lot again*/
		$val=wppizza_email_html_entities($val);
	}

	function wppizza_filter_sanitize_post_vars($arr){
		if(is_array($arr)){
			array_walk_recursive($arr,'wppizza_filter_sanitize_post_vars_recursive');
		}
		/**as tips belong to order details and not customer details, we exclude them from the post vars that get stored in the db customer_ini*/
		if(isset($arr['ctips'])){unset($arr['ctips']);}

		return esc_sql(serialize($arr));
	}
?>