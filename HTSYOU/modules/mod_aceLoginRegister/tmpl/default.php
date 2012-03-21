<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_aceLoginRegister
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
JHTML::_('behavior.mootools');
$document = &JFactory::getDocument();

if ($disableJQuery != 1) {
$document->addScript('http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.1.min.js');
}
if ($disableJQueryUI != 1) {
$document->addScript('http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.17/jquery-ui.min.js');
}

$document->addStyleSheet('modules/mod_aceLoginRegister/tmpl/css/default.css' );
$document->addStyleSheet('modules/mod_aceLoginRegister/tmpl/css/layout'.$selectLayout.'.css' );
$document->addStyleSheet('modules/mod_aceLoginRegister/tmpl/css/theme'.$selectTheme.'.css' );

//guest
if ($user->guest) {
	echo "<style type='text/css'>#alrContainMember{display: none;}</style>";
} else {
	echo "<style type='text/css'>#alrContainMaxHeight{display: none;}</style>";
};

//switch controls
echo "<script type='text/javascript'>
		var switchOne = '$switchOne';
	</script>";

//animation effect
echo "<script type='text/javascript'>
		var selectEffect = '$selectEffect';
		if (selectEffect == 1){ toggleEffect1 = 'clip' };
		if (selectEffect == 1){ toggleEffect2 = 'bounce' };
		if (selectEffect == 2){ toggleEffect1 = 'fold' };
		if (selectEffect == 2){ toggleEffect2 = 'fold' };
	</script>";

//login labels inside
if ($loginLabelsInside == 1){
	echo "<style type='text/css'>#alrContainLogin .alrLabelText{display: none;}</style>";
	echo "<script type='text/javascript'>
		jQuery(document).ready(function(){
		jQuery('#alrLoginUsername').prop('value', '$loginUsernameLabel');
		jQuery('#alrLoginPassword').prop('value', 'password');
	});
	</script>";
}

//register labels inside
if ($registerLabelsInside == 1){
	echo "<style type='text/css'>#alrContainRegister .alrLabelText{display: none;}</style>";
	echo "<script type='text/javascript'>
		jQuery(document).ready(function(){
			jQuery('#alrRegisterName').prop('value', '$registerNameLabel');
			jQuery('#alrRegisterUsername').prop('value', '$registerUsernameLabel');
			jQuery('#alrRegisterPassword').prop('value', 'password');
			jQuery('#alrRegisterPassword2').prop('value', 'password');
			jQuery('#alrGuildPassword').prop('value', 'Guild Password');
			jQuery('#alrRegisterEmail').prop('value', '$registerEmailLabel');
			jQuery('#alrRegisterEmail2').prop('value', '$registerEmail2Label');
			jQuery('#alrRegisterSubmitBtn').prop('value', '$registerSubmit');
		});
	</script>";
}

//Hide Register
if ($loginAndRegister == 2){
	echo "<style type='text/css'>#alrContainSwitch{display: none;}</style>";
	echo "<style type='text/css'>#alrContainSwitchOne{display: none !important;}</style>";
	echo "<script type='text/javascript'>
		jQuery(document).ready(function(){
		var maxHeight = (jQuery('#alrContainLogin').outerHeight(true) + 30) + 'px';
		jQuery('#alrContainMaxHeight').css('height', maxHeight);
		});
		</script>";
}

//Hide Login
if ($loginAndRegister == 3){
	echo "<style type='text/css'>#alrContainSwitch{display: none;}</style>";
	echo "<style type='text/css'>#alrContainSwitchOne{display: none !important;}</style>";
	echo "<style type='text/css'>#alrContainLogin{display: none;}</style>";
	echo "<style type='text/css'>#alrContainRegister{display: block !important;}</style>";
	echo "<script type='text/javascript'>
		jQuery(document).ready(function(){
		var maxHeight = (jQuery('#alrContainRegister').outerHeight(true) + 30) + 'px';
		jQuery('#alrContainMaxHeight').css('height', maxHeight);
		});
		</script>";
}

//Hide Forgot
if ($hideForgot == 1){
	echo "<style type='text/css'>.alrForgot{display: none;}</style>";
}

//Hide Forgot
if ($hideRemember == 1){
	echo "<style type='text/css'>.alrLabelRemember{display: none;}</style>";
}

//Hide Forgot
if ($forceRemember == 1){
	echo "<style type='text/css'>.alrForgot{display: none;}</style>";
}

//Width
if ($moduleHeight != "auto"){
	define("moduleHeightpx", "".$moduleHeight."px");
	echo "<style type='text/css'>#alrContainMaxHeight{height: ".moduleHeightpx." !important;}</style>";
}

//Height
if ($moduleWidth != "auto"){
	define("moduleWidthpx", "".$moduleWidth."px");
	echo "<style type='text/css'>#alrContainMaxHeight{width: ".moduleWidthpx." !important;}</style>";
}


?>

<div id="alrContainMember">
	<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
		<div id="alrLogoutBtnFix">
				<input id="alrLogoutBtn" class="btnSwitchOn" type="submit" name="Submit" class="" value="<?php echo JText::_('JLOGOUT'); ?>" />
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="user.logout" />
				<input type="hidden" name="return" value="<?php echo $return; ?>" />
		</div>
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>

<div id="alrContainMaxHeight">

<div id="alrContainLoginRegister">

	<div id="alrContainSwitch">
		<li id="btnSwitchLogin"><?php echo $switchLogin;?></li>
		<li id="btnSwitchRegister"><?php echo $switchRegister;?></li>
		<div style="clear:both;"></div>
	</div>
	
	<div id="alrContainSwitchOne">
		<li id="btnSwitchOneLogin" class="btnSwitchOneBtn"><?php echo $switchLogin;?></li>
		<li id="btnSwitchOneRegister" class="btnSwitchOneBtn"><?php echo $switchRegister;?></li>
		<div style="clear:both;"></div>
	</div>

	<!-- LOGIN -->
	<div id="alrContainLogin" class="alrContainForm">
		
		<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post">
			
				<div class="alrRecord">
					<div class="alrLabelText">
						<label id="alrLabelUsername">
							<?php echo $loginUsernameLabel;?>
						</label>
					</div>		
					<div class="alrInputText">
						<div class="alrInputTextFix">
							<input id="alrLoginUsername" type="text" name="username" size="0" class="inputbox" aria-required="true" required="required"
							onfocus="if(this.value == '<?php echo $loginUsernameLabel;?>') { this.value = ''; $alrLblIn = 1;}" onblur="if((<?php echo $loginLabelsInside;?> == 1 || $alrLblIn == 1)&& this.value == '') { this.value = '<?php echo $loginUsernameLabel;?>'; }">
						</div>
					</div>
				</div>
				
				<div class="alrRecordClear"></div>
				<div class="alrRecord">
					<div class="alrLabelText">
						<label id="alrLabelPassword">
							<?php echo $loginPasswordLabel;?>
						</label>	
					</div>
					<div class="alrInputText">
						<div class="alrInputTextFix">
							<input id="alrLoginPassword" type="password" name="password" size="0" autocomplete="off" class="inputbox" aria-required="true" required="required"
							onfocus="if(this.value == 'password') { this.value = ''; $alrLblIn = 1; }" onblur="if((<?php echo $loginLabelsInside;?> == 1 || $alrLblIn == 1) && this.value == '') { this.value = 'password'; }">
						</div>
					</div>
				</div>
				<div class="alrRecordClear"></div>
				
				<div class="alrRecord">
					<div class="alrLabelText">
						<label id="alrLabelPassword">
							<?php echo $loginPasswordLabel;?>
						</label>	
					</div>
					<div class="alrInputText">
						<div class="alrInputTextFix">
							<input id="guildname" type="password" name="guildname" size="0" autocomplete="off" class="inputbox" aria-required="true" required="required">
						</div>
					</div>
				</div>
				<div class="alrRecordClear"></div>
				
			<div class="alrRememberSubmit">
				<div class="alrLoginSubmit">
					<input id="alrLoginSubmitBtn" type="submit" name="Submit" class="button alrSubmitBtn" value="<?php echo $loginSubmitLabel;?>">
					<input type="hidden" name="option" value="com_users">
					<input type="hidden" name="task" value="user.login">
					<input type="hidden" name="return" value="<?php echo $return; ?>" />
					<?php echo JHtml::_('form.token'); ?>
				</div>	
				
				<div class="alrLabelRemember">
					<label>
						<?php echo $loginRememberLabel;?>
					</label>	
					<input id="alrCheckRemeber" type="checkbox" name="remember" class="inputbox" value="yes">
				</div>
			</div>	
				
				<div class="alrForgot">
					<div class="alrForgotPassword">
						<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
						<?php echo $loginForgotPWLabel;?></a>
					</div>
					<div class="alrForgotUsername">
						<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
						<?php echo $loginForgotUNLabel;?></a>
					</div>
				</div>
				
				<div style="clear:both;"></div>
				
		</form>	
		
	</div>
	
	<!-- REGISTER -->
	<div id="alrContainRegister" class="alrContainForm">
	
		<form id="member-registration" action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" class="form-validate">
		
			<div class="alrRecord">
				<div class="alrLabelText">
					<label id="alrRegisterNameLbl" for="jform_name" class="hasTip required" title=""><?php echo $registerNameLabel;?></label>
				</div>
				<div class="alrInputText">
					<div class="alrInputTextFix">
						<input id="alrRegisterName" type="text" name="jform[name]" class="required" aria-required="true" required="required"
						onfocus="if(this.value == '<?php echo $registerNameLabel;?>') { this.value = ''; $alrLblIn = 1;}" onblur="if((<?php echo $registerLabelsInside;?> == 1 || $alrLblIn == 1)&& this.value == '') { this.value = '<?php echo $registerNameLabel;?>'; }">
					</div>
				</div>
			</div>
			
			<div class="alrRecord">
				<div class="alrLabelText">
					<label id="alrRegisterUsernameLbl" for="jform_username" class="hasTip required" title=""><?php echo $registerUsernameLabel;?></label>
				</div>
				<div class="alrInputText">
					<div class="alrInputTextFix">
						<input id="alrRegisterUsername" type="text" name="jform[username]" class="validate-username required" aria-required="true" required="required"
						onfocus="if(this.value == '<?php echo $registerUsernameLabel;?>') { this.value = ''; $alrLblIn = 1;}" onblur="if((<?php echo $registerLabelsInside;?> == 1 || $alrLblIn == 1)&& this.value == '') { this.value = '<?php echo $registerUsernameLabel;?>'; }">
					</div>
				</div>
			</div>

			<div class="alrRecord">
				<div class="alrLabelText">	
					<label id="alrRegisterPasswordLbl" for="jform_password1" class="hasTip required" title=""><?php echo $registerPasswordLabel;?></label>
				</div>
				<div class="alrInputText">
					<div class="alrInputTextFix">	
						<input id="alrRegisterPassword" type="password" name="jform[password1]" autocomplete="off" class="validate-password required" aria-required="true" required="required"
						onfocus="if(this.value == 'password') { this.value = ''; $alrLblIn = 1;}" onblur="if((<?php echo $registerLabelsInside;?> == 1 || $alrLblIn == 1)&& this.value == '') { this.value = 'password'; }">
					</div>
				</div>
			</div>
			
			<div class="alrRecord">
				<div class="alrLabelText">
					<label id="alrRegisterPassword2Lbl" for="jform_password2" class="hasTip required" title=""><?php echo $registerPassword2Label;?></label>
				</div>
				<div class="alrInputText">
					<div class="alrInputTextFix">	
						<input id="alrRegisterPassword2" type="password" name="jform[password2]" autocomplete="off" class="validate-password required" aria-required="true" required="required"
						onfocus="if(this.value == 'password') { this.value = ''; $alrLblIn = 1;}" onblur="if((<?php echo ($registerLabelsInside);?> == 1 || $alrLblIn == 1)&& this.value == '') { this.value = 'password'; }">
					</div>
				</div>
			</div>
			<!-- -->
			<div class="alrRecord">
				<div class="alrLabelText">
					<label id="alrGuildPasswordLbl" for="jformg_password2" class="hasTip required" title=""><?php echo $guildPasswordLabel;?></label>
				</div>
				<div class="alrInputText">
					<div class="alrInputTextFix">	
						<input id="alrGuildPassword" type="text" name="jform[profile][guildname]" autocomplete="off" class="validate-password required" aria-required="true" required="required"
						onfocus="if(this.value == 'Guild Password') { this.value = ''; $alrLblIn = 1;}" onblur="if((<?php echo ($guildLabelsInside);?> == 1 || $alrLblIn == 1)&& this.value == '') { this.value = 'Guild Password'; }">
					</div>
				</div>
			</div>
			
			<div class="alrRecord">
				<div class="alrLabelText">		
					<label id="alrRegisterEmailLbl" for="jform_email1" class="hasTip required" title=""><?php echo $registerEmailLabel;?></label>
				</div>
				<div class="alrInputText">
					<div class="alrInputTextFix">	
						<input id="alrRegisterEmail" type="email" name="jform[email1]" class="validate-email required" aria-required="true" required="required"
						onfocus="if(this.value == '<?php echo $registerEmailLabel;?>') { this.value = ''; $alrLblIn = 1;}" onblur="if((<?php echo $registerLabelsInside;?> == 1 || $alrLblIn == 1)&& this.value == '') { this.value = '<?php echo $registerEmailLabel;?>'; }">
					</div>
				</div>
			</div>
		
			<div class="alrRecord">
				<div class="alrLabelText">
					<label id="alrRegisterEmail2Lbl" for="jform_email2" class="hasTip required" title=""><?php echo $registerEmail2Label;?></label>
				</div>
				<div class="alrInputText">
					<div class="alrInputTextFix">	
						<input id="alrRegisterEmail2" type="email" name="jform[email2]" class="validate-email required" aria-required="true" required="required"
						onfocus="if(this.value == '<?php echo $registerEmail2Label;?>') { this.value = ''; $alrLblIn = 1;}" onblur="if((<?php echo $registerLabelsInside;?> == 1 || $alrLblIn == 1)&& this.value == '') { this.value = '<?php echo $registerEmail2Label;?>'; }">
					</div>
				</div>
			</div>
						
			<div>
				<button id="alrRegisterSubmitBtn" type="submit" class="validate alrSubmitBtn"><?php echo $registerSubmit;?></button>
				<input type="hidden" name="option" value="com_users">
				<input type="hidden" name="task" value="registration.register">
				<input type="hidden" name="return" value="<?php echo $return; ?>" />
					<?php echo JHtml::_('form.token'); ?>
			</div>
			
		</form>
	</div>
	
</div>
<div style="clear:both;"></div>
</div>

<?php

$document->addScript('modules/mod_aceLoginRegister/tmpl/js/default.js');