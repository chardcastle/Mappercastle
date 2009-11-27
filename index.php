<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Google Maps JavaScript API Example</title>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<script type="text/javascript">
		google.load("jquery","1.3.2");		
	</script>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAme0aFH7m6WR-C3sPFGPBuxQfIFOWQfFK3zjKRLbUsEYVdhcLVhRpvs4-pN_as-0m1gQ0n3Odx8rX9g&sensor=false"
            type="text/javascript"></script>
	<script src="http://gmaps-utility-library.googlecode.com/svn/trunk/markermanager/release/src/markermanager.js"></script>
    <script type="text/javascript">
	    var map;
		var mgr;
		var icons = {};
		var allmarkers = [];

		function initialize() {
		  if (GBrowserIsCompatible()) {
		    var map = new GMap2(document.getElementById("map_canvas"));
		    map.setCenter(new GLatLng(37.4419, -122.1419), 13);
		    map.setUIToDefault();
			setupOfficeMarkers(map);

		  }
		}

		var officeLayer = [
		  {
			"zoom": [0, 3],
			"places": [
			  { "name": "US Offices", "icon": ["test", "test"], "posn": [40, -97] },
			  { "name": "Canadian Offices", "icon": ["test", "test"], "posn": [58, -101] }
			]
		  }	  
		]
		var iconData = {
		  "test": { width: 24, height: 14 },
		  "test": { width: 24, height: 14 }
		};

		function getIcon(images) {
		  var icon = null;
		  if (images) {
		    if (icons[images[0]]) {
		      icon = icons[images[0]];
		    } else {
		      icon = new GIcon();
		      icon.image = "images/" 
		          + images[0] + ".jpg";
		      var size = iconData[images[0]];
		      icon.iconSize = new GSize(size.width, size.height);
		      icon.iconAnchor = new GPoint(size.width >> 1, size.height >> 1);
		      icon.shadow = "images/" 
		          + images[1] + ".jpg";
		      size = iconData[images[1]];
		      icon.shadowSize = new GSize(size.width, size.height);
		      icons[images[0]] = icon;
		    }
		  }
		  return icon;
		}

		function setupOfficeMarkers(map) {
		  var mgr = new MarkerManager(map);
		  for (var i in officeLayer) {
			var layer = officeLayer[i];
			var markers = [];
			for (var j in layer["places"]) {
			  var place = layer["places"][j];
			  var icon = getIcon(place["icon"]);
			  var posn = new GLatLng(place["posn"][0], place["posn"][1]);
			  markers.push(new GMarker(posn, { title: place["name"], icon: icon }));
			}
			mgr.addMarkers(markers, layer["zoom"][0], layer["zoom"][1]);
		  }
		  mgr.refresh();
		}

    </script>
  </head>	
	<form method="post" action="getMap.php" id="searchMap">
	<input name="location" type="text" name="location" value=""/>
	<button type="submit">Search</button>
	</form>
	<script type="text/javascript">
		(function($){

			$("#searchMap").submit(function(e){
				$("#locations div[id!=placeTemplate]").remove();
				e.preventDefault();
				var url = $(this).attr("action");
				var data = $(this).serialize();			
				$.post(url,data,function(json){
					//alert(json.isSingle+" "+json.count);
					if(json.isSingle){
						makeRow(json.data.query.results.place);
					}else{
						$.each(json.data.query.results.place,function(i,item){
							makeRow(item);
						});
					}
					var blueIcon = new GIcon(G_DEFAULT_ICON);
					blueIcon.image = "http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png";				
				},"json");

				function makeRow(item){
					if(item){
					var row = $("#placeTemplate")
						.clone()
							.find("h3")
							.html(item.admin2.content)
						.end()
							.find(".goTo")
							.attr("href","#")
							.click(function(e){
								e.preventDefault();
								var map = new GMap2(document.getElementById("map_canvas"));
								map.setCenter(new GLatLng(item.centroid.latitude, item.centroid.longitude), 13);
								var blueIcon = new GIcon(G_DEFAULT_ICON);
								blueIcon.image = "http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png";
								map.addOverlay(new GMarker(new GLatLng(item.centroid.latitude, item.centroid.longitude),{icon:blueIcon}))
							})
						.end()
						.find(".desc")
							.html(item.desc)							
						.end()
						.attr("id","")
						.appendTo("#locations")
						.show();
					console.log(item.name + " " +item.centroid.latitude+" "+item.woeid);
					}
				}
			});
		})(jQuery);
	</script>
  <body onload="initialize()" onunload="GUnload()">
    <div id="map_canvas" style="width: 500px; height: 300px"></div>
	<div id="locations">
		<div id="placeTemplate" style="display:none">
			<h3>Title</h3>
			<p><a href="" class="goTo">Go to location</a><span class="desc">Description</span></p>
		</div>
	</div>
  </body>
</html>
