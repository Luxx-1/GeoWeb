<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeoWeb</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ 'css/index.css' }}">
</head>

<body>

    <header>
        <h1>Peringatan Gempa Terkini</h1>
        <span class="by-geoweb">by GeoWeb</span>
    </header>

    <div id="map"></div>

    <div class="gempa-list" id="gempa-list">
        <h2>Daftar Gempa Terkini</h2>
    </div>

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 GeoWeb. All Rights Reserved.</p>
            <p>Powered by <strong>GeoWeb Team</strong></p>
            <p>Follow us on
                <a href="https://www.instagram.com" target="_blank">Instagram</a>,
                <a href="https://www.twitter.com" target="_blank">Twitter</a>, and
                <a href="https://www.facebook.com" target="_blank">Facebook</a>
            </p>
        </div>
    </footer>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        var gempaData = @json($gempaData);

        var map = L.map('map', {
            maxBounds: [
                [-11.5, 95.0],
                [6.5, 141.5]
            ],
            maxBoundsViscosity: 1.0,
            zoomControl: true,
            scrollWheelZoom: false,
            minZoom: 5,
            maxZoom: 10
        }).setView([-7.08, 106.70], 5);

        var activeImpactCircle = null;
        var activeMarker = null;
        var initialZoomLevel = map.getZoom();

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        function getColorByMagnitude(magnitude) {
            if (magnitude < 4.0) return 'circle-green';
            if (magnitude < 6.0) return 'circle-yellow';
            return 'circle-red';
        }

        function getImpactRadius(magnitude) {
            if (magnitude < 4.0) return 50000;
            if (magnitude < 6.0) return 100000;
            return 200000;
        }

        gempaData.forEach(function(gempa) {
            var coords = gempa.Coordinates.split(',');
            var lat = parseFloat(coords[0]);
            var lon = parseFloat(coords[1]);

            if (lat >= -11.5 && lat <= 6.5 && lon >= 95.0 && lon <= 141.5) {
                var colorClass = getColorByMagnitude(parseFloat(gempa.Magnitude));

                var marker = L.marker([lat, lon], {
                        icon: L.divIcon({
                            className: 'custom-icon ' + colorClass,
                            html: '',
                            iconSize: [15, 15]
                        })
                    }).addTo(map)
                    .bindPopup('<b>Gempa:</b><br>' + gempa.Tanggal + ' ' + gempa.Jam + '<br>' +
                        '<b>Magnitude:</b> ' + gempa.Magnitude + ' SR<br>' +
                        '<b>Wilayah:</b> ' + gempa.Wilayah);

                var radius = getImpactRadius(parseFloat(gempa.Magnitude));
                var circle = L.circle([lat, lon], {
                    color: colorClass === 'circle-green' ? 'green' : colorClass === 'circle-yellow' ?
                        'yellow' : 'red',
                    fillColor: colorClass === 'circle-green' ? 'green' : colorClass === 'circle-yellow' ?
                        'yellow' : 'red',
                    fillOpacity: 0.3,
                    radius: radius
                });

                marker._impactCircle = circle;

                var gempaItem = document.createElement('div');
                gempaItem.className = 'gempa-item';
                gempaItem.innerHTML = '<b>' + gempa.Tanggal + ' - ' + gempa.Jam + '</b><br>' +
                    '<p><b>Magnitude:</b> ' + gempa.Magnitude + ' SR</p>' +
                    '<p><b>Wilayah:</b> ' + gempa.Wilayah + '</p>' +
                    '<p><b>Koordinat:</b> ' + gempa.Coordinates + '</p>' +
                    '<div class="details">Detail Gempa: ' + gempa.Dirasakan + '</div>';

                document.getElementById('gempa-list').appendChild(gempaItem);

                gempaItem.addEventListener('click', function() {
                    gempaItem.classList.toggle('open');
                });

                marker.on('click', function() {
                    map.setView([lat, lon], 7);

                    if (activeMarker === marker) {
                        if (map.hasLayer(marker._impactCircle)) {
                            map.removeLayer(marker._impactCircle);
                            activeImpactCircle = null;
                            activeMarker = null;
                            map.setZoom(initialZoomLevel);
                        }
                    } else {
                        if (activeImpactCircle) {
                            map.removeLayer(activeImpactCircle);
                        }
                        map.addLayer(marker._impactCircle);
                        activeImpactCircle = marker._impactCircle;
                        activeMarker = marker;
                    }
                });
            }
        });

        var legend = L.control({
            position: 'topright'
        });

        legend.onAdd = function() {
            var div = L.DomUtil.create('div', 'legend');
            div.innerHTML = '<strong>Magnitudo dan Radius</strong><br>' +
                '<div><span class="color-box" style="background-color: #2ecc71;"></span> Magnitudo < 4</div>' +
                '<div><span class="color-box" style="background-color: #f39c12;"></span> Magnitudo 4 - 6</div>' +
                '<div><span class="color-box" style="background-color: #e74c3c;"></span> Magnitudo >= 6</div>' +
                '<br>' +
                '<strong>Radius Terdampak</strong><br>' +
                '<div><span class="radius-box radius-green"></span> 50 km (Magnitudo < 4)</div>' +
                '<div><span class="radius-box radius-yellow"></span> 100 km (Magnitudo 4 - 6)</div>' +
                '<div><span class="radius-box radius-red"></span> 200 km (Magnitudo >= 6)</div>';
            return div;
        };

        legend.addTo(map);
    </script>

</body>

</html>
