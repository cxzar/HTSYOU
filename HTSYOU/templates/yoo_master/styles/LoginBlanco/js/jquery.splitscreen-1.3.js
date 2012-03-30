/*!
 *
 * SPLITSCREEN - Fullscreen Splitting Page Splash Ad Jquery Plugin
 * URL: http://www.codecanyon.net/user/d8n
 * Version: 1.3
 * Author: Daton Lynch
 * Author URL: http://www.codecanyon.net/user/d8n
 * Copyright © 2011
 * All rights reserved.
 *
 * HOW TO CLOSE SPLITSCREEN EXTERNALLY:
 *
 * (1) To close SPLITSCREEN via javascript call, use this function: $.closeSplitscreen();
 *
 *
 * (2) To close SPLITSCREEN via element action add this class: x-splitscreen 
 *     FOR EXAMPLE: <a href='#' class="x-splitscreen">Continue to website</a>
 *
 */
(function($){	

		/**
		* jQuery Cookie plugin
		*
		* Copyright (c) 2010 Klaus Hartl (stilbuero.de)
		* Dual licensed under the MIT and GPL licenses:
		* http://www.opensource.org/licenses/mit-license.php
		* http://www.gnu.org/licenses/gpl.html
		*
		*/
		jQuery.cookie = function (key, value, options) {
			if (arguments.length > 1 && String(value) !== "[object Object]") {
				options = jQuery.extend({}, options);
				if (value === null || value === undefined) {
					options.expires = -1;
				}
				if (typeof options.expires === 'number') {
					var days = options.expires, t = options.expires = new Date();
					t.setDate(t.getDate() + days);
				}
				value = String(value);
				return (document.cookie = [
					encodeURIComponent(key), '=',
					options.raw ? value : encodeURIComponent(value),
					options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
					options.path ? '; path=' + options.path : '',
					options.domain ? '; domain=' + options.domain : '',
					options.secure ? '; secure' : ''
				].join(''));
			}
			options = value || {};
			var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
			return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
		};
		/** End jQuery Cookie plugin */		
	
	function showOption(settingName){
		if (settingName === false){
			return 'display:none;';
			}
			else{return 'display:block;';
			}	
	}
	function showOptionBtn(settingName){
		if (settingName === false){
			return 'display:none;';
		}
			else{return 'display:inline-block;';
			}	
		}
	function showLinkHint(settingName, button, value){ // *NEW 1.2.Z
		if (settingName === true && button == "promo"){
			return 'title="'+ value +'"';
			}
		if (settingName === true && button == "continue"){
			return 'title="'+ value +'"';
			}			
		if (settingName === false){
			return;
			}	
	}		
	
  $.fn.splitscreen = function(options) {  
    var
	  defaults = {

		/* Mode */
		splitMode:			'splash',			// 'splash' mode OR 'promo' mode OR 'teaser' mode
		cookieName:			'splitscreen',		// Cookie name to be set if 'cookieOn' is set to true
		
		/* True or False */
		cookieOn:			false,				// (true, false) If true, User only sees splitscreen once during a site visit 			
		showPromoBtn:		true,				// (true, false) If true, Show left side and right side fade overlay images
		showContinueBtn:	true, 				// (true, false) If true, Show raster image			
		showFade:			true,				// (true, false) If true, Show left side and right side fade overlay images
		showRaster:			true, 				// (true, false) If true, Show raster image
		
		showCenter:			true, 				// (true, false) If true, Show center image
		showCenterTop:		false, 				// (true, false) If true, Show center image in the center top*
		showCenterBottom:		false, 			// (true, false) If true, Show center image*		

		showFloatRightTop:  false,				// (true, false) If true, Show floating image in the right top*
		showFloatRightCenter:  false,			// (true, false) If true, Show floating image in the right top*
		showFloatRightBottom:  false,			// (true, false) If true, Show floating image in the right top*

		showFloatLeftTop:   false,				// (true, false) If true, Show floating image in the right top*
		showFloatLeftCenter:  false,			// (true, false) If true, Show floating image in the right top*
		showFloatLeftBottom:  false,			// (true, false) If true, Show floating image in the right top*	
		
		showUnderlay:		true,				// (true, false) If true, Show underlay div (ref. 'colorUnderlay', 'alphaUnderlay')		
		showIntroSplit:		true, 				// (true, false) If true, Show the split once when page first loads (ref. 'splitTimeIntro')	
		showAutoSplit:		true, 				// (true, false) If true, Show the split every interval (ref. 'splitTimeAuto')
		showHints:			false, 				// (true, false) If true, Shows the alt/title hint boxes when hovering on the Splitscreen *NEW 1.2.Z			

		/* Timer */
		splitTimeIntro: 	2000, 				// Time in milliseconds after page loads that splitscreen will 'showIntroSplit'			
		splitTimeAuto:		15000, 				// Interval in milliseconds that 'showAutoSplit' will loop and play		

		/* Images */		
		imageDir: 			'img/',				// Image directory for splitscreen images ('img/')
		imageBG:			'background-1.jpg', 	// Splitscreen background image is increased to fullscreen ('background-1.jpg')
		imageWidthBG: 		1920, 				// ^Required: Actual width of the background image 
		imageHeightBG:		1080, 				// ^Required: Actual height of the background image		
		imageRaster:		'raster00.png', 	// Raster image to use
		alphaRaster:		'80', 				// Alpha transparency of the raster image (i.e. '80', range is '0' to '99') 	
		imageCenter:		'logo-large.png',	// set the center image to use		
		imageFloating:		'logo-large.png',	// set the floating image to use*		
		imageFadeLeft:		'fade-left.png',	// Fade image overlayed on the left
		imageFadeRight:		'fade-right.png',	// Fade image overlayed on the right
		imageLoading:		'loader-black.gif',	// Preloading image gif (i.e. 'black-loader.gif' or 'white-loader.gif')
		imageLoadingBG: 	'#000000',			// Background color for the preloading image (i.e. '#000000' or '#ffffff')
		
		/* Align Teaser */
		verticalAlignTeaser:	'middle',		// Align teaser html content to the 'top', 'middle' or 'bottom' 		

		/* Align Buttons*/
		verticalAlignBtn:	'bottom',			// Align promo buttons to the 'top', 'middle' or 'bottom' 
		
		/* Continue button */		
		textContinueBtn:		'Continue to site &raquo;', // Text shown on the button that continues to the website
		colorContinueTxt:		'#ffffff', 					// Color of the Continue text shown ('#ffffff')*NEW 1.2.Z
		fontContinueTxt:		'Georgia1, Georgia, serif', // Font for Continue text ('Georgia1, Georgia, serif') *NEW 1.2.Z			
		sizeContinueBtn:		'14px',						// Size in pixels of the Continue button text ('18px') *UPDATED 1.2.Z	
		colorContinueBtn:		'#333333',					// Continue Button color ('#333333')
		colorContinueRoll:		'#999999',					// Continue Roll Button color ('#333333')
		
		/* Promo button */
		textPromoBtn:		'Click here for the story true story of karate &raquo;',	// Text shown on the button that continues to advertisment url
		colorPromoTxt:		'#ffffff', 													// Color of the Promo text ('#ffffff') *NEW 1.2.Z
		fontPromoTxt:		'Georgia1, Georgia, serif', 								// Font for Promo text ('Georgia1, Georgia, serif') *NEW 1.2.Z		
		sizePromoBtn:		'18px',													 	// Size in pixels of the Promo button text ('18px') *UPDATED 1.2.Z
		colorPromoBtn:		'#cc0000',													// Promo Button color ('#cc0000'))
		colorPromoRoll:		'#ff0000',													// Promo Rollover Button color ('#333333')		
		urlPromoBtn:		'http://www.google.com',									// The url that the promo button links to (i.e. 'http://www.google.com')
		targetPromoBtn:		'_blank',													// Where to open the promo link (i.e. '_blank' or '_parent')		

		/* Underlay */
		colorUnderlay:			'#000000',			// Color of the underlay div (i.e. '#000000')
		alphaUnderlay:			'80'				// Alpha transparency of the underlay div (i.e. '80', range is '0' to '99') 		
	  },
	  settings = $.extend({}, defaults, options);

	/*--------------------------------------------------------------------*/
	/*--------------------------------------------------------------------*/	
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/* COOKIE CHECK */
	/*--------------------------------------------------------------*/				

	var splitscreenCookie = $.cookie(settings.cookieName);
	
	if(settings.cookieOn === true && splitscreenCookie === null || settings.cookieOn === false){
		if(settings.cookieOn === true){$.cookie(settings.cookieName, settings.cookieName);}	

	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	
	
		return this.each(function() {		
			
			var alreadyopen = false;
			var splitscreenHtml;
				
			// SPLITSCREEN HTML	
			if(settings.splitMode == 'splash'){		
			splitscreenHtml = 
				'<!--[ START SPLITSCREEN ]-->' +
				'<div class="splitscreen-load-mask" style="background:'+ settings.imageLoadingBG  +' url('+ settings.imageDir + settings.imageLoading +') center center no-repeat;"></div>' +
				'<div class="splitscreen">' +
					'<!--[ SPLIT SCREEN ]-->' +
					'<div class="splitscreen-main splitscreen-erase">' +
						'<div class="splitscreen-activate x-splitscreen splitscreen-erase" '+ showLinkHint(settings.showHints, "continue", settings.textContinueBtn) +'></div>' +
						'<div class="splitscreen-fade-left splitscreen-erase splitscreen-sector" style="background:transparent url('+ settings.imageDir + settings.imageFadeLeft +') 0 0 repeat-y; background-size: 50%; '+ showOption(settings.showFade) +'"></div>' +
						'<div class="splitscreen-fade-right splitscreen-erase splitscreen-sector" style="background:transparent url('+ settings.imageDir + settings.imageFadeRight +') 100% 0 repeat-y; background-size: 50%; '+ showOption(settings.showFade) +'"></div>' +
						'<div class="splitscreen-left splitscreen-erase splitscreen-sector">' +
							'<div class="splitscreen-bg-left">' +
								'<img src="'+ settings.imageDir + settings.imageBG +'" width="'+ settings.imageWidthBG +'" height="'+ settings.imageHeightBG +'" class="splitscreen-fullscreen" />' +
								'<div class="splitscreen-raster-left main-raster" style="background:transparent url('+ settings.imageDir + settings.imageRaster +') 0 0; '+ showOption(settings.showRaster) +' filter:alpha(opacity='+ settings.alphaRaster +'); -moz-opacity:0.'+ settings.alphaRaster +'; opacity:0.'+ settings.alphaRaster +'; filter:progid:DXImageTransform.Microsoft.Alpha(opacity='+ settings.alphaRaster +');"></div>' +
							'</div>' +
							'<div class="splitscreen-logo-left splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center top no-repeat; '+ showOption(settings.showCenterTop) +'"></div>' +
							'<div class="splitscreen-logo-left splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center center no-repeat; '+ showOption(settings.showCenter) +'"></div>' +
							'<div class="splitscreen-logo-left splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center bottom no-repeat; '+ showOption(settings.showCenterBottom) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') left top no-repeat; '+ showOption(settings.showFloatLeftTop) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') left center no-repeat; '+ showOption(settings.showFloatLeftCenter) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') left bottom no-repeat; '+ showOption(settings.showFloatLeftBottom) +'"></div>' +
						'</div>' +

						'<div class="splitscreen-right splitscreen-erase splitscreen-sector">' +
							'<div class="splitscreen-bg-right">' +
								'<img src="'+ settings.imageDir + settings.imageBG +'" width="'+ settings.imageWidthBG +'" height="'+ settings.imageHeightBG +'" class="splitscreen-fullscreen" />' +
								'<div class="splitscreen-raster-right main-raster" style="background:transparent url('+ settings.imageDir + settings.imageRaster +') 0 0; '+ showOption(settings.showRaster) +' filter:alpha(opacity='+ settings.alphaRaster +'); -moz-opacity:0.'+ settings.alphaRaster +'; opacity:0.'+ settings.alphaRaster +'; filter:progid:DXImageTransform.Microsoft.Alpha(opacity='+ settings.alphaRaster +');"></div>' +
							'</div>' +
							'<div class="splitscreen-logo-right splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center top no-repeat; '+ showOption(settings.showCenterTop) +'"></div>' +
							'<div class="splitscreen-logo-right splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center center no-repeat; '+ showOption(settings.showCenter) +'"></div>' +
							'<div class="splitscreen-logo-right splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center bottom no-repeat; '+ showOption(settings.showCenterBottom) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') right top no-repeat; '+ showOption(settings.showFloatRightTop) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') right center no-repeat; '+ showOption(settings.showFloatRightCenter) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') right bottom no-repeat; '+ showOption(settings.showFloatRightBottom) +'"></div>' +
						'</div>' +
						//DROP SHADOW
						'<div class="splitscreen-shadow-left splitscreen-erase">' +
							'<div class="splitscreen-shadow splitscreen-erase"></div>' +		
						'</div>' +
						'<div class="splitscreen-shadow-right splitscreen-erase">' +
							'<div class="splitscreen-shadow splitscreen-erase"></div>' +							
						'</div>' +						
						//END DROP SHADOW					
						'<!--[ UNDERLAY ]-->' +
						'<div class="splitscreen-underlay splitscreen-erase-fade" style="background:'+ settings.colorUnderlay +'; filter:alpha(opacity='+ settings.alphaUnderlay +'); -moz-opacity:0.'+ settings.alphaUnderlay +'; opacity:0.'+ settings.alphaUnderlay +'; filter:progid:DXImageTransform.Microsoft.Alpha(opacity='+ settings.alphaUnderlay +'); '+ showOption(settings.showUnderlay) +';"></div>' +		
					'</div>' +
				'</div>' +
				'<!--[ STOP SPLITSCREEN ]-->'
			;}
			
			if(settings.splitMode == 'promo'){
			splitscreenHtml = 			
				'<style type="text/css">' +	
				'.awesome-promo, .awesome-promo:visited {background-color:'+ settings.colorPromoBtn +';}' +
				'.awesome-promo:hover {background-color:'+ settings.colorPromoRoll +';}' +
				'.awesome-continue, .awesome-continue:visited {background-color:'+ settings.colorContinueBtn +';}' +
				'.awesome-continue:hover {background-color:'+ settings.colorContinueRoll +';}' +	
				'</style>' +			
				'<!--[ START SPLITSCREEN ]-->' +
			'<div class="splitscreen-load-mask" style="background:'+ settings.imageLoadingBG  +' url('+ settings.imageDir + settings.imageLoading +') center center no-repeat;"></div>' +
				'<div class="splitscreen">' +
					'<!--[ SPLIT SCREEN ]-->' +
					'<div class="splitscreen-main splitscreen-erase">' +
						'<div class="splitscreen-activate splitscreen-erase"></div>' +
						'<div class="splitscreen-activate-2 splitscreen-erase"></div>' +
						'<div class="splitscreen-fade-left splitscreen-erase splitscreen-sector" style="background:transparent url('+ settings.imageDir + settings.imageFadeLeft +') 0 0 repeat-y; background-size: 50%; '+ showOption(settings.showFade) +'"></div>' +
						'<div class="splitscreen-fade-right splitscreen-erase splitscreen-sector" style="background:transparent url('+ settings.imageDir + settings.imageFadeRight +') 100% 0 repeat-y; background-size: 50%; '+ showOption(settings.showFade) +'"></div>' +
						'<div class="splitscreen-left splitscreen-erase splitscreen-sector">' +
							'<div class="splitscreen-bg-left">' +
								'<img src="'+ settings.imageDir + settings.imageBG +'" width="'+ settings.imageWidthBG +'" height="'+ settings.imageHeightBG +'" class="splitscreen-fullscreen" />' +
								'<div class="splitscreen-raster-left main-raster" style="background:transparent url('+ settings.imageDir + settings.imageRaster +') 0 0; '+ showOption(settings.showRaster) +' filter:alpha(opacity='+ settings.alphaRaster +'); -moz-opacity:0.'+ settings.alphaRaster +'; opacity:0.'+ settings.alphaRaster +'; filter:progid:DXImageTransform.Microsoft.Alpha(opacity='+ settings.alphaRaster +');"></div>' +
							'</div>' +
							'<div class="splitscreen-logo-left splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center top no-repeat; '+ showOption(settings.showCenterTop) +'"></div>' +
							'<div class="splitscreen-logo-left splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center center no-repeat; '+ showOption(settings.showCenter) +'"></div>' +
							'<div class="splitscreen-logo-left splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center bottom no-repeat; '+ showOption(settings.showCenterBottom) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') left top no-repeat; '+ showOption(settings.showFloatLeftTop) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') left center no-repeat; '+ showOption(settings.showFloatLeftCenter) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') left bottom no-repeat; '+ showOption(settings.showFloatLeftBottom) +'"></div>' +
						'</div>' +
						'<div class="splitscreen-right splitscreen-erase splitscreen-sector">' +
							'<div class="splitscreen-bg-right">' +
								'<img src="'+ settings.imageDir + settings.imageBG +'" width="'+ settings.imageWidthBG +'" height="'+ settings.imageHeightBG +'" class="splitscreen-fullscreen" />' +
								'<div class="splitscreen-raster-right main-raster" style="background:transparent url('+ settings.imageDir + settings.imageRaster +') 0 0; '+ showOption(settings.showRaster) +' filter:alpha(opacity='+ settings.alphaRaster +'); -moz-opacity:0.'+ settings.alphaRaster +'; opacity:0.'+ settings.alphaRaster +'; filter:progid:DXImageTransform.Microsoft.Alpha(opacity='+ settings.alphaRaster +');"></div>' +
							'</div>' +
							'<div class="splitscreen-logo-right splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center top no-repeat; '+ showOption(settings.showCenterTop) +'"></div>' +
							'<div class="splitscreen-logo-right splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center center no-repeat; '+ showOption(settings.showCenter) +'"></div>' +
							'<div class="splitscreen-logo-right splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center bottom no-repeat; '+ showOption(settings.showCenterBottom) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') right top no-repeat; '+ showOption(settings.showFloatRightTop) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') right center no-repeat; '+ showOption(settings.showFloatRightCenter) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') right bottom no-repeat; '+ showOption(settings.showFloatRightBottom) +'"></div>' +
						'</div>' +
						//DROP SHADOW
						'<div class="splitscreen-shadow-left splitscreen-erase">' +
							'<div class="splitscreen-shadow splitscreen-erase"></div>' +		
						'</div>' +
						'<div class="splitscreen-shadow-right splitscreen-erase">' +
							'<div class="splitscreen-shadow splitscreen-erase"></div>' +							
						'</div>' +						
						//END DROP SHADOW							
						'<!--[ UNDERLAY ]-->' +
						'<div class="splitscreen-underlay splitscreen-erase-fade" style="background:'+ settings.colorUnderlay +'; filter:alpha(opacity='+ settings.alphaUnderlay +'); -moz-opacity:0.'+ settings.alphaUnderlay +'; opacity:0.'+ settings.alphaUnderlay +'; filter:progid:DXImageTransform.Microsoft.Alpha(opacity='+ settings.alphaUnderlay +'); '+ showOption(settings.showUnderlay) +';">' +
						'</div>' +
						'<div class="splitscreen-button-div">' +
							'<table class="splitscreen-button-holder" cellpadding="0" cellspacing="0"><tbody><td align="center" valign="'+ settings.verticalAlignBtn +'">' +
							'<a href="'+ settings.urlPromoBtn +'" target="'+ settings.targetPromoBtn +'" class="' + settings.sizePromoBtn + ' awesome awesome-promo" '+ showLinkHint(settings.showHints, "promo", settings.textPromoBtn) +' style="font-size:'+ settings.sizePromoBtn +'; font-family:'+ settings.fontPromoTxt +'; color:'+ settings.colorPromoTxt +'; '+ showOptionBtn(settings.showPromoBtn) +'">'+ settings.textPromoBtn +'</a><br /><br />' +
							'<a class="x-splitscreen ' + settings.sizeContinueBtn + ' awesome awesome-continue" '+ showLinkHint(settings.showHints, "continue", settings.textContinueBtn) +' style="font-size:'+ settings.sizeContinueBtn +'; font-family:'+ settings.fontContinueTxt +'; color:'+ settings.colorContinueTxt +'; '+ showOptionBtn(settings.showContinueBtn) +'">'+ settings.textContinueBtn +'</a>' +
							'</td></tbody></table>' +
						'</div>' +						
					'</div>' +
				'</div>' +
				'<!--[ STOP SPLITSCREEN ]-->'
			;}			

			// SPLITSCREEN HTML	
			if(settings.splitMode == 'teaser'){				
			splitscreenHtml = 
				'<!--[ START SPLITSCREEN ]-->' +
			'<div class="splitscreen-load-mask" style="background:'+ settings.imageLoadingBG  +' url('+ settings.imageDir + settings.imageLoading +') center center no-repeat;"></div>' +
				'<div class="splitscreen">' +
					'<!--[ SPLIT SCREEN ]-->' +
					'<div class="splitscreen-main splitscreen-erase">' +
						'<div class="splitscreen-activate splitscreen-erase"></div>' +
						'<div class="splitscreen-activate-2 splitscreen-erase"></div>' +
						'<div class="splitscreen-fade-left splitscreen-erase splitscreen-sector" style="background:transparent url('+ settings.imageDir + settings.imageFadeLeft +') 0 0 repeat-y; background-size: 50%; '+ showOption(settings.showFade) +'"></div>' +
						'<div class="splitscreen-fade-right splitscreen-erase splitscreen-sector" style="background:transparent url('+ settings.imageDir + settings.imageFadeRight +') 100% 0 repeat-y; background-size: 50%; '+ showOption(settings.showFade) +'"></div>' +
						'<div class="splitscreen-left splitscreen-erase splitscreen-sector">' +
							'<div class="splitscreen-bg-left">' +
								'<img src="'+ settings.imageDir + settings.imageBG +'" width="'+ settings.imageWidthBG +'" height="'+ settings.imageHeightBG +'" class="splitscreen-fullscreen" />' +
								'<div class="splitscreen-raster-left main-raster" style="background:transparent url('+ settings.imageDir + settings.imageRaster +') 0 0; '+ showOption(settings.showRaster) +' filter:alpha(opacity='+ settings.alphaRaster +'); -moz-opacity:0.'+ settings.alphaRaster +'; opacity:0.'+ settings.alphaRaster +'; filter:progid:DXImageTransform.Microsoft.Alpha(opacity='+ settings.alphaRaster +');"></div>' +
							'</div>' +
							'<div class="splitscreen-logo-left splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center top no-repeat; '+ showOption(settings.showCenterTop) +'"></div>' +
							'<div class="splitscreen-logo-left splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center center no-repeat; '+ showOption(settings.showCenter) +'"></div>' +
							'<div class="splitscreen-logo-left splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center bottom no-repeat; '+ showOption(settings.showCenterBottom) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') left top no-repeat; '+ showOption(settings.showFloatLeftTop) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') left center no-repeat; '+ showOption(settings.showFloatLeftCenter) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') left bottom no-repeat; '+ showOption(settings.showFloatLeftBottom) +'"></div>' +
						'</div>' +
						'<div class="splitscreen-right splitscreen-erase splitscreen-sector">' +
							'<div class="splitscreen-bg-right">' +
								'<img src="'+ settings.imageDir + settings.imageBG +'" width="'+ settings.imageWidthBG +'" height="'+ settings.imageHeightBG +'" class="splitscreen-fullscreen" />' +
								'<div class="splitscreen-raster-right main-raster" style="background:transparent url('+ settings.imageDir + settings.imageRaster +') 0 0; '+ showOption(settings.showRaster) +' filter:alpha(opacity='+ settings.alphaRaster +'); -moz-opacity:0.'+ settings.alphaRaster +'; opacity:0.'+ settings.alphaRaster +'; filter:progid:DXImageTransform.Microsoft.Alpha(opacity='+ settings.alphaRaster +');"></div>' +
							'</div>' +
							'<div class="splitscreen-logo-right splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center top no-repeat; '+ showOption(settings.showCenterTop) +'"></div>' +
							'<div class="splitscreen-logo-right splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center center no-repeat; '+ showOption(settings.showCenter) +'"></div>' +
							'<div class="splitscreen-logo-right splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageCenter +') center bottom no-repeat; '+ showOption(settings.showCenterBottom) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') right top no-repeat; '+ showOption(settings.showFloatRightTop) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') right center no-repeat; '+ showOption(settings.showFloatRightCenter) +'"></div>' +
							'<div class="splitscreen-logo" style="background:transparent url('+ settings.imageDir + settings.imageFloating +') right bottom no-repeat; '+ showOption(settings.showFloatRightBottom) +'"></div>' +
						'</div>' +
						//DROP SHADOW
						'<div class="splitscreen-shadow-left splitscreen-erase">' +
							'<div class="splitscreen-shadow splitscreen-erase"></div>' +		
						'</div>' +
						'<div class="splitscreen-shadow-right splitscreen-erase">' +
							'<div class="splitscreen-shadow splitscreen-erase"></div>' +							
						'</div>' +						
						//END DROP SHADOW							
						'<!--[ UNDERLAY ]-->' +
						'<div class="splitscreen-underlay splitscreen-erase-fade" style="background:'+ settings.colorUnderlay +'; filter:alpha(opacity='+ settings.alphaUnderlay +'); -moz-opacity:0.'+ settings.alphaUnderlay +'; opacity:0.'+ settings.alphaUnderlay +'; filter:progid:DXImageTransform.Microsoft.Alpha(opacity='+ settings.alphaUnderlay +'); '+ showOption(settings.showUnderlay) +';"></div>' +		
						'<div class="splitscreen-teaser">' +
							'<table class="splitscreen-teaser-holder" cellpadding="0" cellspacing="0"><tbody><td class="splitscreen-teaser-body" valign="'+ settings.verticalAlignTeaser +'"></td></tbody></table>' +						
						'</div>' +
					'</div>' +
				'</div>' +
				'<!--[ STOP SPLITSCREEN ]-->'
			;}
			
			// PREPEND SPLITSCREEN to THE BODY	
			$(this).prepend(splitscreenHtml);
			
			// HIDE SCROLLBAR WHILE SPLITSCREEN IS ACTIVE
			$("body").addClass("splitscreen_body");

			// ADD TEASER HTML TO THE TEASER SECTION
			$('div.splitscreen-teaser td.splitscreen-teaser-body').append($('div.splitscreen-teaser-html'));			
			

			// FULLSCREEN IMAGING
			function FullScreenBackground(theItem){
				
				var winWidth=$(window).width();
				var winHeight=$(window).height();
				var imageWidth=$(theItem).width();
				var imageHeight=$(theItem).height();
				
				var picHeight = imageHeight / imageWidth;
				var picWidth = imageWidth / imageHeight;
				
			if ((winHeight / winWidth) < picHeight) {
				$(theItem).css("width",winWidth);
				$(theItem).css("height",picHeight*winWidth);
			} else {
				$(theItem).css("height",winHeight);
				$(theItem).css("width",picWidth*winHeight);
				}
				$(theItem).css("margin-left",winWidth / $(theItem).width() / 2); //$(theItem).css("margin-left",winWidth / 2 - $(theItem).width() / 2);
				$(theItem).css("margin-top",winHeight / $(theItem).height() / 2); //$(theItem).css("margin-top",winHeight / 2 - $(theItem).height() / 2);				
				
			}			
			
			window.onload = function (){FullScreenBackground('.splitscreen-fullscreen'); $(".splitscreen-load-mask").css({'display':'none'});};
			$(window).resize(function(){FullScreenBackground('.splitscreen-fullscreen');});
				
			// AUTOMATE				
			function update() {
				if (alreadyopen === false){	
				$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "-15%"}, {duration: 1500, easing: 'jswing'});
				$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "65%"}, {duration: 1500, easing: 'jswing'});
				$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "0%"}, {duration: 1000, easing: 'jswing'});
				$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "50%"}, {duration: 1000, easing: 'jswing'});		
				}				
				} 

			if(settings.showIntroSplit === true){setTimeout(update, settings.splitTimeIntro);}			// Activates splitscreen once after the page is loaded
			if(settings.showAutoSplit === true){var autoplay = setInterval(update, settings.splitTimeAuto);}  // Makes splitscreen activate every (x) seconds
			
if (DetectTierIphone() || DetectTierTablet()){

			// MOUSE ACTION MOBILE	
			if(settings.splitMode == 'splash'){			
				$('.splitscreen-activate').mousedown(function () { 
						$('.splitscreen-left, .splitscreen-shadow-left').queue('fx', []);
						$('.splitscreen-right, .splitscreen-shadow-right').queue('fx', []);
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "-10%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "60%"}, {duration: 1000, easing: 'jswing'});
						alreadyopen = true;
						});					
				$('.splitscreen-sector').mousedown(function () { 
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "0%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "50%"}, {duration: 1000, easing: 'jswing'});
						alreadyopen = false;
						});						
			}
			
			if(settings.splitMode == 'promo' || settings.splitMode == 'teaser'){			
				$('.splitscreen-activate').mousedown(function () { 
						$('.splitscreen-left, .splitscreen-shadow-left').queue('fx', []);
						$('.splitscreen-right, .splitscreen-shadow-right').queue('fx', []);						
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "-10%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "60%"}, {duration: 1000, easing: 'jswing'});					
						$(".splitscreen-activate").animate({width: "2px"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-activate-2").animate({width: "2px", left: "69.9%"}, {duration: 1000, easing: 'jswing'});	
						alreadyopen = true;
						});	
				$('.splitscreen-underlay').mousedown(function () { 		
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "-10%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "60%"}, {duration: 1000, easing: 'jswing'});
						alreadyopen = true;
						});
				$('.splitscreen-teaser').mousedown(function () { 			
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "-10%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "60%"}, {duration: 1000, easing: 'jswing'});
						alreadyopen = true;
						});								
				$('.splitscreen-sector').mousedown(function () { 
						$('.splitscreen-left, .splitscreen-shadow-left').queue('fx', []);
						$('.splitscreen-right, .splitscreen-shadow-right').queue('fx', []);
						$('.splitscreen-activate').queue('fx', []);
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "0%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "50%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-activate").animate({width: "20%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-activate-2").animate({width: "20%", left: "50%"}, {duration: 1000, easing: 'jswing'});
						alreadyopen = false;
						});
			}

	}else{			
			// MOUSE ACTION	STANDARD
			if(settings.splitMode == 'splash'){			
				$('.splitscreen-activate').mousedown(function () { 
						$('.splitscreen-left, .splitscreen-shadow-left').queue('fx', []);
						$('.splitscreen-right, .splitscreen-shadow-right').queue('fx', []);
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "-10%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "60%"}, {duration: 1000, easing: 'jswing'});
						alreadyopen = true;
						});					
				$('.splitscreen-sector').mousedown(function () { 
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "0%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "50%"}, {duration: 1000, easing: 'jswing'});
						alreadyopen = false;
						});						
			}
			
			if(settings.splitMode == 'promo' || settings.splitMode == 'teaser'){			
				$('.splitscreen-activate').mousedown(function () { 
						$('.splitscreen-left, .splitscreen-shadow-left').queue('fx', []);
						$('.splitscreen-right, .splitscreen-shadow-right').queue('fx', []);						
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "-10%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "60%"}, {duration: 1000, easing: 'jswing'});					
						$(".splitscreen-activate").animate({width: "2px"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-activate-2").animate({width: "2px", left: "69.9%"}, {duration: 1000, easing: 'jswing'});	
						alreadyopen = true;
						});	
				$('.splitscreen-underlay').mousedown(function () { 		
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "-10%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "60%"}, {duration: 1000, easing: 'jswing'});
						alreadyopen = true;
						});
				$('.splitscreen-teaser').mousedown(function () { 			
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "-10%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "60%"}, {duration: 1000, easing: 'jswing'});
						alreadyopen = true;
						});								
				$('.splitscreen-sector').mousedown(function () { 
						$('.splitscreen-left, .splitscreen-shadow-left').queue('fx', []);
						$('.splitscreen-right, .splitscreen-shadow-right').queue('fx', []);
						$('.splitscreen-activate').queue('fx', []);
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "0%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "50%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-activate").animate({width: "20%"}, {duration: 1000, easing: 'jswing'});
						$(".splitscreen-activate-2").animate({width: "20%", left: "50%"}, {duration: 1000, easing: 'jswing'});
						alreadyopen = false;
						});
			}
}			
				// CLOSE SPLITSCREEN: FUNCTION
				$.closeSplitscreen = function() {
					function slideOut() {
						$(".splitscreen-left, .splitscreen-shadow-left").animate({left: "-150%"}, {duration: 850, easing: 'jswing'});
						$(".splitscreen-right, .splitscreen-shadow-right").animate({left: "200%"}, {duration: 850, easing: 'jswing'});
						setTimeout(postFade, 1000);
						}				
					function postFade() {
						$(".splitscreen-erase").fadeOut({duration: 1000});
						$(".splitscreen-erase-fade").fadeOut({duration: 1000});
						$(".splitscreen").fadeOut({duration: 2000});
						$('body').removeClass('splitscreen_body');
						}
					var closeSplitscreen = slideOut();										
				};				
				
				// BIND ELEMENT TO CLOSE SPLITSCREEN ON ELEMENT ACTION						
				$('.x-splitscreen').bind('click', function() {						
						$.closeSplitscreen();
						if(settings.autoSplit === true){clearInterval(autoplay);}
				});				
						
		});		
		
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/
		}else{} //CLOSE OF COOKIE ELSE
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/	
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/ 
	/*--------------------------------------------------------------*/
	/*--------------------------------------------------------------*/		

 	};	
})(jQuery);