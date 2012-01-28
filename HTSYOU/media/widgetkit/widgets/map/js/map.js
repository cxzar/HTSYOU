/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

(function(d){var e=function(){},g=!1,i=!1,f=[];window.google&&google.maps&&(i=g=!0);d.extend(e.prototype,{name:"googlemaps",options:{lat:53.553407,lng:9.992196,marker:!0,popup:!1,text:"",zoom:13,mapCtrl:1,zoomWhl:!0,mapTypeId:"roadmap",typeCtrl:!0,directions:!0,directionsDestUpdate:!0,mainIcon:"red-dot",otherIcon:"blue-dot",iconUrl:"http://maps.google.com/mapfiles/ms/micons/"},initialize:function(a,c){this.options.msgFromAddress=$widgetkit.trans.get("FROM_ADDRESS");this.options.msgGetDirections=$widgetkit.trans.get("GET_DIRECTIONS");
this.options.msgEmpty=$widgetkit.trans.get("FILL_IN_ADDRESS");this.options.msgNotFound=$widgetkit.trans.get("ADDRESS_NOT_FOUND");this.options.msgAddressNotFound=$widgetkit.trans.get("LOCATION_NOT_FOUND");this.options=d.extend({},this.options,c);this.container=a;i?this.setupMap():f.push(this)},setupMap:function(){var a=this.options;this.map=new google.maps.Map(this.container.get(0),{mapTypeId:a.mapTypeId,center:new google.maps.LatLng(a.lat,a.lng),streetViewControl:a.mapCtrl?!0:!1,navigationControl:a.mapCtrl,
scrollwheel:a.zoomWhl?!0:!1,mapTypeControl:a.typeCtrl?!0:!1,zoomControl:a.mapCtrl?!0:!1,zoomControlOptions:{style:a.mapCtrl==1?google.maps.ZoomControlStyle.DEFAULT:google.maps.ZoomControlStyle.SMALL}});this.infowindow=new google.maps.InfoWindow;a.marker&&(a.popup==0?(this.map.setCenter(new google.maps.LatLng(a.lat,a.lng)),this.map.setZoom(a.zoom)):this.addMarkerLatLng(a.lat,a.lng,a.text,!0));if(a.mapTypeId=="roadmap")this.map.mapTypeIds=["custom"],this.map.mapTypes.set("custom",new google.maps.StyledMapType([{featureType:"all",
elementType:"all",stylers:[{invert_lightness:a.styler_invert_lightness},{hue:a.styler_hue},{saturation:a.styler_saturation},{lightness:a.styler_lightness},{gamma:a.styler_gamma}]}],{name:"CustomStyle"})),this.map.setMapTypeId("custom");if(a.adresses&&a.adresses.length)for(var c=0;c<a.adresses.length;c++){var b=a.adresses[c];b.lat&&b.lng&&this.addMarkerLatLng(b.lat,b.lng,b.popup,b.center,b.icon)}a.directions&&this.setupDirections()},createMarker:function(a,c,b){var d=this,e=this.map,f=this.infowindow,
g=new google.maps.MarkerImage(this.options.iconUrl+b+".png",new google.maps.Size(32,32),new google.maps.Point(0,0),new google.maps.Point(16,32)),b=b.match("pushpin")?this.options.iconUrl+"pushpin_shadow.png":this.options.iconUrl+"msmarker.shadow.png",b=new google.maps.MarkerImage(b,new google.maps.Size(56,32),new google.maps.Point(0,0),new google.maps.Point(16,32)),h=new google.maps.Marker({position:a,icon:g,shadow:b,map:this.map});google.maps.event.addListener(h,"click",function(){c.length&&(f.setContent(c),
f.open(e,h));if(d.options.directionsDestUpdate)d.options.lat=h.getPosition().lat(),d.options.lng=h.getPosition().lng()});return h},centerMap:function(a,c){this.map.setCenter(new google.maps.LatLng(a,c));this.map.setZoom(this.options.zoom)},addMarkerLatLng:function(a,c,b,d,e){e=e||this.options.otherIcon;if(d)e=this.options.mainIcon;a=new google.maps.LatLng(a,c);e=this.createMarker(a,b,e);d&&(this.map.setCenter(a),this.map.setZoom(this.options.zoom));d&&b&&b.length&&this.options.popup==2&&(this.infowindow.setContent(b),
this.infowindow.open(this.map,e))},setupDirections:function(){var a=this;this.directionsService=new google.maps.DirectionsService;this.directionsDisplay=new google.maps.DirectionsRenderer;this.directionsDisplay.setMap(this.map);this.directionsDisplay.setPanel(d("<div>").addClass("directions").css("position","relative").insertAfter(this.container).get(0));var c=d("<p>").append('<label for="from-address">'+this.options.msgFromAddress+"</label>").append('<input type="text" name="address" style="margin:0 5px;" />').append('<button type="submit">'+
this.options.msgGetDirections+"</button>");d('<form method="get" action="#"></form>').append(c).insertAfter(this.container).bind("submit",function(b){b.preventDefault();b.stopPropagation();a.setDirections(d(this))})},setDirections:function(a){var c=this;this.container.parent().find("div.alert").remove();a=a.find('input[name="address"]').val();a===""?this.showAlert(this.options.msgEmpty):this.directionsService.route({origin:a,destination:new google.maps.LatLng(this.options.lat,this.options.lng),travelMode:google.maps.DirectionsTravelMode.DRIVING},
function(a,d){d==google.maps.DirectionsStatus.OK?c.directionsDisplay.setDirections(a):c.showAlert(c.options.msgNotFound)})},showAlert:function(a){d("<div>").addClass("alert").append(d("<strong>").text(a)).insertAfter(this.container)},cmd:function(){var a=arguments,c=a[0]?a[0]:null;this.map[c]&&this.map[c].apply(this.map,Array.prototype.slice.call(a,1))}});d.fn[e.prototype.name]=function(){var a=arguments,c=a[0]?a[0]:null;return this.each(function(){if(!g){var b=document.createElement("script");b.type=
"text/javascript";b.async=1;b.src=location.protocol+"//maps.google.com/maps/api/js?sensor=false&callback=jQuery.fn.googlemaps.ready";document.body.appendChild(b);g=!0}b=d(this);if(e.prototype[c]&&b.data(e.prototype.name)&&c!="initialize")b.data(e.prototype.name)[c].apply(b.data(e.prototype.name),Array.prototype.slice.call(a,1));else if(!c||d.isPlainObject(c)){var f=new e;e.prototype.initialize&&f.initialize.apply(f,d.merge([b],a));b.data(e.prototype.name,f)}else d.error("Method "+c+" does not exist on jQuery."+
e.name)})};d.fn[e.prototype.name].ready=function(){for(var a=0;a<f.length;a++)f[a].setupMap&&f[a].setupMap();i=!0}})(jQuery);
