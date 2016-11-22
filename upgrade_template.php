<?php 
if(!session_id()){
	session_start();
	
}
if(isset($_POST['seat_number']))
{
	$content_url =SERVER_APP_URL . '/api/subscription/upgrade';
	$data = array('api_key' => get_option('om_token_code') , 'admin_api_key' => @$_REQUEST['admin_api_key'] , 'seat_number' =>  @$_REQUEST['seat_number']);
	$content = skst_get_url_content($content_url , true , $data );
	//echo($content);
	$return = @json_decode($content , true);
	if(isset($return['error_message'])){
		$error = $return['error_message'];
	}
	if(isset($return['success_message'])){
		$success = $return['success_message'];
	}
} else{
	$_SESSION['refurl'] = $_SERVER['HTTP_REFERER'];
}
$admin_api_key = isset($_REQUEST['admin_api_key']) ? $_REQUEST['admin_api_key'] : '';



?>
<html>
<head><title>Upgrade subscription</title></head>
<style>.red{color:red; font-weight:bold} .green{color:green; font-weight:bold}</style>
<body>
<?php if(!isset($success)){ ?>
<form name="sform" method="post">
<?php if(isset($error)){ ?>
<p class="red">
<?php echo $error?>
</p>
<?php } ?>
<?php if(isset($success)){ ?>
<p class="green">
<?php echo $success?>
</p>
<?php } ?>

<input type="hidden" name="admin_api_key" value="<?php echo $admin_api_key?>"/>
Number of seats: <input type="number" name="seat_number"/>
<input type="submit" value="Upgrade"/>
</form>
<?php } else { ?>
<br/>You are being redirected to your site....
 <META http-equiv="refresh" content="2;URL=<?php echo @$_SESSION['refurl'] ?>"> 

<?php  } ?>
</body>
</html>