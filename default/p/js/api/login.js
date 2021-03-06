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
                        location.reload();
						return;
					}
					else
					{
		                $('.login-button img').show();
						leOpenDialog('登入錯誤', json.message, leDialogType.MESSAGE);
					}
				},
				error: function(xhr, status, err)
				{
					if(status == 'timeout' || status == 'abort')
					{
						leOpenDialog('登入錯誤', '連線逾時或網路錯誤，請按確定返回登入畫面重新登入。', leDialogType.MESSAGE, function()
						{
							location.reload();
						});
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

function OnQuickLogin(deviceId, gameId)
{
    location.href='/api/ui_quick_login?deviceid=' + deviceId + '&site=' + gameId;
}