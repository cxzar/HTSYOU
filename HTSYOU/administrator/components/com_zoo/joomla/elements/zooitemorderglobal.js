/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

(function(c){var a=function(){};c.extend(a.prototype,{name:"ZooItemOrderGlobal",initialize:function(a){var b=a.closest(".zoo-itemorder-global").find('input:checkbox[name="_global"]');a.find("select.element, input:checkbox").each(function(){c(this).data("_name",c(this).attr("name"))});b.is(":checked")&&a.hide().find("select.element, input:checkbox").attr("name","");b.bind("change",function(){a.slideToggle().find("select.element, input:checkbox").attr("name",function(){return b.is(":checked")?"":c(this).data("_name")})})}});
c.fn[a.prototype.name]=function(){var e=arguments,b=e[0]?e[0]:null;return this.each(function(){var d=c(this);if(a.prototype[b]&&d.data(a.prototype.name)&&b!="initialize")d.data(a.prototype.name)[b].apply(d.data(a.prototype.name),Array.prototype.slice.call(e,1));else if(!b||c.isPlainObject(b)){var f=new a;a.prototype.initialize&&f.initialize.apply(f,c.merge([d],e));d.data(a.prototype.name,f)}else c.error("Method "+b+" does not exist on jQuery."+a.name)})}})(jQuery);
