<?php 
$user_api = get_option('om_token_code');
$admin_api = 'f9903425df0934kjert0943509sdf';
if(isset($_REQUEST['token_code'])){
	$content_url =SERVER_APP_URL . '/api/subscription/userstat?admin_api_key='.$admin_api.'&api_key='.$_REQUEST['token_code'];
	
	$content = skst_get_url_content($content_url );
	$json_decoded = @json_decode($content , true);
}
?>
<html>
<head><title>View User subscription</title></head>
<style>.red{color:red; font-weight:bold} .green{color:green; font-weight:bold}</style>
<body>
<?php
if(isset($json_decoded) && !empty($json_decoded)){
	echo '<ul>';
	foreach($json_decoded as $key=>$row){
		if(!is_array($row)){
			echo '<li>'.'<b> '.$key.': </b>'.$row.'</li>';
		} else {
			echo '<li>'.'<b> '.$key.': </b>';
			if(!empty($row)){
				echo '<ul>';
				foreach($row as $k=>$value){
					echo '<li>'.'<b> '.$k.': </b>'.$value.'</li>';
				}
				echo '</ul>';
			}else{
				echo 'None';
			}
			echo  '</li>';
		}
	}
	echo '</ul>';
}

 ?>
<form method="post" action="">
Enter User Token Code: <input type="text" name="token_code" value="<?php echo $user_api?>"/>
<input type="submit" value="View stat"/>
</form>
</body>
</html>