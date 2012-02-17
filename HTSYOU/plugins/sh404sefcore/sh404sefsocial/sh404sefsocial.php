<?php
/**
 * SEF module for Joomla!
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007-2011
 * @package     sh404SEF-16
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: sh404sefsocial.php 2310 2012-02-11 15:32:15Z silianacom-svn $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport( 'joomla.plugin.plugin' );

class plgSh404sefcoresh404sefSocial extends JPlugin {

  private $_params = null;
  private $_enabledButtons = array( 'facebooklike', 'facebooksend', 'twitter', 'googleplusone', 'googlepluspage');

  public function __construct(&$subject, $config = array())
  {
    parent::__construct($subject);
    // get plugin params
    $plugin =& JPluginHelper::getPlugin('sh404sefcore', 'sh404sefsocial');
    $this->_params = new JRegistry;
    $this->_params->loadString($plugin->params);
  }

  /**
   * Insert appropriate script links into document
   */
  public function onSh404sefInsertSocialButtons( &$page, $sefConfig) {

    $app = JFactory::getApplication();

    // are we in the backend - that would be a mistake
    if (!defined( 'SH404SEF_IS_RUNNING') || $app->isAdmin()) {
      return;
    }

    // don't display on errors
    $pageInfo = Sh404sefFactory::getPageInfo();
    if(!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404) {
      return ;
    }

    // regexp to catch plugin requests
    $regExp = '#{sh404sef_social_buttons(.*)}#Us';

    // search for our marker}
    if (preg_match_all( $regExp, $page, $matches, PREG_SET_ORDER) > 0) {

      // process matches
      foreach($matches as $id => $match) {
        $url = '';
        // extract target URL
        if(!empty($match[1])) {
          $raw = explode( ' ', $match[1]);
          $attributes = array();
          $enabledButtons = array();
          foreach($raw as $attribute) {
            $attribute = JString::trim($attribute);
            if(strpos( $attribute, '=') === false) {
              continue;
            }
            $bits = explode('=', $attribute);
            if(empty($bits[1])) {
              continue;
            }
            switch($bits[0]) {
              case 'url':
                $base = JURI::base(true);
                if(substr( $bits[1], 0, 10) == 'index.php?' ) {
                  $url = JURI::getInstance()->toString(array('scheme', 'host', 'port')) . JRoute::_( $bits[1]);
                } else if(substr( $bits[1], 0, JString::strlen($base)) == $base) {
                  $url = JURI::getInstance()->toString(array('scheme', 'host', 'port')) . $bits[1];
                } else if(substr( $bits[1], 0, 1) == '/') {
                  $url = JString::rtrim( JURI::base(), '/') . $bits[1];
                } else {
                  $url = $bits[1];
                }
                break;
              case 'type':
                $enabledButtons[] = strtolower( $bits[1]);
                break;
            }
          }

          if(!empty($enabledButtons)) {
            $this->_enabledButtons = $enabledButtons;
          }
        }
        // get buttons html
        $buttons = $this->_sh404sefGetSocialButtons( $sefConfig, $url);
        $buttons = str_replace( '\'', '\\\'', $buttons);

        // replace in document
        $page = str_replace( $match[0], $buttons, $page);
      }
    }

    // insert head links as needed
    $this->_insertSocialLinks( $page, $sefConfig);

  }

  public function onContentBeforeDisplay($context, &$row, &$params, $page=0) {

    $app = JFactory::getApplication();

    // are we in the backend - that would be a mistake
    if (!defined( 'SH404SEF_IS_RUNNING') || $app->isAdmin()) {
      return;
    }

    // don't display on errors
    $pageInfo = Sh404sefFactory::getPageInfo();
    if(!empty($pageInfo->httpStatus) && $pageInfo->httpStatus == 404) {
      return '';
    }

    if($this->_params->get( 'buttonsContentLocation', 'onlyTags') == 'before') {
      $buttons = $this->_sh404sefGetSocialButtons( Sh404sefFactory::getConfig(), $url = '', $context, $row);
    } else {
      $buttons = '';
    }
    return $buttons;

  }
  public function onContentAfterDisplay($context, &$row, &$params, $page=0) {

    if($this->_params->get( 'buttonsContentLocation', 'onlyTags') == 'after') {
      $buttons = $this->_sh404sefGetSocialButtons(Sh404sefFactory::getConfig(), $url = '', $context, $row);
    } else {
      $buttons = '';
    }
    return $buttons;

  }

  public function onSh404sefInsertFBJavascriptSDK( &$page, $sefConfig) {

    static $_inserted = false;

    if($sefConfig->shMetaManagementActivated && !$_inserted && ( $this->_params->get('enableFbLike', true) || $this->_params->get('enableFbSend', true))) {

      $_inserted = true;

      // G! use underscore in language tags
      $locale = str_replace( '-', '_', JFactory::getLanguage()->getTag());

      // append Facebook SDK
      $socialSnippet = "
    <div id='fb-root'></div><script type='text/javascript'>
    
    // Load the SDK Asynchronously
    (function(d){
     var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
     js = d.createElement('script'); js.id = id; js.async = true;
     js.src = '//connect.facebook.net/".$locale."/all.js';
     d.getElementsByTagName('head')[0].appendChild(js);
    }(document));
    
    </script>";

      // use page rewrite utility function to insert as needed
      $page = shPregInsertCustomTagInBuffer( $page, '<\s*body[^>]*>', 'after', $socialSnippet, $firstOnly = 'first');
    }
  }

  private function _sh404sefGetSocialButtons( $sefConfig, $url = '', $context = '', $content = null) {

    // if no URL, use current
    if(empty( $url)) {
      // no url set on social button tag, we should
      // use current URL, except if we are on a page
      // where this would cause the wrong url to be shared
      // try identify this condition
      if($this->_shouldDisplaySocialButtons( $sefConfig, $context, $content)) {
        Sh404sefHelperShurl::updateShurls();
        $pageInfo = Sh404sefFactory::getPageInfo();
        $url = !$this->_params->get('useShurl', true) || empty($pageInfo->shURL) ? JURI::current() : JURI::base() . $pageInfo->shURL;
      } else {
        return '';
      }
    }

    // buttons html
    $buttonsHtml = '';

    // get language from Joomla
    $longLang = JFactory::getLanguage()->getTag();
    // networks use en_GB, not en-GB
    $shortLang = substr($longLang, 0, 2);

    // we wrap buttons in unordered list
    $wrapperOpen = '<li>';
    $wrapperClose = '</li>';

    // Tweet
    if($this->_params->get('enableTweet', true) && in_array( 'twitter',$this->_enabledButtons)) {
      $buttonsHtml .= $wrapperOpen . '<a href="https://twitter.com/share" data-via="'.$this->_params->get('viaAccount', '').'" data-count="'.$this->_params->get('tweetLayout', 'none').'" data-url="'.$url.'" data-lang="'.$shortLang.'" class="twitter-share-button">Tweet</a>' . $wrapperClose;
    }

    // plus One
    if($this->_params->get('enablePlusOne', true) && in_array( 'googleplusone', $this->_enabledButtons)) {
      $buttonsHtml .= $wrapperOpen . '<g:plusone callback="_sh404sefSocialTrackGPlusTracking" annotation="'.$this->_params->get('plusOneAnnotation', 'none').'" size="'.$this->_params->get('plusOneSize', '').'" href="'.$url.'"></g:plusone>' . $wrapperClose;
    }

    // Google plus page badge
    $page = $this->_params->get('googlePlusPage', '');
    $page = JString::trim( $page, '/');
    if( $this->_params->get('enableGooglePlusPage', true)  && in_array( 'googlepluspage', $this->_enabledButtons) && !empty( $page)) {
      $buttonsHtml .= $wrapperOpen . '<a class="google-page-badge" onclick="_sh404sefSocialTrack.GPageTracking(\'/'.$page.'/\', \''.$url.'\')" href="https://plus.google.com/'.$page.'/?prsrc=3">';

      // badge image
      switch ($this->_params->get( 'googlePlusPageSize', 'medium')) {
        case 'small':
          $size = '16';
          $buttonsHtml .= '<div style="display: inline-block;">';
          // custom text
          if($this->_params->get( 'googlePlusCustomText', '')) {
            $buttonsHtml .= '<span style="float: left; font: bold 13px/16px arial,sans-serif; margin-right: 4px;">'
            . htmlspecialchars ($this->_params->get( 'googlePlusCustomText', '')) . '</span><span style="float: left; font: 13px/16px arial,sans-serif; margin-right: 11px;">'
            . htmlspecialchars ($this->_params->get( 'googlePlusCustomText2', '')) . '</span>';
          }

          $buttonsHtml .= '<div style="float: left;"><img src="https://ssl.gstatic.com/images/icons/gplus-16.png" width="16" height="16" style="border: 0;"/></div><div style="clear: both"></div>';

          break;
        case 'large':
          $size = '64';
          $buttonsHtml .= '<div style="display: inline-block; *display: inline;"><div style="text-align: center;"><img src="https://ssl.gstatic.com/images/icons/gplus-64.png" width="64" height="64" style="border: 0;"></img></div><div style="font: bold 13px/16px arial,sans-serif; text-align: center;">'
          . $this->_params->get( 'googlePlusCustomText', '') . '</div><div style="font: 13px/16px arial,sans-serif; text-align: center;">'
          . htmlspecialchars ($this->_params->get( 'googlePlusCustomText2', '')) . '</div>';

          break;
        default:
          $size = '32';
        $buttonsHtml .= '<div style="display: inline-block;">';
        // custom text
        if($this->_params->get( 'googlePlusCustomText', '')) {
          $buttonsHtml .= '<span style="float: left; font: bold 13px/16px arial,sans-serif; margin-right: 4px; margin-top: 7px;">'
          . htmlspecialchars ($this->_params->get( 'googlePlusCustomText', '')) . '</span><span style="float: left; font: 13px/16px arial,sans-serif; margin-right: 11px; margin-top: 7px;">'
          . htmlspecialchars ($this->_params->get( 'googlePlusCustomText2', '')) . '</span>';
        }
        $buttonsHtml .= '<div style="float: left;"><img src="https://ssl.gstatic.com/images/icons/gplus-32.png" width="32" height="32" style="border: 0;"/></div><div style="clear: both"></div>';

        break;
      }

      $buttonsHtml .= '</div></a>' . $wrapperClose;
    }

    // FB Like
    if($this->_params->get('enableFbLike', 1) && in_array( 'facebooklike', $this->_enabledButtons)) {
      $layout = $this->_params->get('fbLayout', '') == 'none' ? '' : $this->_params->get('fbLayout', '');
      if($this->_params->get('fbUseHtml5', false)) {
        $buttonsHtml .= $wrapperOpen . '<div class="fb-like" data-href="'.$url.'" data-send="'.($this->_params->get('enableFbSend', 1) ? 'true' : 'false').'" data-action="'.$this->_params->get('fbAction', '') .'" data-width="'.$this->_params->get('fbWidth', '').'" data-layout="'.$layout.'" data-show-faces="'.$this->_params->get('fbShowFaces', 'true').'" data-colorscheme="'.$this->_params->get('fbColorscheme', 'light').'"></div>' . $wrapperClose;
      } else {
        $buttonsHtml .= $wrapperOpen . '<fb:like href="'.$url.'" send="'.($this->_params->get('enableFbSend', 1) ? 'true' : 'false').'" action="'.$this->_params->get('fbAction', '') .'" width="'.$this->_params->get('fbWidth', '').'" layout="'.$layout.'" show_faces="'.$this->_params->get('fbShowFaces', 'true').'" colorscheme="'.$this->_params->get('fbColorscheme', '') . '"></fb:like>' . $wrapperClose;
      }
    } else if($this->_params->get('enableFbSend', 1) && in_array( 'facebooksend', $this->_enabledButtons)){
      if($this->_params->get('fbUseHtml5', false)) {
        $buttonsHtml .= $wrapperOpen . '<div class="fb-send" data-href="'.$url.'" data-colorscheme="'.$this->_params->get('fbColorscheme', '').'"></div>' . $wrapperClose;
      } else {
        $buttonsHtml .= $wrapperOpen . '<fb:send href="'.$url.'" colorscheme="'.$this->_params->get('fbColorscheme', '').'"></fb:send>' . $wrapperClose;
      }
    }

    // perform replace
    if(!empty( $buttonsHtml)) {
      $buttonsHtml = '<div class="sh404sef-social-buttons"><ul>' . $buttonsHtml . '</ul></div>';
    }

    return $buttonsHtml;

  }

  private function _shouldDisplaySocialButtons( $sefConfig, $context = '', $content = null) {

    // if SEO off, don't do anything
    if(!$sefConfig->shMetaManagementActivated) {
      return false;
    }

    $shouldDisplay = true;

    // user can disable this attempt to identify possible failure
    // to select the correct url
    if(!$this->_params->get('onlyDisplayOnCanonicalUrl', true)) {
      return $shouldDisplay;
    }

    // get component
    if(empty($context)) {
      $component = JRequest::getCmd('option');
      $view = JRequest::getCmd('view');
    } else {
      $bits = explode( '.', $context);
      if(!empty( $bits)) {
        $component = $bits[0];
        $view = empty( $bits[1]) ? JRequest::getCmd('view', '') : $bits[1];
      }
    }
    $printing = JRequest::getInt( 'print');

    switch ($component) {
      case 'com_content':
        // only display if on an article page
        $shouldDisplay = $view == 'article' && empty($printing);
        // check category
        if( $shouldDisplay) {
          $cats = $this->_params->get('enabledCategories', array());
          if(!empty($cats) && ($cats[0] != 'show_on_all')) {
            // find about article category
            if(!empty( $content)) {
              // we have article details
              $catid = empty($content->catid) ? 0 : (int) $content->catid;
            } else {
              // no article details, use request
              $catid = JRequest::getInt( 'catid', 0);
              if(empty($catid)) {
                $id = JRequest::getInt( 'id', 0);
                if ($id) {
                  $article = JTable::getInstance('content');
                  $article->load($id);
                  $catid = $article->catid;
                }
              }
            }
            if(!empty($catid)) {
              $shouldDisplay = in_array( $catid, $cats);
            }
          }
        }
        break;
      case 'com_k2':
        $shouldDisplay = $view == 'item';
        break;
      default:
        break;
    }

    return $shouldDisplay;
  }

  /**
   * Insert appropriate script links into document
   */
  private function _insertSocialLinks( & $page, $sefConfig) {

    $headLinks = '';
    $bottomLinks = '';

    // what do we must link to
    $showFb = strpos( $page, '<div class="fb-"') !== false || strpos( $page, '<fb:') !== false;
    $showTwitter = strpos( $page, '<a href="https://twitter.com/share"') !== false;
    $showPlusOne = strpos( $page, '<g:plusone callback="_sh404sefSocialTrackGPlusTracking"') !== false;
    $gPlusPage = $this->_params->get('googlePlusPage', '');
    $gPlusPage = JString::trim( $gPlusPage, '/');
    $showGPlusPage = strpos( $page, 'onclick="_sh404sefSocialTrack.GPageTracking') !== false && !empty( $gPlusPage);
     
    // insert social tracking javascript
    if($showFb || $showTwitter | $showPlusOne || $showGPlusPage) {
      // G! use underscore in language tags
      $locale = str_replace( '-', '_', JFactory::getLanguage()->getTag());
      $channelUrl = JURI::base() . 'index.php?option=com_sh404sef&view=channelurl&format=raw&langtag=' . $locale;
      $channelUrl = str_replace( array('http://', 'https://') , '//',  $channelUrl);
      $headLinks .= "\n<script src='" . JURI::base(true) . '/plugins/sh404sefcore/sh404sefsocial/sh404sefsocial.js' . "' type='text/javascript' ></script>";
      $headLinks .= "\n<script type='text/javascript'>
      _sh404sefSocialTrack.options = {enableGoogleTracking:".($this->_params->get('enableGoogleSocialEngagement') ? 'true' : 'false').",
      enableAnalytics:".($this->_params->get('enableSocialAnalyticsIntegration') && $sefConfig->analyticsEnabled ? 'true' : 'false'). ", trackerName:'',
      FBChannelUrl:'" . $channelUrl . "'};
    	window.fbAsyncInit = _sh404sefSocialTrack.setup;
      </script>";
    }

    if($showFb) {
      $page = str_replace( '<html ', '<html xmlns:fb="http://ogp.me/ns/fb#" ', $page);
    }


    // tweeter share
    if($showTwitter) {
      $bottomLinks .= "\n<script src='//platform.twitter.com/widgets.js' type='text/javascript'></script>";
    }

    // plus one
    if($showPlusOne) {
      $bottomLinks .= "\n<script src='https://apis.google.com/js/plusone.js' type='text/javascript'></script>";
    }

    // google plus page badge
    if($showGPlusPage) {
      $headLinks .= "\n<link href='https://plus.google.com/".$gPlusPage."/' rel='publisher' />";
    }

    // actually insert
    if(!empty( $headLinks)) {
      // add our wrapping css
      $headLinks .= "\n<link rel='stylesheet' href='" . JURI::base(true) . '/plugins/sh404sefcore/sh404sefsocial/sh404sefsocial.css' . "' type='text/css' />";

      // insert everything in page
      $page = shInsertCustomTagInBuffer( $page, '</head>', 'before', $headLinks, $firstOnly = 'first');
    }

    if(!empty( $bottomLinks)) {
      // insert everything in page
      $page = shInsertCustomTagInBuffer( $page, '</body>', 'before', $bottomLinks, $firstOnly = 'first');
    }


  }

}