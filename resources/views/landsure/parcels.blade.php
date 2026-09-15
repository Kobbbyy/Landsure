<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LandSure — My Parcels</title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        :root {
            --white: #ffffff;
            --canvas: #fafafa;
            --line: #eaeaea;
            --subtext: #666666;
            --icon: #7d7d7d;
            --ink: #171717;
            --black: #000000;

            --warning-bg: #fffbeb;
            --warning-text: #92400e;

            --success-bg: #f0fdf4;
            --success-text: #166534;

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

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            padding: 0;

            background: var(--canvas);
            color: var(--ink);

            font-family: GeistSans, Inter, Arial, sans-serif;

            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* =========================================
           PAGE
        ========================================= */

        .page {
            width: min(1180px, calc(100% - 48px));

            margin: 0 auto;

            padding: 48px 0 80px;
        }

        /* =========================================
           HEADER
        ========================================= */

        .page-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;

            gap: 24px;

            margin-bottom: 32px;
        }

        .eyebrow {
            display: flex;
            align-items: center;
            gap: 8px;

            margin-bottom: 10px;

            color: var(--subtext);

            font-family: GeistMono, Consolas, monospace;
            font-size: 10px;

            line-height: 1.5;

            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .eyebrow-dot {
            width: 6px;
            height: 6px;

            border-radius: 50%;

            background: #a3a3a3;
        }

        .page-title {
            margin: 0;

            font-size: 32px;
            line-height: 1.25;

            font-weight: 600;

            letter-spacing: -0.035em;
        }

        .page-description {
            max-width: 650px;

            margin: 9px 0 0;

            color: var(--subtext);

            font-size: 15px;
            line-height: 1.5;
        }

        .add-button {
            height: 40px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            gap: 8px;

            padding: 0 15px;

            background: var(--black);
            color: var(--white);

            border-radius: 7px;

            font-size: 13px;
            font-weight: 500;

            white-space: nowrap;

            transition: opacity 0.15s ease;
        }

        .add-button:hover {
            opacity: 0.85;
        }

        .plus {
            font-size: 16px;
            line-height: 1;
        }

        /* =========================================
           SUMMARY
        ========================================= */

        .summary-grid {
            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 12px;

            margin-bottom: 32px;
        }

        .summary-card {
            padding: 18px 20px;

            background: var(--white);

            border: 1px solid var(--line);
            border-radius: var(--radius-xl);

            box-shadow: var(--shadow-subtle-3);
        }

        .summary-label {
            margin: 0 0 10px;

            color: var(--subtext);

            font-size: 12px;
            line-height: 1.5;
        }

        .summary-value {
            margin: 0;

            color: var(--ink);

            font-size: 24px;
            line-height: 1.33;

            font-weight: 600;

            letter-spacing: -0.025em;
        }

        .summary-note {
            margin: 4px 0 0;

            color: var(--subtext);

            font-size: 11px;
        }

        /* =========================================
           SECTION HEADER
        ========================================= */

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-bottom: 14px;
        }

        .section-title {
            margin: 0;

            font-size: 16px;
            line-height: 1.5;

            font-weight: 500;

            letter-spacing: -0.01em;
        }

        .section-count {
            color: var(--subtext);

            font-family: GeistMono, Consolas, monospace;

            font-size: 10px;
        }

        /* =========================================
           PARCEL GRID
        ========================================= */

        .parcel-grid {
            display: grid;

            grid-template-columns:
                repeat(3, minmax(0, 1fr));

            gap: 14px;
        }

        /* =========================================
           PARCEL CARD
        ========================================= */

        .parcel-card {
            display: flex;
            flex-direction: column;

            min-width: 0;

            background: var(--white);

            border: 1px solid var(--line);
            border-radius: var(--radius-xl);

            overflow: hidden;

            box-shadow: var(--shadow-subtle-3);

            transition:
                transform 0.15s ease,
                box-shadow 0.15s ease,
                border-color 0.15s ease;
        }

        .parcel-card:hover {
            transform: translateY(-2px);

            border-color: #dcdcdc;

            box-shadow:
                rgba(0, 0, 0, 0.08) 0px 8px 24px -12px;
        }

        .parcel-top {
            padding: 18px 18px 14px;

            border-bottom: 1px solid var(--line);
        }

        .parcel-top-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;

            gap: 12px;
        }

        .parcel-name {
            margin: 0;

            font-size: 16px;
            line-height: 1.4;

            font-weight: 500;

            letter-spacing: -0.015em;

            word-break: break-word;
        }

        .parcel-id {
            margin: 5px 0 0;

            color: var(--subtext);

            font-family: GeistMono, Consolas, monospace;

            font-size: 10px;
        }

        .status {
            display: inline-flex;
            align-items: center;

            flex-shrink: 0;

            padding: 5px 8px;

            background: var(--warning-bg);
            color: var(--warning-text);

            border-radius: 999px;

            font-size: 10px;
            font-weight: 500;

            white-space: nowrap;
        }

        .status-dot {
            width: 5px;
            height: 5px;

            margin-right: 6px;

            border-radius: 50%;

            background: #f59e0b;
        }

        .parcel-body {
            padding: 16px 18px;
        }

        .data-row {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 16px;

            padding: 9px 0;

            border-bottom: 1px solid #f0f0f0;
        }

        .data-row:first-child {
            padding-top: 0;
        }

        .data-row:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .data-label {
            color: var(--subtext);

            font-size: 12px;
        }

        .data-value {
            color: var(--ink);

            font-size: 12px;
            font-weight: 500;

            text-align: right;

            word-break: break-word;
        }

        .mono {
            font-family: GeistMono, Consolas, monospace;
            font-size: 11px;
        }

        .parcel-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 10px;

            margin-top: auto;

            padding: 13px 18px;

            background: #fcfcfc;

            border-top: 1px solid var(--line);
        }

        .saved-date {
            color: var(--subtext);

            font-size: 10px;
        }

        .view-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;

            color: var(--ink);

            font-size: 12px;
            font-weight: 500;
        }

        .view-link:hover {
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        /* =========================================
           EMPTY STATE
        ========================================= */

        .empty-state {
            padding: 70px 24px;

            background: var(--white);

            border: 1px solid var(--line);
            border-radius: var(--radius-xl);

            box-shadow: var(--shadow-subtle-3);

            text-align: center;
        }

        .empty-icon {
            width: 46px;
            height: 46px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin: 0 auto 18px;

            background: #f5f5f5;

            border: 1px solid var(--line);
            border-radius: 12px;

            font-size: 18px;
        }

        .empty-title {
            margin: 0;

            font-size: 18px;
            line-height: 1.5;

            font-weight: 500;
        }

        .empty-description {
            max-width: 450px;

            margin: 7px auto 20px;

            color: var(--subtext);

            font-size: 13px;
            line-height: 1.5;
        }

        .empty-button {
            height: 38px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 0 14px;

            background: var(--black);
            color: var(--white);

            border-radius: 7px;

            font-size: 13px;
            font-weight: 500;
        }

        /* =========================================
           FOOTER
        ========================================= */

        .footer {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-top: 64px;
            padding-top: 24px;

            border-top: 1px solid var(--line);

            color: var(--subtext);

            font-size: 12px;
        }

        .footer-brand {
            color: var(--ink);
            font-weight: 500;
        }

        /* =========================================
           TABLET
        ========================================= */

        @media (max-width: 1000px) {

            .parcel-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }

            .summary-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* =========================================
           MOBILE
        ========================================= */

        @media (max-width: 680px) {

            .page {
                width: calc(100% - 24px);

                padding: 28px 0 50px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;

                gap: 18px;

                margin-bottom: 24px;
            }

            .page-title {
                font-size: 28px;
            }

            .page-description {
                font-size: 14px;
            }

            .add-button {
                width: 100%;
            }

            .summary-grid {
                grid-template-columns: 1fr;

                gap: 10px;

                margin-bottom: 28px;
            }

            .summary-card {
                padding: 16px;
            }

            .summary-value {
                font-size: 22px;
            }

            .section-header {
                margin-bottom: 12px;
            }

            .parcel-grid {
                grid-template-columns: 1fr;

                gap: 12px;
            }

            .parcel-top {
                padding: 16px;
            }

            .parcel-body {
                padding: 15px 16px;
            }

            .parcel-footer {
                padding: 12px 16px;
            }

            .empty-state {
                padding: 55px 20px;
            }

            .footer {
                flex-direction: column;
                align-items: flex-start;

                gap: 8px;

                margin-top: 45px;
            }
        }

        @media (max-width: 420px) {

            .page {
                width: calc(100% - 20px);
            }

            .page-title {
                font-size: 25px;
            }

            .parcel-top-row {
                flex-direction: column;
            }

            .status {
                align-self: flex-start;
            }

            .data-row {
                gap: 10px;
            }
        }
    </style>
</head>

<body>

    <x-navbar />


    <main class="page">


        <header class="page-header">

            <div>

                <div class="eyebrow">

                    <span class="eyebrow-dot"></span>

                    LandSure Workspace

                </div>


                <h1 class="page-title">
                    My Parcels
                </h1>


                <p class="page-description">
                    Manage the land parcels you have saved and review their
                    location, size and verification status.
                </p>

            </div>


            <a href="/map" class="add-button">

                <span class="plus">
                    +
                </span>

                Add New Parcel

            </a>

        </header>


        <section class="summary-grid">

            <div class="summary-card">

                <p class="summary-label">
                    Saved parcels
                </p>

                <p class="summary-value">
                    {{ $parcels->count() }}
                </p>

                <p class="summary-note">
                    Parcels recorded in LandSure
                </p>

            </div>


            <div class="summary-card">

                <p class="summary-label">
                    Verification
                </p>

                <p class="summary-value">
                    {{ $parcels->whereNull('verified_at')->count() }}
                </p>

                <p class="summary-note">
                    Awaiting verification
                </p>

            </div>


            <div class="summary-card">

                <p class="summary-label">
                    Analysis
                </p>

                <p class="summary-value">
                    Pending
                </p>

                <p class="summary-note">
                    Risk engine coming next
                </p>

            </div>

        </section>


        <section>

            <div class="section-header">

                <h2 class="section-title">
                    Saved land
                </h2>

                <span class="section-count">
                    {{ $parcels->count() }}
                    {{ $parcels->count() === 1 ? 'PARCEL' : 'PARCELS' }}
                </span>

            </div>


            @if ($parcels->count() > 0)

                <div class="parcel-grid">

                    @foreach ($parcels as $parcel)

                        <article class="parcel-card">


                            <div class="parcel-top">

                                <div class="parcel-top-row">

                                    <div>

                                        <h3 class="parcel-name">

                                            {{ $parcel->name ?: 'Land Parcel #' . $parcel->id }}

                                        </h3>


                                        <p class="parcel-id">

                                            PARCEL #{{ $parcel->id }}

                                        </p>

                                    </div>


                                    <span class="status">

                                        <span class="status-dot"></span>

                                        Pending

                                    </span>

                                </div>

                            </div>


                            <div class="parcel-body">


                                <div class="data-row">

                                    <span class="data-label">
                                        Area
                                    </span>

                                    <span class="data-value">

                                        {{ number_format($parcel->area_square_meters, 2) }}
                                        m²

                                    </span>

                                </div>


                                <div class="data-row">

                                    <span class="data-label">
                                        Size
                                    </span>

                                    <span class="data-value">

                                        {{ number_format($parcel->area_acres, 3) }}
                                        acres

                                    </span>

                                </div>


                                <div class="data-row">

                                    <span class="data-label">
                                        Latitude
                                    </span>

                                    <span class="data-value mono">

                                        {{ $parcel->latitude }}

                                    </span>

                                </div>


                                <div class="data-row">

                                    <span class="data-label">
                                        Longitude
                                    </span>

                                    <span class="data-value mono">

                                        {{ $parcel->longitude }}

                                    </span>

                                </div>

                            </div>


                            <div class="parcel-footer">

                                <span class="saved-date">

                                    Saved
                                    {{ $parcel->created_at->format('M d, Y') }}

                                </span>


                                <a
                                    href="/parcels/{{ $parcel->id }}"
                                    class="view-link"
                                >

                                    View parcel

                                    <span>
                                        →
                                    </span>

                                </a>

                            </div>

                        </article>

                    @endforeach

                </div>


            @else


                <div class="empty-state">

                    <div class="empty-icon">
                        ◇
                    </div>


                    <h2 class="empty-title">
                        No saved parcels yet
                    </h2>


                    <p class="empty-description">
                        Start by opening the map and drawing the boundary
                        of a piece of land you want to investigate.
                    </p>


                    <a href="/map" class="empty-button">
                        Open Land Map
                    </a>

                </div>


            @endif

        </section>


        <footer class="footer">

            <span class="footer-brand">
                LandSure
            </span>

            <span>
                Land intelligence for better decisions.
            </span>

        </footer>

    </main>

</body>
</html>