/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

jQuery(function(a){a("div.zoo-feed").each(function(){var b=a(this).find("div.input"),c=a(this).find("input:radio");c.first().is(":checked")&&b.hide();c.bind("change",function(){b.slideToggle()})})});
