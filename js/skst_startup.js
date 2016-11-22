// JavaScript Document
(function($) {		

	$(".yes_no").live("click",function()
	{
		val=0;
		if($(this).is(":checked"))
		{	
			val=1;
		}
		allowAccess(val);
					
	});
	$(".enable").live("click",function()
	{
		val=1;
		allowAccess(val);
					
	});
	
	
	

})(jQuery);
function allowAccess(val)
{
	data={action:"allowAccess",val:val};
		jQuery.post(ajaxurl,data,function(res){	
				
				if(val==1)
				{
					//alert("Access Enabled");	
				}
				else
				{
					//alert("Access Disabled");					
				}				
				location.reload();							
					
		});	
}