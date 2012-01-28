/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

(function(c){var b=function(){};b.prototype=c.extend(b.prototype,{name:"ImportExport",options:{msgImportWarning:"Please choose a file first!"},initialize:function(a,e){this.options=c.extend({},this.options,e);var b=this;a.find("div.uploadbox").each(function(){var a=c(this).find("input.filename");c(this).find('input[name^="import-"]').bind("change",function(){a.val(this.value)});c(this).find("button.upload").bind("click",function(){a.val()==""?alert(b.options.msgImportWarning):submitbutton("import")})});
a.find("a.exporter-link").each(function(){var b=c(this);b.bind("click",function(){a.find('input[name="exporter"]').val(b.attr("id"));submitbutton("importfrom")})});a.find("button.export").bind("click",function(){var c=a.find('input[name="format"]');c.val("raw");a.find('input[name="exporter"]').val("zoo2");submitbutton("doexport");c.val("html")})}});c.fn[b.prototype.name]=function(){var a=arguments,e=a[0]?a[0]:null;return this.each(function(){var d=c(this);if(b.prototype[e]&&d.data(b.prototype.name)&&
e!="initialize")d.data(b.prototype.name)[e].apply(d.data(b.prototype.name),Array.prototype.slice.call(a,1));else if(!e||c.isPlainObject(e)){var f=new b;b.prototype.initialize&&f.initialize.apply(f,c.merge([d],a));d.data(b.prototype.name,f)}else c.error("Method "+e+" does not exist on jQuery."+b.name)})}})(jQuery);
(function(c){var b=function(){};b.prototype=c.extend(b.prototype,{name:"Import",options:{msgSelectWarning:"You didn't assign all item types. Items of these types will not be imported. Do you want to proceed anyway?",msgWarningDuplicate:"There are duplicate assignments.",msgNameWarning:"Please choose a name column.",task:"doimport"},initialize:function(a,b){this.options=c.extend({},this.options,b);var d=this;this.form=a;a.find("fieldset.frontpage, fieldset.categories").each(function(){var a=c(this);
a.find('input[name^="import-"]').bind("change",function(){c(this).is(":checked")?a.find("div.assign-group").show():a.find("div.assign-group").hide()})});a.find("fieldset.items").each(function(){var a=c(this);a.find("select.type").bind("change",function(){var b=c(this),e=RegExp("\\["+b.val()+"\\]$");a.find("li.assign select.assign").each(function(){var a=c(this);if(a.attr("name").match(e)){var b=a.nextAll(".name").text()+" "+a.nextAll(".type").text();a.show().find("option").each(function(){c(this).text()==
b&&a.val(c(this).val())})}else a.hide()})})});c("#submit-button").bind("click",function(){d.options.task!="doimportcsv"&&d.hasDuplicateSelectedAssignments()?alert(d.options.msgWarningDuplicate):d.options.task=="doimportcsv"&&!d.hasNameAssigned()?alert(d.options.msgNameWarning):(!d.hasUnassignedTypes()||confirm(d.options.msgSelectWarning))&&submitbutton(d.options.task)})},hasNameAssigned:function(){var a=!1;this.form.find("div.assign-group").each(function(){var b=RegExp("\\["+c(this).find("select.type").val()+
"\\]$");c(this).find("select.assign").each(function(){c(this).attr("name").match(b)&&c(this).val()=="_name"&&(a=!0)})});return a},hasUnassignedTypes:function(){var a=!1;this.form.find("select.type").each(function(){c(this).val()==""&&(a=!0)});return a},hasDuplicateSelectedAssignments:function(){var a=!1;this.form.find("div.assign-group").each(function(){var b=c(this).find("select.assign");for(i=0;i<b.length;i++)for(j=i+1;j<b.length;j++)if(value_a=c(b.get(i)).val(),value_b=c(b.get(j)).val(),value_a!=
""&&value_a!="_category"&&value_a==value_b){a=!0;return}});return a}});c.fn[b.prototype.name]=function(){var a=arguments,e=a[0]?a[0]:null;return this.each(function(){var d=c(this);if(b.prototype[e]&&d.data(b.prototype.name)&&e!="initialize")d.data(b.prototype.name)[e].apply(d.data(b.prototype.name),Array.prototype.slice.call(a,1));else if(!e||c.isPlainObject(e)){var f=new b;b.prototype.initialize&&f.initialize.apply(f,c.merge([d],a));d.data(b.prototype.name,f)}else c.error("Method "+e+" does not exist on jQuery."+
b.name)})}})(jQuery);
