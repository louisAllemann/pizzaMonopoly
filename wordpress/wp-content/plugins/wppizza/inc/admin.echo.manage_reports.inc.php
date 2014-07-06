<?php
/******************************************************************************************************************************************************
*
*
*	[get the data]
*
*
******************************************************************************************************************************************************/
$data=wppizza_report_dataset($this->pluginOptions,$this->pluginLocale,$this->pluginOrderTable);
/**make some vars to use**/
$selectedReport=!empty($_GET['report']) ? $_GET['report'] : '';
$fromVal=!empty($_GET['from']) ? $_GET['from'] : '';
$toVal=!empty($_GET['to']) ? $_GET['to'] : '';
$exportLabel=($data['view']=='ini') ? __('Export All',$this->pluginLocale) : __('Export Range',$this->pluginLocale);
/******************************************************************************************************************************************************
*
*
*	[echo html]
*
*
******************************************************************************************************************************************************/
	/**h2**/
	echo"<h2>". $this->pluginName." ".__('Reports', $this->pluginLocale)."</h2>";
?>

<!-- range select -->
<div id="wppizza-reports-range"  class="button">

	<?php
	echo"".__('Report',$this->pluginLocale)." : ";
	?>
	<select id="wppizza-reports-set-range">
		<?php
		print"<option value='' >".__('Overview',$this->pluginLocale)."</option>";
		foreach($data['reportTypes'] as $rkey=>$rArr){
			print"<option value='".$rkey."' ".selected($selectedReport,$rkey,true).">".$rArr['lbl']."</option>";
		}
		if(isset($_GET['from']) && isset($_GET['to'])){
			print"<option selected='selected'>".__('Custom Range',$this->pluginLocale)."</option>";
		}
		?>
	</select>
	<?php
	echo"".__('Custom range',$this->pluginLocale)." : ";
	?>
	<input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php echo $fromVal ?>" name="wppizza_reports_start_date" id="wppizza_reports_start_date" readonly="readonly" />
	<input type="text" size="9" placeholder="yyyy-mm-dd" value="<?php echo $toVal ?>" name="wppizza_reports_end_date" id="wppizza_reports_end_date" readonly="readonly" />
	<input type="button" class="button" value="<?php _e('Go',$this->pluginLocale) ?>" id="wppizza_reports_custom_range" />
	<input type="button" class="button" value="<?php echo $exportLabel ?>" id="wppizza_reports_export" />
</div>


<!--  boxes and graphs -->
<div id="wppizza-reports-details">
	<!--  sidebar boxes -->
	<div id="wppizza-sidebar-reports" class="wppizza-sidebar">
	<?php
		foreach($data['boxes'] as $vals){
			print'<div id="'.$vals['id'].'" class="postbox wppizza-reports-postbox">';
			print'<h3 class="button">'.$vals['lbl'].'</h3>';
			print''.$vals['val'].'';
			print'</div>';
		}
	?>
	</div>
	<!--  graphs -->
	<div id="wppizza-reports-canvas-wrap">
		<h4><?php print"".$data['graphs']['label'] ?></h4>
		<div id="wppizza-reports-canvas"></div>
		<ul id="wppizza-report-choices"></ul>
	</div>

	<div id="wppizza-sidebar-reports-right" class="wppizza-sidebar-right">
	<?php
		foreach($data['boxesrt'] as $vals){
			print'<div id="'.$vals['id'].'" class="postbox wppizza-reports-postbox-right">';
			print'<h3 class="button">'.$vals['lbl'].'</h3>';
			print''.$vals['val'].'';
			print'</div>';
		}
	?>
	</div>
</div>
<?php
/******************************************************************************************************************************************************
*
*
*	[javascript]
*
*
******************************************************************************************************************************************************/
?>
<script>
jQuery(document).ready(function($){
$(function() {
		var datasets = {
			<?php
				$i=0;
				foreach($data['graphs']['data'] as $gk=>$gv){
					if($i>0){print",";};
					print'"'.$gk.'":{'.$gv.'}';
				$i++;
				}
			?>
		};
		/*********tooltip hover*****/
		$("<div id='wppizza-reports-tooltip'></div>").appendTo("body");
		$("#wppizza-reports-canvas").bind("plothover", function (event, pos, item) {
				if (item) {
					var x = item.datapoint[0],
						y = item.datapoint[1].toFixed(2);

					$("#wppizza-reports-tooltip").html(y)
						.css({top: item.pageY-<?php echo $data['graphs']['hoverOffsetTop'] ?>, left: item.pageX+<?php echo $data['graphs']['hoverOffsetLeft'] ?>})
						.fadeIn(200);
				} else {
					$("#wppizza-reports-tooltip").hide();
				}
		});
		/************colours***************/
		var i = 1;
		$.each(datasets, function(key, val) {
			val.color = i;
			++i;
		});

		/************radios***************/
		var choiceContainer = $("#wppizza-report-choices");
		$.each(datasets, function(key, val) {
			if(key=='sales_value'){var valchkd='checked="checked"';}else{var valchkd='';}
			choiceContainer.append("<li><label for='" + key + "'><input type='radio' name='wppizza-graph-select' "+valchkd+" id='" + key + "' />"+ val.label + "</label></li>");
		});
		choiceContainer.find("input").click(plotAccordingToChoices);

		/************format legend***************/
		function legendFormatter(v, axis) {
			if(axis.n==1){
				return "<?php echo $data['currency'] ?> "+v.toFixed(2);
			}else{
				return v.toFixed(0);
			}
		}
		/************plot***************/
		function plotAccordingToChoices() {
			var data = [];
			choiceContainer.find("input:checked").each(function () {
				var key = $(this).attr("id");
				if (key && datasets[key]) {
					data.push(datasets[key]);
				}
			});
			if (data.length > 0) {
				$.plot("#wppizza-reports-canvas", data,{
					series: {
						lines: {
							show: <?php echo $data['graphs']['series']['lines'] ?>
						},
						bars: {
							show: <?php echo $data['graphs']['series']['bars'] ?>,
							barWidth: 0.6,
							align: "center"
						},
						points: {
							show: <?php echo $data['graphs']['series']['points'] ?>
						}
					},
					grid: {
						hoverable: true
					},
					xaxis: {
						mode: "categories"
					},
					yaxis: {
						min:0,
						tickDecimals: 0,
						tickFormatter: legendFormatter
					}
				});
			}
		}
		plotAccordingToChoices();
	});

});
</script>