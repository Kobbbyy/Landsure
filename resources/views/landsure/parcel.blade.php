<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        LandSure — Parcel Analysis
    </title>

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

            --green-bg: #f0fdf4;
            --green-border: #bbf7d0;
            --green-text: #166534;

            --yellow-bg: #fffbeb;
            --yellow-border: #fde68a;
            --yellow-text: #92400e;

            --red-bg: #fef2f2;
            --red-border: #fecaca;
            --red-text: #991b1b;

            --gray-bg: #f5f5f5;
            --gray-border: #e5e5e5;
            --gray-text: #525252;

            --radius-md: 4px;
            --radius-lg: 8px;
            --radius-xl: 12px;
            --radius-2xl: 16px;

            --shadow-subtle:
                rgba(0, 0, 0, 0.08) 0px 0px 0px 1px,
                rgba(0, 0, 0, 0.04) 0px 2px 1px 0px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--canvas);
            color: var(--ink);
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button {
            font: inherit;
        }

        .page {
            min-height: 100vh;
        }

        /* MAIN */

        .main {
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
            padding: 42px 32px 80px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--subtext);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 28px;
        }

        .back-link:hover {
            color: var(--ink);
        }

        .back-arrow {
            font-size: 16px;
        }

        /* HEADER */

        .parcel-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 30px;
        }

        .eyebrow {
            margin-bottom: 9px;
            color: var(--subtext);
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            font-size: 10px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: 32px;
            line-height: 1.2;
            letter-spacing: -0.035em;
            font-weight: 600;
        }

        .parcel-id {
            margin-top: 9px;
            color: var(--subtext);
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            font-size: 11px;
        }

        /* STATUS */

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
            border: 1px solid;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-unknown {
            color: var(--gray-text);
            background: var(--gray-bg);
            border-color: var(--gray-border);
        }

        .status-good {
            color: var(--green-text);
            background: var(--green-bg);
            border-color: var(--green-border);
        }

        .status-moderate {
            color: var(--yellow-text);
            background: var(--yellow-bg);
            border-color: var(--yellow-border);
        }

        .status-high {
            color: var(--red-text);
            background: var(--red-bg);
            border-color: var(--red-border);
        }

        /* GRID */

        .top-grid {
            display: grid;
            grid-template-columns: 0.85fr 1.15fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: var(--paper-white);
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-subtle);
        }

        .card-header {
            padding: 20px 22px 16px;
            border-bottom: 1px solid var(--line);
        }

        .card-title {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: -0.01em;
        }

        .card-description {
            margin: 5px 0 0;
            color: var(--subtext);
            font-size: 13px;
            line-height: 1.5;
        }

        .card-body {
            padding: 20px 22px;
        }

        /* PARCEL INFORMATION */

        .info-list {
            display: flex;
            flex-direction: column;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 13px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:first-child {
            padding-top: 0;
        }

        .info-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            color: var(--subtext);
            font-size: 13px;
        }

        .info-value {
            color: var(--ink);
            font-size: 13px;
            font-weight: 500;
            text-align: right;
        }

        /* MAP */

        .map-card {
            overflow: hidden;
        }

        .map-container {
            height: 390px;
            width: 100%;
            background: #f3f3f3;
        }

        #parcel-map {
            width: 100%;
            height: 100%;
        }

        /* RISK SECTION */

        .risk-card {
            margin-top: 20px;
            overflow: hidden;
        }

        .risk-header {
            padding: 24px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .risk-header-left h2 {
            margin: 0;
            font-size: 20px;
            line-height: 1.3;
            letter-spacing: -0.025em;
        }

        .risk-header-left p {
            margin: 6px 0 0;
            color: var(--subtext);
            font-size: 13px;
            line-height: 1.5;
        }

        .risk-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
        }

        .risk-item {
            padding: 22px 24px;
            border-bottom: 1px solid var(--line);
        }

        .risk-item:nth-child(odd) {
            border-right: 1px solid var(--line);
        }

        .risk-item:nth-last-child(-n+2) {
            border-bottom: 0;
        }

        .risk-item-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
        }

        .risk-name {
            margin: 0;
            font-size: 14px;
            font-weight: 500;
        }

        .risk-status {
            flex-shrink: 0;
        }

        .risk-message {
            margin: 9px 0 0;
            color: var(--subtext);
            font-size: 13px;
            line-height: 1.55;
        }

        .risk-score {
            margin-top: 11px;
            color: #888;
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            font-size: 10px;
        }

        /* BUFFER AREA DETAILS */

        .buffer-area-details {
            margin-top: 14px;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: #fafafa;
        }

        .buffer-area-title {
            margin: 0 0 8px;
            color: var(--subtext);
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            font-size: 10px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .buffer-area-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 4px 0;
            font-size: 12px;
        }

        .buffer-area-label {
            color: var(--subtext);
        }

        .buffer-area-value {
            color: var(--ink);
            font-weight: 500;
            text-align: right;
        }

        /* OVERALL */

        .overall-card {
            background: var(--ink);
            color: white;
            border-radius: var(--radius-xl);
            padding: 24px;
            margin-top: 20px;
        }

        .overall-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .overall-label {
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            font-size: 10px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #a3a3a3;
        }

        .overall-title {
            margin: 6px 0 0;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .overall-description {
            max-width: 650px;
            margin: 8px 0 0;
            color: #a3a3a3;
            font-size: 13px;
            line-height: 1.55;
        }

        .overall-status {
            padding: 8px 12px;
            border-radius: 999px;
            background: #262626;
            border: 1px solid #3a3a3a;
            color: #d4d4d4;
            font-size: 12px;
            font-weight: 500;
        }

        /* NEXT STEPS */

        .next-steps-card {
            margin-top: 20px;
            overflow: hidden;
        }

        .next-steps-header {
            padding: 22px 24px 18px;
            border-bottom: 1px solid var(--line);
        }

        .next-steps-header h2 {
            margin: 0;
            font-size: 17px;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .next-steps-header p {
            margin: 6px 0 0;
            color: var(--subtext);
            font-size: 13px;
            line-height: 1.55;
        }

        .next-steps-body {
            padding: 4px 0;
        }

        .next-step {
            display: flex;
            gap: 14px;
            padding: 18px 24px;
            border-bottom: 1px solid #f0f0f0;
        }

        .next-step:last-child {
            border-bottom: 0;
        }

        .next-step-index {
            flex-shrink: 0;
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: #f5f5f5;
            border: 1px solid var(--line);
            display: grid;
            place-items: center;
            color: var(--subtext);
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
            font-size: 11px;
            font-weight: 600;
        }

        .next-step-body {
            flex: 1;
            min-width: 0;
        }

        .next-step-title {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
        }

        .next-step-text {
            margin: 5px 0 0;
            color: var(--subtext);
            font-size: 13px;
            line-height: 1.55;
        }

        /* NOTICE */

        .notice {
            display: flex;
            gap: 12px;
            padding: 15px 16px;
            margin-top: 20px;
            background: #fafafa;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
        }

        .notice-icon {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #ededed;
            color: #555;
            font-size: 11px;
            font-weight: 700;
        }

        .notice-title {
            margin: 0 0 3px;
            font-size: 12px;
            font-weight: 600;
        }

        .notice-text {
            margin: 0;
            color: var(--subtext);
            font-size: 12px;
            line-height: 1.5;
        }

        .notice-text + .notice-title {
            margin-top: 12px;
        }

        .notice-text + .notice-text {
            margin-top: 8px;
        }

        .notice-link {
            color: var(--ink);
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .notice-link:hover {
            color: var(--ink);
            opacity: 0.7;
        }

        /* FOOTER */

        .footer {
            margin-top: 55px;
            padding-top: 24px;
            border-top: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            color: #888;
            font-size: 11px;
        }

        .footer-mono {
            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;
        }

        /* LEAFLET */

        .leaflet-container {
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                sans-serif;
        }

        .leaflet-control-zoom {
            border: 0 !important;
            box-shadow: var(--shadow-subtle) !important;
        }

        .leaflet-control-zoom a {
            color: #333 !important;
            border: 0 !important;
        }

        /* MOBILE */

        @media (max-width: 850px) {

            .main {
                padding: 30px 18px 55px;
            }

            .parcel-header {
                flex-direction: column;
                gap: 16px;
            }

            h1 {
                font-size: 27px;
            }

            .top-grid {
                grid-template-columns: 1fr;
            }

            .map-container {
                height: 330px;
            }

            .risk-grid {
                grid-template-columns: 1fr;
            }

            .risk-item {
                border-right: 0 !important;
                border-bottom: 1px solid var(--line) !important;
            }

            .risk-item:last-child {
                border-bottom: 0 !important;
            }

            .risk-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .overall-top {
                align-items: flex-start;
                flex-direction: column;
            }

            .next-step {
                gap: 12px;
                padding: 16px 20px;
            }

            .footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <x-navbar />


    <main class="main">

        <a
            href="/parcels"
            class="back-link"
        >
            <span class="back-arrow">←</span>
            Back to My Parcels
        </a>


        <div class="parcel-header">

            <div>

                <div class="eyebrow">
                    LAND PARCEL
                </div>

                <h1>
                    {{ $parcel->name ?: 'Saved Land Parcel' }}
                </h1>

                <div class="parcel-id">
                    PARCEL #{{ str_pad($parcel->id, 6, '0', STR_PAD_LEFT) }}
                </div>

            </div>

            @php
                $overallStatus = strtolower(
                    $riskAnalysis->overall_status ?? 'unknown'
                );

                $overallLabel = match ($overallStatus) {
                    'good', 'low' => 'Low Risk',
                    'moderate', 'medium' => 'Moderate Risk',
                    'high' => 'High Risk',
                    default => 'Risk Unknown',
                };

                $overallClass = match ($overallStatus) {
                    'good', 'low' => 'status-good',
                    'moderate', 'medium' => 'status-moderate',
                    'high' => 'status-high',
                    default => 'status-unknown',
                };
            @endphp

            <div class="status-badge {{ $overallClass }}">

                <span class="status-dot"></span>

                {{ $overallLabel }}

            </div>

        </div>


        <div class="top-grid">

            <section class="card">

                <div class="card-header">

                    <h2 class="card-title">
                        Parcel Information
                    </h2>

                    <p class="card-description">
                        Basic information recorded by LandSure.
                    </p>

                </div>

                <div class="card-body">

                    <div class="info-list">

                        <div class="info-row">

                            <span class="info-label">
                                Area
                            </span>

                            <span class="info-value">
                                {{ number_format($parcel->area_square_meters, 2) }}
                                m²
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Acres
                            </span>

                            <span class="info-value">
                                {{ number_format($parcel->area_acres, 3) }}
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Latitude
                            </span>

                            <span class="info-value">
                                {{ $parcel->latitude }}
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Longitude
                            </span>

                            <span class="info-value">
                                {{ $parcel->longitude }}
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Saved
                            </span>

                            <span class="info-value">
                                {{ $parcel->created_at?->format('M d, Y') }}
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Analysis
                            </span>

                            <span class="info-value">
                                {{ $riskAnalysis->analyzed_at?->format('M d, Y H:i') ?? 'Pending' }}
                            </span>

                        </div>

                    </div>

                </div>

            </section>


            <section class="card map-card">

                <div class="card-header">

                    <h2 class="card-title">
                        Parcel Location
                    </h2>

                    <p class="card-description">
                        The boundary recorded when this parcel was checked.
                    </p>

                </div>

                <div class="map-container">

                    <div id="parcel-map"></div>

                </div>

            </section>

        </div>


        <section class="overall-card">

            <div class="overall-top">

                <div>

                    <div class="overall-label">
                        LANDSure verification engine
                    </div>

                    <div class="overall-title">
                        Overall Assessment
                    </div>

                    <p class="overall-description">
                        @if ($overallStatus === 'unknown')
                            LandSure does not yet have enough authoritative
                            evidence to determine the overall risk of this
                            parcel.
                        @elseif ($overallStatus === 'high')
                            One or more available checks indicate a high-risk
                            condition requiring further investigation.
                        @elseif ($overallStatus === 'moderate')
                            Some available checks indicate conditions that
                            require further investigation.
                        @else
                            Available evidence currently indicates a lower-risk
                            result.
                        @endif
                    </p>

                </div>

                <div class="overall-status">
                    {{ strtoupper($overallStatus) }}
                </div>

            </div>

        </section>


        <section class="card risk-card">

            <div class="risk-header">

                <div class="risk-header-left">

                    <h2>
                        Land Risk Analysis
                    </h2>

                    <p>
                        Results from the LandSure verification engine.
                    </p>

                </div>

                <div class="status-badge {{ $overallClass }}">

                    <span class="status-dot"></span>

                    {{ $overallLabel }}

                </div>

            </div>


            <div class="risk-grid">

                @php
                    $floodStatus = strtolower(
                        $riskAnalysis->flood_status ?? 'unknown'
                    );

                    $floodClass = match ($floodStatus) {
                        'good', 'low' => 'status-good',
                        'moderate', 'medium' => 'status-moderate',
                        'high', 'very_high' => 'status-high',
                        default => 'status-unknown',
                    };

                    $floodLabel = match ($floodStatus) {
                        'good', 'low' => 'Low',
                        'moderate', 'medium' => 'Moderate',
                        'high' => 'High',
                        'very_high' => 'Very High',
                        default => 'Unknown',
                    };

                    $floodFindingMessage = data_get(
                        $riskAnalysis->findings,
                        'flood.message'
                    );
                @endphp

                <div class="risk-item">

                    <div class="risk-item-top">

                        <h3 class="risk-name">
                            Flood Risk
                        </h3>

                        <div class="risk-status status-badge {{ $floodClass }}">

                            <span class="status-dot"></span>

                            {{ $floodLabel }}

                        </div>

                    </div>

                    <p class="risk-message">

                        @if ($floodStatus === 'unknown')
                            {{ $floodFindingMessage
                                ?? 'Flood-risk screening could not be completed for this parcel.' }}
                        @elseif ($floodStatus === 'very_high')
                            The parcel intersects modeled flood-hazard cells with a maximum modeled depth of at least 2 meters.
                        @elseif ($floodStatus === 'high')
                            The parcel intersects modeled flood-hazard cells with elevated modeled flood depth.
                        @elseif ($floodStatus === 'moderate')
                            The parcel intersects modeled flood-hazard cells with moderate modeled flood depth.
                        @else
                            Flood-risk assessment is available for this parcel.
                        @endif

                    </p>

                    @if ($riskAnalysis->flood_score !== null)

                        <div class="risk-score">
                            SCORE:
                            {{ number_format($riskAnalysis->flood_score, 0) }}/100
                        </div>

                    @endif

                </div>


                @php
                    $bufferStatus = strtolower(
                        $riskAnalysis->buffer_status ?? 'unknown'
                    );

                    $bufferClass = match ($bufferStatus) {
                        'good', 'low' => 'status-good',
                        'moderate', 'medium' => 'status-moderate',
                        'high' => 'status-high',
                        default => 'status-unknown',
                    };

                    $bufferLabel = match ($bufferStatus) {
                        'good', 'low' => 'Low',
                        'moderate', 'medium' => 'Moderate',
                        'high' => 'High',
                        default => 'Unknown',
                    };

                    $bufferFindingMessage = data_get(
                        $riskAnalysis->findings,
                        'buffer.message'
                    );

                    $bufferParcelAreaKm2 = data_get(
                        $riskAnalysis->findings,
                        'buffer.parcel_area_km2'
                    );

                    $bufferParcelAreaAcres = data_get(
                        $riskAnalysis->findings,
                        'buffer.parcel_area_acres'
                    );

                    $bufferParcelAreaHectares = data_get(
                        $riskAnalysis->findings,
                        'buffer.parcel_area_hectares'
                    );

                    $bufferMaxScreeningAreaKm2 = data_get(
                        $riskAnalysis->findings,
                        'buffer.maximum_screening_area_km2'
                    );

                    $showBufferAreaDetails =
                        $bufferParcelAreaKm2 !== null;
                @endphp

                <div class="risk-item">

                    <div class="risk-item-top">

                        <h3 class="risk-name">
                            Buffer Zones
                        </h3>

                        <div class="risk-status status-badge {{ $bufferClass }}">

                            <span class="status-dot"></span>

                            {{ $bufferLabel }}

                        </div>

                    </div>

                    <p class="risk-message">
                        {{ $bufferFindingMessage
                            ?? 'Waterway buffer screening could not be completed for this parcel.' }}
                    </p>

                    @if ($showBufferAreaDetails)

                        <div class="buffer-area-details">

                            <p class="buffer-area-title">
                                Parcel area measured
                            </p>

                            @if ($bufferParcelAreaKm2 !== null)

                                <div class="buffer-area-row">

                                    <span class="buffer-area-label">
                                        Square kilometers
                                    </span>

                                    <span class="buffer-area-value">
                                        {{ number_format($bufferParcelAreaKm2, 2) }}
                                        km²
                                    </span>

                                </div>

                            @endif

                            @if ($bufferParcelAreaHectares !== null)

                                <div class="buffer-area-row">

                                    <span class="buffer-area-label">
                                        Hectares
                                    </span>

                                    <span class="buffer-area-value">
                                        {{ number_format($bufferParcelAreaHectares, 2) }}
                                        ha
                                    </span>

                                </div>

                            @endif

                            @if ($bufferParcelAreaAcres !== null)

                                <div class="buffer-area-row">

                                    <span class="buffer-area-label">
                                        Acres
                                    </span>

                                    <span class="buffer-area-value">
                                        {{ number_format($bufferParcelAreaAcres, 2) }}
                                        acres
                                    </span>

                                </div>

                            @endif

                            @if ($bufferMaxScreeningAreaKm2 !== null)

                                <div class="buffer-area-row">

                                    <span class="buffer-area-label">
                                        LandSure screening limit
                                    </span>

                                    <span class="buffer-area-value">
                                        {{ number_format($bufferMaxScreeningAreaKm2, 0) }}
                                        km²
                                    </span>

                                </div>

                            @endif

                        </div>

                    @endif

                    @if ($riskAnalysis->buffer_score !== null)

                        <div class="risk-score">
                            SCORE:
                            {{ number_format($riskAnalysis->buffer_score, 0) }}/100
                        </div>

                    @endif

                </div>


                @php
                    $planningStatus = strtolower(
                        $riskAnalysis->planning_status ?? 'unknown'
                    );

                    $planningClass = match ($planningStatus) {
                        'good', 'low' => 'status-good',
                        'moderate', 'medium' => 'status-moderate',
                        'high' => 'status-high',
                        default => 'status-unknown',
                    };

                    $planningLabel = match ($planningStatus) {
                        'good', 'low' => 'Low',
                        'moderate', 'medium' => 'Moderate',
                        'high' => 'High',
                        default => 'Unknown',
                    };
                @endphp

                <div class="risk-item">

                    <div class="risk-item-top">

                        <h3 class="risk-name">
                            Planning & Zoning
                        </h3>

                        <div class="risk-status status-badge {{ $planningClass }}">

                            <span class="status-dot"></span>

                            {{ $planningLabel }}

                        </div>

                    </div>

                    <p class="risk-message">

                        @if ($planningStatus === 'unknown')
                            LandSure does not currently have access to an authoritative national planning or zoning dataset for Ghana. This check requires confirmation with the relevant District, Municipal, or Metropolitan Assembly.
                        @else
                            Planning information has been assessed for this parcel.
                        @endif

                    </p>

                    @if ($riskAnalysis->planning_score !== null)

                        <div class="risk-score">
                            SCORE:
                            {{ number_format($riskAnalysis->planning_score, 0) }}/100
                        </div>

                    @endif

                </div>


                @php
                    $landStatus = strtolower(
                        $riskAnalysis->land_status ?? 'unknown'
                    );

                    $landClass = match ($landStatus) {
                        'good', 'low' => 'status-good',
                        'moderate', 'medium' => 'status-moderate',
                        'high' => 'status-high',
                        default => 'status-unknown',
                    };

                    $landLabel = match ($landStatus) {
                        'good', 'low' => 'Low',
                        'moderate', 'medium' => 'Moderate',
                        'high' => 'High',
                        default => 'Unknown',
                    };
                @endphp

                <div class="risk-item">

                    <div class="risk-item-top">

                        <h3 class="risk-name">
                            Land Status
                        </h3>

                        <div class="risk-status status-badge {{ $landClass }}">

                            <span class="status-dot"></span>

                            {{ $landLabel }}

                        </div>

                    </div>

                    <p class="risk-message">

                        @if ($landStatus === 'unknown')
                            LandSure does not currently have access to authoritative Lands Commission records. This check requires an official search or professional verification.
                        @else
                            Land-status information has been assessed.
                        @endif

                    </p>

                    @if ($riskAnalysis->land_score !== null)

                        <div class="risk-score">
                            SCORE:
                            {{ number_format($riskAnalysis->land_score, 0) }}/100
                        </div>

                    @endif

                </div>


                @php
                    $boundaryStatus = strtolower(
                        $riskAnalysis->boundary_status ?? 'unknown'
                    );

                    $boundaryClass = match ($boundaryStatus) {
                        'good', 'low' => 'status-good',
                        'moderate', 'medium' => 'status-moderate',
                        'high' => 'status-high',
                        default => 'status-unknown',
                    };

                    $boundaryLabel = match ($boundaryStatus) {
                        'good', 'low' => 'Good',
                        'moderate', 'medium' => 'Moderate',
                        'high' => 'Poor',
                        default => 'Unknown',
                    };

                    $boundaryFinding =
                        data_get(
                            $riskAnalysis->findings,
                            'boundary.message'
                        )
                        ?? 'Boundary information is unavailable.';
                @endphp

                <div class="risk-item">

                    <div class="risk-item-top">

                        <h3 class="risk-name">
                            Parcel Boundary
                        </h3>

                        <div class="risk-status status-badge {{ $boundaryClass }}">

                            <span class="status-dot"></span>

                            {{ $boundaryLabel }}

                        </div>

                    </div>

                    <p class="risk-message">
                        {{ $boundaryFinding }}
                    </p>

                    @if ($riskAnalysis->boundary_score !== null)

                        <div class="risk-score">
                            DATA QUALITY:
                            {{ number_format($riskAnalysis->boundary_score, 0) }}/100
                        </div>

                    @endif

                </div>


                @php
                    $locationRecorded =
                        $parcel->latitude !== null &&
                        $parcel->longitude !== null;
                @endphp

                <div class="risk-item">

                    <div class="risk-item-top">

                        <h3 class="risk-name">
                            Location
                        </h3>

                        <div class="risk-status status-badge {{ $locationRecorded ? 'status-good' : 'status-unknown' }}">

                            <span class="status-dot"></span>

                            {{ $locationRecorded ? 'Recorded' : 'Unknown' }}

                        </div>

                    </div>

                    <p class="risk-message">

                        @if ($locationRecorded)
                            Latitude and longitude have been recorded for this parcel.
                        @else
                            A valid parcel location has not been recorded.
                        @endif

                    </p>

                </div>

            </div>

        </section>


        @php
            $needsPlanningStep = ($planningStatus ?? 'unknown') === 'unknown';
            $needsLandStep = ($landStatus ?? 'unknown') === 'unknown';
            $needsBufferStep = ($bufferStatus ?? 'unknown') === 'unknown';
            $needsFloodStep = ($floodStatus ?? 'unknown') === 'unknown';

            $nextSteps = [];

            if ($needsPlanningStep) {
                $nextSteps[] = [
                    'title' => 'Planning & Zoning',
                    'text' =>
                        'Contact the Physical Planning Department of the District, Municipal, or Metropolitan Assembly that covers this parcel. Ask about the current planning scheme, permitted land uses, and any zoning or setback requirements that apply to this area.',
                ];
            }

            if ($needsLandStep) {
                $nextSteps[] = [
                    'title' => 'Land Status',
                    'text' =>
                        'Request a Lands Commission search at the relevant regional office, or engage a licensed surveyor or lawyer to confirm the land\'s status and whether any government interest, acquisition, or reservation affects it. LandSure cannot perform this check from public data alone.',
                ];
            }

            if ($needsBufferStep) {
                $nextSteps[] = [
                    'title' => 'Buffer Zones',
                    'text' =>
                        'LandSure could not complete a waterway buffer screening for this parcel. This usually means the drawn area was too large to screen meaningfully, or that the local river dataset did not return a usable result. Redraw a smaller parcel for the actual property, or have the site inspected by a surveyor.',
                ];
            }

            if ($needsFloodStep) {
                $nextSteps[] = [
                    'title' => 'Flood Risk',
                    'text' =>
                        'No modeled flood-hazard data was available for this location. Treat the flood risk as unconfirmed. If relevant, ask the relevant Assembly or drainage authority about local drainage conditions and any history of flooding in the area.',
                ];
            }

            $showNextSteps = count($nextSteps) > 0;
        @endphp

        @if ($showNextSteps)

            <section class="card next-steps-card">

                <div class="next-steps-header">

                    <h2>
                        What you should do next
                    </h2>

                    <p>
                        LandSure cannot yet answer every question about this
                        parcel from authoritative Ghanaian sources. For the
                        checks that came back <strong>Unknown</strong>, you
                        should verify with the responsible institution before
                        making any commitment.
                    </p>

                </div>

                <div class="next-steps-body">

                    @foreach ($nextSteps as $index => $step)

                        <div class="next-step">

                            <div class="next-step-index">
                                {{ $index + 1 }}
                            </div>

                            <div class="next-step-body">

                                <p class="next-step-title">
                                    {{ $step['title'] }}
                                </p>

                                <p class="next-step-text">
                                    {{ $step['text'] }}
                                </p>

                            </div>

                        </div>

                    @endforeach

                </div>

            </section>

        @endif


        <div class="notice">

            <div class="notice-icon">
                i
            </div>

            <div>

                <p class="notice-title">
                    Important
                </p>

                <p class="notice-text">
                    A LandSure analysis is not a substitute for an official
                    land search, survey, title verification, planning approval,
                    or professional legal advice. An "Unknown" result means
                    LandSure does not currently have sufficient authoritative
                    data to make that determination.
                </p>

                <p class="notice-title">
                    <a
                        href="/methodology"
                        class="notice-link"
                    >
                        How the overall status is decided
                    </a>
                </p>

                <p class="notice-text">
                    LandSure runs each check independently. If any connected
                    check reports a high-risk condition, the overall status is
                    High Risk. Otherwise, if any check reports Moderate, the
                    overall is Moderate. Otherwise, if any check is still
                    Unknown, the overall is Unknown. Only when every check
                    LandSure can currently run returns Low does the overall
                    show Low Risk. LandSure does not compute an overall numeric
                    score, because combining different types of checks into a
                    single number would imply a precision we do not have.
                </p>

            </div>

        </div>


        <footer class="footer">

            <span>
                LandSure · Ghana land intelligence
            </span>

            <span class="footer-mono">
                ANALYSIS #{{ str_pad($parcel->id, 6, '0', STR_PAD_LEFT) }}
            </span>

        </footer>

    </main>

</div>


<script>

    document.addEventListener('DOMContentLoaded', function () {

        const mapElement = document.getElementById('parcel-map');

        if (!mapElement) {
            return;
        }

        const latitude = {{ $parcel->latitude ?? 7.9465 }};
        const longitude = {{ $parcel->longitude ?? -1.0232 }};

        const map = L.map(mapElement, {
            zoomControl: true,
            attributionControl: true
        }).setView(
            [latitude, longitude],
            16
        );

        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }
        ).addTo(map);


        @if (!empty($parcel->boundary))

            const boundary = @json($parcel->boundary);

            try {

                const boundaryLayer = L.geoJSON(
                    boundary,
                    {
                        style: {
                            color: '#171717',
                            weight: 3,
                            fillColor: '#171717',
                            fillOpacity: 0.10
                        }
                    }
                ).addTo(map);

                map.fitBounds(
                    boundaryLayer.getBounds(),
                    {
                        padding: [30, 30]
                    }
                );

            } catch (error) {

                console.error(
                    'Unable to display parcel boundary:',
                    error
                );

            }

        @endif


        L.marker([
            latitude,
            longitude
        ])
        .addTo(map)
        .bindPopup(
            '<strong>LandSure Parcel</strong><br>' +
            latitude.toFixed(7) +
            ', ' +
            longitude.toFixed(7)
        );


        setTimeout(function () {

            map.invalidateSize();

        }, 250);

    });

</script>

</body>
</html>