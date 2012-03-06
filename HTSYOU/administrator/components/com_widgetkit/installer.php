<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
	Class: com_widgetkitInstallerScript
		Installer script
*/
class com_widgetkitInstallerScript extends InstallerScript {}

/*
	Class: InstallerScript
		Installer script
*/
class InstallerScript {

	public function install($parent) {

		// init vars
		$installer  = $parent->getParent();
		$extensions = $this->getAdditionalExtensions($installer);

		// install additional extensions
		foreach ($extensions as $extension) {

			if (!$extension->preInstall() || !$extension->install()) {

				// rollback on installation errors
				$installer->abort(JText::_('Component').' '.JText::_('Install').': '.JText::_('Error'), 'component');
				foreach ($extensions as $extension) {
					if ($extension->status) {
						$extension->abort();
					}
				}

				break;
			}

			if ($extension->type == 'plugin') {
				$extension->enable();
			}

		}

		// display table
		if ($extensions) {
			self::displayAdditionalExtensions($extensions);
		}

		// update tinymce
		$db = JFactory::getDBO();
		$db->setQuery("SELECT e.params FROM #__extensions AS e WHERE e.name='plg_editors_tinymce'");
		$data = new JRegistry($db->loadResult());
		$data->set('extended_elements', $data->get('extended_elements').',@[data-lightbox],@[data-spotlight]');
		$db->setQuery("UPDATE #__extensions AS e SET e.params='".$data->toString()."' WHERE e.name='plg_editors_tinymce'");
		$db->query();
	}

	public function uninstall($parent) {

		// init vars
		$installer  = $parent->getParent();
		$extensions = $this->getAdditionalExtensions($installer);

		// uninstall additional extensions
		foreach ($extensions as $extension) {
			$extension->uninstall();
		}

		// display table
		if ($extensions) {
			self::displayAdditionalExtensions($extensions);
		}
	}

	public function update($parent) {
		return $this->install($parent);
	}

	public function preflight($type, $parent) {

		// remove ZOO from admin menus
		$db = JFactory::getDBO();
		$db->setQuery('DELETE FROM #__menu WHERE alias = "comwidgetkit" AND menutype = "main"');
		$db->query();
		$db->setQuery('DELETE FROM #__assets WHERE title = "widgetkit"');
		$db->query();

	}

	public function postflight($type, $parent) {}

	protected function getAdditionalExtensions($installer) {

		// init vars
		$extensions = array();

		// additional extensions
		if (($manifest = simplexml_load_file($installer->getPath('manifest'))) && ($additional = $manifest->xpath('additional/*'))) {
			foreach ($additional as $data) {
				$extensions[] = new AdditionalExtension($installer, $data);
			}
		}

		return $extensions;
	}

	public static function displayAdditionalExtensions($extensions) {
		?>
		<table class="adminlist">
			<thead>
				<tr>
					<th class="title"><?php echo JText::_('Extension'); ?></th>
					<th width="60%"><?php echo JText::_('Status'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
			</tfoot>
			<tbody>
				<?php
					foreach ($extensions as $i => $extension) : ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="key"><?php echo $extension->name; ?></td>
						<td>
							<?php $style = $extension->status ? 'font-weight: bold; color: green;' : 'font-weight: bold; color: red;'; ?>
							<span style="<?php echo $style; ?>"><?php echo $extension->message; ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

}

/*
	Class: AdditionalExtension
		Additional extension class
*/
class AdditionalExtension {

	public $name;
	public $element;
	public $type;
	public $status;
	public $message;
	public $data;
	public $parent;
	public $installer;
	public $database;

	public function __construct($parent, $data) {

		// init vars
		$this->name = (string) $data;
		$this->element = (string) $data->attributes()->name;
		$this->type = $data->getName();
		$this->status = false;
		$this->data = $data;
		$this->parent = $parent;
		$this->installer = new JInstaller();
		$this->database = JFactory::getDBO();
		$this->folder = (string) $data->attributes()->folder;
		$this->source_path = rtrim($this->parent->getPath('source').'/'.$this->folder, "\\/") . '/';

	}

	public function preInstall() {

		// remove zoo layout config files on update
		if ($this->element == 'widgetkit_zoo' && JFolder::exists($this->source_path) && JFolder::exists(JPATH_ROOT.'/plugins/system/widgetkit_zoo')) {
			foreach (JFolder::files($this->source_path, '(positions\.(config|xml)|metadata\.xml)$', true, true) as $file) {
				JFile::delete($file);
			}
		}

		return true;

	}

	public function install() {

		// set message
		if ($this->status = $this->installer->install($this->parent->getPath('source').'/'.$this->data->attributes()->folder)) {
			$this->message = JText::_('Installed successfully');
		} else {
			$this->message = JText::_('NOT Installed');
		}

		return $this->status;
	}

	public function uninstall() {

		// get extension id and client id
		$result    = $this->load();
		$ext_id    = isset($result->extension_id) ? $result->extension_id  : 0;
		$client_id = isset($result->client_id) ? $result->client_id : 0;

		// set message
		if ($this->status = $ext_id > 0 && $this->installer->uninstall($this->type, $ext_id, $client_id)) {
			$this->message = JText::_('Uninstalled successfully');
		} else {
			$this->message = JText::_('Uninstall FAILED');
		}

		return $this->status;
	}

	public function abort() {
		$this->installer->abort(JText::_($this->type).' '.JText::_('Install').': '.JText::_('Error'), $this->type);
		$this->status = false;
	}

	public function load() {
		$this->database->setQuery(sprintf("SELECT * FROM #__extensions WHERE type='%s' AND element='%s'", $this->type, $this->element));
		return $this->database->loadObject();
	}

	public function enable() {
		$this->database->setQuery(sprintf("UPDATE #__extensions SET enabled=1 WHERE type='%s' AND element='%s'", $this->type, $this->element));
		$this->database->query();
	}

}