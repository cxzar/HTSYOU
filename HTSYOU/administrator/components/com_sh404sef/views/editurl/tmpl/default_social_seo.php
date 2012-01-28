<?php

/**
 * SEF module for Joomla!
 *
 * @author      $Author: shumisha $
 * @copyright   Yannick Gaultier - 2007-2011
 * @package     sh404SEF-16
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     $Id: default_social_seo.php 2157 2011-11-18 17:51:49Z silianacom-svn $
 */

// Security check to ensure this file is being included by a parent file.
if (!defined('_JEXEC')) die('Direct Access to this location is not allowed.');

?>

<table class="adminlist">
  <thead>
  </thead>
  <?php
  
  $x = 1;
  Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_DATA_ENABLED_BY_URL'),
  JText::_('COM_SH404SEF_TT_OG_DATA_ENABLED_BY_URL'),
  $this->ogData['og_enable'] );
  
  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_OG_REQUIRED_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_TYPE_SELECT'),
  JText::_('COM_SH404SEF_TT_OG_TYPE_SELECT'),
  $this->ogData['og_type'] );
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_IMAGE_PATH'),
  JText::_('COM_SH404SEF_TT_OG_IMAGE_PATH'),
                'og_image',
  $this->ogData['og_image'], 50, 255 );
  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_OG_OPTIONAL_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_DESCRIPTION'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_DESCRIPTION'),
  $this->ogData['og_enable_description'] );
  Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_SITE_NAME'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_SITE_NAME'),
  $this->ogData['og_enable_site_name'] );
  
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_SITE_NAME'),
  JText::_('COM_SH404SEF_TT_OG_SITE_NAME'),
                'og_site_name',
  $this->ogData['og_site_name'], 50, 255 );
  
  Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_ENABLE_FB_ADMIN_IDS'),
  JText::_('COM_SH404SEF_TT_OG_ENABLE_FB_ADMIN_IDS'),
  $this->ogData['og_enable_fb_admin_ids']);
  
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_FB_ADMIN_IDS'),
  JText::_('COM_SH404SEF_TT_FB_ADMIN_IDS'),
                'fb_admin_ids',
  $this->ogData['fb_admin_ids'], 50, 255 );
  
  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_OG_LOCATION_TITLE'); ?></th>
    </tr>
  </thead>
  
  <?php
  Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_LOCATION'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_LOCATION'),
  $this->ogData['og_enable_location'] );
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_LATITUDE'),
  JText::_('COM_SH404SEF_TT_OG_LATITUDE'),
                'og_latitude',
  $this->ogData['og_latitude'], 30, 30 );
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_LONGITUDE'),
  JText::_('COM_SH404SEF_TT_OG_LONGITUDE'),
                'og_longitude',
  $this->ogData['og_longitude'], 30, 30 );
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_STREET_ADDRESS'),
  JText::_('COM_SH404SEF_TT_OG_STREET_ADDRESS'),
                'og_street_address',
  $this->ogData['og_street_address'], 50, 255);
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_LOCALITY'),
  JText::_('COM_SH404SEF_TT_OG_LOCALITY'),
                'og_locality',
  $this->ogData['og_locality'], 50, 255);
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_POSTAL_CODE'),
  JText::_('COM_SH404SEF_TT_OG_POSTAL_CODE'),
                'og_postal_code',
  $this->ogData['og_postal_code'], 30, 30 );
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_COUNTRY_NAME'),
  JText::_('COM_SH404SEF_TT_OG_COUNTRY_NAME'),
                'og_country_name',
  $this->ogData['og_country_name'], 50, 255 );
  
  ?>
  <thead>
    <tr>
      <th class="title" style="text-align: left;" colspan="3"><?php echo JText::_('COM_SH404SEF_OG_CONTACT_TITLE'); ?></th>
    </tr>
  </thead>
  <?php
  Sh404sefViewHelperConfig::shYesNoParamHTML( $x,
  JText::_('COM_SH404SEF_OG_INSERT_CONTACT'),
  JText::_('COM_SH404SEF_TT_OG_INSERT_CONTACT'),
  $this->ogData['og_enable_contact'] );
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_EMAIL'),
  JText::_('COM_SH404SEF_TT_OG_EMAIL'),
                'og_email',
  $this->ogData['og_email'], 50, 255 );
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_PHONE_NUMBER'),
  JText::_('COM_SH404SEF_TT_OG_PHONE_NUMBER'),
                'og_phone_number',
  $this->ogData['og_phone_number'], 50, 255 );
  Sh404sefViewHelperConfig::shTextParamHTML( $x,
  JText::_('COM_SH404SEF_OG_FAX_NUMBER'),
  JText::_('COM_SH404SEF_TT_OG_FAX_NUMBER'),
                'og_fax_number',
  $this->ogData['og_fax_number'], 50, 255 );
  
  ?></table>