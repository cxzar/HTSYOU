<?php 
/**
 * SEF module for Joomla!
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007-2011
 * @package     sh404SEF-16
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: social_seo.php 2157 2011-11-18 17:51:49Z silianacom-svn $
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');
// we'll use panes so import Joomla library and instantiate one
jimport( 'joomla.html.pane');
$pane =& JPane::getInstance('Tabs');

?>

<div class="sh404sef-popup" id="sh404sef-popup">

<!-- markup common to all config layouts -->

<?php include JPATH_ADMINISTRATOR . DS . 'components' . DS. 'com_sh404sef' . DS . 'views' . DS . 'config' . DS . 'tmpl' . DS . 'common_header.php'; ?>

<!-- start general configuration markup -->

<div id="element-box">
  <div class="t">
    <div class="t">
      <div class="t"></div>
    </div>
  </div>
<div class="m">

<form action="index.php" method="post" name="adminForm" id="adminForm">

  <div id="editcell">

  <?php  echo JText::_('COM_SH404SEF_CONF_SOCIAL_SEO_HELP')?>

  <!-- start of configuration html -->
  
  <?php 
    echo $pane->startPane('sh404SEFConf');
    echo $pane->startPanel( JText::_('COM_SH404SEF_OG_CONFIG'), 'ogconfig' );

  // params for open graph data configuration  -->
  ?>
  <table class="adminlist">
  <thead>
  </thead>
  <?php
  
  $x = 1;
  echo Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_DATA_ENABLED'),
  JText::_('COM_SH404SEF_TT_OG_DATA_ENABLED'),
  $this->lists['og_enable'] );
  
  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_OG_REQUIRED_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  $x++;
  echo Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_TYPE_SELECT'),
  JText::_('COM_SH404SEF_TT_OG_TYPE_SELECT'),
  $this->lists['og_type_select'] );
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_IMAGE_PATH'),
  JText::_('COM_SH404SEF_TT_OG_IMAGE_PATH'),
                'ogImage',
  $this->lists['og_image'], 50, 255 );
  
  ?></table>
    
  <?php
    echo $pane->endPanel();
    echo $pane->startPanel( JText::_('COM_SH404SEF_OG_OPTIONAL_TITLE'), 'ogoptional' ); 
  ?>
  <table class="adminlist">
  <?php
  $x++;
  echo Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_DESCRIPTION'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_DESCRIPTION'),
  $this->lists['og_insert_description'] );
  $x++;
  echo Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_SITE_NAME'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_SITE_NAME'),
  $this->lists['og_insert_site_name'] );
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_SITE_NAME'),
  JText::_('COM_SH404SEF_TT_OG_SITE_NAME'),
                'ogSiteName',
  $this->lists['og_site_name'], 50, 255 );
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_FB_ADMIN_IDS'),
  JText::_('COM_SH404SEF_TT_FB_ADMIN_IDS'),
                'fbAdminIds',
  $this->lists['fb_admin_ids'], 50, 255 );
  
  ?></table>
    
  <?php
    echo $pane->endPanel();
    echo $pane->startPanel( JText::_('COM_SH404SEF_OG_LOCATION_TITLE'), 'oglocation' ); 
  ?>
  <table class="adminlist">
  
  <?php
  $x++;
  echo Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_LOCATION'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_LOCATION'),
  $this->lists['og_insert_location'] );
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_LATITUDE'),
  JText::_('COM_SH404SEF_TT_OG_LATITUDE'),
                'ogLatitude',
  $this->lists['og_latitude'], 30, 30 );
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_LONGITUDE'),
  JText::_('COM_SH404SEF_TT_OG_LONGITUDE'),
                'ogLongitude',
  $this->lists['og_longitude'], 30, 30 );
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_STREET_ADDRESS'),
  JText::_('COM_SH404SEF_TT_OG_STREET_ADDRESS'),
                'ogStreetAddress',
  $this->lists['og_street_address'], 50, 255);
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_LOCALITY'),
  JText::_('COM_SH404SEF_TT_OG_LOCALITY'),
                'ogLocality',
  $this->lists['og_locality'], 50, 255);
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_POSTAL_CODE'),
  JText::_('COM_SH404SEF_TT_OG_POSTAL_CODE'),
                'ogPostalCode',
  $this->lists['og_postal_code'], 30, 30 );
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_COUNTRY_NAME'),
  JText::_('COM_SH404SEF_TT_OG_COUNTRY_NAME'),
                'ogCountryName',
  $this->lists['og_country_name'], 50, 255 );
  
  ?></table>
    
  <?php
    echo $pane->endPanel();
    echo $pane->startPanel( JText::_('COM_SH404SEF_OG_CONTACT_TITLE'), 'ogcontact' ); 
  ?>
  <table class="adminlist">
  
  <?php
  $x++;
  echo Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_CONTACT'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_CONTACT'),
  $this->lists['og_insert_contact'] );
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_EMAIL'),
  JText::_('COM_SH404SEF_TT_OG_EMAIL'),
                'ogEmail',
  $this->lists['og_email'], 50, 255 );
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_PHONE_NUMBER'),
  JText::_('COM_SH404SEF_TT_OG_PHONE_NUMBER'),
                'ogPhoneNumber',
  $this->lists['og_phone_number'], 50, 255 );
  $x++;
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_FAX_NUMBER'),
  JText::_('COM_SH404SEF_TT_OG_FAX_NUMBER'),
                'ogFaxNumber',
  $this->lists['og_fax_number'], 50, 255 );
  
  ?></table>
    
  <?php
    echo $pane->endPanel(); 
    echo $pane->endPane(); 
  ?>
  
  <!-- end of configuration html -->

    <input type="hidden" name="c" value="config" />
    <input type="hidden" name="view" value="config" />
    <input type="hidden" name="layout" value="social_seo" />
    <input type="hidden" name="format" value="raw" />
    <input type="hidden" name="option" value="com_sh404sef" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="shajax" value="1" />
    <input type="hidden" name="tmpl" value="component" />
    
    <?php echo JHTML::_( 'form.token' ); ?>
  </div>  
</form>

  <div class="clr"></div>
</div>
  <div class="b">
    <div class="b">
      <div class="b"></div>
    </div>
  </div>
</div>
</div>
