<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
   Class: ElementSocialbuttons
       The Socialbuttons element class
*/
class ElementSocialbuttons extends Element implements iSubmittable {

	/*
		Function: hasValue
			Checks if the repeatables element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array()) {
		return $this->get('value', $this->config->get('default')) && ($this->config->get('twitter') || $this->config->get('google') || $this->config->get('facebook'));
	}

	/*
		Function: render
			Override. Renders the element.

	   Parameters:
            $params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array()) {

		// render html
		if ($this->get('value', $this->config->get('default'))) {

			//init vars
			$params = $this->app->data->create($params);
			$html = array();
			$item_route = JRoute::_($this->app->route->item($this->_item, false), true, -1);
			$locale		= $this->config->get('locale') ? '' : str_replace('-', '_', $this->app->system->getLanguage()->getTag());

			// add assets
			$this->app->document->addStylesheet('elements:socialbuttons/assets/css/style.css');

			$html[] = '<div class="yoo-zoo socialbuttons clearfix">';

			// Tweet Button
			if ($this->config->get('twitter')) {
				$this->app->system->document->addScript('http://platform.twitter.com/widgets.js');
				$html[] = '<div><a href="http://twitter.com/share" class="twitter-share-button"'
							.' data-url="'.htmlspecialchars($item_route).'"'
							. ($params->get('twvia') ? ' data-via="'.$params->get('twvia').'"' : '')
							. ($params->get('twtext') ? ' data-text="'.$params->get('twtext').'"' : '')
							. ($params->get('twrelated') ? ' data-related="'.$params->get('twrelated').'"' : '')
							. ($params->get('twcount') ? ' data-count="'.$params->get('twcount').'"' : '')
							. ($locale ? ' data-lang="'.$locale.'"' : '')
							.'>'.JText::_('Tweet').'</a></div>';
			}

			// Google Plus One
			if ($this->config->get('google')) {
				$this->app->system->document->addScript('https://apis.google.com/js/plusone.js');
				$html[] = '<div><div class="g-plusone" data-href="'.htmlspecialchars($item_route).'" data-annotation="none"'
							.($params->get('ggsize') ? ' data-size="'.$params->get('ggsize').'"' : '')
							.($params->get('ggcount') ? ' data-count="true"' : ' data-count="false"')
							.($locale ? '' : ' data-lang="'.$locale.'"')
							.'></div></div>';
			}

			// Facebook Like
			if ($this->config->get('facebook')) {
				$this->app->system->document->addScriptDeclaration(
						'jQuery(function($) { if (!$("body").find("#fb-root").length) {
							$("body").append(\'<div id="fb-root"></div>\');
							(function(d, s, id) {
							var js, fjs = d.getElementsByTagName(s)[0];
							if (d.getElementById(id)) return;
							js = d.createElement(s); js.id = id;
							js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
							fjs.parentNode.insertBefore(js, fjs);
							}(document, \'script\', \'facebook-jssdk\'));
						}});');

				$html[] = '<div><div class="fb-like"'
						.'data-href="'.htmlspecialchars($item_route).'"'
						.'data-send="false"'
						.'data-layout="'.$params->get('fblayout').'"'
						.'data-width="'.$params->get('fbwidth').'"'
						.'data-show-faces="'.$params->get('fbshow_faces').'"'
						.'data-action="'.$params->get('fbaction').'"'
						.'data-colorscheme="'.$params->get('fbcolorscheme').'"'
						.($locale ? 'data-locale="'.$locale.'"' : '')
						.($params->get('ref') ? 'data-ref="'.$params->get('fbref').'"' : '')
					.'></div></div>';

			}

			$html[] = '</div>';

			return implode("\n", $html);
		}

		return null;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return $this->app->html->_('select.booleanlist', $this->getControlName('value'), '', $this->get('value', $this->config->get('default')));
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - AppData submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {
        return $this->edit();
	}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - AppData value
            $params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params) {
		return array('value' => $value->get('value'));
	}

}