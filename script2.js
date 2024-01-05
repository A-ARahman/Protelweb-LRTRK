// Define the map variable outside to make it accessible in both functions
// Define the map and historyLayer variables outside to make them accessible in all functions
let map;
let markerLayer = L.layerGroup(); // Initialize a layer group for markers
let historyLayer = L.layerGroup(); // Initialize a layer group for historical markers


document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map on the "map" div
    map = L.map('map').setView([-7.284531, 112.796682], 12);

    // Add an OpenStreetMap tile layer to the map
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add the marker layer to the map
    markerLayer.addTo(map);
    historyLayer.addTo(map); // Make sure to add this line to initialize the historyLayer

    // Fetch the latest positions and positions every 5 seconds
    setInterval(function() {
        fetchLatestPositions();
        fetchPositions();
    }, 5000); 
});

function fetchLatestPositions() {
    fetch('http://localhost:3000/latest-positions') // Adjust this URL to where your server is hosted
        .then(response => response.json())
        .then(data => {
            // Clear existing markers
            markerLayer.clearLayers();

            // Add new markers from the latest data
            data.forEach(position => {
                if (position.latitude && position.longitude) {
                  var userMarker = L.marker([position.latitude, position.longitude]);
              
                  userMarker.bindPopup(`
                      <b>Profile ID:</b> ${position.profile_id}<br>
                      <b>LORA Series:</b> ${position.lora_noseries}<br>
                      <b>Latitude:</b> ${position.latitude}<br>
                      <b>Longitude:</b> ${position.longitude}<br>
                      <a href="#" onclick="fetchPositionHistory('${position.lora_noseries}', '${position.profile_id}'); return false;">View History</a><br>
                      <button onclick="closePopup();">Close History</button>
                  `, {
                      autoClose: false, // Prevent the popup from closing when another popup opens or the map is clicked
                      closeOnClick: false // Prevent the popup from closing when the map is clicked
                  });
              
                  markerLayer.addLayer(userMarker);
                }
              });
              
              // Add a custom 'click' event listener to the map
              map.on('click', function(e) {
                // Do nothing. This will override the default behavior of closing the popup.
              });
              
              // Function to close the popup, can be called from the button's onclick event
              function closePopup() {
                map.closePopup();
              }
        })
        .catch(error => console.error('Error fetching latest positions:', error));
}

// Function to fetch and display position history
// Function to fetch and display position history
function fetchPositionHistory(lora_noseries, profile_id) {
    // Clear existing historical markers
    historyLayer.clearLayers();

    // Fetch the position history from the server
    fetch(`http://localhost:3000/position-history/${lora_noseries}/${profile_id}`)
        .then(response => response.json())
        .then(data => {
            data.forEach(position => {
                if (position.latitude && position.longitude) {
                    L.circleMarker([position.latitude, position.longitude], {
                        radius: 3,
                        fillColor: "#ff7800",
                        color: "#000",
                        weight: 1,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(historyLayer);
                }
            });

            // Add the history layer to the map
            historyLayer.addTo(map);

            // Optionally, fit the map to the history layer bounds
            if (historyLayer.getLayers().length > 0) {
                map.fitBounds(historyLayer.getBounds());
            }
        })
        .catch(error => console.error('Error fetching position history:', error));
}

// Function to clear history markers
function clearHistory() {
    historyLayer.clearLayers();
}




function fetchPositions() {
    fetch('http://localhost:3000/positions')
        .then(response => response.json())
        .then(data => {
            console.log('Positions data:', data); // For debugging
            data.forEach(pos => {
                // Check if latitude and longitude are present and valid
                if (pos.latitude && pos.longitude) {
                    var customIcon = new L.Icon({
                        iconUrl: 'directional-sign.png', // Path to your marker icon
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });

                    var positionMarker = L.marker([pos.latitude, pos.longitude], { icon: customIcon })
                        .addTo(map);

                    // Bind popup with Pos Name, Latitude, and Longitude
                    positionMarker.bindPopup(`
                        <b>Pos Name:</b> ${pos.pos_name}<br>
                        <b>Latitude:</b> ${pos.latitude}<br>
                        <b>Longitude:</b> ${pos.longitude}
                    `);
                }
            });
        })
        .catch(error => console.error('Error fetching positions:', error));
}


