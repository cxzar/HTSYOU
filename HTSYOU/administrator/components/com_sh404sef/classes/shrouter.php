<?php
/**
 *
 * @version   $Id: shrouter.php 2157 2011-11-18 17:51:49Z silianacom-svn $
 * @copyright Copyright (C) 2010 Yannick Gaultier. All rights reserved.
 * @license   GNU/GPL, see LICENSE.php
 * Sh404sefClassShdb is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 * Class adding a few method to Joomla! default database class
 *
 */

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die;

/**
 * Compatibility layer for 3rdt party plugins
 *
 *
 * @author yannick
 *
 */
class shRouter  {

  public static function &shGetConfig() {

    $config = & Sh404sefFactory::getConfig();

    return $config;
  }

  public static function &shPageInfo() {

    $config = & Sh404sefFactory::getPageInfo();

    return $config;
  }

}