<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_aceLoginRegister
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$type	= modaceLoginRegisterHelper::getType();
$return	= modaceLoginRegisterHelper::getReturnURL($params, $type);
$user	= JFactory::getUser();

$loginUsernameLabel = $params->get("loginUsernameLabel", 'Username');
$loginPasswordLabel = $params->get("loginPasswordLabel", 'Password');
$loginRememberLabel = $params->get("loginRememberLabel", 'Remember Me');
$loginSubmitLabel = $params->get("loginSubmitLabel", 'Log in');
$loginForgotPWLabel = $params->get("loginForgotPWLabel", 'Forgot your password?');
$loginForgotUNLabel = $params->get("loginForgotUNLabel", 'Forgot your username?');

$registerNameLabel = $params->get("registerNameLabel", 'Name');
$registerUsernameLabel = $params->get("registerUsernameLabel", 'Username');
$registerPasswordLabel = $params->get("registerPasswordLabel", 'Password');
$registerPassword2Label = $params->get("registerPassword2Label", 'Confirm Password');
$registerEmailLabel = $params->get("registerEmailLabel", 'Email Address');
$registerEmail2Label = $params->get("registerEmail2Label", 'Confirm Email');
$registerSubmit = $params->get("registerSubmit", 'Register');

$loginLabelsInside = $params->get("loginLabelsInside", '0');
$registerLabelsInside = $params->get("registerLabelsInside", '0');

$switchLogin = $params->get("switchLogin", 'Login');
$switchRegister = $params->get("switchRegister", 'Register');

$moduleWidth = $params->get("moduleWidth", 'auto');
$moduleHeight = $params->get("moduleHeight", 'auto');
$loginAndRegister = $params->get("loginAndRegister", '1');
$switchOne = $params->get("switchOne", '0');
$hideForgot = $params->get("hideForgot", '0');
$hideRemember = $params->get("hideRemember", '0');
$forceRemember = $params->get("forceRemember", '0');
$selectLayout = $params->get("selectLayout", '1');
$selectTheme = $params->get("selectTheme", '1');
$selectEffect = $params->get("selectEffect", '1');

$disableJQuery = $params->get("disableJQuery", '0');
$disableJQueryUI = $params->get("disableJQueryUI", '0');

require JModuleHelper::getLayoutPath('mod_aceLoginRegister', $params->get('layout', 'default'));
