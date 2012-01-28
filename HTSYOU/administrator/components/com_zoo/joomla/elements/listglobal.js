/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

jQuery(function(a){a("div.list-global").each(function(){var b=a(this).find("input:checkbox"),c=a(this).find("div.input");b.is(":checked")&&c.hide();b.change(function(){c.slideToggle().find("select").attr("name",a(this).is(":checked")?"":a(this).val())})})});
