/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

jQuery(function(a){a("div.radio-global").each(function(){var b=a(this).find("input:checkbox"),c=a(this).find("div.input");b.is(":checked")&&c.hide();b.change(function(){c.slideToggle().find("input[type=radio]").attr("name",a(this).is(":checked")?a(this).attr("id"):a(this).val())})})});
