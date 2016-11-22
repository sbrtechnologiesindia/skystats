<?php
set_time_limit(0);

class skst_Administration {

    var $data = array();

    function skst_Administration() {
		
		add_filter('admin_head', array($this, PLUGIN_PREFIX.'add_jquery_data'));
        add_action('admin_menu', array($this, PLUGIN_PREFIX.'AddAdminMenu'));
    }
	
	function skst_add_jquery_data() {
	
	?>
<script>
	jQuery(window).load(function($){
	 msieversion($);
});
function msieversion($) {
           
            if (jQuery.browser.msie) {
				
				jQuery('body').addClass('ie');
				
			} else{
				  return false;
			}
            return false;
}
</script>
<style>

 .loader {
      margin: 10px auto;
		width: 50px;
		height: 30px;
		font-size: 10px;
    }

    .rect2 , .rect5 ,  .rect3 ,  .rect4  {
       animation: 1.2s ease-in-out 0s normal none infinite stretchdelay;
	   -webkit-animation: stretchdelay 1.2s infinite ease-in-out;
		background-color: #333333;
		display: inline-block;
		height: 126%;
		margin: 0 2px;
		width: 6px;
    }

    .rect2 {
      -webkit-animation-delay: -1.1s;
      animation-delay: -1.1s;
    }

     .rect3 {
      -webkit-animation-delay: -1.0s;
      animation-delay: -1.0s;
    }

     .rect4 {
      -webkit-animation-delay: -0.9s;
      animation-delay: -0.9s;
    }

     .rect5 {
      -webkit-animation-delay: -0.8s;
      animation-delay: -0.8s;
    }

    @-webkit-keyframes stretchdelay {
      0%, 40%, 100% { -webkit-transform: scaleY(0.4) }
      20% { -webkit-transform: scaleY(1.0) }
    }

    @keyframes stretchdelay {
      0%, 40%, 100% {
        transform: scaleY(0.4);
        -webkit-transform: scaleY(0.4);
      } 20% {
        transform: scaleY(1.0);
        -webkit-transform: scaleY(1.0);
      }
    }
  
.ie .loader {
    background: url("<?php echo MARKET_PLUGIN_URL?>/img/18-1.gif") no-repeat  !important;
    display: block  !important;
    height: 11px  !important;
    width: 43px  !important;
}

.ie .loader .rect5 , .ie .loader .rect4 ,.ie .loader .rect3 ,.ie .loader .rect2 ,.ie .loader .rect1{
display: none !important;
}
</style>
	<?php
    
}

	
    function skst_AddAdminMenu() {
       
        
            add_menu_page(APP_NAME . ' Dashboard', NEWAPPNAME, 'read', 'wp-' . APP_NAME_SLUG, array($this, PLUGIN_PREFIX.'siteOverview'), MARKET_PLUGIN_URL . '/img/mashboard-icon.png');
            add_submenu_page('wp-' . APP_NAME_SLUG, APP_NAME . ' Dashboard', 'Dashboard', 'read', 'wp-' . APP_NAME_SLUG, array($this, PLUGIN_PREFIX.'siteOverview'), MARKET_PLUGIN_URL . '/img/mashboard-icon.png');
            add_submenu_page('wp-' . APP_NAME_SLUG, 'Google Analytics', 'Google Analytics', 'read', 'wp-' . APP_NAME_SLUG . '-ga-stats', array($this, PLUGIN_PREFIX.'adminGaStat'));
            add_submenu_page('wp-' . APP_NAME_SLUG, ' Facebook', ' Facebook', 'read', 'wp-' . APP_NAME_SLUG . '-fb-page-stats', array($this, PLUGIN_PREFIX.'adminFbPageStat'));

            add_submenu_page('wp-' . APP_NAME_SLUG, APP_NAME . ' Settings', 'Settings', 'read', 'wp-' . APP_NAME_SLUG . '-settings', array($this, PLUGIN_PREFIX.'siteSettings'));
        
    }
	
	function skst_addXchart() 
	{
		 wp_enqueue_script('jquery');
		 wp_enqueue_script('xcharts',MARKET_PLUGIN_URL.'/js/xcharts.js',array('jquery'));
		 wp_enqueue_script('d3',MARKET_PLUGIN_URL.'/js/d3.min.js',array('jquery','xcharts'));
		
      ?>
      
       
 		
	<?php 	
	
		 
		 wp_enqueue_script('jqplot',MARKET_PLUGIN_URL.'/js/jquery.jqplot.min.js',array('jquery','xcharts','d3'));
		 wp_enqueue_script('cursor',MARKET_PLUGIN_URL.'/js/jqplot.cursor.min.js',array('jquery','xcharts','d3','jqplot'));
		 wp_enqueue_script('dateAxis',MARKET_PLUGIN_URL.'/js/jqplot.dateAxisRenderer.min.js');
		 wp_enqueue_script('highlighter',MARKET_PLUGIN_URL.'/js/jqplot.highlighter.min.js');

	}

    function skst_adminGaStat() 
	{
		
		if(!function_exists('curl_version') )
		{
			?>
            
            
		
			<div style="clear:both;margin-top:10px;"></div>
            <div style="background-color:#FFF;border:1px solid #333;padding:10px 0px 10px 10px;width:400px;color:#F00">
			<article id="overview3" class="common_back overview">                               
                                      <div class="viewClassFull">                                      			
                                                	<?php echo NOCURL; ?>                  
                                      
                                      </div>                    	 			 
             </article>
             </div>
            <?php
		}
		else
		{
		
        wp_enqueue_script('jquery-ui-datepicker', array('jquery'));

        $api_key = get_option('om_token_code');
        if (empty($api_key)) 
		{
            echo "<script>document.location.href='" . admin_url('admin.php?page=wp-' . APP_NAME_SLUG) . "-settings'</script>";
            die();
        }
		wp_enqueue_style('custom-style', MARKET_PLUGIN_URL . '/css/admin.css');
		wp_enqueue_style('custom-responsive-style', MARKET_PLUGIN_URL . '/css/responsive.css');
		wp_enqueue_style('ng_responsive_tables', MARKET_PLUGIN_URL . '/css/ng_responsive_tables.css');
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        
        wp_enqueue_style('flexigrid-style', MARKET_PLUGIN_URL . '/js/Flexigrid/css/flexigrid.pack.css');
		wp_enqueue_script('jquery-ui', MARKET_PLUGIN_URL . '/js/jquery-ui.js', array('jquery'));
        wp_enqueue_script('custom-script', MARKET_PLUGIN_URL . '/js/stats_ga.js', array('jquery', 'jquery-ui-datepicker', 'jquery-flexigrid'));
        wp_enqueue_script('jquery-flexigrid', MARKET_PLUGIN_URL . '/js/Flexigrid/js/flexigrid.pack.js', array('jquery', 'jquery-ui-datepicker'));
        wp_enqueue_script('ng_responsive_tables', MARKET_PLUGIN_URL . '/js/ng_responsive_tables.js', array('jquery'));
        wp_enqueue_script('ng_custom', MARKET_PLUGIN_URL . '/js/custom.js', array('jquery'));
		wp_enqueue_style('jqplot', MARKET_PLUGIN_URL . '/css/jqplot.css');
		
		// ADDED ON 04-06-2014
		
		wp_enqueue_style('font-montserrat', 'http://fonts.googleapis.com/css?family=Montserrat:400,700');
		wp_enqueue_style('font-open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,600');
		
		
        add_action( 'admin_footer', array($this , PLUGIN_PREFIX.'addXchart')  ); 
        
        $startDate = date('Y-m-d', strtotime('-30days'));
        $endDate = date('Y-m-d', strtotime('now'));

        $ga_account_to_track = @json_decode($ga_account_to_track, true);

        $api_key = get_option('om_token_code');
        echo '<div class="wrap">';
		echo '<div class="wrapper">';
		?>
		<h1><?php echo NEWAPPNAME; ?></h1>
        <h2>Google Analytics</h2>
	

		<?php
		
		if(get_option('skst_allow_access')==1)
		{		
        $output = "";
        $output .= '<article id="wrap"><div class="pcontainer">';
        $output .= '<input type="hidden" id="api_key" value="' . $api_key . '"/>';

       	$output .= '<div class="row dateRan"><b>Date Range: </b><span class="field"><input type="text" id="from" placeholder="From" value="' . $startDate . '"/><span class="inLine-space"></span><input type="text" id="to" placeholder="To" value="' . $endDate . '"/></span>';
		
		$output .= '<b>Chart Plotting Frequency:&nbsp;&nbsp; </b>';
		
		$output .= '<span class="field"><select name="frequency" id="frequency">
                               <option value="daily">Daily</option>
                               <option value="monthly">Monthly</option>
                             </select></span>';
		
		$output .= '<input type="button" id="analytics_fetch" class="button action" value="Update"/>';
		
		$output .= '</div>';
		
        $output .= '<div class="row" id="result"></div>';
       
	   

       $output .='</div></article>';
	   
	   
	   echo $output;
	   
	    	
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
		
		
		
		//echo '<div class="clear"></div>';
		 echo '</div>';
		 echo '</div>';
		}
	
    }
	

    function skst_adminFbPageStat() 
	{
		
		
		if(!function_exists('curl_version') )
		{
			?>
            
            
		
			<div style="clear:both;margin-top:10px;"></div>
            <div style="background-color:#FFF;border:1px solid #333;padding:10px 0px 10px 10px;width:400px;color:#F00">
			<article id="overview3" class="common_back overview">                               
                                      <div class="viewClassFull">                                      			
                                                	<?php echo NOCURL; ?>                  
                                      
                                      </div>                    	 			 
             </article>
             </div>
            <?php
		}else
		{
        wp_enqueue_style('custom-style', MARKET_PLUGIN_URL . '/css/admin.css');
		// ADDED ON 04-06-2014
		wp_enqueue_style('custom-responsive-style', MARKET_PLUGIN_URL . '/css/responsive.css');
		wp_enqueue_style('ng_responsive_tables', MARKET_PLUGIN_URL . '/css/ng_responsive_tables.css');
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_script('jquery-ui-datepicker', array('jquery'));		
		wp_enqueue_script('jquery-ui', MARKET_PLUGIN_URL . '/js/jquery-ui.js', array('jquery'));
        wp_enqueue_script('custom-script', MARKET_PLUGIN_URL . '/js/stats_fb_page.js', array('jquery'));
		wp_enqueue_script('ng_responsive_tables', MARKET_PLUGIN_URL . '/js/ng_responsive_tables.js', array('jquery'));
		wp_enqueue_style('jqplot', MARKET_PLUGIN_URL . '/css/jqplot.css');
		wp_enqueue_script('ng_custom', MARKET_PLUGIN_URL . '/js/custom.js', array('jquery'));
		
		wp_enqueue_style('font-montserrat', 'http://fonts.googleapis.com/css?family=Montserrat:400,700');
		wp_enqueue_style('font-open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,600');
		
		
        add_action( 'admin_footer', array($this , PLUGIN_PREFIX.'addXchart')  ); 
		
        $output = '';
        $api_key = get_option('om_token_code');
        $startDate = date('Y-m-d', strtotime('-30days'));
        $endDate = date('Y-m-d', strtotime('now'));
        if (empty($api_key)) 
		{
            echo "<script>document.location.href='" . admin_url('admin.php?page=wp-' . APP_NAME_SLUG) . "-settings'</script>";
            die();
        }
		?>
		<div class="wrap">
		<div class="wrapper">
            <h1><?php echo NEWAPPNAME; ?></h1>
            <h2>Facebook Analytics</h2>
            
            <article id="wrap">
			<?php
			if(get_option('skst_allow_access')==1)
			{	
			$output .= '<div class="pcontainer">';
			$output .= ' <input type="hidden" id="api_key" value="' . $api_key . '"/>
						   ';
			$output .= '<div class="row dateRan"><b>Date Range: </b><span class="field"><input type="text" id="from" placeholder="From" value="' . $startDate . '"/><span class="inLine-space"></span><input type="text" id="to" placeholder="To" value="' . $endDate . '"/></span><input type="button" id="fb_page_fetch" class="button action" value="Update"/></div>';
			/* $output .= '<div class="row"><input type="button" id="fb_page_fetch" class="button action" value="Fetch records"/></div>'; */
			$output .= '<div class="row" id="result"></div>';
			$output .= '</div>';
			echo $output;
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
			?>
			</article>
		</div>
		</div>
			<?php
		}
    }

    public function getPlanDetail()
	{
        $api_key = get_option('om_token_code');
        if (empty($api_key))
            return false;

        $url = SERVER_APP_URL . '/api/users/user_plan?api_key=' . $api_key;
        $return = skst_get_url_content($url);
		
        $decoded = @json_decode($return, true);
        if (empty($decoded))
            return false;

        return $decoded;
    }

    public function getSiteStatus() {
        $api_key = get_option('om_token_code');
        if (empty($api_key))
            return false;

        $url = SERVER_APP_URL . '/api/users/sites/domainstatus?api_key=' . $api_key . '&domain=' . $_SERVER['SERVER_NAME'];
        $return = skst_get_url_content($url);
        $decoded = @json_decode($return, true);
        if (empty($decoded))
            return false;
        return @$decoded['domain_status'];
    }

    public static function aasort(&$array, $key) {
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }
        $array = $ret;
    }
	
	function setting_scripts() 
	{			
		 wp_enqueue_script('jquery-ui-script', '//code.jquery.com/ui/1.10.4/jquery-ui.js', array('jquery'));
    ?>
   
    <?php
    }
	
    function skst_siteSettings() {
		
		wp_enqueue_style('jqueryui' , '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        wp_enqueue_script('custom-script', MARKET_PLUGIN_URL . '/js/settings.js', array('jquery'));
        wp_enqueue_style('custom-style', MARKET_PLUGIN_URL . '/css/settings.css');
		wp_enqueue_style('adminCSS', MARKET_PLUGIN_URL . '/css/admin.css');
			wp_enqueue_style('custom-responsive-style', MARKET_PLUGIN_URL . '/css/responsive.css');
		add_action('admin_footer',array($this, 'setting_scripts'));

        if (isset($_REQUEST['action'])) {
            switch ($_REQUEST['action']) {
                case 'save_ga_token' :
                    update_option('ga_accesscode', $_REQUEST['ga_accesscode']);
                    $token_save_url_ga = SERVER_APP_URL . '/api/apps/ga/authenticate?api_key=' . get_option('om_token_code') . '&accesscode=' . $_REQUEST['ga_accesscode'];
                    $ga_account = skst_get_url_content($token_save_url_ga, true, array());

                    break;
                case 'logout' :
                    delete_option('om_token_code');
                    $logout_url = SERVER_APP_URL . '/api/users/logout?api_key=' . get_option('om_token_code');
                    echo '<script>document.location.href="' . admin_url('admin.php?page=wp-' . APP_NAME_SLUG) . '-settings"</script>';
					die();
                    break;
            }
        }

        if (isset($_REQUEST['ga_account_to_track'])) {
            //update_option('ga_account_to_track', $_REQUEST['ga_account_to_track']);
            $url = SERVER_APP_URL . '/api/apps/ga/saveproperty?api_key=' . get_option('om_token_code');
            $data['ga_property'] = @$_REQUEST['ga_account_to_track'];

            $response = @json_decode(skst_get_url_content($url, true, $data), true);
            if (isset($response['error_code']) && !empty($response['error_code'])) {
                $error = "<div id=\"message\" class=\"error\"><p>{$response['error_msg']}</p></div>";
            }

            if (empty($response['error_code'])) {
                $error = "<div id=\"message\" class=\"updated\"><p>{$response['success_msg']}</p></div>";
            }
        }
        if (isset($_REQUEST['fb_account_to_track'])) {
            $url = SERVER_APP_URL . '/api/apps/fb/saveproperty?api_key=' . get_option('om_token_code');
            $data = array();
            $data['fb_property'] = @$_REQUEST['fb_account_to_track'];
            $data['api_key'] = get_option('om_token_code');

            $ret = skst_get_url_content($url, true, $data);

            $response = @json_decode($ret, true);
            if (isset($response['error_code']) && !empty($response['error_code'])) {
                $error = "<div id=\"message\" class=\"error\"><p>{$response['error_msg']}</p></div>";
            }

            if (empty($response['error_code'])) {
                $error = "<div id=\"message\" class=\"updated\"><p>{$response['success_msg']}</p></div>";
            }
        }
        ?>

        <?php
        if (isset($error)) {
            echo $error;
        }
        ?>
        <div class="wrap">


        </div>
        <div class="clear"></div>
        <?php
    }
	
	
	function startTooltip() {		
		
      wp_enqueue_script('jquery-ui-script', '//code.jquery.com/ui/1.10.4/jquery-ui.js', array('jquery'));
	  wp_enqueue_script('jquery-ui-core', array('jquery'));	 		
 	  wp_enqueue_script('hoverIntent', '', array('jquery','jquery-ui-core'));
	 
	 ?>   
        		
		
        <script>
        jQuery(function($){
           
		   jQuery(document).on('mouseenter','.ui-sortable',function() { 
		   
			  jQuery(".tip").hoverIntent({
            over: emin,
            out: emout,
            timeout : 300,
        });
		    
         jQuery('.tip_div').mouseleave(function(){
            jQuery(this).hide();
         });   
		   });
          
        
            
        });
        function IsMouseOver(oi)
        {
           var temp = jQuery(oi).find(":hover");
           return temp.length == 1;
        }


         var emout = function(){
                
                var this_id = jQuery(this).attr('id');
                
                var this_tip_div = 'div-'+this_id;
              
                if(!IsMouseOver('#'+this_tip_div))
                    jQuery('#'+this_tip_div).hide();

                   
            }
        var emin = function (){
               
                var this_id = jQuery(this).attr('id');
                var this_tip_div = 'div-'+this_id;
                //alert(this_tip_div);
                
                jQuery('#'+this_tip_div).show();
                //$('#'+this_id + ' img').show();
                
                jQuery('#'+this_tip_div).addClass('tip_before');
                }
        </script>
	
				
		 <style>
			.tip_before:before {
				content : url('<?php echo MARKET_PLUGIN_URL; ?>/img/chat-tail-white.png'); 
				position: absolute;
				transform:rotate(360deg);
				-ms-transform:rotate(360deg); /* IE 9 */
				-webkit-transform:rotate(360deg);
		
				  left: 2em;
				 top: -1em;
			}
			.maintip.tip_before:before {
				left: 4.6em;
				
			}
			.tip_after:after {
				content : ''; 
				
			}
			.tip_div {
				background-color : white;
				}
			img.tip {
			  padding-left: 2px;
			  vertical-align: middle;
			  cursor:pointer;
			}
			  #donut-example{
			 margin-top: -10px;

			  }
		</style>

    
<?php }
	
	

    public function skst_siteOverview() 
	{

        wp_enqueue_script('jquery-ui-datepicker', array('jquery'));
        wp_enqueue_script('custom-script', MARKET_PLUGIN_URL . '/js/overview.js', array('jquery', 'jquery-ui-datepicker'));
        wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		wp_enqueue_style('custom-style', MARKET_PLUGIN_URL . '/css/admin.css');
		wp_enqueue_style('custom-responsive-style', MARKET_PLUGIN_URL . '/css/responsive.css');
		wp_enqueue_style('font-montserrat', 'http://fonts.googleapis.com/css?family=Montserrat:400,700');
		wp_enqueue_style('font-open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,600');		


		add_action( 'admin_footer', array($this , 'startTooltip')  ); 
		$api_key = get_option('om_token_code');
		if(empty($api_key)){
			echo '<script>document.location.href="' . admin_url('admin.php?page=wp-' . APP_NAME_SLUG) . '-settings"</script>';
			die();
		}
        wp_enqueue_style('custom-style', MARKET_PLUGIN_URL . '/css/overview.css');
        $startDate = date('Y-m-d', strtotime('-30days'));
        $endDate = date('Y-m-d', strtotime('now'));
        ?> 

        <div class="wrap">
		<div class="wrapper">
			<h1><?php echo NEWAPPNAME; ?></h1>
            <h2>Overview</h2>
			
			<article id="wrap">
                    <div class="pcontainer">
                        <input type="hidden" id="api_key" value=""/>
                
                        <div class="row">
                            <b>Date Range:&nbsp;&nbsp; </b>
                            <span class="field">
                                <input type="text" id="start_date" name="start_date" value="<?php echo $startDate ?>" placeholder="from"><div class="inLine-space"></div><input type="text" id="end_date" name="end_date" value="<?php echo $endDate ?>"/>
                            </span>
							<input type="button" value="Update" class="button button-primary action" id="view_stats"/>
                            
                        </div>
   
                        <div class="row" id="result"></div>
                    </div>
            </article>
            <div class="clear"></div>
            <div id="result">

            </div>
			 </div>
        </div><div class="clear"></div>
        <?php
    }	
	
    public static function skst_formatNum($num, $addPtg = false) {
        if ($addPtg)
            $numX = $num . '%';
        else
            $numX = $num;
        if ($num < 0)
            return '<span class="red"/>' . $numX . '</span>';
        elseif ($num == 0)
            return '<span class="gray"/>' . $numX . '</span>';
        else
            return '<span class="green"/>+' . $numX . '</span>';
    }

}//gti end of class 
