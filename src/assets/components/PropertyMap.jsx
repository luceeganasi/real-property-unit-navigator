import React, { useEffect } from 'react';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const PropertyMap = ({ properties }) => {
  useEffect(() => {
    // Initialize the map
    const map = L.map('map').setView([14.5995, 120.9842], 10); // Default view centered on Manila

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add markers for each property
    properties.forEach(property => {
      if (property.latitude && property.longitude) {
        L.marker([property.latitude, property.longitude])
          .addTo(map)
          .bindPopup(`
            <strong>${property.title}</strong><br>
            Price: ₱${property.price.toLocaleString()}<br>
            <a href="/property/${property.id}">View Details</a>
          `);
      }
    });

    // Cleanup function to remove the map when the component unmounts
    return () => {
      map.remove();
    };
  }, [properties]);

  return <div id="map" style={{ height: '400px', width: '100%' }}></div>;
};

export default PropertyMap;

