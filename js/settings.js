jQuery(function($){
    load_current($);
    
  
	 
   
});
function authenticate(){
    api_url = jQuery('#api_url').val();
    token_code = jQuery('#token_code').val();
    query_url = api_url+'/api/apps/fb/authenticate?api_key='+token_code;
    jQuery('<form>', {
        'action': query_url,
        'method': 'post'
    }).appendTo('body')
    .submit();
}
function logout_facebook(){
    api_url = jQuery('#api_url').val();
    token_code = jQuery('#token_code').val();
    query_url = api_url+'/api/apps/fb/deauthenticate?api_key='+token_code;
    jQuery('<form>', {
        'action': query_url,
        'method': 'post'
    }).appendTo('body')
    .submit();
}

function load_current($){
    jQuery('.wrap').html('<div class="loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');
    data = {
        action : 'settings_page'
    }
    jQuery.get(ajaxurl , data , function(response){
        jQuery('.loader').remove(); 
        jQuery('.wrap').append(response);
		
		
		$('#activate_button').click(function(){
			activate($);
		});
		$('#deactivate_button').click(function(){
			deactivate($);
		});
        $('#register').click(function(){
            $(this).next('.load_span').html('<div class="loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');
            var $email = $('#email');
            var $first_name = $('#first_name');
            var $last_name = $('#last_name');
            var $password = $('#password');
            var $password2 = $('#password2');
            if($password.val() != $password2.val()){
                alert("Passwords do not match!");
                $('.load_span').html('');
                return false;
            }
            var data = {
                'email' : $email.val() ,  
                'first_name' :$first_name.val()  , 
                'last_name' :$last_name.val() , 
                'password' :$password.val() , 
                'action' : 'registration'
            };
       
            $.post(ajaxurl , data , function(response){
               
                respJson = $.parseJSON(response);
                $('.load_span').html('');
                console.log(response);
                if(respJson.error_code !=  null){
                    alert(respJson.error_msg);
					return;
                }else{
                    document.location.href=window.location.href;
                }
               
                $('.load_span').html('');
                  document.location.href=window.location.href;
            });
        }); 
        $('#login_button').click(function(){
            $(this).next('.load_span').html('<div class="loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');
            var $login_email = $('#login_email');
            var $login_password = $('#login_password');

            var data = {
                'username' : $login_email.val() ,  
                'password' :$login_password.val()  ,  
                'action' : 'login'
            };
       
            $.post(ajaxurl , data , function(response){
                respJson = $.parseJSON(response);
                $('.load_span').html('');
                console.log(response);
                if(respJson.error_code !=  null ){
                    alert(respJson.error_msg);
                }else{
                    document.location.reload();
                }
                   
                     
            });
        });
       
    
        $('#fb_account_auth').click(function(){
            authenticate();
        });
		  $('#fb_account_reauth').click(function(){
            logout_facebook();
        });
		
    })
    
    function activate($){
		$('#load').html('<div class="loader"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div>');
		var data = {
               'action' : 'activate'
            };
		
            $.post(ajaxurl , data , function(response){
				console.log(response);
				
				//document.location.reload();
				document.location.reload();
			}
			)
	}
	function deactivate($){
		$('#load').html('<span class="loader"></span>');
		var data = {
               'action' : 'deactivate'
            };
       
            $.post(ajaxurl , data , function(response){
				document.location.reload();
			}
			)
	}
}