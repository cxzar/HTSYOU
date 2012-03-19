$(document).ready(function(){

	if (switchOne == 1){
		$("#alrContainSwitch").css("display", "none");
		$("#alrContainSwitchOne").css("display", "block");
		$("#btnSwitchOneLogin").css("display", "none");
	}
	
	//height
	if ( $("#alrContainRegister").height() > $("#alrContainLogin").height() ){
		$maxHeight = ($("#alrContainRegister").height()) + + 120 + "px";
	} else {
		$maxHeight = ($("#alrContainLogin").outerHeight(true) + 120) + "px";
	}
	$("#alrContainMaxHeight").css("height", $maxHeight);
	
	$("#alrContainRegister").css("display", "none");
	$("#btnSwitchRegister").addClass("btnSwitchOn");
	$("#btnSwitchLogin").addClass("btnSwitchOff");
	
	
	$isLogin = 1;
	
	$("#btnSwitchRegister, #btnSwitchOneRegister").click(function(){
		if ($isLogin == 1){ $isLogin = 0;
			//switch one
				$("#btnSwitchOneRegister").css("display", "none");
				$("#btnSwitchOneLogin").css("display", "block");
			//switch multi
			$("#alrContainLogin").toggle(toggleEffect1,'fast',function(){
				$("#alrContainLogin").css("display", "none");
			});
				$("#btnSwitchRegister").removeClass("btnSwitchOn");
				$("#btnSwitchLogin").removeClass("btnSwitchOff");
				$("#btnSwitchRegister").addClass("btnSwitchOff");
				$("#btnSwitchLogin").addClass("btnSwitchOn");
			$("#alrContainRegister").delay(200).toggle(toggleEffect2,'fast',function(){
				$isLogin = 2;	
			});
		};
	});
	$("#btnSwitchLogin, #btnSwitchOneLogin").click(function(){
		if ($isLogin == 2){ $isLogin = 0;
			//switch one
				$("#btnSwitchOneLogin").css("display", "none");
				$("#btnSwitchOneRegister").css("display", "block");
			//switch multi
			$("#alrContainRegister").toggle(toggleEffect1,'fast',function(){
				$("#alrContainRegister").css("display", "none");
			});
				$("#btnSwitchRegister").removeClass("btnSwitchOff");
				$("#btnSwitchLogin").removeClass("btnSwitchOn");
				$("#btnSwitchRegister").addClass("btnSwitchOn");
				$("#btnSwitchLogin").addClass("btnSwitchOff");
			$("#alrContainMaxHeight").css("height", $maxHeight);
			$("#alrContainLogin").delay(200).toggle(toggleEffect2,'fast',function(){
				$isLogin = 1;	
			});
		};
	});
	
	//Remember Login
	$isRemember = 0;
	$(".alrLabelRemember").click(function(){
		if ($isRemember == 0){
			$(".alrCheckRemeber").prop("checked", true);
			$(this).addClass("checked");
			$isRemember = 1;
		}
		else if ($isRemember == 1){
			$(".alrCheckRemeber").prop("checked", false);
			$(this).removeClass("checked");
			$isRemember = 0;
		};
		
	});
	
	//Resize Login Width
	if ($("#alrContainLogin").width() < 180){
		$loginUsernameLabel = $.trim($('#alrLabelUsername').html());
		$loginPasswordLabel = $.trim($('#alrLabelPassword').html());
		$('#alrLoginUsername').prop('value', $loginUsernameLabel);
		$('#alrLoginPassword').prop('value', 'password');
		$("#alrContainLogin .alrLabelText").css("display", "none");
		$(".alrLoginSubmit").addClass("nextLine");
		$(".alrForgotUsername").addClass("nextLine");
		$(".alrForgotPassword").addClass("nextLine");
		$("#alrContainLogin a").css("font-size", "10px !important");
		$(".alrLabelRemember label").css("font-size", "10px !important");
		$("#alrContainSwitch li").addClass("nextLine");
		$("#btnSwitchLogin").css("margin-bottom", "6px");
	} else if ($("#alrContainLogin").width() < 220){
		$("#alrContainLogin .alrRecord").css("font-size", "12px");
		$("#alrContainLogin input").css("font-size", "12px !important");
		$("#alrContainLogin a").css("font-size", "10px !important");
		$("#alrContainLogin label").css("font-size", "10px !important");
		$(".alrLabelRemember label").css("font-size", "10px !important");
	}else if ($("#alrContainLogin").width() < 250){
		$("#alrContainLogin .alrRecord").css("font-size", "12px");
		$("#alrContainLogin input").css("font-size", "14px !important");
		$("#alrContainLogin a").css("font-size", "10px !important");
		$("#alrContainLogin label").css("font-size", "12px !important");
	} else if ($("#alrContainLogin").width() < 320){
		$("#alrContainLogin .alrRecord").css("font-size", "14px !important");
		$("#alrContainLogin input").css("font-size", "14px !important");
		$("#alrContainLogin a").css("font-size", "12px !important");
		$("#alrContainLogin label").css("font-size", "12px !important");
	} else if ($("#alrContainLogin").width() < 400){
		$("#alrContainLogin .alrRecord").css("font-size", "18px");
		$("#alrContainLogin input").css("font-size", "18px !important");
		$("#alrContainLogin a").css("font-size", "14px !important");
		$(".alrLabelRemember label").css("font-size", "14px !important");
	} else {
		$("#alrContainLogin .alrRecord").css("font-size", "20px");
		$("#alrContainLogin input").css("font-size", "20px !important");
		$("#alrContainLogin a").css("font-size", "14px !important");
		$(".alrLabelRemember label").css("font-size", "14px !important");
	};
	
	//Resize Register Width
	if ($("#alrContainLoginRegister").width() < 220){
		$("#alrContainRegister .alrLabelText").css("display", "none");
		$registerNameLabel = $.trim($('#alrRegisterNameLbl').html());
		$registerUsernameLabel = $.trim($('#alrRegisterUsernameLbl').html());
		$registerPasswordLabel = $.trim($('#alrRegisterPasswordLbl').html());
		$registerPassword2Label = $.trim($('#alrRegisterPassword2Lbl').html());
		$registerEmailLabel = $.trim($('#alrRegisterEmailLbl').html());
		$registerEmail2Label = $.trim($('#alrRegisterEmail2Lbl').html());
		$('#alrRegisterName').prop('value', $registerNameLabel);
		$('#alrRegisterUsername').prop('value', $registerUsernameLabel);
		$('#alrRegisterPassword').prop('value', 'password');
		$('#alrRegisterPassword2').prop('value', 'password');				$('#alrGuildPassword').prop('value', 'Guild Password');
		$('#alrRegisterEmail').prop('value', $registerEmailLabel);
		$('#alrRegisterEmail2').prop('value', $registerEmail2Label);
		$('li').css('font-size', '12px');		
	} else if ($("#alrContainLoginRegister").width() < 320){
		$("#alrContainRegister .alrLabelText label").css("font-size", "10px !important");
		$("#alrContainRegister input").css("font-size", "12px !important");
	} else if ($("#alrContainLoginRegister").width() < 420){
		$("#alrContainRegister .alrLabelText label").css("font-size", "12px !important");
		$("#alrContainRegister input").css("font-size", "12px !important");
	} else if ($("#alrContainLoginRegister").width() < 500){
		$("#alrContainRegister .alrLabelText label").css("font-size", "16px !important");
		$("#alrContainRegister input").css("font-size", "16px !important");
	} else if ($("#alrContainLoginRegister").width() > 500){
		$("#alrContainRegister .alrLabelText label").css("font-size", "16px !important");
		$("#alrContainRegister input").css("font-size", "16px !important");
	};
	
	
		
});