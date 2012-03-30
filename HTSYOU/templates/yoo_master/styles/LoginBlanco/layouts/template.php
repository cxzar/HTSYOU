<?php
/**
* @package   yoo_master
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get template configuration
include($this['path']->path('layouts:template.config.php'));
?>

<!DOCTYPE HTML>
<html lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>">

<head>
<link rel="stylesheet" href="css/splitscreen.css" type="text/css" media="all"/>
<link rel="stylesheet" type="text/css" media="all" href="pandaria.css">
<?php echo $this['template']->render('head'); ?>
</head>

<body id="page" class="page <?php echo $this['config']->get('body_classes'); ?>" data-config='<?php echo $this['config']->get('body_config','{}'); ?>'>

	<?php if ($this['modules']->count('absolute')) : ?>
	<div id="absolute">
		<?php echo $this['modules']->render('absolute'); ?>
	</div>
	<?php endif; ?>
	
	<div class="wrapper grid-block">

		<header id="header">

			<div id="toolbar" class="grid-block">

				<?php if ($this['modules']->count('toolbar-l') || $this['config']->get('date')) : ?>
				<div class="float-left">
				
					<?php if ($this['config']->get('date')) : ?>
					<time datetime="<?php echo $this['config']->get('datetime'); ?>"><?php echo $this['config']->get('actual_date'); ?></time>
					<?php endif; ?>
				
					<?php echo $this['modules']->render('toolbar-l'); ?>
					
				</div>
				<?php endif; ?>
					
				<?php if ($this['modules']->count('toolbar-r')) : ?>
				<div class="float-right"><?php echo $this['modules']->render('toolbar-r'); ?></div>
				<?php endif; ?>
				
			</div>

			<div id="headerbar" class="grid-block">
			
				<?php if ($this['modules']->count('logo')) : ?>	
				<a id="logo" href="<?php echo $this['config']->get('site_url'); ?>"><?php echo $this['modules']->render('logo'); ?></a>
				<?php endif; ?>
				
				<?php if($this['modules']->count('headerbar')) : ?>
				<div class="left"><?php echo $this['modules']->render('headerbar'); ?></div>
				<?php endif; ?>
				
			</div>

			<div id="menubar" class="grid-block">
				
				<?php  if ($this['modules']->count('menu')) : ?>
				<nav id="menu"><?php echo $this['modules']->render('menu'); ?></nav>
				<?php endif; ?>

				<?php if ($this['modules']->count('search')) : ?>
				<div id="search"><?php echo $this['modules']->render('search'); ?></div>
				<?php endif; ?>
				
			</div>
		
			<?php if ($this['modules']->count('banner')) : ?>
			<div id="banner"><?php echo $this['modules']->render('banner'); ?></div>
			<?php endif;  ?>
		
		</header>
		
		<!--CODIGO PROPIO CZAR-->
		<div id="Titulo">
			<p id="Beta">En construcción : Beta 0.002</p>
			<p id="Hates">Hates You</p>
		</div>

	<!--	<div id="benjaminbutton" id="asdButton"> asd </button>
			<button type="button">
		</div>
			
			<div id="incense-wrapper">
				<div id="incense">
				<object width="650" height="400" type="application/x-shockwave-flash" data="http://us.media.blizzard.com/wow/mists-of-pandaria/flash/incense.swf" id="incense" style="visibility: visible;">
				<param name="allowFullScreen" value="true"/><param name="bgcolor" value="#000000"/>
				<param name="allowScriptAccess" value="always"/>
				<param name="wmode" value="transparent"/>
				<param name="menu" value="false"/>
				<param name="base" value="http://us.media.blizzard.com/wow/media/videos"/>
				</object>
				</div>
			</div>
			-->


		<?php if ($this['modules']->count('top-a')) : ?>
		<section id="top-a" class="grid-block"><?php echo $this['modules']->render('top-a', array('layout'=>$this['config']->get('top-a'))); ?></section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('top-b')) : ?>
		<section id="top-b" class="grid-block"><?php echo $this['modules']->render('top-b', array('layout'=>$this['config']->get('top-b'))); ?></section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('innertop + innerbottom + sidebar-a + sidebar-b') || $this['config']->get('system_output')) : ?>
		
		<div id="main" class="grid-block">
		
			<div id="maininner" class="grid-box">
			
				<?php if ($this['modules']->count('innertop')) : ?>
				<section id="innertop" class="grid-block"><?php echo $this['modules']->render('innertop', array('layout'=>$this['config']->get('innertop'))); ?></section>
				<?php endif; ?>

				<?php if ($this['modules']->count('breadcrumbs')) : ?>
				<section id="breadcrumbs"><?php echo $this['modules']->render('breadcrumbs'); ?></section>
				<?php endif; ?>
			
			<!-- SUPUESTO DIV DE LOS MENSAJES DE SISTEMA -->
				<?php if ($this['config']->get('system_output')) : ?>
				<section id="content" class="grid-block"><?php echo $this['template']->render('content'); ?></section>
				<?php endif; ?>

				<?php if ($this['modules']->count('innerbottom')) : ?>
				<section id="innerbottom" class="grid-block"><?php echo $this['modules']->render('innerbottom', array('layout'=>$this['config']->get('innerbottom'))); ?></section>
				<?php endif; ?>

			</div>
			<!-- maininner end -->
			
			<?php if ($this['modules']->count('sidebar-a')) : ?>
			<aside id="sidebar-a" class="grid-box"><?php echo $this['modules']->render('sidebar-a', array('layout'=>'stack')); ?></aside>
			<?php endif; ?>
			
			<?php if ($this['modules']->count('sidebar-b')) : ?>
			<aside id="sidebar-b" class="grid-box"><?php echo $this['modules']->render('sidebar-b', array('layout'=>'stack')); ?></aside>
			<?php endif; ?>

		</div>
		<?php endif; ?>
		<!-- main end -->

		<?php if ($this['modules']->count('bottom-a')) : ?>
		<section id="bottom-a" class="grid-block"><?php echo $this['modules']->render('bottom-a', array('layout'=>$this['config']->get('bottom-a'))); ?></section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('bottom-b')) : ?>
		<section id="bottom-b" class="grid-block"><?php echo $this['modules']->render('bottom-b', array('layout'=>$this['config']->get('bottom-b'))); ?></section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('footer + debug') || $this['config']->get('warp_branding')) : ?>
		<footer id="footer" class="grid-block">

			<?php if ($this['config']->get('totop_scroller')) : ?>
			<a id="totop-scroller" href="#page"></a>
			<?php endif; ?>

			<?php
				echo $this['modules']->render('footer');
				$this->output('warp_branding');
				echo $this['modules']->render('debug');
			?>

		</footer>
		<?php endif; ?>

	</div>
	
	<?php echo $this->render('footer'); ?>
	
	<!-- Splitscreen Teaser Html -->
	<!--	<div class="splitscreen-teaser-html" style="color:#fff; font-weight:bold">
			IF SPLITMODE IS SET TO TEASER, TEASER HTML CODE BELONGS IN THIS DIV
			<a href='#' class="x-splitscreen" style="color:#ccc">Continue to website</a>
		</div> -->
	
		<div style="display:none;">
			<div>
			<jdoc:include type="message" />
	<!--		<?php	$app = &JFactory::getApplication();
					$messages = $app->getMessageQueue();
				?> -->
			</div>
		</div>
		
		<div class="splitscreen-teaser-html" id="loginoob">
			<jdoc:include type="modules" name="loginpro" />
			<a href='#' class="x-splitscreen" style="color:#ccc">Continue to website</a>
		</div>
	
	<!-- Splitscreen Initialize Plugin -->

  	<script type="text/javascript">
	$('body').splitscreen({
		
		/* Mode */
				splitMode:		'teaser',			
				cookieName:		'splitscreen',			
				
				/* True or False */
				cookieOn:		false,				 			
				showPromoBtn:		true,				
				showContinueBtn:	true, 						
				showFade:		true,				
				showRaster:		true, 				
				showCenter:		true, 				
		/* NEW */     showCenterTop:	    false, 					
		/* NEW */     showCenterBottom:     false, 								
		/* NEW */     showFloatRightTop:    false, 						
		/* NEW */     showFloatRightCenter: false, 						
		/* NEW */     showFloatRightBottom: false, 							
		/* NEW */     showFloatLeftTop:     false, 						
		/* NEW */     showFloatLeftCenter:  false, 						
		/* NEW */     showFloatLeftBottom:  false, 								
				showUnderlay:		true,					
				showIntroSplit:		false, 				
				showAutoSplit:		false, 				
				showHints:		false,
				
				/* Timer */
				splitTimeIntro: 	2000, 							
				splitTimeAuto:		15000, 					
		
				/* Images */		
				imageDir: 		'http://haterz.org/haterz/img/',				
				imageBG:		'background-1.jpg',
				imageWidthBG: 		2000, 				
				imageHeightBG:		1333, 						
				imageRaster:		'raster00.png', 		
				alphaRaster:		'0', 				 		
				imageCenter:		'logo-large.png',
		/* NEW */     imageFloating:	   'logo-large.png', 		
				imageFadeLeft:		'fade-left.png',		
				imageFadeRight:		'fade-right.png',		
				imageLoading:		'loader-black.gif',		
				imageLoadingBG: 	'#000000',			
				
				/* Align Teaser */
				verticalAlignTeaser:	'middle',					
		
				/* Align Buttons*/
				verticalAlignBtn:	'middle',			
				
				/* Continue button */		
				textContinueBtn:	'Más Información',	 	
				colorContinueTxt:	'#ffffff',
				fontContinueTxt:	'Georgia1, Georgia, serif',	
				sizeContinueBtn:	'14px',				
				colorContinueBtn:	'#333333',			
				colorContinueRoll:	'#999999',			
				
				/* Promo button */
				textPromoBtn:		'Take A Test Drive »',		
				colorPromoTxt:		'#ffffff',
				fontPromoTxt:		'Georgia1, Georgia, serif',	
				sizePromoBtn:		'18px',				
				colorPromoBtn:		'#cc0000',			
				colorPromoRoll:		'#ff0000',				
				urlPromoBtn:		'http://www.google.com',	
				targetPromoBtn:		'_blank',				
		
				/* Underlay */
				colorUnderlay:		'#000000',			
				alphaUnderlay:		'90'
	});
	</script>
	
</body>
</html>