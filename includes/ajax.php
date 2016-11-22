<?php
error_reporting(0);
add_action('wp_ajax_fetch_analytics_data', PLUGIN_PREFIX.'fetch_analytics_data_callback');
add_action('wp_ajax_settings_page', PLUGIN_PREFIX.'settings_page_callback');
add_action('wp_ajax_overview', PLUGIN_PREFIX.'overview_callback');
add_action('wp_ajax_fetch_fb_page_data', PLUGIN_PREFIX.'fetch_fb_page_data_callback');
add_action('wp_ajax_fetch_acc_list', PLUGIN_PREFIX.'fetch_acc_list_callback');
add_action('wp_ajax_fetch_acc_ga_list', PLUGIN_PREFIX.'fetch_acc_list_ga_callback');
add_action('wp_ajax_registration', PLUGIN_PREFIX.'registration_callback');
add_action('wp_ajax_login', PLUGIN_PREFIX.'login_callback');
add_action('wp_ajax_activate', PLUGIN_PREFIX.'activate_callback');
add_action('wp_ajax_deactivate', PLUGIN_PREFIX.'deactivate_callback');
add_action('wp_ajax_set_overview_div_position', PLUGIN_PREFIX.'set_overview_div_position');
add_action('wp_ajax_set_section_div_visibility', PLUGIN_PREFIX.'set_section_div_visibility');
add_action( 'admin_enqueue_scripts',  PLUGIN_PREFIX.'addPieChart');
add_action( 'admin_enqueue_scripts',  PLUGIN_PREFIX.'startUp');
function skst_addPieChart()
{
     wp_enqueue_script('pie-min',MARKET_PLUGIN_URL.'/js/raphael-min.js');
     wp_enqueue_script('morris-min',MARKET_PLUGIN_URL.'/js/morris-0.4.1.min.js');       
       
   
}//end of fucnion
function skst_startUp()
{
     wp_enqueue_script('start-up',MARKET_PLUGIN_URL.'/js/skst_startup.js');
   
}//end of function
function skst_fetch_analytics_data_callback() {
   
   
    $fromDate = @$_REQUEST['from'];
    $toDate = @$_REQUEST['to'];
    $frequency = @$_REQUEST['frequency'];
    $intoTheFuture = false;
    if(strtotime($toDate) > strtotime("now")){
        $intoTheFuture = true;
        $toDate = date('Y-m-d');
    }
    $temp = explode('-' , $toDate);
    $first_date = "{$temp[0]}-{$temp[1]}-01";
    $api_key = get_option('om_token_code');
    $endDateChange = $fromDate;
    $dateDiff = (abs(strtotime($toDate) - strtotime($fromDate))) / (60 * 60 * 24);
    if ($dateDiff < 1)
        $dateDiff = 1;
    $diff = strtotime("- {$dateDiff} days", strtotime($endDateChange));
    $startDateChange = date('Y-m-d', $diff);
   $url = SERVER_APP_URL . '/api/apps/ga/getstat/?from=' . $fromDate . '&to=' . $toDate . '&api_key=' . $api_key;
    $return = skst_get_url_content($url);
   $url = SERVER_APP_URL . '/api/apps/ga/getstat/?from=' . $startDateChange . '&to=' . $endDateChange . '&api_key=' . $api_key;
    $returnChange = skst_get_url_content($url);
    $response = @json_decode($return, true);
   
    if (!$response)
        die('No data found');
    if (isset($response['error_code']) && $response['error_code'] == 115) {
        die( $response['error_message']);
    }
    $responseChange = @json_decode($returnChange, true);
//$response  $responseChange
   ?>
  <article class="common_back">
  <?php
 ?>
        <div class="inLine">
                <p><span class="graph-axes"><img src="<?php echo MARKET_PLUGIN_URL; ?>/img/graph-x.png"></span><span class="titles">Unique Visits</span></p>
                <p><span class="graph-axes"><img src="<?php echo MARKET_PLUGIN_URL; ?>/img/graph-y.png"></span><span>Page Views</span></p>
                <?php if($intoTheFuture) { ?><p> <span id="tomato">* Current month is incomplete. So graph data may be inconsistent. </span></p><?php }?>
                <!--<p><span> <button id="reset-button">Reset Graph</button></span></p>-->
        </div>
        <div class="col-md-6 myGraph" id="chart-container-1">
                <div id="chart2" style="height:300px; width:100%"></div>
        </div>
        <div class="viewClass">
                <div class="params">
                    <p>Unique Visits</p>
                    <?php
                   
                    //$response  $responseChange
                    $unique_visits = skst_formatNum(round((($response['unique_visitors'] - $responseChange['unique_visitors']) / $responseChange['unique_visitors'] ) * 100, 2));  
                    //if($unique_visits > 0) { $class="green"; $delta = '&Delta;';} elseif($unique_visits == 0) { $class="grey"; $delta = '';} else { $class="red"; $delta = '&#9661;'; }
                    if($unique_visits > 0) { $class="green"; $delta = 'greater';} elseif($unique_visits == 0) { $class="grey"; $delta = '';} else { $class="red"; $delta = 'lesser'; }
                    ?>
                    <p><span class="big" title="<?php echo $response['unique_visitors']?>"><?php echo skst_numFormat($response['unique_visitors']); ?></span></p>
                    <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?>"><?php echo $unique_visits; ?>%</span></p>
                </div>
                <div  class="params">
                    <p>Page Views</p>
                    <?php $pageviews = skst_formatNum(round((($response['page_views'] - $responseChange['page_views']) / $responseChange['page_views'] ) * 100, 2));
                    //if($pageviews > 0) { $class="green"; $delta = '&Delta;'; } elseif($pageviews == 0) { $class="grey"; $delta = ''; }  else { $class="red"; $delta = '&#9661;'; }
                    if($pageviews > 0) { $class="green"; $delta = 'greater'; } elseif($pageviews == 0) { $class="grey"; $delta = ''; }  else { $class="red"; $delta = 'lesser'; }
                    ?>
                    <p><span class="big"  title="<?php echo $response['page_views']?>"><?php echo skst_numFormat($response['page_views']);?></span></p>
                    <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?>"><?php echo $pageviews; ?>%</span></p>
                </div>
                <div  class="params">
                    <p>Pages/visit</p>
                    <?php $pagespervisit = skst_formatNum(round((($response['pages_per_visit'] - $responseChange['pages_per_visit']) / $responseChange['pages_per_visit'] ) * 100, 2));
                    //if($pagespervisit > 0) { $class="green"; $delta = '&Delta;'; } elseif($pagespervisit == 0) { $class="grey"; $delta = ''; }  else { $class="red"; $delta = '&#9661;'; }
                    if($pagespervisit > 0) { $class="green"; $delta = 'greater'; } elseif($pagespervisit == 0) { $class="grey"; $delta = ''; }  else { $class="red"; $delta = 'lesser'; }
                    ?>
                    <p><span class="big"  title="<?php echo $response['pages_per_visit']?>"><?php echo round($response['pages_per_visit'], 2); ?></span></p>
                    <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?>"><?php echo $pagespervisit; ?>%</span></p>
                </div>
                   
         
                   <div  class="params">
                    <p>Avg Visit Duration</p>
                    <?php $avgvstdur = skst_formatNum(round((($response['avg_visit_duration'] - $responseChange['avg_visit_duration']) / $responseChange['avg_visit_duration'] ) * 100, 2));
                    //if($avgvstdur > 0) { $class="green"; $delta = '&Delta;'; } elseif($avgvstdur == 0) { $class="grey"; $delta = ''; }  else { $class="red"; $delta = '&#9661;'; }
                    if($avgvstdur > 0) { $class="green"; $delta = 'greater'; } elseif($avgvstdur == 0) { $class="grey"; $delta = ''; }  else { $class="red"; $delta = 'lesser'; }
                    ?>
                    <p><span class="big" title="<?php echo $response['avg_visit_duration']?>"><?php echo round($response['avg_visit_duration'], 2); ?></span></p>
                    <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?>"><?php echo $avgvstdur; ?>%</span></p>
                </div>
                    <div  class="params">
                    <p>Bounce Rate</p>
                    <?php $bouncerate = skst_formatNum(round((($response['bounce_rate'] - $responseChange['bounce_rate']) / $responseChange['bounce_rate'] ) * 100, 2));
                    //if($bouncerate > 0) { $class="green"; $delta = '&Delta;'; } elseif($bouncerate == 0) { $class="grey"; $delta = ''; }  else { $class="red"; $delta = '&#9661;'; }
                    if($bouncerate > 0) { $class="green"; $delta = 'greater'; } elseif($bouncerate == 0) { $class="grey"; $delta = ''; }  else { $class="red"; $delta = 'lesser'; }
                    ?>
                    <p><span class="big"  title="<?php echo $response['bounce_rate']?>"><?php echo round($response['bounce_rate'], 2); ?></span></p>
                    <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?>"><?php echo $bouncerate; ?>%</span></p>
                </div>
                    <div  class="params">
                    <p>Search Engine Visits</p>
                    <?php $bouncerate = skst_formatNum(round((($response['search_engine_visits'] - $responseChange['search_engine_visits']) / $responseChange['search_engine_visits'] ) * 100, 2));
                    //if($bouncerate > 0) { $class="green"; $delta = '&Delta;'; } elseif($bouncerate == 0) { $class="grey"; $delta = ''; }  else { $class="red"; $delta = '&#9661;'; }
                    if($bouncerate > 0) { $class="green"; $delta = 'greater'; } elseif($bouncerate == 0) { $class="grey"; $delta = ''; }  else { $class="red"; $delta = 'lesser'; }
                    ?>
                    <p><span class="big" title="<?php echo $response['search_engine_visits']?>"><?php echo skst_numFormat($response['search_engine_visits']); ?></span></p>
                    <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?>"><?php echo $bouncerate; ?>%</span></p>
                </div>
                   
                </div>
     
    <script>
        jQuery(document).ready(function(){
          var line1 = [];
          var line2 = [];
         
         <?php
         
         if(!empty( $response['unique_visits_graph'])) {
       
                foreach($response['unique_visits_graph'] as $key=>$visit){
                      ?>
                       line1.push(['<?php echo substr($key , 0 , 4).'-'.substr($key , 4 , 2).'-'.substr($key , 6 , 2).' 0:00AM'?>',<?php echo $visit?>]);
                <?php
                    }
                }
                if(!empty( $response['unique_visits_graph'])) {
                    foreach($response['page_views_graph'] as $key=>$visit){
                          ?>
                           line2.push(['<?php echo substr($key , 0 , 4).'-'.substr($key , 4 , 2).'-'.substr($key , 6 , 2).' 0:00AM'?>',<?php echo $visit?>]);
                    <?php }
                    }
                 
       
         
       
          ?>
        function getInterval(dataset){
			 total_data = dataset.length;
			var chartWidth = $('#chart2').width();
			var factor = 0.0278;
			total_possible_column = (chartWidth * factor) - 1;
			//console.log(total_possible);
			var op = Math.round(total_data / total_possible_column);
			return op+' day';
			
		}		
        $("#chart-container-1").resizable({delay:5, helper:'ui-resizable-helper'});
         
                    $(window).resize(function(event, ui) {
                        //console.log("hh");
                          // pass in resetAxes: true option to get rid of old ticks and axis properties
                          // which should be recomputed based on new plot size.
						
						 plot1.replot( { resetAxes: false,  axes:{
                xaxis:{
                    renderer:jQuery.jqplot.DateAxisRenderer,
                    <?php if($frequency == 'daily'){ ?>
                    tickInterval: getInterval(line1),
                     <?php } ?>
                     },
                yaxis:{
                    min:0,
                      pad: 0
						}
					}  } );
                    });
					
					 
          var plot1 = jQuery.jqplot('chart2', [line1 , line2], {
            // series:[{lineWidth:1, markerOptions:{style:'circle'} ,  neighborThreshold: -1 }] ,
            series:[{lineWidth:0.5, markerOptions:{style:'filledCircle'} ,  neighborThreshold: -1, shadow: false ,   showLine: true,  showMarker: true }] ,
            seriesDefaults: {
                  rendererOptions: {
                      smooth: false,
					   showLine: false, 
					   showMarker: true 
                  }
              },
            axes:{
                xaxis:{
                    renderer:jQuery.jqplot.DateAxisRenderer,
                    <?php if($frequency != 'daily'){ ?>
                   
                 
                    tickInterval:'1 month',
                     tickOptions:{formatString:'%m/%y' ,   angle: -90},
                    <?php } else { ?>
                    tickOptions:{formatString:'%m/%d' ,   angle: -90},
                    tickInterval:  getInterval(line1),
                     <?php } ?>
                    min:'<?php echo $fromDate?>',
                    max:'<?php $dat = strtotime("+1 day", strtotime($toDate)); echo date('Y-m-d', $dat); ?>',
                    pad: 0,
                },
                yaxis:{
                    min:0,
                      pad: 0
                }
            }, 
            //grid: {borderColor: 'white', shadow: false, drawBorder: true},
            grid: {borderColor: 'white', gridLineColor: '#eee', background: '#fff', shadow: false, drawBorder: true},
             highlighter: {
                show: true,
                sizeAdjust: 7.5
              },
              cursor:{
                show: true,
                zoom:false,
                showTooltip:false
              }
          });
         
          $('#reset-button').on('click',function() { plot1.resetZoom() });
        });
        </script>
       
    <?php
    error_reporting(E_ALL);
    echo '';
    echo '<div class="handleDivFull">';
    echo '<h2>Top Keywords</h2>
        <table class="wp-list-table widefat resulttbl responsive">';
    echo '<tr><th>Keyword</th><th>Visits</th><th>%</th></tr>';
    if (!empty($response['top_keywords'])) {
         
        foreach ($response['top_keywords'] as  $row) {
            $keyword = $row[0];
            $visits = (int) $row[1];
            $change =  $response['unique_visitors'] > 0 ? (round(($visits / $response['unique_visitors']) * 100, 2) ) : 0;
            echo '<tr><td data-content="Keyword">' . $keyword . '</td><td data-content="Visits">' . $visits . '</td><td data-content="%">' . $change . '%</td></tr>';
             
        }
    } else {
        echo '<tr><td colspan="3" align="center">Not enough Data</td></tr>';
    }
    echo '</table>';
    echo '</div>';
   
    echo '<div class="handleDivFull">';
    echo '<h2>Top Search Engine Referrals</h2>
        <table class="wp-list-table widefat resulttbl responsive">';
    echo '<tbody><tr><th>Search Engine</th><th>Visits</th><th>%</th></tr>';
    if (!empty($response['top_search_engines'])) {
         
        foreach ($response['top_search_engines'] as  $row) {
            $search_engine = $row[0];
            $visits = (int) $row[1];
            $change =  $response['unique_visitors'] > 0 ? (round(($visits / $response['unique_visitors']) * 100, 2) ) : 0;
            echo '<tr><td data-content="Search Engine">' . $search_engine . '</td><td data-content="Visits">' . $visits . '</td><td data-content="%">' . $change . '%</td></tr>';
             
        }
    }else {
        echo '<tr><td colspan="3" align="center">Not enough Data</td></tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
   
    echo '<div class="handleDivFull">';
    echo '<h2>Top Landing pages</h2>
        <table class="wp-list-table widefat resulttbl responsive">';
    echo '<tr><th>URL</th><th>Visits</th><th>%</th></tr>';
   
    if (!empty($response['top_landing_pages'])) {
         
        foreach ($response['top_landing_pages'] as  $row) {
            $landing_page = $row[0];
            $visits = (int) $row[1];
            $change =  $response['unique_visitors'] > 0 ? (round(($visits / $response['unique_visitors']) * 100, 2) ) : 0;
            echo '<tr><td data-content="URL">' . $landing_page . '</td><td data-content="Visits">' . $visits . '</td><td data-content="%">' . $change . '%</td></tr>';
             
        }
    }else {
        echo '<tr><td colspan="3" align="center">Not enough Data</td></tr>';
    }
    echo '</table>';
    echo '</div>';
   
    echo '<div class="handleDivFull">';
    echo '<h2>Visitor Locations</h2>
        <table class="wp-list-table widefat resulttbl responsive">';
    echo '<tr><th>Country</th><th>Visits</th><th>%</th></tr>';
    if (!empty($response['top_countries'])) {
         
        foreach ($response['top_countries'] as  $row) {
            $top_countries = $row[0];
            $visits = (int) $row[1];
            $change =  $response['unique_visitors'] > 0 ? (round(($visits / $response['unique_visitors']) * 100, 2) ) : 0;
            echo '<tr><td data-content="Country">' . $top_countries . '</td><td data-content="Visits">' . $visits . '</td><td data-content="%">' . $change . '%</td></tr>';
             
        }
    }else {
        echo '<tr><td colspan="3" align="center">Not enough Data</td></tr>';
    }
    echo '</table>';
    echo '</div>';
    die();
}
   
function skst_fetch_fb_page_data_callback() {
    $api_key = @$_REQUEST['api_key'];
    $from = isset($_REQUEST['from']) ? $_REQUEST['from'] : date('Y-m-d', strtotime("-30 days"));
    $to = isset($_REQUEST['to']) ? $_REQUEST['to'] : date('Y-m-d');
    $url = SERVER_APP_URL . '/api/apps/fb/pagestat/?api_key=' . $api_key . '&from=' . $from . '&to=' . $to;
    $url_posts = SERVER_APP_URL . '/api/apps/fb/poststat/?api_key=' . $api_key . '&from=' . $from . '&to=' . $to;
    set_time_limit(0);
    $return = skst_get_url_content($url);
    $return_posts = skst_get_url_content($url_posts);
   
    $content = @json_decode($return, true);
    $content_posts = @json_decode($return_posts, true);
   
    $items = @array_slice($content['result'], -5);
    $item_posts = @array_slice($content_posts, -5);
   
    $items_full =  $content['result'];
     
    if (!$content || empty($content))
        die('No result found');
    if (isset($content['error_code']) && $content['error_code'] == 115)
        die("No result found");
       
    if (isset($content['overview']) && !empty($content['overview'])) {
            $overview = $content['overview'];
            ?>
<style>
 #chart-container{
        padding: 0 0 0 19px;
  }
</style>
           
        <article class="common_back">
                    <div class="inLine">
                        <!--<p><span id="yellow"> &#8226; </span><span>Page Visits</span></p>
                        <p><span id="red"> &#8226; </span><span>Total Reach</span></p>-->
                       
                         <p><span class="graph-axes"><img src="<?php echo MARKET_PLUGIN_URL; ?>/img/graph-y.png"></span><span>Weekly Total Reach</span></p>
                         <p><span class="graph-axes"><img src="<?php echo MARKET_PLUGIN_URL; ?>/img/graph-x.png"></span><span>People Talking About This</span></p>
                    </div>
               
                    <div class="col-md-6" id="chart-container">
                        <div id="chart3" style="height:300px;  width:98%"></div>
                    </div>
                   
                    <div class="viewClassFull">
                        <?php if (isset($overview['page_fans']['current'])) {
                            $class = '';
                        $delta = "";
                        ?>
                        <div>
                            <p>Total Likes</p>
                            <?php //if($overview['page_fans']['change']>0) { $class = "green"; $delta = "&Delta;"; }  elseif((int) $overview['page_fans']['change'] == 0) {$class = "grey"; $delta = ""; }  else { $class = "red"; $delta = "&#9661;"; } ?>
                              <?php if($overview['page_fans']['change']>0) { $class = "green"; $delta = "greater"; }  elseif((int) $overview['page_fans']['change'] == 0) {$class = "grey"; $delta = ""; }  else { $class = "red"; $delta = "lesser"; } ?>
 
                            <p><span class="big"><?php echo!empty($overview['page_fans']['current']) ? $overview['page_fans']['current'] : 0 ?></span></p>
                            <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?>"><?php echo ($overview['page_fans']['change']!=0) ? $overview['page_fans']['change'] : '&nbsp;' ?></span></p>
                        </div>
                        <?php } ?>
                        <?php if (isset($overview['page_views_login']['current'])) {
                            $class = '';
                        $delta = "";
                        ?>
                        <div>
                            <p>Page Visits</p>
                            <?php if($overview['page_views_login']['change']>0) { $class = "green"; $delta = "greater"; } elseif((int) $overview['page_views_login']['change'] == 0) {$class = "grey"; $delta = "";} else { $class = "red"; $delta = "lesser"; } ?>
                            <p><span class="big"><?php echo!empty($overview['page_views_login']['current']) ? $overview['page_views_login']['current'] : 0 ?></span></p>
                            <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?>"><?php echo ($overview['page_views_login']['change']!=0) ? $overview['page_views_login']['change'] : '&nbsp;' ?></span></p>
                        </div>
                        <?php } ?>
                        <?php if (isset($overview['page_impressions_unique']['current'])) {
                        $class = '';
                        $delta = "";
                        ?>
                        <div>
                            <p>Total Reach</p>
                        <?php if($overview['page_impressions_unique']['change']>0) { $class = "green"; $delta = "greater"; } elseif($overview['page_impressions_unique']['change'] == 0) {} else { $class = "red"; $delta = "lesser"; } ?>
                            <p><span class="big"><?php echo!empty($overview['page_impressions_unique']['current']) ? $overview['page_impressions_unique']['current'] : 0 ?></span></p>
                                                      <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?>"><?php echo ($overview['page_impressions_unique']['change']==0) ? '&nbsp;' : $overview['page_impressions_unique']['change']; ?></span></p>
                        </div>
                        <?php } ?>
                        <?php if (isset($overview['page_storytellers']['current'])) {
                        $class = '';
                        $delta = "";
                        ?>
                        <div>
                            <p>People Talking</p>
                            <?php if($overview['page_storytellers']['change']>0) { $class = "green"; $delta = "greater"; } elseif( (int) $overview['page_storytellers']['change'] == 0) {$class = "grey"; $delta = "&nbsp;";} else { $class = "red"; $delta = "lesser"; } ?>
                            <p><span class="big"><?php echo!empty($overview['page_storytellers']['current']) ? $overview['page_storytellers']['current'] : 0 ?></span></p>
                            <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?>"><?php echo ($overview['page_storytellers']['change']==0) ? '&nbsp;' : $overview['page_storytellers']['change'];  ?></span></p>
                        </div>
                        <?php }
                        ?>
                       
                    </div>
       
        <script>
         function getInterval(dataset){
			 total_data = dataset.length;
			var chartWidth = jQuery('#chart3').width();
			var factor = 0.0278;
			total_possible_column = (chartWidth * factor) - 1;
			//console.log(total_possible);
			var op = Math.round(total_data / total_possible_column);
			return op+' day';
			
		}	
		jQuery(document).ready(function($){
       
          
           var line1 = [];
          var line2 = [];
          <?php if(!empty( $items_full)) {
                $count = count($items_full);
                foreach($items_full as $key=>$day){
          ?>
           line1.push(['<?php echo  $key.' 0:00AM'?>',<?php echo @$day['page_views_login']?>]);
          line2.push(['<?php echo  $key.' 0:00AM'?>',<?php echo @$day['page_impressions_unique']?>]);
          <?php
                }
          } ?>
         
           
            $("#chart-container").resizable({delay:5, helper:'ui-resizable-helper'});
 
            $(window).resize(function(event, ui) {
               
                  // pass in resetAxes: true option to get rid of old ticks and axis properties
                  // which should be recomputed based on new plot size.
                  plot1.replot(  { resetAxes: false,  
				  axes:{
					xaxis:{
						renderer:jQuery.jqplot.DateAxisRenderer,
						tickInterval: getInterval(line1),
						},
					yaxis:{
						min:0,
						  pad: 0
					}
            }  } );
            });
           
          
            
            var plot1 = jQuery.jqplot('chart3', [line1 , line2], {
         
            axes:{
                xaxis:{
                    renderer:jQuery.jqplot.DateAxisRenderer,
                    tickOptions:{formatString:'%m/%d'},
                    min:'<?php echo $from?>',
                    max:'<?php $stamp = strtotime($to); $plus1 = strtotime("+1 day", $stamp); echo date('Y-m-d' , $plus1)?>',
                    
                    tickInterval:getInterval(line1)
                   
                },
                yaxis:{
                min:0
                }
            },
            series:[{lineWidth:0.5, markerOptions:{style:'markerOptions'}}] ,
            grid: {borderColor: 'white', gridLineColor: '#eee', background: '#fff', shadow: false, drawBorder: true},
            highlighter: {
                show: true,
                sizeAdjust: 7.5
              },
              cursor: {
                show: false
              }
          });
        });
        </script>
           
          
            <?php
        }
       
       
       
  
        echo '<div class="handleDivEntire">';
        echo '<h2>Top Posts</h2>';
        echo '<table class="wp-list-table widefat resulttbl facebook_posts responsive"><tbody><tr>';
        echo '<th>Day </th><th>Posts</th>';
        echo '<th>Likes</th>';
        echo '<th>Comments</th>';
        echo '<th>Reach</th>';
        echo '</tr>';
         if (!empty($item_posts)) {
            foreach ($item_posts as $key => $fb_result) {
                echo '<tr>';
                echo '<td data-content="Day">' . $fb_result['date'] . '</td>';
                echo '<td data-content="Posts" >' . $fb_result['name'] . '</td>';
                echo '<td data-content="Likes">' . $fb_result['likes'] . '</td>';
                echo '<td data-content="Comments">' . $fb_result['comments'] . '</td>';
                echo '<td data-content="Reach">' . $fb_result['reach'] . '</td>';
            }
        }else{
                echo '<td align="center" style="color:tomato" colspan="5">0 posts in this date range.</td>';
        }
        echo '</tbody></table>';
        echo '</div';
       
   
    die();
    ?>
    </article>
    <?php
}
function skst_fetch_acc_list_callback() {
    $url = SERVER_APP_URL . '/api/apps/fb/pageslist?api_key=' . get_option('om_token_code');
    echo $content = skst_get_url_content($url);
    die();
}
function skst_fetch_acc_list_ga_callback() {
    $url = SERVER_APP_URL . '/api/apps/ga/urlslist?api_key=' . get_option('om_token_code');
    $ga_content = skst_get_url_content($url);
    echo $ga_content;
    die();
}
function skst_registration_callback() {
    $data['first_name'] = @$_REQUEST['first_name'];
    $data['last_name'] = @$_REQUEST['last_name'];
    $data['email'] = @$_REQUEST['email'];
    $data['password'] = @$_REQUEST['password'];
    $url = SERVER_APP_URL . '/api/users/register/';
    $return = skst_get_url_content($url, true, $data);
    json_encode($return);
    $response = @json_decode($return, true);
    if (!empty($response['error_code'])) {
        echo json_encode($response);
    } else {
        update_option('om_token_code', $response['token_code']);
        echo json_encode($response);
    }
    die();
}
function skst_login_callback() {
    $url = SERVER_APP_URL . '/api/users/login/?username=' . @$_REQUEST['username'] . '&password=' . $_REQUEST['password'];
    $resp = skst_get_url_content($url, false);
    $response = @json_decode($resp, true);
    if (!empty($response['error_code'])) {
        echo json_encode($response);
    } else {
        update_option('om_token_code', $response['token_code']);
        echo json_encode($response);
    }
    die();
}
function skst_activate_callback() {
    $url = SERVER_APP_URL . '/api/subscription/activate?api_key=' . get_option('om_token_code');
    echo skst_get_url_content($url, true , null);
    die();
}
function skst_deactivate_callback() {
    $url = SERVER_APP_URL . '/api/subscription/deactivate?api_key=' . get_option('om_token_code');
    echo skst_get_url_content($url, true , null);
    die();
}
function skst_settings_page_callback()
{
   
    if(!function_exists('curl_version') )
        {
            ?>
           
           
         <div class="wrapper">
            <div style="clear:both;margin-top:10px;"></div>
            <div style="color:#F00">
            <article id="overview3" class="common_back overview">                              
                                      <div class="viewClassFull">                                                 
                                                    <?php echo NOCURL; ?>                 
                                     
                                      </div>                                      
             </article>
             </div>
             </div>
            <?php
        }
        else
        {
   
   
    $token_code = get_option('om_token_code');
    $ga_url = SERVER_APP_URL . '/api/apps/ga/urlslist?api_key=' . $token_code;
    $ga_account = skst_get_url_content($ga_url);
    $fb_url = SERVER_APP_URL . '/api/apps/fb/pageslist?api_key=' . $token_code;
    $fb_account = skst_get_url_content($fb_url);
    $administration = new skst_Administration;
    $current_plan = $administration->getPlanDetail();
   
    $actDetail = $current_plan['act_detail'];
     
    $site_status = $administration->getSiteStatus();
    $current_plan = isset($current_plan['plan_detail']) ? $current_plan['plan_detail'] : null;
   
    $domain_count = $current_plan['domain_count'];
    $site_remaining = $domain_count > 0 ? $domain_count - $current_plan['used_domains'] : 'N/A';
    $site_activated = $actDetail  ? 'Activated' : 'Not activated';
    if($domain_count < 1 ){
        $plan_name = "Free Plan";
    } else{
        $plan_name = "Premium Plan";
    }
   
    $activate_button =   $actDetail || $domain_count < 1  ? ''  :'<button id="activate_button" class="button button-primary">Activate Site</button>';
    if($domain_count > 0 && $actDetail){
            $activate_button =   '<button id="deactivate_button" class="button ">Deactivate</button>';
   
    }
    if($domain_count > 0 && !$actDetail &&($domain_count - intval($current_plan['used_domains'])) <= 0 ){
            $activate_button =   '<button id="deactivate_button" class="button button-secondary" disabled="disabled">Activate</button>';
   
    }
    $ga_account_decoded = @json_decode($ga_account, true);
    $fb_account_decoded = @json_decode($fb_account, true);
    $items = @$ga_account_decoded['items'];
    ?>
    <style>
    #load {
    display: inline-block;
    padding: 10px;
}
#activate_button , #deactivate_button {
    margin: -6px 17px;
}
    </style>
      <script>
          jQuery(function($) {
            jQuery( "#accordion" ).accordion({
              collapsible: true,
              heightStyle: "content"
            });
          });
  </script>
 
 
 
          <style>
   
           
           
    </style>
   
   
   
 <div class="wrapper">
            <h1><?php echo NEWAPPNAME; ?></h1>
            <h2>Settings</h2>
             
             <div style="clear:both;margin-top:10px;"></div>
             <?php
             
                         if(get_option('skst_allow_access') <>'')
                        {
                            $checked=get_option('skst_allow_access')==1?"checked=checked":"";
                           
                        }
             
             
             ?>
            <article id="overview3" class="common_back update-nag">                              
                                      <div class="viewClassFull">                                                 
                                               <?php echo ACTIVEMSG; ?>
                                               <!-- <input type="checkbox" name="allow_access" value="allow_access" class="yes_no" <?php echo $checked; ?>>    -->                  
                                      </div>                 
                                  
             </article>
             <div style="clear:both;"></div>
             <article id="overview3" class="common_back onOffArticle">
              <div style="display:inline-block;display:table-cell;width:100px;vertical-align:middle">Enable Now?</div>
              <div class="onoffswitch"  style="display:inline-block;">
            
                    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox yes_no" id="myonoffswitch" <?php echo $checked; ?>>
                    <label class="onoffswitch-label" for="myonoffswitch">
                        <div class="onoffswitch-inner"></div>
                        <div class="onoffswitch-switch"></div>
                    </label>
                </div>
            </article>
             
           
   
   
   
       
       <?php
       
       
            if(get_option('skst_allow_access')==1)
            {
   
    ?>
   
                    <div class="wrap2">
  
                                         <div id="accordion">
          <h3>  Account Setup <span class="green right">Active</span></h3>
         
          <div>
                <div class="row myAccord">
                <span class="label">First Name</span><span class="text"><input type="text" name="first_name" id="first_name" autocomplete="off" value="<?php echo isset($current_plan['first_name']) ? $current_plan['first_name'] : '' ?>"/></span>
                <button id="deactivate" class="button button-primary">Deactivate</button> 
                </div>
                <div class="row myAccord">
                <span class="label">Last Name</span><span class="text"><input type="text" name="last_name" id="last_name" autocomplete="off" value="<?php echo isset($current_plan['last_name']) ? $current_plan['last_name'] : '' ?>"/></span>
                </div>
                <div class="row myAccord">
                <span class="label">Email Address</span><span class="text"><input type="text" name="email" id="email" autocomplete="off" value="<?php echo isset($current_plan['email']) ? $current_plan['email'] : '' ?>"/></span>
                </div>
               <?php
                if (!$current_plan) {
                    ?>
                    <div class="row myAccord">
                        <span class="label">Password</span><span class="text"><input type="password" name="password" id="password" autocomplete="off"/></span>
                    </div>
                    <div class="row myAccord">
                        <span class="label">Re-enter Password</span><span class="text"><input type="password" name="password2" id="password2" autocomplete="off"/></span>
                    </div>
                    <?php
                }
                if (!empty($current_plan)) {
                ?>
                <div class="row myAccord">
                <span class="label">Plan</span>
                    <p class="green">
                        <?php echo $plan_name ?>
                    </p>
                    <p class="green">
                        <button id="upgrade_button" class="button  buttonClass">Upgrade</button>
                    </p>
                </div>
                <div class="row myAccord">
                <span class="label">Site Status</span>
                    <p class="green">
                        <?php echo $site_activated; ?>
                    </p>
                    <?php if($activate_button) { ?>
                    <p>
                        <?php echo $activate_button; ?>
                    </p>
                    <?php } ?>
                </div>
                <div class="row myAccord">
                <span class="label">Site Licences Remaining </span>
                  
                    <p class="buy_more">
                        <span><?php echo $site_remaining ?></span> <button id="buy" class="button button-primary buttonClass">Buy More</button>
                        <!--<a href="<? bloginfo('url')?>/viewuser" target="_blank">View user stats </a>-->
                    </p>
                </div>
               
                <div class="row myAccord">
                <button id="upgrade_button"  class="button" onclick="document.location.href='<?php bloginfo('url')?>/upgrade?admin_api_key=f9903425df0934kjert0943509sdf'">Upgrade</button> &nbsp; &nbsp;  &nbsp; &nbsp; <button id="logout_button" style="background-color:#AC433F; border-color:#AC433F" class="button button-primary buttonClass" onclick='document.location.href = "<?php echo admin_url('admin.php?page=wp-' . APP_NAME_SLUG . '-settings&action=logout') ?>";'>Log-out</button><span id="load"></span>
               
                </div>
               
                <?php } else { ?>
            <div class="row myAccord">
                <button id="register" class="button button-primary">Register</button><span class="load_span"></span>
            </div>
            <br/>
            <h3>Or, login :</h3>
            <div class="row myAccord">
                <span class="label">Email Address</span><span class="text"><input type="text" name="login_email" id="login_email" autocomplete="off"/></span>
            </div>
            <div class="row myAccord">
                <span class="label">Password</span><span class="text"><input type="password" name="login_password" id="login_password" autocomplete="off"/></span>
            </div>
            <div class="row myAccord">
                <button id="login_button" class="button button-primary">Login</button><span class="load_span"></span>
            </div>
        <?php } ?>
          </div>
         
         
          <?php if (!empty($current_plan)) { ?>
        <h3>Google Analytics <span class="green right">Active</span></h3>
       
               
                              <div>
                                            <input type="hidden" id="api_url" value="<?php echo SERVER_APP_URL ?>"/>
                                            <input type="hidden" id="token_code" value="<?php echo $token_code ?>"/>
                                            <form name="ga_form" action="" method="post">
                                            <?php if (empty($items)) { ?>
                                                <input type="hidden" name="action" value="save_ga_token"/>
                                            <?php } else { ?>
                                                <input type="hidden" name="action" value="save_ga_url"/>
                                            <?php } ?>
                                             
                                             
                                                <div class="row myAccord">
                                                   
                                                    <span class="label">Select a URL to track </span>
                                                     <?php
                                                    if (!empty($ga_account_decoded)) {
                                   
                                                    if (!empty($items)) {
                                                        ?>
                                                    <select name="ga_account_to_track">
                                                            <option value="">--SELECT ACCOUNT--</option>
                                                            <?php
                                                            foreach ($items as $item) {
                                                                $selected = '';
                                   
                                                                if (isset($item['current']))
                                                                    $selected = "selected='selected'";
                                                                echo '<option value="' . $item['profile_id'] . '"' . $selected . '>' . $item['name'] . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                   
                                                <?php } else { ?>
                                                    <input type="button" class="button button-secondary" value="Authorize Google" id="ga_account_auth" onclick="document.location.href='<?php echo SERVER_APP_URL . '/api/apps/ga/authenticate?api_key=' . $token_code ?>'"/>
                                   
                                                <?php } ?>
                                                <button id="deactivate" class="button button-primary">Deactivate</button>
                                                 <?php if (!empty($ga_account_decoded)) { ?>
                                                    <p> <input type="submit" id="buy" value="Save" class="button button-primary"/>&nbsp;&nbsp;
                                                        <input type="button" id="reauth" class="button button-primary buttonClass" value="Re-authorize" id="ga_account_auth" onclick="document.location.href='<?php echo SERVER_APP_URL . '/api/apps/ga/authenticate?api_key=' . $token_code ?>'"/>
                                                    <?php } ?>
                                                    </p>
                                                     
                                                </div>
                                                <!--<p>
                                                    <button id="buy" class="button button-primary">Save</button>
                                                    <button id="buy" class="button button-primary">Re-authorize</button>
                                                </p>-->
                                             
                                              </form>
                              </div>
         
                  <?php
               
              }//current plan close
                ?>
         
          <h3>Facebook <span class="green right">Active</span></h3>         
               
               
               
                  <div>
                <button id="deactivate" class="button button-primary">Deactivate</button>
          <form name="fb_form" action="" method="post">
         
            <?php if (!empty($fb_account_decoded) && $fb_account_decoded['error_code'] == null) {
                ?>
                <select name="fb_account_to_track">
                    <option value="">--SELECT ACCOUNT--</option>
                    <?php
                    if (empty($fb_account_decoded['error_code'])) {
                        $items = $fb_account_decoded['result'];
                        if (!empty($items)) {
                            foreach ($items as $item
                            ) {
                                $selected = '';
                                if (isset($item['current']))
                                    $selected = "selected='selected'";
                                echo '<option value="' . $item['id'] . '"' . $selected . '>' . $item['name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                   
                    <?php
                }
            } else {
                ?>
                <input type="button" class="button button-secondary" value="Authorize Facebook" id="fb_account_auth"/>
            <?php } ?>
            <?php if (!empty($fb_account_decoded) && $fb_account_decoded['error_code'] == null) { ?>
                <p> <input type="submit" id="save" value="Save" class="button button-primary buttonClass"/> &nbsp;&nbsp; <input type="button" class="button button-primary buttonClass" value="Log-out to re-authorize" id="fb_account_reauth"/></p>
            <?php } ?>
       
        <?php
    //}
    ?>
  
          
          </form>
         
          </div>
                 
         
        
        </div>
 
    <?php
   
            }
              die();
            ?>
     </div>
</div>
<?php
}
die();
}
function skst_overview_callback()
{
   
   
    if(!function_exists('curl_version') )
        {
            ?>
           
           
       
             <div class="wrapper">
            <div style="clear:both;margin-top:10px;"></div>
            <div style="color:#F00">
            <article id="overview3" class="common_back overview">                              
                                      <div class="viewClassFull">                                                 
                                                    <?php echo NOCURL; ?>                 
                                     
                                      </div>                                      
             </article>
             </div>
             </div>
          
            <?php
        }
        else
        {
                if(get_option('skst_allow_access')==1)
                {
                   
               
               
               
                //error_reporting(0);
               
                $startDate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : date('Y-m-d', strtotime('-30days'));
                $endDateChange = isset($_REQUEST['start_date']) ? date('Y-m-d', strtotime("-1 days", strtotime($_REQUEST['start_date']))) : date('Y-m-d', strtotime('-30days'));
                $endDate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : date('Y-m-d', strtotime('now'));
           
                $dateDiff = (abs(strtotime($endDate) - strtotime($startDate))) / (60 * 60 * 24);
                $diff = strtotime("- {$dateDiff} days", strtotime($endDateChange));
                $startDateChange = date('Y-m-d', $diff);
                $token_code = get_option('om_token_code');
                if (empty($token_code)) {
                    echo "<script>document.location.href='" . admin_url('admin.php?page=wp-onemashboard') . "'</script>";
                    die();
                }
               
                 $analyticsUrl = SERVER_APP_URL . '/api/apps/ga/getstat?api_key=' . $token_code . '&from=' . $startDate . '&to=' . $endDate;
                 $facebookUrl = SERVER_APP_URL . '/api/apps/fb/pagestat?api_key=' . $token_code . '&from=' . $startDate . '&to=' . $endDate;
                $analyticsUrlChange = SERVER_APP_URL . '/api/apps/ga/getstat?api_key=' . $token_code . '&from=' . $startDateChange . '&to=' . $endDateChange;
               
                $analyticsResponse = @json_decode(skst_get_url_content($analyticsUrl), true);
           
                $analyticsResponseChange = @json_decode(skst_get_url_content($analyticsUrlChange), true);
                $facebookResponse = @json_decode(skst_get_url_content($facebookUrl), true);
               
               
              
                ?>
           
                <style>.red{font-color:red; font-weight:bold} .gray{font-color:gray; font-weight:bold} .green{font-color:green; font-weight:bold}</style>
               
               
                <?php
               
                 if (isset($analyticsResponse['error_code']) && !empty($analyticsResponse['error_code'] )) {
                    echo("<p>Analytics Account could not be found.</p>");
                }
                if (isset($facebookResponse['error_code']) && $facebookResponse['error_code'] == 115) {
                    echo("<p>Facebook Account could not be found.</p>");
                }
                ?>
                <?php if (isset($analyticsResponse['unique_visitors'])) {
                     
                ?>   
               
               
               
               
               
                    <?php
                   
                    $avg = @($analyticsResponse['bounce_rate'] / $analyticsResponseChange['bounce_rate']) * 100;
                    $avg = round($avg);
                    $neg = ($analyticsResponse['bounce_rate'] - $analyticsResponseChange['bounce_rate']) >= 0 ? false : true;
                    if(!empty($analyticsResponse['bounce_rate'])){ ?>
                    <script>
                    var $j = jQuery.noConflict();
                   
                    Morris.Donut({
                  element: 'donut-example',
                   display : 'inline-block',
                  width : '100%', 
                    labelColor:'#666666',
                    labelColor1:'<?php if($neg){echo '#AC433D';} else echo '#70A73F';?>',
                  data: [
                    {label: "<?php echo (float) round($analyticsResponse['bounce_rate'])?>%", value: <?php echo (float)$analyticsResponse['bounce_rate']?>},
                    {label: "<?php echo 100 - (float) round($analyticsResponse['bounce_rate'])?>%", value: <?php echo 100 - (float)round($analyticsResponse['bounce_rate'])?>},
                   
                  ],
                  formatter : function(y , data){
                     // console.log(y);
                     
                        return '<?php if($neg){echo '▽ - ';} else echo 'Δ + ';?><?php echo $avg;?>%';
                     
                      }
                });
                             
                          jQuery(document).ready(function(){
                             jQuery('tspan').attr('style' , '-webkit-tap-highlight-color: red;');
                          })
                    </script>
                    <?php } ?>
                   
                        <!------------ DRAGGABLE ----------------->
                     <script>
                      jQuery(function($) {
                        jQuery( "#sortable" ).sortable({
                        handle: ".left > img",
                            stop: function(event, ui) {
                                 
                                 $.map($(this).find('article'), function(el) {
                                         updatePositon( el.id  ,  $(el).index() , $);
                                    })
                            }
                        });
                        jQuery( "#sortable" ).disableSelection();
                       
                        function updatePositon( elId , elIndex, $){
                              dat =  {'action' : 'set_overview_div_position' , 'elId' : elId , 'elIndex' : elIndex };
                               $.post(ajaxurl , dat , function(response){
                                    console.log(response);
                               });
                        }
                      });
                     
                     
                      </script>
           
					
                   
                    <style>
                    #donut-example svg text tspan { color : green; }
           
                    </style>
               
               
                <!-- JS CODE ENDS HERE ---->
               
                <?php
                // get current div positons
                 $overview1 = skst_get_overview_div_position('overview1');
                 $overview2 = skst_get_overview_div_position('overview2');
               
               
               
                if( $overview2 == 0 && $overview1 == 0)
                    $overview2 = 1;
                        $unique_visits_display = get_option('div_unique_visits');
                       $ga_page_views_display = get_option('div_ga_page_views');
                       $bounce_rate_display = get_option('div_bounce_rate');
                      $search_engine_visits_display = get_option('div_search_engine_visits');
                     
                      if( $unique_visits_display == ''){
                                         $unique_visits_display = 'inline-block';
                                   }
                                   if( $ga_page_views_display == ''){
                                         $ga_page_views_display = 'inline-block';
                                   }
                                   if( $bounce_rate_display == ''){
                                         $bounce_rate_display = 'inline-block';
                                   }
                                   if( $search_engine_visits_display == ''){
                                         $search_engine_visits_display = 'inline-block';
                                   }
                ?>
                   
                   
                   
               
                    <div id="sortable">
                            <?php ob_start();?>
                               
                                    <!-- GA -->
                               
                                        <?php
                   
                   
                                        //if(get_option('skst_allow_access')==1)
                                        //{
                               
                                        ?>
                                   
                                    <article id="overview1" class="common_back overview">
                                <span class="left"><img src="<?php echo MARKET_PLUGIN_URL; ?>/img/over1.png" /></span>
                                <h2 class="left">Google Analytics</h2>
                                <span class="right fullDisplay">
                                    <img src="<?php echo MARKET_PLUGIN_URL; ?>/img/settings1.png" id="tip66" class="tip" title="" />  
                                 
                                        <div class="tip_div maintip" id="div-tip66">
                                        <input type="checkbox" name="unique_visits" id="unique_visits" <?php if($unique_visits_display == 'inline-block') { ?>checked='checked'<?php } ?> class="disableSection"/> <span class="green">Unique Visits</span> <br>
                                        <input type="checkbox" name="ga_page_views" id="ga_page_views" <?php if($ga_page_views_display == 'inline-block') { ?>checked='checked'<?php } ?> class="disableSection"/> <span class="green">Page Views</span> <br>
                                        <input type="checkbox" name="bounce_rate" id="bounce_rate" <?php if($bounce_rate_display == 'inline-block') { ?>checked='checked'<?php } ?> class="disableSection" /> <span class="green">Bounce Rate</span> <br>
                                        <input type="checkbox"  name="search_engine_visits" id="search_engine_visits" <?php if($search_engine_visits_display == 'inline-block') { ?>checked='checked'<?php } ?> class="disableSection"/> <span class="green">SE visits</span>
                                        </div>
                                 
                                </span>
                                <div class="viewClassFull">
                                 
                                    <div class="fifty <?php echo $unique_visits_display?>" id="div_unique_visits" style="display:<?php echo $unique_visits_display?>">
                                        <h4 class="customSize">Unique Visits
                                        <span class="fullDisplay">
                                            <img src="<?php echo MARKET_PLUGIN_URL; ?>/img/help.png"  title="" class="tip"  id="tip1"/>
                                         
                                            <div class="tip_div" id="div-tip1">
                                                Unique visits is for tracking total number of unique visitors who visited your website.
                                            </div>
                                         </span>
                                        </h4>
                                       
                                       
                                        <p><span class="bigger"><?php echo skst_numFormat($analyticsResponse['unique_visitors']) ?></span></p>
                                        <?php
                                        $temp = skst_formatNum(round((($analyticsResponse['unique_visitors'] - $analyticsResponseChange['unique_visitors'])*(100/$analyticsResponseChange['unique_visitors']))  ,2));
                                        if( $temp > 0) { $class = "green"; $delta = "greater"; } elseif($temp == 0){ $class = "grey"; $delta = "";} else { $class = "red"; $delta = "lesser"; } ?>
                                        <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?> mediumSize"></span><span class="<?php echo $class; ?> mediumSize"><?php echo $analyticsResponseChange['unique_visitors'] != 0 ? skst_formatNum(round((($analyticsResponse['unique_visitors'] - $analyticsResponseChange['unique_visitors'])*(100/$analyticsResponseChange['unique_visitors']))  ,2)) ."%" : '-' ?></span></p>
                                    </div>
                                     
                                     
                                     <div class="fifty <?php echo $ga_page_views_display?>"  id="div_ga_page_views" style="display:<?php echo $ga_page_views_display?>">
                                         <h4 class="customSize">Page Views
                                        <span class="fullDisplay">
                                            <img src="<?php echo MARKET_PLUGIN_URL; ?>/img/help.png"  title="" class="tip"  id="tip2"/>
                                         
                                            <div class="tip_div" id="div-tip2">
                                                 Page Views is for tracking total number of page views by all the visitors.
                                            </div>
                                         </span>
                                        </h4>
                                                                   
                                        <p><span class="bigger"><?php echo skst_numFormat($analyticsResponse['page_views']) ?></span></p>
                                        <?php
                                        $temp = skst_formatNum(round((($analyticsResponse['page_views'] - $analyticsResponseChange['page_views']) * (100 /$analyticsResponseChange['page_views'] ) ), 2));
                                        if($temp > 0) { $class = "green"; $delta = "greater"; } elseif($temp == 0) { $class = "grey"; $delta = ""; } else { $class = "red"; $delta = "lesser"; }  ?>
                                        <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?> mediumSize"></span><span class="<?php echo $class; ?> mediumSize"><?php echo $analyticsResponseChange != 0 ? skst_formatNum(round((($analyticsResponse['page_views'] - $analyticsResponseChange['page_views']) * (100 /$analyticsResponseChange['page_views'] ) ), 2)) ."%": '-' ?></span></p>
                                    </div>
                                     
                                     
                                    <div class="fifty <?php echo $bounce_rate_display?>"  id="div_bounce_rate" style="display:<?php echo $bounce_rate_display?>">
                                        <h4 class="customSize">Bounce Rate
                                        <span class="fullDisplay">
                                            <img src="<?php echo MARKET_PLUGIN_URL; ?>/img/help.png"  title="" class="tip"  id="tip3"/>
                                         
                                            <div class="tip_div" id="div-tip3">
                                                Bounce Rate is the percentage of single-page visits (i.e. visits in which the person left your site from the entrance page without interacting with the page).
                                            </div>
                                         </span>
                                        </h4>
                                                                   
                                        <div id="donut-example" style="width:150px; height:130px; "></div>
                                       
                                    </div>
                                 <div class="fifty <?php echo $search_engine_visits_display?>"  id="div_search_engine_visits" style="display:<?php echo $search_engine_visits_display?>">
                                         <h4 class="customSize">Search Engine Visits
                                        <span class="fullDisplay">
                                            <img src="<?php echo MARKET_PLUGIN_URL; ?>/img/help.png"  title="" class="tip"  id="tip4"/>
                                         
                                            <div class="tip_div" id="div-tip4">
                                                  Search Engine Visits is the number of visits to your website referred by common search engines.
                                            </div>
                                         </span>
                                        </h4>
                                                                   
                                        <p><span class="bigger"><?php echo skst_numFormat($analyticsResponse['search_engine_visits']) ?></span></p>
                                        <?php
                                        $temp = skst_formatNum(round((($analyticsResponse['search_engine_visits'] - $analyticsResponseChange['search_engine_visits']) * (100 /$analyticsResponseChange['search_engine_visits'] ) ), 2));
                                        if($temp > 0) { $class = "green"; $delta = "greater"; } elseif($temp == 0) { $class = "grey"; $delta = ""; } else { $class = "red"; $delta = "lesser"; }  ?>
                                        <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?> mediumSize"></span><span class="<?php echo $class; ?> mediumSize"><?php echo $analyticsResponseChange != 0 ? skst_formatNum(round((($analyticsResponse['search_engine_visits'] - $analyticsResponseChange['search_engine_visits']) * (100 /$analyticsResponseChange['search_engine_visits'] ) ), 2)) ."%": '-' ?></span></p>
                                    </div>
                                 
                                   
                                     
                                    <span class="clear"></span>
                                   <div class="lower_border"></div>
                                </div>
                                <div class="left view"><a style="color:#7db051" href="<?php echo admin_url('admin.php?page=wp-skystats-ga-stats')?>">View Details</a></div>
                              
                                       
                            </article>
                                        <?php
                                        //}
                                        ?>
                           
                                   
                                    <!-- END OF GA -->
                            <?php $overview1_html = ob_get_clean();
                       
                            ?>
                             <?php } else if((isset($analyticsResponse['error_code']) && !empty($analyticsResponse['error_code'] ))) {
                                    echo '<p>No data found for Google Analytics</p>';
                               
                              } ?>
                             
                              <?php
                            if (isset($facebookResponse['overview']) && !empty($facebookResponse['overview'])) {
                                $overview = $facebookResponse['overview'];
                                   $total_likes_display = get_option('div_total_likes');
                                   $page_visits_display = get_option('div_page_visits');
                                   $total_reach_display = get_option('div_total_reach');
                                  $talking_about_display = get_option('div_talking_about');
                                   if( $total_likes_display == ''){
                                         $total_likes_display = 'inline-block';
                                   }
                                   if( $page_visits_display == ''){
                                         $page_visits_display = 'inline-block';
                                   }
                                   if( $total_reach_display == ''){
                                         $total_reach_display = 'inline-block';
                                   }
                                   if( $talking_about_display == ''){
                                         $talking_about_display = 'inline-block';
                                   }
                                  ob_start(); ?> 
                             
                              
                                                <article id="overview2" class="common_back overview">
                                <span class="left"><img src="<?php echo MARKET_PLUGIN_URL; ?>/img/over1.png" /></span>
                                <h2 class="left">Facebook</h2>
                                <span class="right fullDisplay ">
                                    <img src="<?php echo MARKET_PLUGIN_URL; ?>/img/settings1.png" id="tip5" class="tip" title="" />  
                                 
                                        <div class="tip_div maintip" id="div-tip5">
                                        <input type="checkbox" name="total_likes" id="total_likes" <?php if($total_likes_display == 'inline-block') { ?>checked='checked'<?php } ?> class="disableSection"/> <span class="green">Total Likes</span> <br>
                                        <input type="checkbox" name="page_visits" id="page_visits" <?php if($page_visits_display == 'inline-block') { ?>checked='checked'<?php } ?> class="disableSection"/> <span class="green">Page Visits</span> <br>
                                        <input type="checkbox" name="total_reach" id="total_reach" <?php if($total_reach_display == 'inline-block') { ?>checked='checked'<?php } ?> class="disableSection" /> <span class="green">Total Reach</span> <br>
                                        <input type="checkbox"  name="talking_about" id="talking_about" <?php if($talking_about_display == 'inline-block') { ?>checked='checked'<?php } ?> class="disableSection"/> <span class="green">Talking About</span>
                                        </div>
                                 
                                </span>
                               
                               
                              
                                <div class="viewClassFull">
                                    <?php if (isset($overview['page_fans']['current'])) { ?>
                                   
                                    <div class="fifty <?php echo $total_likes_display?>" id="div_total_likes" style="display:<?php echo $total_likes_display?>">
                                        <h4 class="customSize">Total Likes
                                        <span class="fullDisplay">
                                            <img src="<?php echo MARKET_PLUGIN_URL; ?>/img/help.png"  title="" class="tip"  id="tip6"/>
                                         
                                            <div class="tip_div" id="div-tip6">
                                                Total likes is for calculating the total likes for your page over a specific period.
                                            </div>
                                         </span>
                                        </h4>
                                       
                                        <p><span class="bigger"><?php echo !empty($overview['page_fans']['current']) ? $overview['page_fans']['current'] : 0 ?></span></p>
                                        <?php if($overview['page_fans']['change'] > 0 ) { $class= "green"; $delta = "greater"; } elseif($overview['page_fans']['change'] == 0 ) { $class= "grey"; $delta = ""; } else { $class="red"; $delta = "lesser";}?>
                                        <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?> mediumSize"></span><span class="<?php echo $class; ?>"><?php echo ($overview['page_fans']['change']==0) ? '&nbsp;' : $overview['page_fans']['change']; ?></span></p>
                                    </div>
                                     
                                       
                                    <?php } ?>
                                    <?php if (isset($overview['page_impressions_unique']['current'])) { ?>
                                           
                                    <div class="fifty <?php echo $total_reach_display?>" id="div_total_reach" style="display:<?php echo $total_reach_display?>">
                                       
                                        <h4 class="customSize">Total Reach
                                        <span class="fullDisplay">
                                            <img src="<?php echo MARKET_PLUGIN_URL; ?>/img/help.png"  title="" class="tip"  id="tip7"/>
                                         
                                            <div class="tip_div" id="div-tip7">
                                                 Total reach counts the number of unique people who saw your posts, regardless of where they saw it. If your post reaches a person organically and through an ad, that person will count as one for organic reach, one for paid reach and one for total reach.
                                            </div>
                                         </span>
                                        </h4>
                                       
                                        <p><span class="bigger"><?php echo!empty($overview['page_impressions_unique']['current']) ? $overview['page_impressions_unique']['current'] : 0 ?></span></p>
                                        <?php if($overview['page_impressions_unique']['change'] > 0 ) { $class= "green"; $delta = "greater"; } if($overview['page_impressions_unique']['change'] == 0 ) { $class= "grey"; $delta = ""; }  else { $class="red"; $delta = "lesser";}?>
                                        <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?> mediumSize"></span><span class="<?php echo $class; ?>"><?php echo ($overview['page_impressions_unique']['change']==0) ? '&nbsp;' : $overview['page_impressions_unique']['change']; ?></span></p>
                                    </div>
                                   
                                     
                                    <?php } ?>
                                    <?php if (isset($overview['page_storytellers']['current'])) { ?>
                                             
                                    <div class="fifty <?php echo $talking_about_display?>"  id="div_talking_about" style="display:<?php echo $talking_about_display?>">
                                        <h4 class="customSize">Talking about this
                                        <span class="fullDisplay">
                                            <img src="<?php echo MARKET_PLUGIN_URL; ?>/img/help.png"  title="" class="tip"  id="tip8"/>
                                         
                                            <div class="tip_div" id="div-tip8">
                                               People Talking About This is the number of unique users who have created a “story” about a page in a seven-day period. On Facebook, stories are items that display in News Feed.
                                            </div>
                                         </span>
                                        </h4>
                                       
                                        <p><span class="bigger"><?php echo!empty($overview['page_storytellers']['current']) ? $overview['page_storytellers']['current'] : 0 ?></span></p>
                                        <?php if($overview['page_storytellers']['change'] > 0 ) { $class= "green"; $delta = "greater"; } if($overview['page_storytellers']['change'] == 0 ) { $class= "grey"; $delta = ""; }  else { $class="red"; $delta = "lesser";}?>
                                        <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?> mediumSize"></span><span class="<?php echo $class; ?>"><?php echo ($overview['page_storytellers']['change']==0) ? '&nbsp;' : $overview['page_storytellers']['change']; ?></span></p>
                                    </div>
                                   
                                    <?php } ?>
                                    <?php if (isset($overview['page_views_login']['current'])) { ?>
                                     
                                    <div class="fifty <?php echo $page_visits_display?>" id="div_page_visits" style="display:<?php echo $page_visits_display?>">
                                        <h4 class="customSize">Page views
                                        <span class="fullDisplay">
                                            <img src="<?php echo MARKET_PLUGIN_URL; ?>/img/help.png"  title="" class="tip"  id="tip9"/>
                                         
                                            <div class="tip_div" id="div-tip9">
                                                Page Views is the total number of views to your page by the logged in users.
                                            </div>
                                         </span>
                                        </h4>
                                       
                                        <p><span class="bigger"><?php echo!empty($overview['page_views_login']['current']) ? $overview['page_views_login']['current'] : 0 ?></span></p>
                                        <?php if($overview['page_views_login']['change'] > 0 ) { $class= "green"; $delta = "greater"; } if($overview['page_views_login']['change'] == 0 ) { $class= "grey"; $delta = ""; } else { $class="red"; $delta = "lesser";}?>
                                        <p><span class="<?php echo $delta; ?>"></span><span class="<?php echo $class; ?> mediumSize"></span><span class="<?php echo $class; ?>"><?php echo ($overview['page_views_login']['change']==0)? '&nbsp;' : $overview['page_views_login']['change']; ?></span></p>
                                    </div>
                                 
                                    <?php } ?>
                                    <span class="clear"></span>
                                    <div class="lower_border"></div>
                                </div>
                               
                                <div class="left view"><a  style="color:#7db051" href="<?php echo admin_url('admin.php?page=wp-skystats-fb-page-stats')?>">View Details</a></div>
                            </article>
                           
                                <?php
                               
                                        //}
                                ?>
                                   
                                <!-- END OF FB -->
                           
                                <?php $overview2_html = ob_get_clean();
                               
                               
                                ?>
                                <?php
                            } else if(!(isset($facebookResponse['error_code']) && $facebookResponse['error_code'] == 115)){
                                echo '<p>No data found for Facebook</p>';
                            }
                            if($overview1 == 1 && $overview2 == 0 ){
                                   
                                    echo $overview2_html;
                                    echo $overview1_html;
                                } else{
                                   
                                    echo $overview1_html;
                                    echo $overview2_html;
                                }
                            ?>
                             
                            <script>
                    jQuery(function($){
                        $('.disableSection').on('change',function(){
                            elId = $(this).attr('id');
                            if($(this).is(':checked')){
                                display = 'inline-block';
                            }else{
                                display = 'none';
								
                            }
                            updateVisibility( elId , display, $);
                      });
                     
                     
                    function updateVisibility( elId , display, $){
                             
                              dat =  {'action' : 'set_section_div_visibility' , 'elId' : 'div_'+elId , 'display' : display };
                              
                               $.post(ajaxurl , dat , function(response){
                                if(display == 'none'){
									$('#div_'+elId).addClass('none');	
								}else{
									$('#div_'+elId).removeClass('none');
								}
								$('#div_'+elId).css({'display': display});
                                 var tot =  $('#overview2 .fifty:visible').length;
                                 $('#overview2 .fifty:visible').each(function(i){
                                   
                                    // odd selector
                                    if( i % 2 == 0)
                                     {
                                        $(this).addClass('bdright');
                                       
                                     }else{
                                            $(this).removeClass('bdright');
                                         }
                                     if( tot > 2 && (i  == 0 || i == 1))
                                     {
                                        $(this).addClass('bdbottom');
                                       
                                     }else{
                                        $(this).removeClass('bdbottom');
                                     }
                                     
                                }) ;
                                var tot =  $('#overview1 .fifty:visible').length;
                                 $('#overview1 .fifty:visible').each(function(i){
                                   
                                     if( i % 2 == 0)
                                     {
                                        $(this).addClass('bdright');
                                       
                                     }else{
                                            $(this).removeClass('bdright');
                                         }
                                     
                                     if( tot > 2 &&  (i  == 0 || i == 1))
                                     {
                                        $(this).addClass('bdbottom');
                                       
                                     }else{
                                        $(this).removeClass('bdbottom');
                                     }
                                }) ;
								
								/* SUBINA */
								
								var maxWidth = Math.max.apply( null, $( "#overview1 > .viewClassFull > div.fifty" ).map( function () {
								return $( this ).outerWidth( true );
								}).get() );
								//alert(maxWidth);
								$( "#overview1 > .viewClassFull > div.fifty" ).each(function() {
									$(this).css('min-width',maxWidth);
								});
																
								var maxWidth2 = Math.max.apply( null, $( "#overview2 > .viewClassFull > div.fifty" ).map( function () {
								return $( this ).outerWidth( true );
								}).get() );
								//alert("maxWidth2 "+maxWidth2);
								$( "#overview2 > .viewClassFull > div.fifty" ).each(function() {
									$(this).css('min-width',maxWidth2);
								});
								
                               });
                             
                        }
                        var tot =  $('#overview2 .fifty:visible').length;
                         $('#overview2 .fifty:visible').each(function(i){
                           
                            // odd selector
                            if( i % 2 == 0)
                             {
                                $(this).addClass('bdright');
                               
                             }else{
                                $(this).removeClass('bdright');
                             }
                             if( tot > 2 && (i  == 0 || i == 1))
                             {
                                $(this).addClass('bdbottom');
                               
                             }else{
                                $(this).removeClass('bdbottom');
                             }
                             
                        }) ;
                        var tot =  $('#overview1 .fifty:visible').length;
                         $('#overview1 .fifty:visible').each(function(i){
                             
                             if( i % 2 == 0)
                             {
                                $(this).addClass('bdright');
                               
                             }else{
                                $(this).removeClass('bdright');
                             }
                             
                             if( tot > 2 &&  (i  == 0 || i == 1))
                             {
                                $(this).addClass('bdbottom');
                               
                             }else{
                                $(this).removeClass('bdbottom');
                             }
                        }) ;
                    });
                   
                    </script>
					<script>
						jQuery(document).ready(function($) {
							
							var maxWidth = Math.max.apply( null, $( "#overview1 > .viewClassFull > div.fifty" ).map( function () {
							return $( this ).outerWidth( true );
							}).get() );
							//alert(maxWidth);
							$( "#overview1 > .viewClassFull > div.fifty" ).each(function() {
								$(this).css('min-width',maxWidth);
							});
							
							
							var maxWidth2 = Math.max.apply( null, $( "#overview2 > .viewClassFull > div.fifty" ).map( function () {
							return $( this ).outerWidth( true );
							}).get() );
							//alert("maxWidth2 " + maxWidth2); 
							$( "#overview2 > .viewClassFull > div.fifty" ).each(function() {
								$(this).css('min-width',maxWidth2);
							});
							
							
							var numItems = $('#overview1 > .viewClassFull > .none').length;
							var numItems2 = $('#overview2 > .viewClassFull > .none').length;
							if(numItems==3) {
								$("#overview1 > .viewClassFull > .fifty:not(.none)").css("cssText", "border-right: 0px !important");
							}
							if(numItems2==3) {
								$("#overview2 > .viewClassFull > .fifty:not(.none)").css("cssText", "border-right: 0px !important");
							}
							
						});
					</script>
                        </div>
                       
                     
                   
                   
                       
               
              
                <?php
                die();
                }
                else
                {
                    ?>
                       
                         <div style="clear:both;margin-top:10px;"></div>
                        <article id="overview3" class="common_back overview">                              
                                                  <div class="viewClassFull">                                                 
                                                                <?php echo ACTIVELINK; ?>                 
                                                 
                                                  </div>                                      
                         </article>
                   
                    <?php
                   
                }
        }
    die();
}
// getter is defined in functions.php.
function skst_set_overview_div_position(){
     $elId = @$_REQUEST['elId'];
     $elIndex = @$_REQUEST['elIndex'];
     if( $elId == '' || $elIndex == ''){
        echo 'false';die();
    }
     update_option('pos_'.$elId ,  $elIndex );
     echo 'true';die();
}   
function skst_set_section_div_visibility(){
     $elId = @$_REQUEST['elId'];
     $display = @$_REQUEST['display'];
   
     if( $elId == '' || $display == ''){
        echo 'false';die();
    }
     update_option($elId ,  $display);
     echo 'true';die();
}   
add_action("wp_ajax_allowAccess","skst_allowAccess");   
function skst_allowAccess()
{
    //echo "Access";
    $option_key="skst_allow_access";
    $val=$_POST["val"];
    global $wpdb;
    $table_name = $wpdb->prefix . "options";   
    if(get_option($option_key)<>'')
    {
        $sql = "update " . $table_name.           
        " set option_value='".$val."' where option_name='".$option_key."'" ;
    }
    else
    {
        $sql = "INSERT INTO " . $table_name.           
        " VALUES ('','".$option_key."','".$val."','yes')";   
    }   
    $wpdb->query($sql);                                       
    die();   
   
}
