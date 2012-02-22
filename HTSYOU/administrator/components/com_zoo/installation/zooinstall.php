<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class ZooInstall {

	public static function doInstall(JInstaller &$installer) {

		// create applications folder
		if (!JFolder::exists(JPATH_ROOT . '/media/zoo/applications/')) {
			JFolder::create(JPATH_ROOT . '/media/zoo/applications/');
		}

		// initialize zoo framework
		require_once($installer->getPath('extension_administrator').'/config.php');

		// get zoo instance
		$zoo = App::getInstance('zoo');

		// copy checksums file
		if (JFile::exists($installer->getPath('source').'/checksums')) {
			JFile::copy($installer->getPath('source').'/checksums', $zoo->path->path('component.admin:').'/checksums');
		}

		try {

			// clean ZOO installation
			$zoo->modification->clean();

		} catch (Exception $e) {}

		// fix joomla 1.5 bug
		if ($zoo->joomla->isVersion('1.5')) {
			$installer->getDBO = $installer->getDBO();
		}

		// applications
		$applications = array();
		foreach (JFolder::folders($installer->getPath('source').'/applications', '.', false, true) as $folder) {
			try {

				$obj = new stdClass();

				if ($manifest = $zoo->install->findManifest($folder)) {

					$obj->name = (string) $manifest->name;
					$obj->status = $zoo->install->installApplicationFromFolder($folder);
					$obj->message = $obj->status == 2 ? 'Updated successfully': 'Installed successfully';

				}

			} catch (AppException $e) {

				$obj->name = basename($folder);
				$obj->status = false;
				$obj->message = JText::_('NOT Installed');

			}

			$applications[] = $obj;
		}

		// display application installation results
		self::displayResults($applications, 'Applications', 'Application');

		// additional extensions
		$extensions = self::_getAdditionalExtensions($zoo, $installer);

		// install additional extensions
		foreach ($extensions as $extension) {

			if (JFolder::exists($extension->source_path)) {
				if (!$extension->preInstall() || !$extension->install()) {

					// rollback on installation errors
					$installer->abort(JText::_('Component').' '.JText::_('Install').': '.JText::_('Error'), 'component');
					foreach ($extensions as $extension) {
						if ($extension->status) {
							$extension->abort();
						}
					}

					return false;
				}

				if ($extension->element == 'mod_zooquickicon') {
					$zoo->module->enable('mod_zooquickicon', 'icon');
				}

				if ($extension->type ==  'plugin') {
					$zoo->plugin->enable($extension->element);
				}

			} else {
				$extension->message = 'Extension not included in package';
			}

		}

		// display extension installation results
		self::displayResults($extensions, 'Extensions', 'Extension');

		// finally update
		if ($zoo->update->required()) {
			$zoo->error->raiseNotice(0, JText::_('ZOO requires an update. Please click <a href="index.php?option=com_zoo">here</a>.'));
		}

		return true;
	}

	public static function doUninstall(JInstaller &$installer) {

		// initialize zoo framework
		require_once($installer->getPath('extension_administrator').'/config.php');

		// get zoo instance
		$zoo = App::getInstance('zoo');

		// remove media folder
		if (JFolder::exists(JPATH_ROOT . '/media/zoo/applications/')) {
			JFolder::delete(JPATH_ROOT . '/media/zoo/applications/');
		}

		// init vars
		$extensions = self::_getAdditionalExtensions($zoo, $installer);

		// uninstall additional extensions
		foreach ($extensions as $extension) {
			$extension->uninstall();
		}

		// display table
		if ($extensions) {
			self::displayResults($extensions, 'Extensions', 'Extension');
		}

	}

	public static function displayResults($result, $name, $type) {

		?>

		<h3><?php echo JText::_($name); ?></h3>
		<table class="adminlist">
			<thead>
				<tr>
					<th class="title"><?php echo JText::_($type); ?></th>
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
					foreach ($result as $i => $ext) : ?>
					<tr class="row<?php echo $i++ % 2; ?>">
						<td class="key"><?php echo $ext->name; ?></td>
						<td>
							<?php $style = $ext->status ? 'font-weight: bold; color: green;' : 'font-weight: bold; color: red;'; ?>
							<span style="<?php echo $style; ?>"><?php echo JText::_($ext->message); ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php

	}

	protected function _getAdditionalExtensions($app, $installer) {

		// init vars
		$manifest = simplexml_load_file($installer->getPath('manifest'));
		$extensions = array();

		// additional extensions
		if ($additional = $manifest->xpath('additional/*')) {
			foreach ($additional as $data) {
				$extensions[] = new AdditionalExtension($app, $installer, $data);
			}
		}

		return $extensions;
	}

}


/*
	Class: AdditionalExtension
		Additional extension class
*/
class AdditionalExtension {

	public $app;
	public $name;
	public $element;
	public $folder;
	public $type;
	public $status;
	public $message;
	public $data;
	public $parent;
	public $installer;
	public $database;
	public $update;
	public $source_path;

	public function __construct($app, $parent, $data) {

		// init vars
		$this->app = $app;
		$this->name = (string) $data;
		$this->element = (string) $data->attributes()->name;
		$this->folder = (string) $data->attributes()->folder;
		$this->type = $data->getName();
		$this->status = false;
		$this->data = $data;
		$this->parent = $parent;
		$this->installer = new JInstaller();
		$this->database = JFactory::getDBO();
		$this->source_path = rtrim($this->parent->getPath('source').'/'.$this->folder, "\\/") . '/';

	}

	public function preInstall() {

		$this->update = ($this->type == 'module' && JFolder::exists(JPATH_ROOT.((string) $this->data->attributes()->client == 'administrator' ? '/administrator' : '').'/modules/'.$this->element)) || ($this->type == 'plugin' && JFolder::exists(JPATH_ROOT.'/plugins/'.$this->data->attributes()->group.'/'.$this->element));

		if (JFolder::exists($this->source_path) && $this->update) {
			foreach ($this->app->filesystem->readDirectoryFiles($this->source_path, $this->source_path, '/(positions\.(config|xml)|metadata\.xml)$/', true) as $file) {
				JFile::delete($file);
			}
		}

		return true;

	}

	public function install() {

		// set message
		$path = $this->parent->getPath('source').'/'.$this->folder;
		if (JFolder::exists($this->source_path) && ($this->status = $this->installer->install($path))) {
			$this->message = $this->update ? 'Updated successfully' : 'Installed successfully';
		} else {
			$this->message = JText::_('NOT Installed');
		}

		return $this->status;
	}

	public function uninstall() {

		// get extension id and client id
		$result    = $this->load();
		$ext_id    = isset($result->id) ? $result->id : 0;
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

		// set query
		if ($this->app->joomla->isVersion('1.5')) {
			switch ($this->type) {
				case 'plugin':
					$query = "SELECT * FROM #__plugins WHERE element = '%s'";
					break;
				case 'module':
					$query = "SELECT * FROM #__modules WHERE module = '%s'";
					break;
			}
		} else {
			$query = "SELECT *, extension_id as id FROM #__extensions WHERE element = '%s'";
		}

		return $this->app->database->queryObject(sprintf($query, $this->element));
	}

}