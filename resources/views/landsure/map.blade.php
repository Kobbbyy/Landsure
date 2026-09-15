<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LandSure — Analyze Land</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        :root {
            --paper-white: #ffffff;
            --canvas: #fafafa;
            --line: #eaeaea;
            --subtext: #666666;
            --icon: #7d7d7d;
            --ink: #171717;
            --onyx: #000000;

            --radius-md: 4px;
            --radius-lg: 8px;
            --radius-xl: 12px;
            --radius-2xl: 16px;

            --shadow-subtle:
                rgba(0, 0, 0, 0.08) 0px 0px 0px 1px,
                rgba(0, 0, 0, 0.04) 0px 2px 1px 0px;

            --shadow-subtle-3:
                rgba(0, 0, 0, 0.04) 0px 2px 2px 0px,
                rgba(0, 0, 0, 0.04) 0px 8px 8px -8px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background: var(--canvas);
            color: var(--ink);
            font-family: Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            overflow: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* =========================
           WORKSPACE
        ========================== */

        .workspace {
            width: 100%;
            height: calc(100vh - 64px);
            display: grid;
            grid-template-columns: 330px 1fr;
        }

        /* =========================
           SIDEBAR
        ========================== */

        .sidebar {
            background: var(--paper-white);
            border-right: 1px solid var(--line);
            overflow-y: auto;
            position: relative;
            z-index: 900;
        }

        .sidebar-inner {
            padding: 24px;
        }

        .sidebar-kicker {
            font-family: "Courier New", monospace;
            font-size: 10px;
            color: var(--icon);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
        }

        .sidebar-title {
            margin: 0;
            font-size: 24px;
            line-height: 1.15;
            letter-spacing: -0.045em;
            font-weight: 600;
        }

        .sidebar-description {
            margin: 12px 0 0;
            color: var(--subtext);
            font-size: 13px;
            line-height: 1.55;
        }

        /* =========================
           STATUS
        ========================== */

        .status-card {
            margin-top: 26px;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            background: var(--canvas);
        }

        .status-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-label {
            font-size: 11px;
            font-weight: 600;
        }

        .status-code {
            font-family: "Courier New", monospace;
            color: var(--icon);
            font-size: 9px;
        }

        .status-message {
            margin-top: 10px;
            color: var(--subtext);
            font-size: 11px;
            line-height: 1.5;
        }

        /* =========================
           INSTRUCTIONS
        ========================== */

        .sidebar-section {
            margin-top: 30px;
        }

        .section-label {
            margin-bottom: 12px;
            font-family: "Courier New", monospace;
            font-size: 10px;
            color: var(--icon);
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }

        .instruction {
            display: flex;
            gap: 11px;
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
        }

        .instruction:last-child {
            border-bottom: none;
        }

        .instruction-number {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--line);
            border-radius: 6px;
            font-family: "Courier New", monospace;
            font-size: 9px;
            color: var(--subtext);
        }

        .instruction-content strong {
            display: block;
            margin-bottom: 3px;
            font-size: 12px;
            font-weight: 600;
        }

        .instruction-content span {
            color: var(--subtext);
            font-size: 11px;
            line-height: 1.45;
        }

        /* =========================
           ANALYSIS
        ========================== */

        .analysis-card {
            margin-top: 26px;
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            overflow: hidden;
        }

        .analysis-header {
            padding: 13px 14px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .analysis-header strong {
            font-size: 12px;
        }

        .analysis-header span {
            font-family: "Courier New", monospace;
            font-size: 9px;
            color: var(--icon);
        }

        .analysis-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border-bottom: 1px solid var(--line);
        }

        .analysis-row:last-child {
            border-bottom: none;
        }

        .analysis-name {
            font-size: 11px;
            color: var(--subtext);
        }

        .analysis-value {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            font-weight: 500;
        }

        .analysis-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #bdbdbd;
        }

        /* =========================
           SIDEBAR FOOTER
        ========================== */

        .sidebar-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--line);
        }

        .sidebar-link {
            display: block;
            padding: 8px 0;
            color: var(--subtext);
            font-size: 11px;
        }

        .sidebar-link:hover {
            color: var(--ink);
        }

        /* =========================
           MAP
        ========================== */

        .map-wrapper {
            position: relative;
            min-width: 0;
            min-height: 0;
            height: 100%;
            background: #eeeeee;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        /* =========================
           MAP LABEL
        ========================== */

        .map-label {
            position: absolute;
            top: 16px;
            left: 16px;
            z-index: 800;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 9px 12px;
            box-shadow: var(--shadow-subtle-3);
        }

        .map-label-title {
            font-size: 11px;
            font-weight: 600;
        }

        .map-label-subtitle {
            margin-top: 3px;
            font-family: "Courier New", monospace;
            font-size: 9px;
            color: var(--subtext);
        }

        /* =========================
           MAP BOTTOM
        ========================== */

        .map-bottom {
            position: absolute;
            left: 16px;
            right: 16px;
            bottom: 16px;
            z-index: 800;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            pointer-events: none;
        }

        .map-info {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 9px 12px;
            box-shadow: var(--shadow-subtle-3);
            pointer-events: auto;
        }

        .map-info-text {
            font-family: "Courier New", monospace;
            font-size: 9px;
            color: var(--subtext);
        }

        .map-info-text strong {
            color: var(--ink);
            font-weight: 500;
        }

        .map-note {
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 9px 12px;
            box-shadow: var(--shadow-subtle-3);
            color: var(--subtext);
            font-size: 10px;
            pointer-events: auto;
        }

        /* =========================
           LEAFLET
        ========================== */

        .leaflet-control-geocoder {
            border: 1px solid var(--line) !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: var(--shadow-subtle-3) !important;
        }

        .leaflet-control-geocoder-form input {
            font-family: Arial, sans-serif !important;
            font-size: 12px !important;
        }

        .leaflet-control-zoom {
            border: none !important;
            box-shadow: var(--shadow-subtle-3) !important;
        }

        .leaflet-control-zoom a {
            color: var(--ink) !important;
            border-color: var(--line) !important;
        }

        .leaflet-control-layers {
            border: 1px solid var(--line) !important;
            border-radius: var(--radius-lg) !important;
            box-shadow: var(--shadow-subtle-3) !important;
        }

        .leaflet-popup-content-wrapper,
        .leaflet-popup-tip {
            box-shadow: var(--shadow-subtle) !important;
        }

        .leaflet-popup-content {
            font-family: Arial, sans-serif;
        }

        /* =====================================================
           TABLET
        ====================================================== */

        @media (max-width: 900px) {

            .workspace {
                grid-template-columns: 285px 1fr;
            }

            .sidebar-inner {
                padding: 18px;
            }

            .sidebar-title {
                font-size: 22px;
            }
        }

        /* =====================================================
           MOBILE
        ====================================================== */

        @media (max-width: 700px) {

            html,
            body {
                width: 100%;
                min-height: 100%;
                height: auto;
                overflow-x: hidden;
                overflow-y: auto;
            }

            .workspace {
                width: 100%;
                height: auto;
                min-height: calc(100vh - 64px);
                display: flex;
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                max-height: none;
                overflow: visible;
                border-right: none;
                border-bottom: 1px solid var(--line);
                order: 1;
            }

            .sidebar-inner {
                padding: 18px 16px 16px;
            }

            .sidebar-kicker {
                margin-bottom: 7px;
            }

            .sidebar-title {
                font-size: 22px;
                line-height: 1.1;
            }

            .sidebar-description {
                max-width: 620px;
                margin-top: 9px;
                font-size: 12px;
                line-height: 1.5;
            }

            .status-card {
                margin-top: 14px;
                padding: 12px;
            }

            .status-message {
                margin-top: 7px;
                font-size: 10px;
            }

            .sidebar-section,
            .analysis-card,
            .sidebar-footer {
                display: none;
            }

            .map-wrapper {
                width: 100%;
                height: 65vh;
                min-height: 430px;
                max-height: 700px;
                order: 2;
            }

            #map {
                width: 100%;
                height: 100%;
            }

            .map-label {
                top: 12px;
                left: 12px;
                padding: 8px 10px;
            }

            .map-label-title {
                font-size: 10px;
            }

            .map-label-subtitle {
                font-size: 8px;
            }

            .map-bottom {
                left: 10px;
                right: 10px;
                bottom: 10px;
            }

            .map-info {
                padding: 8px 10px;
            }

            .map-info-text {
                font-size: 8px;
            }

            .map-note {
                display: none;
            }

            .leaflet-control-geocoder {
                max-width: calc(100vw - 30px);
            }

            .leaflet-control-geocoder-form input {
                max-width: 180px;
                font-size: 11px !important;
            }

            .leaflet-control-zoom {
                margin-top: 10px !important;
            }
        }

        /* =====================================================
           SMALL PHONES
        ====================================================== */

        @media (max-width: 420px) {

            .sidebar-inner {
                padding: 15px 14px 14px;
            }

            .sidebar-title {
                font-size: 20px;
            }

            .sidebar-description {
                font-size: 11px;
            }

            .status-card {
                margin-top: 12px;
            }

            .map-wrapper {
                height: 68vh;
                min-height: 400px;
            }

            .map-label {
                top: 10px;
                left: 10px;
            }

            .map-bottom {
                left: 8px;
                right: 8px;
                bottom: 8px;
            }
        }
    </style>
</head>

<body>

    <x-navbar />


    <div class="workspace">


        <aside class="sidebar">

            <div class="sidebar-inner">

                <div class="sidebar-kicker">
                    Land analysis
                </div>

                <h1 class="sidebar-title">
                    Analyze a property.
                </h1>

                <p class="sidebar-description">
                    Locate a property on the map, define its boundary
                    and prepare it for LandSure's land-risk analysis.
                </p>


                <div class="status-card">

                    <div class="status-top">

                        <span class="status-label">
                            Analysis workspace
                        </span>

                        <span class="status-code">
                            READY
                        </span>

                    </div>

                    <div class="status-message">
                        Use the search box to find a location or use
                        the drawing tools to define a parcel.
                    </div>

                </div>


                <div class="sidebar-section">

                    <div class="section-label">
                        Start here
                    </div>

                    <div class="instruction">

                        <div class="instruction-number">
                            01
                        </div>

                        <div class="instruction-content">

                            <strong>
                                Find the property
                            </strong>

                            <span>
                                Search for a place or navigate directly
                                on the map.
                            </span>

                        </div>

                    </div>


                    <div class="instruction">

                        <div class="instruction-number">
                            02
                        </div>

                        <div class="instruction-content">

                            <strong>
                                Define the boundary
                            </strong>

                            <span>
                                Use the polygon or rectangle tool to
                                mark the land.
                            </span>

                        </div>

                    </div>


                    <div class="instruction">

                        <div class="instruction-number">
                            03
                        </div>

                        <div class="instruction-content">

                            <strong>
                                Save the parcel
                            </strong>

                            <span>
                                LandSure will calculate the area and
                                save the parcel for analysis.
                            </span>

                        </div>

                    </div>

                </div>


                <div class="analysis-card">

                    <div class="analysis-header">

                        <strong>
                            Analysis checks
                        </strong>

                        <span>
                            COMING NEXT
                        </span>

                    </div>


                    <div class="analysis-row">

                        <span class="analysis-name">
                            Flood exposure
                        </span>

                        <span class="analysis-value">
                            <span class="analysis-dot"></span>
                            Pending
                        </span>

                    </div>


                    <div class="analysis-row">

                        <span class="analysis-name">
                            Buffer zones
                        </span>

                        <span class="analysis-value">
                            <span class="analysis-dot"></span>
                            Pending
                        </span>

                    </div>


                    <div class="analysis-row">

                        <span class="analysis-name">
                            Planning constraints
                        </span>

                        <span class="analysis-value">
                            <span class="analysis-dot"></span>
                            Pending
                        </span>

                    </div>


                    <div class="analysis-row">

                        <span class="analysis-name">
                            Parcel verification
                        </span>

                        <span class="analysis-value">
                            <span class="analysis-dot"></span>
                            Pending
                        </span>

                    </div>

                </div>


                <div class="sidebar-footer">

                    <a href="/" class="sidebar-link">
                        ← Back to LandSure
                    </a>

                    <a href="/parcels" class="sidebar-link">
                        View saved parcels →
                    </a>

                </div>

            </div>

        </aside>


        <main class="map-wrapper">

            <div id="map"></div>


            <div class="map-label">

                <div class="map-label-title">
                    LandSure Map
                </div>

                <div class="map-label-subtitle">
                    GHANA / PROPERTY ANALYSIS
                </div>

            </div>


            <div class="map-bottom">

                <div class="map-info">

                    <div class="map-info-text">
                        <strong>BASE MAP</strong>
                        &nbsp; OpenStreetMap
                    </div>

                </div>


                <div class="map-note">
                    Draw a parcel to begin analysis.
                </div>

            </div>

        </main>

    </div>

</body>
</html>