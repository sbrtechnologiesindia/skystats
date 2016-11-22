facebookPageId = jQuery('#fbpageid').val(); 
token_code = jQuery('#token_code').val(); 
app_url = jQuery('#api_url').val(); 
app_name = jQuery('#app_name').val(); 
page_url = jQuery('#page_url').val(); 
ga_accesscode = jQuery('#ga_accesscode').val(); 
gaurltotrack =  jQuery('#gaurltotrack_hdn').val(); 

jQuery(function($){
    
	
    var data = {
        'action': 'fetch_acc_list' , 
        'pageid' : facebookPageId 
        
    };
    jQuery('#facebook_div').html('<span class="loader"></span>');
   
    $.post(ajaxurl, data,
        function(response) {
            
            ret = $.parseJSON(response);
          
            if(ret && ret.error_code  == null){
                saveform = '<h2> Facebook Account </h2><form action="" method="post"> <select name="fbpagetotrack">';
                
                $.each(ret.result, function(index, value) {
                    selected = '';
                    if(facebookPageId == value.id){
                        selected += 'selected="selected"';
                    }
                    saveform += '<option value="'+value.id+'"'+selected+'>'+value.name+'</option>';
                }); 
                saveform += '</select><br/><br/>';
                saveform += '<h2>Google Analytics Account</h2><p>Enter Google Analytics Access Code (<a href="'+app_url+'/api/apps/ga/authenticate?api_key='+token_code+'" target="_blank">Get your code here</a>)</p>';
                saveform += '<input type="text" id="ga_accesscode_text" name="ga_accesscode" value="'+ga_accesscode+'"/><br/><br/>';
                saveform += '<input type="submit" id="submit" value="Save Info" class="button button-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Log Out" class="button button-primary" onclick="document.location.href=\''+page_url+'&action=logout\'">\n\
</form>';
                jQuery('#facebook_div').html(saveform);
               
            } else {
                if(ret.error_code == 99){
                    var regform = '<h2>'+app_name+' Account Setup</h2><div style="width:300px; display:block">\n\
                    <div class="error_summary" id="reg_form_error"></div>\n\
                    <div class="form-field form-required">\n\
                    <span class="label">Username: </span>\n\
                    <span class="input"><input type="text" id="reg_username" class="regcular-text"/></span>\n\
                    </div>\n\
                     \n\
                    <div class="form-field form-required">\n\
                    <span class="label">Password: </span>\n\
                    <span class="input"><input type="password" id="reg_password" class="regcular-text"/></span>\n\
                    </div>\n\
                     <div class="form-field form-required">\n\
                    <span class="label">Re-enter Password: </span>\n\
                    <span class="input"><input type="password" id="reg_password2" class="regcular-text"/></span>\n\
                    </div>\n\
                     <div class="form-field form-required">\n\
                    <span class="label">E-mail Address: </span>\n\
                    <span class="input"><input type="text" id="reg_email" class="regcular-text"/></span>\n\
                    </div>\n\
                     <div class="form-field form-required"><br/><input class="button button-primary" type="button" value="Register" id="register_button"/></div>\n\
                    <br/><br/><b>Or,</b><br/><br/> <div class="error_summary" id="login_form_error"></div>\n\
                     <div class="form-field form-required">\n\
                    <span class="label">E-mail Address / username: </span>\n\
                    <span class="input"><input type="text" id="login_email" class="regcular-text"/></span>\n\
                    </div>\n\<div class="form-field form-required">\n\
                    <span class="label">Password: </span>\n\
                    <span class="input"><input type="password" id="login_password" class="regcular-text"/></span></div>\n\
                    <div class="form-field form-required"><br/><input class="button button-primary" type="button" value="Login" id="login_button"/></div>\n\
                    </div>\n\
                    ';
                    jQuery('#facebook_div').html(regform);
                    jQuery('#register_button').on('click' , registration);
                    jQuery('#login_button').on('click' , login);

                } else if(ret.error_code == 115){
                   
                    var authenticate_button = '<h2>Facebook Account</h2>';
                    authenticate_button += '<input type="button" class="button button-primary button-large" value="Authenticate" onclick="authenticate()"/>';
                    authenticate_button += '<h2>Google Analytics Account</h2><p>Enter Google Analytics Access Code (<a href="'+app_url+'/api/apps/ga/authenticate?api_key='+token_code+'" target="_blank">Get your code here</a>)</p>';
                    authenticate_button += '<input type="text" id="ga_accesscode_text" name="ga_accesscode" value="'+ga_accesscode+'"/><br/><br/>';
                    authenticate_button += '<input type="submit" id="submit" value="Save Info" class="button button-primary">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Unauthorize" class="button button-primary" onclick="document.location.href=\''+page_url+'&action=logout\'">';       
                    jQuery('#facebook_div').html(authenticate_button);
                   
                }
               
            }
            
            jQuery('<div id="loading_ga"><br/><br/><p>Fetching analytics account....</p><span class="loader"></span></div>').insertAfter('#ga_accesscode_text');
            data2 = {
                'action' : 'fetch_acc_ga_list'
            };
            $.get(ajaxurl , data2 , function(response){
                $('#loading_ga').remove();
                console.log(response);
                response = $.parseJSON(response);
                if(response && response.error_code == null){
                    selectBox = '<br/><br/><select name="gaurltotrack" id="gaurltotrack">';
                    $.each(response.items, function(index, value) {
                        opVal = window.btoa(JSON.stringify({
                            'accountId' : value.accountId , 
                            'profile_id' : value.profile_id ,
                            'url' : value.url
                        }));
                       
                        opSelected = opVal == gaurltotrack ? 'selected="selected"' : '';
                        selectBox += '<option value=\''+opVal+'\' '+opSelected+'>'+value.name+'</option>';
                    });
                    selectBox += '</select>';
                    $(selectBox).insertAfter('#ga_accesscode_text');
                } else{
                    $('<p>Google analytics account not authenticated. Please update the token.</p>').insertAfter('#ga_accesscode_text');
                }
            }); 
        });
	
		
		jQuery(".enable").on("click",function()
		{
			alert('hi');			
		});
		
		
   

});

function registration(){
    var $ = jQuery;
    var username = $('#reg_username').val();
    var password = $('#reg_password').val();
    var password2 = $('#reg_password2').val();
    var email = $('#reg_email').val();
    var username_regex = /^[a-z0-9_-]{3,16}$/;
    var password_regex = /^[a-z0-9_-]{6,18}$/;
    var email_regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    var error = '';
    
    if(!username_regex.test(username)){
        error += "<b>* Username should be 3-16 charecters long and can contain [a-z] [0-9] [_-]</b><br/>";
    }
    if(!password_regex.test(password)){
        error += "<b>* Password should be 6-18 charecters long and can contain [a-z] [0-9] [_-]</b><br/>";
    }
    if(password != password2){
        error += "<b>* Password fields do not match.</b><br/>";
    }
    
    if(!email_regex.test(email)){
        error += "<b>* Invalid Email Address.</b><br/>";
    }
    if(error != '')
    {
        $('#reg_form_error').html(error);
        
    }else{
        var data = {
            'username' : username , 
            'password' : password, 
            'email' : email ,  
            'action': 'registration'
        };
        jQuery('#facebook_div').html('<span class="loader"></span>');
        $.post(ajaxurl, data, function(response) {
            location.reload();
        })   
    }
}

function login(){
    var $ = jQuery;
    var username = $('#login_email').val();
    var password = $('#login_password').val();
    var error = '';
    if(username == ''){
        error += '<b>* Username can\'t be empty.</b><br/>'; 
    }
    if(password == ''){
        error += '<b>* Password can\'t be empty.</b><br/>'; 
    }
    if(error != '')
    {
        $('#login_form_error').html(error);
        
    }else{
        var data = {
            'username' : username , 
            'password' : password,
            'action'   : 'login'
        };
        $.post(ajaxurl, data, function(response) {
            location.reload();
        })   
    }
}
function authenticate(){
    query_url = app_url+'/api/apps/fb/authenticate?api_key='+token_code;
    jQuery('<form>', {
        'action': query_url,
        'method': 'post'
    }).appendTo('body')
    .submit();
}

