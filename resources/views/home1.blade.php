<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Map Location Selection</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@1.13.1/dist/Control.Geocoder.css" />
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <script src="https://unpkg.com/leaflet-control-geocoder@1.13.1/dist/Control.Geocoder.js"></script>
  <style>
    #map {
      height: 500px;
    }
  </style>
</head>
<body>
  <button id="open-map-btn">Select Location</button>
  <input type="text" id="location-input">
  <button id="detect-location-btn">Detect My Location</button>
  <div id="map"></div>
  <script>
    var map = L.map('map').setView([51.505, -0.09], 13);
    var marker = null;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      maxZoom: 18,
      id: 'mapbox/streets-v11',
      tileSize: 512,
      zoomOffset: -1
    }).addTo(map);

    function onLocationFound(e) {
      var lat = e.latitude;
      var lng = e.longitude;
      if (marker === null) {
        marker = L.marker([lat, lng]).addTo(map);
      } else {
        marker.setLatLng([lat, lng]);
      }
      map.setView([lat, lng], 15);
      updateLocationInput(lat, lng);
    }

    function onLocationError(e) {
      alert("Error: " + e.message);
    }

    function updateLocationInput(lat, lng) {
      document.getElementById("location-input").value = lat + "," + lng;
    }

    map.on('click', function(e) {
      var lat = e.latlng.lat;
      var lng = e.latlng.lng;
      if (marker === null) {
        marker = L.marker([lat, lng]).addTo(map);
      } else {
        marker.setLatLng([lat, lng]);
      }
      updateLocationInput(lat, lng);
    });

    document.getElementById("open-map-btn").addEventListener("click", function() {
      var popup = L.popup()
        .setLatLng([51.5, -0.09])
        .setContent('<div id="map"></div>')
        .openOn(map);

      var searchControl = L.Control.geocoder({
        defaultMarkGeocode: false,
        collapsed: false
      }).on('markgeocode', function(e) {
        map.setView(e.geocode.center, 13);
        if (marker === null)
        document.getElementById("detect-location-btn").addEventListener("click", function() {
        map.on('locationfound', onLocationFound);
        map.on('locationerror', onLocationError);
        map.locate({setView: true, maxZoom: 15});
        });
        </script>

        </body>
        </html>
