<?php
/**
 * sh404SEF prototype support for Banners component.
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007-2011
 * @package     sh404SEF-16
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: com_banners.php 2072 2011-08-31 10:08:52Z silianacom-svn $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

$sefConfig = & Sh404sefFactory::getConfig(); 

$shName = shGetComponentPrefix($option);
$title[] = empty($shName) ? 'banners':$shName;

$title[] = '/';

$title[] = (empty($task) ? '' : $task) . (empty($id) ? '' : (int) $id);


if (count($title) > 0) $string = sef_404::sefGetLocation($string, $title,null);

