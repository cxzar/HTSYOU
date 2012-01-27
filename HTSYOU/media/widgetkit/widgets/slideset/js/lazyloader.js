/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

(function(d){$widgetkit.lazyloaders.slideset=function(b,a){var c=b.find("ul.set").show();gwidth=a.width=="auto"?b.width():a.width;d.browser.msie&&d.browser.version<8&&c.children().css("display","inline");var e=a.height=="auto"?c.eq(0).outerHeight(!0):a.height;c.eq(0).parent().css({height:e});b.css({width:gwidth});c.css({height:e});$widgetkit.load(WIDGETKIT_URL+"/widgets/slideset/js/slideset.js").done(function(){d(b).slideset(a).css("visibility","visible")})}})(jQuery);
