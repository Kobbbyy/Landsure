import './bootstrap';

import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

import 'leaflet-control-geocoder';
import 'leaflet-control-geocoder/dist/Control.Geocoder.css';

import 'leaflet-draw';
import 'leaflet-draw/dist/leaflet.draw.css';

import area from '@turf/area';

const mapElement = document.getElementById('map');

if (mapElement) {
    const map = L.map(mapElement).setView([7.9465, -1.0232], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.Control.geocoder({
        defaultMarkGeocode: true
    }).addTo(map);

    const drawnItems = new L.FeatureGroup();

    map.addLayer(drawnItems);

    const drawControl = new L.Control.Draw({
        edit: {
            featureGroup: drawnItems
        },
        draw: {
            polygon: true,
            rectangle: true,
            circle: false,
            marker: false,
            circlemarker: false,
            polyline: false
        }
    });

    map.addControl(drawControl);

    map.on(L.Draw.Event.CREATED, async function (e) {
        const layer = e.layer;

        drawnItems.addLayer(layer);

        const geoJSON = layer.toGeoJSON();

        const areaSquareMeters = area(geoJSON);
        const areaAcres = areaSquareMeters / 4046.8564224;

        const center = layer.getBounds().getCenter();

        const latitude = center.lat;
        const longitude = center.lng;

        const areaHectares = areaSquareMeters / 10000;
        const areaSquareKilometers = areaSquareMeters / 1000000;

        const popupContent = `
            <div style="min-width: 220px;">
                <strong>Selected Parcel</strong>

                <div style="margin-top: 8px;">
                    <div>
                        <strong>Area:</strong>
                        ${areaSquareMeters.toLocaleString(undefined, {
                            maximumFractionDigits: 2
                        })} m²
                    </div>

                    <div>
                        <strong>Acres:</strong>
                        ${areaAcres.toFixed(4)}
                    </div>

                    <div>
                        <strong>Hectares:</strong>
                        ${areaHectares.toFixed(4)}
                    </div>

                    <div>
                        <strong>Coordinates:</strong>
                        ${latitude.toFixed(6)},
                        ${longitude.toFixed(6)}
                    </div>
                </div>

                <button
                    type="button"
                    id="save-parcel"
                    style="
                        margin-top: 12px;
                        width: 100%;
                        padding: 9px 12px;
                        border: none;
                        border-radius: 6px;
                        background: #171717;
                        color: white;
                        cursor: pointer;
                        font-size: 13px;
                    "
                >
                    Save & Analyze
                </button>
            </div>
        `;

        layer.bindPopup(popupContent).openPopup();

        setTimeout(() => {
            const saveButton = document.getElementById('save-parcel');

            if (!saveButton) {
                return;
            }

            saveButton.addEventListener('click', async function () {
                saveButton.disabled = true;
                saveButton.textContent = 'Analyzing...';

                try {
                    const response = await fetch('/parcels', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify({
                            name: null,
                            latitude: latitude,
                            longitude: longitude,
                            area_square_meters: areaSquareMeters,
                            area_acres: areaAcres,
                            boundary: geoJSON
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(
                            data.message ||
                            'Unable to save the parcel.'
                        );
                    }

                    const risk = data.risk_analysis;

                    let riskMessage = 'Risk analysis completed.';

                    if (risk && risk.overall_status) {
                        riskMessage =
                            `Overall status: ${formatStatus(
                                risk.overall_status
                            )}`;
                    }

                    layer.bindPopup(`
                        <div style="min-width: 220px;">
                            <strong>Parcel Saved</strong>

                            <div style="margin-top: 8px;">
                                ${riskMessage}
                            </div>

                            <a
                                href="/parcels/${data.parcel.id}"
                                style="
                                    display: block;
                                    margin-top: 12px;
                                    padding: 9px 12px;
                                    border-radius: 6px;
                                    background: #171717;
                                    color: white;
                                    text-align: center;
                                    text-decoration: none;
                                    font-size: 13px;
                                "
                            >
                                View Risk Report
                            </a>
                        </div>
                    `).openPopup();

                } catch (error) {
                    console.error(error);

                    saveButton.disabled = false;
                    saveButton.textContent = 'Save & Analyze';

                    layer.bindPopup(`
                        <div style="min-width: 220px;">
                            <strong>Unable to save parcel</strong>

                            <div style="
                                margin-top: 8px;
                                color: #666;
                            ">
                                ${escapeHtml(error.message)}
                            </div>

                            <button
                                type="button"
                                id="retry-save-parcel"
                                style="
                                    margin-top: 12px;
                                    width: 100%;
                                    padding: 9px 12px;
                                    border: none;
                                    border-radius: 6px;
                                    background: #171717;
                                    color: white;
                                    cursor: pointer;
                                    font-size: 13px;
                                "
                            >
                                Try Again
                            </button>
                        </div>
                    `).openPopup();

                    setTimeout(() => {
                        const retryButton =
                            document.getElementById('retry-save-parcel');

                        if (retryButton) {
                            retryButton.addEventListener(
                                'click',
                                () => saveButton.click()
                            );
                        }
                    }, 0);
                }
            });
        }, 0);
    });

    map.on('click', function (e) {
        const { lat, lng } = e.latlng;

        L.marker([lat, lng])
            .addTo(map)
            .bindPopup(`
                <div>
                    <strong>Selected Location</strong>

                    <div style="
                        margin-top: 6px;
                        font-family: monospace;
                        font-size: 11px;
                    ">
                        ${lat.toFixed(6)}, ${lng.toFixed(6)}
                    </div>
                </div>
            `)
            .openPopup();
    });

    /*
     * Leaflet sometimes initializes before the map container has
     * its final dimensions, especially on responsive layouts.
     * Invalidate the size after the page has rendered.
     */
    setTimeout(() => {
        map.invalidateSize();
    }, 300);

    window.addEventListener('resize', () => {
        map.invalidateSize();
    });
}


/*
 * Get Laravel's CSRF token.
 */
function getCsrfToken() {
    const meta = document.querySelector(
        'meta[name="csrf-token"]'
    );

    if (meta) {
        return meta.getAttribute('content');
    }

    const cookie = document.cookie
        .split('; ')
        .find(row => row.startsWith('XSRF-TOKEN='));

    if (cookie) {
        return decodeURIComponent(
            cookie.split('=')[1]
        );
    }

    return '';
}


/*
 * Convert risk status values into readable labels.
 */
function formatStatus(status) {
    if (!status) {
        return 'Unknown';
    }

    return status
        .replace(/_/g, ' ')
        .replace(/\b\w/g, character => character.toUpperCase());
}


/*
 * Prevent HTML returned from an error message from being
 * interpreted as markup.
 */
function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}