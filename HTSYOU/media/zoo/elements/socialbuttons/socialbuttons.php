<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
   Class: ElementSocialbuttons
       The Socialbuttons element class
*/
class ElementSocialbuttons extends Element implements iSubmittable {

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

			$html[] = '<div class="yoo-zoo socialbuttons">';

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
				$html[] = '<div><g:plusone href="'.htmlspecialchars($item_route).'"'
							.($params->get('ggsize') ? ' size="'.$params->get('ggsize').'"' : '')
							.($params->get('ggcount') ? ' count="true"' : ' count="false"')
							.($locale ? '' : ' lang="'.$locale.'"')
							.'></g:plusone></div>';
			}

			// Facebook Like
			if ($this->config->get('facebook')) {
				$href = 'http://www.facebook.com/plugins/like.php?'
							.'href='.urlencode($item_route)
							.'&amp;layout='.$params->get('fblayout')
							.'&amp;show_faces='.$params->get('fbshow_faces')
							.'&amp;width='.$params->get('fbwidth')
							.'&amp;action='.$params->get('fbaction')
							.'&amp;colorscheme='.$params->get('fbcolorscheme')
							.($locale ? '' : '&amp;locale='.$locale)
							.($params->get('ref') ? '&amp;ref='.$params->get('fbref') : '');
				$html[] = '<div><iframe src="'.$href.'" style="border:none; overflow:hidden; width: '.$params->get('fbwidth').'px; height: '.$params->get('fbheight', '20').'px" ></iframe></div>';
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