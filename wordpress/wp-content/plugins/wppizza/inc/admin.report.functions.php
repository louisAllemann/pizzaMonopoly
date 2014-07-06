<?php
	function wppizza_report_dataset($options,$locale,$orderTable){
		global $wpdb;

			$wpTime=current_time('timestamp');
			$reportCurrency=$options['order']['currency_symbol'];
			$reportCurrencyIso=$options['order']['currency'];
			$hideDecimals=$options['layout']['hide_decimals'];
			$dateformat=get_option('date_format');
			$processOrder=array();

			/************************************************************************
				overview query. do not limit by date to get totals
				any other query, add date range to query
			************************************************************************/
			$reportTypes=array(
				'ytd'=>array('lbl'=>__('year to date',$locale)),
				'ly'=>array('lbl'=>__('last year',$locale)),
				'tm'=>array('lbl'=>__('this month',$locale)),
				'lm'=>array('lbl'=>__('last month',$locale)),
				'12m'=>array('lbl'=>__('last 12 month',$locale)),
				'7d'=>array('lbl'=>__('last 7 days',$locale)),
				'14d'=>array('lbl'=>__('last 14 days',$locale))
			);
			$overview=empty($_GET['report']) || !in_array($_GET['report'],array_keys($reportTypes)) ? true : false;
			$customrange=!empty($_GET['from']) && !empty($_GET['to'])  ? true : false;


			/******************************
			*
			*	[overview]
			*
			******************************/
			if($overview && !$customrange){
				$granularity='Y-m-d';/*days*/
				$daysSelected=30;
				$xaxisFormat='D, d M';
				$serieslines='true';
				$seriesbars='false';
				$seriespoints='true';
				$hoverOffsetLeft=5;
				$hoverOffsetTop=15;
				$firstDateTimestamp=mktime(date('H',$wpTime),date('i',$wpTime),date('s',$wpTime),date('m',$wpTime),date('d',$wpTime)-$daysSelected+1,date('Y',$wpTime));
				$firstDateReport="".date('Y-m-d',$firstDateTimestamp)."";
				$lastDateReport="".date('Y',$wpTime)."-".date('m',$wpTime)."-".date('d',$wpTime)." 23:59:59";
				$oQuery='';
				/***graph label**/
				$graphLabel="".__('Details last 30 days',$locale)." : ";
			}

			/******************************
			*
			*	[custom range]
			*
			******************************/
			if($customrange){
					$selectedReport='customrange';
					$from=explode('-',$_GET['from']);
					$to=explode('-',$_GET['to']);

					$firstDateTs=mktime(0, 0, 0, $from[1], $from[2], $from[0]);
					$lastDateTs=mktime(23, 59, 59, $to[1], $to[2], $to[0]);
					/*invert dates if end<start**/
					if($firstDateTs>$lastDateTs){
						$firstDateTimestamp=$lastDateTs;
						$lastDateTimestamp=$firstDateTs;
					}else{
						$firstDateTimestamp=$firstDateTs;
						$lastDateTimestamp=$lastDateTs;
					}

					$firstDateReport="".date('Y-m-d',$firstDateTimestamp)."";
					$lastDateReport="".date('Y-m-d H:i:s',$lastDateTimestamp)."";
					/*override get vars**/
					$_GET['from']=$firstDateReport;
					$_GET['to']=date('Y-m-d',$lastDateTimestamp);
					/**from/to formatted**/
					$fromFormatted=date($dateformat,$firstDateTimestamp);
					$toFormatted=date($dateformat,$lastDateTimestamp);

					$oQuery="AND order_date >='".$firstDateReport."'  AND order_date <= '".$lastDateReport."' ";
					/***graph label**/
					$graphLabel="".$fromFormatted." - ".$toFormatted." : ";
			}
			/******************************
			*
			*	[predefined reports]
			*
			******************************/
			if(!$overview){
				$selectedReport=$_GET['report'];
				$oQuery='';

				/************************
					year to date
				************************/
				if($selectedReport=='ytd'){
					$firstDateTimestamp=mktime(0, 0, 0, 1, 1, date('Y',$wpTime));
					$firstDateReport="".date('Y-m-d',$firstDateTimestamp)."";
					$lastDateReport="".date('Y',$wpTime)."-".date('m',$wpTime)."-".date('d',$wpTime)." 23:59:59";
					$oQuery="AND order_date >='".$firstDateReport."'  AND order_date <= '".$lastDateReport."' ";
					/***graph label**/
					$graphLabel="".__('Year to date',$locale)." : ";
				}
				/************************
					last year
				************************/
				if($selectedReport=='ly'){
					$firstDateTimestamp=mktime(0, 0, 0, 1, 1, date('Y',$wpTime)-1);
					$firstDateReport="".date('Y-m-d',$firstDateTimestamp)."";
					$lastDateReport=date('Y-m-d H:i:s',mktime(23,59,59,12,31,date('Y',$wpTime)-1));
					$oQuery="AND order_date >='".$firstDateReport."'  AND order_date <= '".$lastDateReport."' ";
					/***graph label**/
					$graphLabel="".__('Last Year',$locale)." : ";
				}
				/************************
					this month
				************************/
				if($selectedReport=='tm'){
					$firstDateTimestamp=mktime(0, 0, 0, date('m',$wpTime), 1, date('Y',$wpTime));
					$firstDateReport="".date('Y-m-d',$firstDateTimestamp)."";
					$lastDateReport="".date('Y-m-d H:i:s',mktime(23, 59, 59, date('m',$wpTime)+1, 0, date('Y',$wpTime)))."";
					$oQuery="AND order_date >='".$firstDateReport."'  AND order_date <= '".$lastDateReport."' ";
					/***graph label**/
					$graphLabel="".__('This Month',$locale)." : ";
				}
				/************************
					last month
				************************/
				if($selectedReport=='lm'){
					$firstDateTimestamp=mktime(0, 0, 0, date('m',$wpTime)-1, 1, date('Y',$wpTime));
					$firstDateReport="".date('Y-m-d',$firstDateTimestamp)."";
					$lastDateReport=date('Y-m-d H:i:s',mktime(23,59,59,date('m',$wpTime),0,date('Y',$wpTime)));
					$oQuery="AND order_date >='".$firstDateReport."'  AND order_date <= '".$lastDateReport."' ";
					/***graph label**/
					$graphLabel="".__('Last Month',$locale)." : ";
				}

				/************************
					last 12month
				************************/
				if($selectedReport=='12m'){
					$firstDateTimestamp=mktime(0, 0, 0, date('m',$wpTime)-12, date('d',$wpTime)+1, date('Y',$wpTime));
					$firstDateReport="".date('Y-m-d',$firstDateTimestamp)."";
					$lastDateReport=date('Y-m-d H:i:s',mktime(23, 59, 59, date('m',$wpTime), date('d',$wpTime), date('Y',$wpTime)));
					$oQuery="AND order_date >='".$firstDateReport."'  AND order_date <= '".$lastDateReport."' ";
					/***graph label**/
					$graphLabel="".__('Last 12 Month',$locale)." : ";
				}
				/************************
					last 7 days
				************************/
				if($selectedReport=='7d'){
					$firstDateTimestamp=mktime(0, 0, 0, date('m',$wpTime), date('d',$wpTime)-6, date('Y',$wpTime));
					$firstDateReport="".date('Y-m-d',$firstDateTimestamp)."";
					$lastDateReport=date('Y-m-d H:i:s',mktime(23, 59, 59, date('m',$wpTime), date('d',$wpTime), date('Y',$wpTime)));
					$oQuery="AND order_date >='".$firstDateReport."'  AND order_date <= '".$lastDateReport."' ";
					/***graph label**/
					$graphLabel="".__('Last 7 days',$locale)." : ";
				}
				/************************
					last 14 days
				************************/
				if($selectedReport=='14d'){
					$firstDateTimestamp=mktime(0, 0, 0, date('m',$wpTime), date('d',$wpTime)-13, date('Y',$wpTime));
					$firstDateReport="".date('Y-m-d',$firstDateTimestamp)."";
					$lastDateReport=date('Y-m-d H:i:s',mktime(23, 59, 59, date('m',$wpTime), date('d',$wpTime), date('Y',$wpTime)));
					/***graph label**/
					$oQuery="AND order_date >='".$firstDateReport."'  AND order_date <= '".$lastDateReport."' ";
					$graphLabel="".__('Last 14 days',$locale)." : ";
				}

			}

			if(!$overview || $customrange){
				$firstDate = new DateTime($firstDateReport);
				$firstDateFormatted = $firstDate->format($dateformat);
				$lastDate = new DateTime($lastDateReport);
				$lastDateFormatted = $lastDate->format($dateformat);
				$dateDifference = $firstDate->diff($lastDate);
				$daysSelected=($dateDifference->days)+1;
				$monthAvgDivider=($dateDifference->m)+1;
				$monthsSelected=$dateDifference->m;
				$yearsSelected=$dateDifference->y;
				/*set granularity to months if months>0 or years>0*/
				if($monthsSelected>0 || $yearsSelected>0 ){
					$granularity='Y-m';/*months*/
					$xaxisFormat='M Y';
					$serieslines='false';
					$seriesbars='true';
					$seriespoints='false';
					$hoverOffsetLeft=-22;
					$hoverOffsetTop=2;
				}else{
					$granularity='Y-m-d';/*days*/
					$xaxisFormat='D, d M';
					$serieslines='true';
					$seriesbars='false';
					$seriespoints='true';
					$hoverOffsetLeft=5;
					$hoverOffsetTop=15;
				}
			}

			/************************
				[run query]
			*************************/
			$ordersQuery="SELECT id,order_date as oDate ,UNIX_TIMESTAMP(order_date) as order_date,order_ini FROM ".$wpdb->prefix . $orderTable." WHERE payment_status IN ('COD','COMPLETED') ";
			$ordersQuery.= $oQuery;
			$ordersQuery.='ORDER BY order_date ASC';
			$ordersQueryRes = $wpdb->get_results($ordersQuery);

			/**************************
				ini dates
			**************************/
			$graphDates=array();
			for($i=0;$i<$daysSelected;$i++){
				$dayFormatted=mktime(date('H',$firstDateTimestamp),date('i',$firstDateTimestamp),date('s',$firstDateTimestamp),date('m',$firstDateTimestamp),date('d',$firstDateTimestamp)+$i,date('Y',$firstDateTimestamp));
				$graphDates[]=date($granularity,$dayFormatted);
			}

			/******************************************************************************************************************************************************
			*
			*
			*
			*	[create dataset from orders]
			*
			*
			*
			******************************************************************************************************************************************************/
					/**********************************************
					*
					*	[get and tidy up order first]
					*
					**********************************************/
					foreach($ordersQueryRes as $k=>$order){
						if($order->order_ini!=''){
							$orderDetails=maybe_unserialize($order->order_ini);/**unserialize order details**/

							/*************************************************************************************
								some collations - especially if importing from other/older db's that were still
								ISO instead of UTF may get confused by the collation and throw serialization errors
								the following *trys* to fix this , but is not 100% guaranteed to work in all cases
								99% of the time though this won't happen anyway, as it should only ever
								possibly be the case with very early versions of wppizza or if importing from early
								versions that have a different charset.
								....worth a try though regardless
							************************************************************************************/
							if(!isset($orderDetails['total'])){
								//print"".PHP_EOL.$order->id." | ". $order->oDate." | ".$orderDetails['total'];
								$orderDetails=$order->order_ini;
								/**convert currency symbols individuallly first to UTF*/
								$convCurr=iconv("ISO-8859-1","UTF-8", $reportCurrency);
								$orderDetails=str_replace($reportCurrency,$convCurr,$orderDetails);
								/**convert to ISO **/
								$encoding   = mb_detect_encoding($orderDetails);
								$orderDetails=iconv($encoding,"ISO-8859-1//IGNORE", $orderDetails);
								/**unseralize**/
								$orderDetails=maybe_unserialize($orderDetails);
								/**if we still have unrescuable errors we *could*  catch them somewhere */
								if(!isset($orderDetails['total'])){
									//$encoding   = mb_detect_encoding($order->order_ini);
									//$errors=wppizza_serialization_errors($order->order_ini);
									//file_put_contents('','.$order->id.': ['.$encoding.'] '.print_r($order->order_ini,true).' '.print_r($errors,true).PHP_EOL.PHP_EOL,FILE_APPEND);
								}
							}

							if(isset($orderDetails['total'])){
								/**tidy up a bit and get rid of stuff we do not need**/
								unset($orderDetails['currencyiso']);
								unset($orderDetails['currency']);
								unset($orderDetails['discount']);
								unset($orderDetails['item_tax']);
								unset($orderDetails['delivery_charges']);
								unset($orderDetails['tips']);
								unset($orderDetails['selfPickup']);
								unset($orderDetails['time']);
								/**add new**/
								$orderDetails['order_date']=substr($order->oDate,0,10);
								$orderDetails['order_date_formatted']=date($granularity,$order->order_date);
								$orderDetails['order_items_count']=0;
								/**sanitize the items**/
								$itemDetails=array();

								foreach($orderDetails['item'] as $k=>$uniqueItems){
									//$itemDetails[$k]['postId']=$uniqueItems['postId'];
									$itemDetails[$k]['name']=$uniqueItems['name'];
									$itemDetails[$k]['size']=$uniqueItems['size'];
									$itemDetails[$k]['quantity']=$uniqueItems['quantity'];
									$itemDetails[$k]['price']=$uniqueItems['price'];
									$itemDetails[$k]['pricetotal']=$uniqueItems['pricetotal'];
									/**add count of items in this order**/
									$orderDetails['order_items_count']+=$uniqueItems['quantity'];
								}
								/**add relevant item info to array**/
								$orderDetails['item']=$itemDetails;


								$processOrder[]=$orderDetails;
							}
						}
					}



					/**********************************************************************************
					*
					*
					*	lets do the calculations, to get the right dataset
					*
					*
					**********************************************************************************/

					/**************************************
						[initialize array and values]
					**************************************/
					$datasets=array();
					$datasets['sales_value_total']=0;/**total of sales/orders INCLUDING taxes, discounts, charges etc**/
					$datasets['sales_count_total']=0;/**total count of sales**/
					$datasets['items_value_total']=0;/**total of items EXLUDING taxes, discounts, charges etc**/
					$datasets['items_count_total']=0;/**total count of items**/
					$datasets['sales']=array();/*holds data on a per day/month basis*/
					$datasets['bestsellers']=array('by_volume'=>array(),'by_value'=>array());

					/**************************************
						[loop through orders and do things]
					**************************************/
					$j=1;
					foreach($processOrder as $k=>$order){
						/****************************************************
							if we are not setting a defined range
							like a whole month, week , or whatever
							(i.e in overview) lets get first and last day
							we have orders for to be able to calc averages
						****************************************************/
						if($j==1){$datasets['first_date']=$order['order_date'];}
						//if($j==count($processOrder)){$datasets['last_date']=$order['order_date'];}

						/****************************************************
							set garnularity (i.e by day, month or year)
						****************************************************/
						$dateResolution=$order['order_date_formatted'];/**set garnularity (i.e by day, month or year)**/

						/****************************************************
							[get/set totals]
						****************************************************/
						$datasets['sales_value_total']+=$order['total'];
						$datasets['sales_count_total']++;
						$datasets['items_value_total']+=$order['total_price_items'];
						$datasets['items_count_total']+=$order['order_items_count'];

						/****************************************************
							[get/set items to sort for bestsellers]
						****************************************************/
						foreach($order['item'] as $iK=>$oItems){
							$uniqueKeyX=explode('|',$iK);
							/**make a unique key by id and name in case an items name was changed */
							$uKey=MD5($uniqueKeyX[0].$oItems['name'].$oItems['size']);
							if(!isset($datasets['bestsellers']['by_volume'][$uKey])){
								/**lets do by valume and by value at the same time**/
								$datasets['bestsellers']['by_value'][$uKey]=array('price'=>$oItems['pricetotal'], 'single_price'=>$oItems['price'], 'quantity'=>$oItems['quantity'], 'name'=>''.$oItems['name'].' ['.$oItems['size'].']');
								$datasets['bestsellers']['by_volume'][$uKey]=array('quantity'=>$oItems['quantity'], 'price'=>$oItems['pricetotal'], 'single_price'=>$oItems['price'], 'name'=>''.$oItems['name'].' ['.$oItems['size'].']');
							}else{
								$datasets['bestsellers']['by_volume'][$uKey]['quantity']+=$oItems['quantity'];
								$datasets['bestsellers']['by_volume'][$uKey]['price']+=$oItems['pricetotal'];
							}
						}

						/****************************************************
							[get/set totals [per granularity]
						****************************************************/
							/**initialize arrays**/
							if(!isset($datasets['sales'][$dateResolution])){
								$datasets['sales'][$dateResolution]['sales_value_total']=0;
								$datasets['sales'][$dateResolution]['sales_count_total']=0;
								$datasets['sales'][$dateResolution]['items_value_total']=0;
								$datasets['sales'][$dateResolution]['items_count_total']=0;
							}
							$datasets['sales'][$dateResolution]['sales_value_total']+=$order['total'];
							$datasets['sales'][$dateResolution]['sales_count_total']++;
							$datasets['sales'][$dateResolution]['items_value_total']+=$order['total_price_items'];
							$datasets['sales'][$dateResolution]['items_count_total']+=$order['order_items_count'];
					$j++;
				}

				/*******************************
					sort and splice bestsellers
				*******************************/
				arsort($datasets['bestsellers']['by_volume']);
				arsort($datasets['bestsellers']['by_value']);

				if(!isset($_GET['b'])){$bCount=10;}else{$bCount=abs((int)$_GET['b']);}
				array_splice($datasets['bestsellers']['by_volume'],$bCount);
				array_splice($datasets['bestsellers']['by_value'],$bCount);

				/************************************************************
					construct bestsellers html
				*************************************************************/
				$htmlBsVol='<ul>';/*by volume*/
				foreach($datasets['bestsellers']['by_volume'] as $bsbv){
					$htmlBsVol.='<li>'.$bsbv['quantity'].' x '.$bsbv['name'].'</li>';
				}
				$htmlBsVol.='</ul>';

				$htmlBsVal='<ul>';/*by value*/
				foreach($datasets['bestsellers']['by_value'] as $bsbv){
					$htmlBsVal.='<li>'.$bsbv['name'].' <span>'.$reportCurrency.''.wppizza_output_format_price($bsbv['price'],$hideDecimals).'</span><br /> ['.$bsbv['quantity'].' x '.$reportCurrency.''.wppizza_output_format_price($bsbv['single_price'],$hideDecimals).'] <span>'.round($bsbv['price']/$datasets['items_value_total']*100,2).'%</span></li>';
				}
				$htmlBsVal.='</ul>';


				/**********************************************************
					get number of months and days in results array
				***********************************************************/
				if($overview && !$customrange){
					/**in case we have an empty results set**/
					if(!isset($datasets['first_date'])){
						$datasets['first_date']="".date('Y',$wpTime)."-".date('m',$wpTime)."-".date('d',$wpTime)." 00:00:00";
					}
					$firstDate = new DateTime($datasets['first_date']);
					$firstDateFormatted = $firstDate->format($dateformat);
					$lastDate = new DateTime("".date('Y',$wpTime)."-".date('m',$wpTime)."-".date('d',$wpTime)." 23:59:59");
					$lastDateFormatted = $lastDate->format($dateformat);
					$dateDifference = $firstDate->diff($lastDate);
					$daysSelected=$dateDifference->days+1;
					$monthAvgDivider=($dateDifference->m)+1;
				}

				/*****************************************************************
					averages
				******************************************************************/
				/*per day*/
				$datasets['sales_count_average']=round($datasets['sales_count_total']/$daysSelected,2);
				$datasets['sales_item_average']=round($datasets['items_count_total']/$daysSelected,2);
				$datasets['sales_value_average']=round($datasets['sales_value_total']/$daysSelected,2);
				/*per month*/
				$datasets['sales_count_average_month']=round($datasets['sales_count_total']/$monthAvgDivider,2);
				$datasets['sales_item_average_month']=round($datasets['items_count_total']/$monthAvgDivider,2);
				$datasets['sales_value_average_month']=round($datasets['sales_value_total']/$monthAvgDivider,2);

			/******************************************************************************************************************************************************
			*
			*
			*	[sidebar boxes]
			*
			*
			******************************************************************************************************************************************************/
			$box=array();
			$boxrt=array();
			if($overview && !$customrange){
				$box[]=array('id'=>'wppizza-report-val-total','lbl'=>__('All Sales: Total',$locale),'val'=>'<p>'.$reportCurrency.' '.wppizza_output_format_price($datasets['sales_value_total'],$hideDecimals).'<br /><span class="description">'.__('incl. taxes, charges and discounts',$locale).'</span></p>');
				$box[]=array('id'=>'wppizza-report-val-avg','lbl'=>__('All Sales: Averages',$locale),'val'=>'<p>'.$reportCurrency.' '.wppizza_output_format_price($datasets['sales_value_average'],$hideDecimals).' '.__('per day',$locale).'<br />'.$reportCurrency.' '.wppizza_output_format_price($datasets['sales_value_average_month'],$hideDecimals).' '.__('per month',$locale).'</p>');
				$box[]=array('id'=>'wppizza-report-count-total','lbl'=>__('All Orders/Items: Total',$locale),'val'=>'<p>'.$datasets['sales_count_total'].' '.__('Orders',$locale).': '.$reportCurrency.' '.$datasets['items_value_total'].'<br />('.$datasets['items_count_total'].' '.__('items',$locale).')<br /><span class="description">'.__('before taxes, charges and discounts',$locale).'</span></p>');
				$box[]=array('id'=>'wppizza-report-count-avg','lbl'=>__('All Orders/Items: Averages',$locale),'val'=>'<p>'.$datasets['sales_count_average'].' '.__('Orders',$locale).' ('.$datasets['sales_item_average'].' '.__('items',$locale).') '.__('per day',$locale).'<br />'.$datasets['sales_count_average_month'].' '.__('Orders',$locale).' ('.$datasets['sales_item_average_month'].' items) '.__('per month',$locale).'</p>');
				$box[]=array('id'=>'wppizza-report-info','lbl'=>__('Range',$locale),'val'=>'<p>'.$firstDateFormatted.' - '.$lastDateFormatted.'<br />'.$daysSelected.' '.__('days',$locale).'<br />'.$monthAvgDivider.' '.__('months',$locale).'</p>');

				$boxrt[]=array('id'=>'wppizza-report-top10-volume','lbl'=>__('Bestsellers by Volume - All',$locale),'val'=>$htmlBsVol);
				$boxrt[]=array('id'=>'wppizza-report-top10-volume','lbl'=>__('Bestsellers by Value - All (% of order total)',$locale),'val'=>$htmlBsVal);
			}
			if(!$overview || $customrange){
				$box[]=array('id'=>'wppizza-report-val-total','lbl'=>__('Sales Total [in range]',$locale),'val'=>'<p>'.$reportCurrency.' '.wppizza_output_format_price($datasets['sales_value_total'],$hideDecimals).'<br /><span class="description">'.__('incl. taxes, charges and discounts',$locale).'</span></p>');
				$box[]=array('id'=>'wppizza-report-val-avg','lbl'=>__('Sales Averages [in range]',$locale),'val'=>'<p>'.$reportCurrency.' '.wppizza_output_format_price($datasets['sales_value_average'],$hideDecimals).' '.__('per day',$locale).'<br />'.$reportCurrency.' '.wppizza_output_format_price($datasets['sales_value_average_month'],$hideDecimals).' '.__('per month',$locale).'</p>');
				$box[]=array('id'=>'wppizza-report-count-total','lbl'=>__('Orders/Items Total [in range]',$locale),'val'=>'<p>'.$datasets['sales_count_total'].' '.__('Orders',$locale).': '.$reportCurrency.' '.$datasets['items_value_total'].'<br /> ('.$datasets['items_count_total'].' '.__('items',$locale).')<br /><span class="description">'.__('before taxes, charges and discounts',$locale).'</span></p>');
				$box[]=array('id'=>'wppizza-report-count-avg','lbl'=>__('Orders/Items Averages [in range]',$locale),'val'=>'<p>'.$datasets['sales_count_average'].' '.__('Orders',$locale).' ('.$datasets['sales_item_average'].' '.__('items',$locale).') '.__('per day',$locale).'<br />'.$datasets['sales_count_average_month'].' '.__('Orders',$locale).' ('.$datasets['sales_item_average_month'].' items) '.__('per month',$locale).'</p>');
				$box[]=array('id'=>'wppizza-report-info','lbl'=>__('Range',$locale),'val'=>'<p>'.$firstDateFormatted.' - '.$lastDateFormatted.'<br />'.$daysSelected.' '.__('days',$locale).'<br />'.$monthAvgDivider.' '.__('months',$locale).'</p>');

				$boxrt[]=array('id'=>'wppizza-report-top10-volume','lbl'=>__('Bestsellers by Volume [in range]',$locale),'val'=>$htmlBsVol);
				$boxrt[]=array('id'=>'wppizza-report-top10-volume','lbl'=>__('Bestsellers by Value [% of all orders in range]',$locale),'val'=>$htmlBsVal);
			}
			/******************************************************************************************************************************************************
			*
			*
			*	[graph data]
			*
			*
			******************************************************************************************************************************************************/

				/***graph data sales value**/
				$grSalesValue=array();
				foreach($graphDates as $date){
					$str2t=strtotime($date.' 12:00:00');
					$xaxis=date($xaxisFormat,$str2t);
					$val=!empty($datasets['sales'][$date]['sales_value_total']) ? $datasets['sales'][$date]['sales_value_total'] : 0;
					$grSalesValue[]='["'.$xaxis.'",'.$val.']';
				}
				$graph['sales_value']='label:"'.__('sales value',$locale).'",data:['.implode(',',$grSalesValue).']';

				/***graph data sales count**/
				$grSalesCount=array();
				foreach($graphDates as $date){
					$str2t=strtotime($date.' 12:00:00');
					$xaxis=date($xaxisFormat,$str2t);
					$val=!empty($datasets['sales'][$date]['sales_count_total']) ? $datasets['sales'][$date]['sales_count_total'] : 0;
					$grSalesCount[]='["'.$xaxis.'",'.$val.']';
				}
				$graph['sales_count']='label:"'.__('number of sales',$locale).'",data:['.implode(',',$grSalesCount).'], yaxis: 2';

				/***graph data items count**/
				$grItemsCount=array();
				foreach($graphDates as $date){
					$str2t=strtotime($date.' 12:00:00');
					$xaxis=date($xaxisFormat,$str2t);
					$val=!empty($datasets['sales'][$date]['items_count_total']) ? $datasets['sales'][$date]['items_count_total'] : 0;
					$grItemsCount[]='["'.$xaxis.'",'.$val.']';
				}
				$graph['items_count']='label:"'.__('items sold',$locale).'",data:['.implode(',',$grItemsCount).'], yaxis: 3';


		/************************************
			make array to return
		*************************************/
		$data=array();
		$data['currency']=$reportCurrency;
		$data['dataset']=$datasets;
		$data['graphs']=array('data'=>$graph,'label'=>$graphLabel,'hoverOffsetTop'=>$hoverOffsetTop,'hoverOffsetLeft'=>$hoverOffsetLeft,'series'=>array('lines'=>$serieslines,'bars'=>$seriesbars,'points'=>$seriespoints));
		$data['boxes']=$box;
		$data['boxesrt']=$boxrt;
		$data['reportTypes']=$reportTypes;
		$data['view']=($overview && !$customrange) ? 'ini' : 'custom';


	return $data;
	}

	function wppizza_report_export($dataset){
		//print_r($dataset);
		$delimiter=',';
		$encoding='base64';
		$mime='text/csv';
		$extension='.csv';

		$result='';
		$result.='"date", "sales value(incl. taxes, charges and discounts)", "order value(before taxes, charges and discounts)", "number of sales", "number of items sold"  '.PHP_EOL;
		foreach($dataset['sales'] as $date=>$order){
			$result.=$date . $delimiter . $order['sales_value_total']  . $delimiter . $order['items_value_total'] . $delimiter . $order['sales_count_total'] . $delimiter . $order['items_count_total'];
			$result.=PHP_EOL;
		}
		$filename = 'wppizza_report_'.$wpTime.''.$extension.'';
		header("Content-Type: ".$mime."");
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Content-Length: " . strlen($result));
		echo $result;
		exit();
	}

?>