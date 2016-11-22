<?php



function skst_get_url_content($url, $post = false, $data = null) {
    // ummm let's make our bot look like human
 
   $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,  CURLOPT_REFERER, @$_SERVER['HTTP_HOST']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:  "));
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_URL, $url);
    return curl_exec($ch);
}
function skst_formatNum($num)
{
	$num = round($num , 2);
	if($num > 0)
		 return "+{$num}";
	return "{$num}";
		 
}

function skst_fetchAnalyticsResult(){
	
    $startDate = isset($_REQUEST['from']) ? $_REQUEST['from'] : date('Y-m-d', strtotime('-30days'));
    $endDateChange = isset($_REQUEST['to']) ? date('Y-m-d', strtotime("-1 days", strtotime($_REQUEST['from']))) : date('Y-m-d', strtotime('-30days'));
    $endDate = isset($_REQUEST['to']) ? $_REQUEST['to'] : date('Y-m-d', strtotime('now'));

    $dateDiff = (abs(strtotime($endDate) - strtotime($startDate))) / (60 * 60 * 24);
    $diff = strtotime("- {$dateDiff} days", strtotime($endDateChange));
    $startDateChange = date('Y-m-d', $diff);
    $token_code = get_option('om_token_code');
   
    $analyticsUrl = SERVER_APP_URL . '/api/apps/ga/overview?api_key=' . $token_code . '&from=' . $startDate . '&to=' . $endDate;
    $facebookUrl = SERVER_APP_URL . '/api/apps/fb/pagestat?api_key=' . $token_code . '&from=' . $startDate . '&to=' . $endDate;
    $analyticsUrlChange = SERVER_APP_URL . '/api/apps/ga/overview?api_key=' . $token_code . '&from=' . $startDateChange . '&to=' . $endDateChange;
    $analyticsResponse = @json_decode(skst_get_url_content($analyticsUrl), true);
	$analyticsResponseChange = @json_decode(skst_get_url_content($analyticsUrlChange), true);
    return array($analyticsResponse  ,$analyticsResponseChange );
	
	
}



function skst_get_overview_div_position($elId){
	  return (int) get_option('pos_'.$elId);
}	
function skst_numFormat($num) {
  $x = round($num);
  $x_number_format = number_format($x);
  $x_array = explode(',', $x_number_format);
  $x_parts = array('k', 'm', 'b', 't');
  $x_count_parts = count($x_array) - 1;
  $x_display = $x;
  $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
  $x_display .= $x_parts[$x_count_parts - 1];
  return $x_display;
}
