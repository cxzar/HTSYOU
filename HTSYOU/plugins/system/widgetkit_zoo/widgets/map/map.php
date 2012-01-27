<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: ZooMap
		Zoo Map class
*/
class ZooMap extends ZooWidget {

	/*
		Function: render
			Render widget on site

		Returns:
			String
	*/
	public function render($widget) {

		if (isset($widget->zoo['params']) && $widget->type == 'map') {

			$params = $this->zoo->data->create($widget->zoo['params']);

			if ($application = $this->zoo->table->application->get($params->get('application', 0))) {

				// load template
				$zoo_items = version_compare($this->zoo->zoo->version(), '2.5.beta') >= 0 ? $this->zoo->module->getItems($params) : $this->zoo->zoomodule->getItems($params);
				if ($zoo_items && !empty($zoo_items)) {

					// set renderer
					$renderer = $this->zoo->renderer->create('item')->addPath(array($this->zoo->path->path('component.site:'), $this->widgetkit['path']->path('zoowidgets:'.$widget->type)));

					// init cache
					$cache = $this->zoo->cache->create($this->zoo->path->path('cache:') . '/geocode_cache.txt');

					// get center marker
					$i = 0;
					$widget_items = array();
					if ($location = $params->get('location', false)) {

						try {

							if ($coordinates = $this->zoo->googlemaps->locate($location, $cache)) {
								// add item title
								$widget_items[$i]['title'] = 'TEST';

								$widget_items[$i]['lat'] = $coordinates['lat'];
								$widget_items[$i]['lng'] = $coordinates['lng'];

								// default icon
								$widget_items[$i]['icon'] = $params->get('main_icon');

								// add item popup
								$widget_items[$i]['popup'] = $this->zoo->googlemaps->stripText($params->get('marker_text', ''));

								$i++;
							}

						} catch (GooglemapsHelperException $e) {

							echo "<div class=\"alert\"><strong>({$e})</strong></div>\n";

						}
					}


					foreach($zoo_items as $item) {

						// if there is no center marker, show popup for item
						if ($i == 0 && $widget->settings['popup'] == 0) {
							$settings = $widget->settings;
							$settings['popup'] = 1;
							$widget->settings = $settings;
						}

						// location
						$elements = $item->getElements();
						foreach ($elements as $element) {
							if (($element->getElementType() == 'googlemaps') && $element->hasValue()) {

								try {

									if ($coordinates = $this->zoo->googlemaps->locate($element->getElementData()->get('location'), $cache)) {

										// add item title
										$widget_items[$i]['title'] = $item->name;

										$widget_items[$i]['lat'] = $coordinates['lat'];
										$widget_items[$i]['lng'] = $coordinates['lng'];

										// default icon
										$widget_items[$i]['icon'] = $params->get('icon');

										// add item popup
										$widget_items[$i]['popup'] = $renderer->render('item.'.$params->get('layout'), compact('item', 'params'));

									}

								} catch (GooglemapsHelperException $e) {

									echo "<div class=\"alert\"><strong>({$e})</strong></div>\n";

								}
							}
						}

						$i++;
					}
					$widget->items = $widget_items;
				}
			}
		}
	}

}

// instantiate ZooMap
new ZooMap();
