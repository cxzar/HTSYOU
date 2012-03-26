<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.event.plugin' );

/**
 * Joomla! Redirect Failed Login
 * Version 1.62
 * @author		Roger Noar
 * @package		Joomla
 * @subpackage	System
 */
class  plgSystemRedirect_Failed_Login extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemRedirect_Failed_Login(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	//joomla 1.6+ compatibility code
 	public function onUserLoginFailure($user, $options){  // joomla 1.6+ event
 	    $result = $this->onLoginFailure($user, $options);
 	    return $result;
 	}

	public function onLoginFailure()  // joomla 1.5 event
	{

		$app = &JFactory::getApplication();

		$redirect_destination = $this->params->def('redirect_destination', '');
		$redirect_message = $this->params->def('redirect_message', JText::_('JLIB_LOGIN_AUTHENTICATE'));
		$time_delay = $this->params->def('time_delay', '');
		$clear_cache = $this->params->def('clear_cache', '');

		// Get current URL, if current URL matches the redirect URL, then no need to redirect
		// Prevents multiple redirections caused by onLoginFailure
		$uri =& JFactory::getURI();
		//$app->enqueueMessage( 'URI is =' . $uri->toString() ) ;
		if ( $time_delay != "0" ) {sleep ( (int)$time_delay ); }  // If a time delay is set, wait before proceeding
		if ( ($uri != $redirect_destination) && ($redirect_destination !='') ) {
			if ($clear_cache == "1") {
			$cache = & JFactory::getCache();    // Reference the cache
			$cache->clean();// Clean the cache so you don't get stale page after redirection
			}
			$app->enqueueMessage($redirect_message, $type = 'error');
			$app->redirect( $redirect_destination );
		}
		return true;
	}
}
?>
