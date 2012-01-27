/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

jQuery(function(a){a("div.feed-global").each(function(){var b=a(this).find("input:checkbox"),c=a(this).find("div.input"),d=c.find("input");b.is(":checked")&&c.hide();b.bind("change",function(){a(this).is(":checked")?d.attr("name",a(this).attr("id")):d.attr("name",function(){return a(this).attr("role")});c.slideToggle()});var b=c.find("input:radio"),e=c.find("div.feed-input");b.first().is(":checked")&&e.hide();b.bind("change",function(){e.slideToggle()})})});
