$(document).ready(function()
{	
	$("#login_form").validate({
		onfocusout: false,
		onkeyup: false,
		onclick: false,
		messages:
		{
			account: {
				required: "`電子信箱`或`行動電話`必填"
			},
			pwd: {
				required: "`密碼`尚未填寫",
				minlength: "`密碼`最少6碼",
				maxlength: "`密碼`最多18碼",
			},
		},
		showErrors: function(errorMap, errorList)
		{
		   var err = '';
		   $(errorList).each(function(i, v)
		   {
			   err += v.message + "<br/>";
		   });
		   if (err)
		   {
		        $('.login-button img').show();
				leOpenDialog('登入錯誤', err, leDialogType.MESSAGE);
		   }
		},
		submitHandler: function(form)
		{
			$(form).ajaxSubmit({
				dataType: 'json',
				success: function(json)
				{
					if (json.status == 'success')
					{
						if ($('#redirect_url').val())
							location.href = $('#redirect_url').val();
						else
							location.href = '/member/index?site='+json.site;
						
						return;
					}
					else
					{
		                $('.login-button img').show();
						leOpenDialog('登入錯誤', json.message, leDialogType.MESSAGE);
					}
				}		
			});
		}
	});
	
    $('.login-button img').on('click',function(event){
		$(this).hide();
		$('#doSubmit').trigger('click');
    });
});

