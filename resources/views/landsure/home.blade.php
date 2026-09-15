<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LandSure — Know the Land Before You Buy It</title>

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
            --radius-3xl: 35px;

            --shadow-subtle:
                rgba(0, 0, 0, 0.08) 0px 0px 0px 1px,
                rgba(0, 0, 0, 0.04) 0px 2px 1px 0px;

            --shadow-subtle-2:
                rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;

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
            font-family: Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .mono {
            font-family: "Courier New", monospace;
        }

        .container {
            width: min(1120px, calc(100% - 40px));
            margin: 0 auto;
        }

        /* =========================
           HERO
        ========================= */

        .hero {
            padding: 100px 0 80px;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 430px;
            gap: 80px;
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 12px;
            font-weight: 500;
            color: var(--subtext);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .eyebrow-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--onyx);
        }

        .hero h1 {
            margin: 0;
            max-width: 700px;
            font-size: clamp(48px, 6vw, 72px);
            line-height: 0.98;
            letter-spacing: -0.065em;
            font-weight: 700;
        }

        /* =========================
           HERO HEADLINE TICKER
        ========================= */

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .hero-headline {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.28em;
        }

        .ticker-slot {
            position: relative;
            display: inline-block;
            height: 1.05em;
            overflow: hidden;
            vertical-align: bottom;
        }

        .ticker-inner {
            display: flex;
            flex-direction: column;
            animation: ticker-up 8s cubic-bezier(0.65, 0, 0.35, 1) infinite;
            will-change: transform;
        }

        .ticker-word {
            height: 1.05em;
            display: flex;
            align-items: center;
            line-height: 1;
            white-space: nowrap;
        }

        @keyframes ticker-up {

            0% {
                transform: translateY(0);
            }

            22% {
                transform: translateY(0);
            }

            25% {
                transform: translateY(-1.05em);
            }

            47% {
                transform: translateY(-1.05em);
            }

            50% {
                transform: translateY(-2.10em);
            }

            72% {
                transform: translateY(-2.10em);
            }

            75% {
                transform: translateY(-3.15em);
            }

            97% {
                transform: translateY(-3.15em);
            }

            100% {
                transform: translateY(-4.20em);
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .ticker-inner {
                animation: none;
            }
        }

        .hero-description {
            max-width: 570px;
            margin: 28px 0 0;
            color: var(--subtext);
            font-size: 18px;
            line-height: 1.55;
            letter-spacing: -0.01em;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 34px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 18px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--line);
            background: var(--paper-white);
            font-size: 14px;
            font-weight: 500;
            transition: 0.2s ease;
        }

        .button:hover {
            box-shadow: var(--shadow-subtle-3);
            transform: translateY(-1px);
        }

        .button.dark {
            background: var(--ink);
            border-color: var(--ink);
            color: var(--paper-white);
        }

        .button.dark:hover {
            background: var(--onyx);
        }

        .button.large {
            min-height: 48px;
            padding: 0 21px;
        }

        /* =========================
           HERO DATA CARD
        ========================= */

        .hero-card {
            background: var(--paper-white);
            border: 1px solid var(--line);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-subtle);
            overflow: hidden;
        }

        .hero-card-top {
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            border-bottom: 1px solid var(--line);
        }

        .window-dots {
            display: flex;
            gap: 5px;
        }

        .window-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #cfcfcf;
        }

        .card-label {
            font-family: "Courier New", monospace;
            font-size: 10px;
            color: var(--icon);
            letter-spacing: 0.03em;
        }

        .map-preview {
            position: relative;
            height: 280px;
            background:
                linear-gradient(
                    135deg,
                    #f2f2f2 25%,
                    transparent 25%
                ) -10px 0 / 40px 40px,
                linear-gradient(
                    45deg,
                    #f2f2f2 25%,
                    transparent 25%
                ) -10px 0 / 40px 40px,
                #fafafa;
            overflow: hidden;
        }

        .map-road {
            position: absolute;
            background: rgba(255, 255, 255, 0.9);
            transform: rotate(-23deg);
        }

        .road-one {
            width: 520px;
            height: 14px;
            top: 85px;
            left: -50px;
        }

        .road-two {
            width: 500px;
            height: 10px;
            top: 190px;
            left: 30px;
            transform: rotate(28deg);
        }

        .road-three {
            width: 400px;
            height: 8px;
            top: 135px;
            left: 140px;
            transform: rotate(70deg);
        }

        .parcel-shape {
            position: absolute;
            width: 155px;
            height: 125px;
            top: 78px;
            left: 135px;
            border: 2px solid var(--onyx);
            background: rgba(0, 0, 0, 0.07);
            transform: rotate(-7deg);
        }

        .parcel-point {
            position: absolute;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--onyx);
            border: 2px solid var(--paper-white);
            box-shadow: 0 0 0 1px var(--onyx);
            top: 137px;
            left: 207px;
        }

        .map-tag {
            position: absolute;
            left: 18px;
            bottom: 18px;
            background: var(--paper-white);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 10px 12px;
            box-shadow: var(--shadow-subtle-3);
        }

        .map-tag-title {
            font-size: 12px;
            font-weight: 600;
        }

        .map-tag-text {
            margin-top: 3px;
            font-family: "Courier New", monospace;
            font-size: 9px;
            color: var(--subtext);
        }

        .risk-summary {
            padding: 18px;
        }

        .risk-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .risk-heading strong {
            font-size: 13px;
        }

        .risk-heading span {
            font-family: "Courier New", monospace;
            font-size: 10px;
            color: var(--subtext);
        }

        .risk-items {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .risk-item {
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 11px;
        }

        .risk-item-label {
            font-size: 10px;
            color: var(--subtext);
        }

        .risk-item-value {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .risk-indicator {
            display: inline-block;
            width: 6px;
            height: 6px;
            margin-right: 4px;
            border-radius: 50%;
            background: #222;
        }

        /* =========================
           TRUST BAR
        ========================= */

        .trust-bar {
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }

        .trust-inner {
            min-height: 74px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .trust-label {
            font-size: 12px;
            color: var(--subtext);
        }

        .trust-items {
            display: flex;
            align-items: center;
            gap: 30px;
            color: var(--icon);
            font-size: 12px;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .trust-icon {
            width: 20px;
            height: 20px;
            border: 1px solid #d8d8d8;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
        }

        /* =========================
           SECTION
        ========================= */

        .section {
            padding: 100px 0;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 30px;
            margin-bottom: 40px;
        }

        .section-kicker {
            margin-bottom: 10px;
            font-family: "Courier New", monospace;
            font-size: 10px;
            color: var(--icon);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .section-title {
            margin: 0;
            font-size: 36px;
            line-height: 1.15;
            letter-spacing: -0.045em;
            font-weight: 600;
        }

        .section-description {
            max-width: 440px;
            margin: 0;
            color: var(--subtext);
            font-size: 15px;
            line-height: 1.55;
        }

        /* =========================
           FEATURES
        ========================= */

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .feature-card {
            background: var(--paper-white);
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            padding: 24px;
            min-height: 245px;
            box-shadow: var(--shadow-subtle-3);
        }

        .feature-number {
            font-family: "Courier New", monospace;
            font-size: 10px;
            color: var(--icon);
        }

        .feature-icon {
            width: 38px;
            height: 38px;
            margin-top: 35px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 600;
        }

        .feature-card h3 {
            margin: 18px 0 8px;
            font-size: 17px;
            letter-spacing: -0.025em;
        }

        .feature-card p {
            margin: 0;
            color: var(--subtext);
            font-size: 13px;
            line-height: 1.55;
        }

        /* =========================
           HOW IT WORKS
        ========================= */

        .process {
            border-top: 1px solid var(--line);
        }

        .process-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }

        .process-step {
            padding: 30px 24px 30px 0;
            border-right: 1px solid var(--line);
        }

        .process-step:not(:first-child) {
            padding-left: 24px;
        }

        .process-step:last-child {
            border-right: none;
        }

        .step-number {
            font-family: "Courier New", monospace;
            font-size: 11px;
            color: var(--icon);
        }

        .process-step h3 {
            margin: 45px 0 10px;
            font-size: 16px;
            letter-spacing: -0.02em;
        }

        .process-step p {
            margin: 0;
            color: var(--subtext);
            font-size: 13px;
            line-height: 1.55;
        }

        /* =========================
           CTA
        ========================= */

        .cta-section {
            padding: 0 0 100px;
        }

        .cta {
            background: var(--ink);
            color: var(--paper-white);
            border-radius: var(--radius-2xl);
            padding: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 40px;
        }

        .cta h2 {
            margin: 0;
            max-width: 600px;
            font-size: 38px;
            line-height: 1.08;
            letter-spacing: -0.05em;
        }

        .cta p {
            max-width: 500px;
            margin: 15px 0 0;
            color: #b5b5b5;
            font-size: 14px;
            line-height: 1.55;
        }

        .cta .button {
            flex-shrink: 0;
            background: var(--paper-white);
            color: var(--ink);
            border-color: var(--paper-white);
        }

        /* =========================
           FOOTER
        ========================= */

        footer {
            border-top: 1px solid var(--line);
            padding: 28px 0;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .footer-brand {
            font-size: 13px;
            font-weight: 600;
        }

        .footer-copy {
            color: var(--subtext);
            font-size: 12px;
        }

        .footer-links {
            display: flex;
            gap: 20px;
            color: var(--subtext);
            font-size: 12px;
        }

        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 900px) {

            .hero-grid {
                grid-template-columns: 1fr;
                gap: 50px;
            }

            .hero {
                padding-top: 70px;
            }

            .hero-card {
                max-width: 520px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .process-grid {
                grid-template-columns: 1fr 1fr;
            }

            .process-step:nth-child(2) {
                border-right: none;
            }

            .process-step:nth-child(3),
            .process-step:nth-child(4) {
                border-top: 1px solid var(--line);
            }

            .cta {
                flex-direction: column;
                align-items: flex-start;
                padding: 40px;
            }
        }

        @media (max-width: 600px) {

            .container {
                width: min(100% - 28px, 1120px);
            }

            .hero {
                padding: 60px 0;
            }

            .hero h1 {
                font-size: 48px;
            }

            .hero-headline {
                display: block;
            }

            .ticker-slot {
                display: inline-block;
            }

            .hero-description {
                font-size: 16px;
            }

            .hero-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .button {
                width: 100%;
            }

            .map-preview {
                height: 240px;
            }

            .parcel-shape {
                left: 90px;
            }

            .parcel-point {
                left: 162px;
            }

            .risk-items {
                grid-template-columns: 1fr;
            }

            .trust-inner {
                flex-direction: column;
                align-items: flex-start;
                justify-content: center;
                padding: 20px 0;
            }

            .trust-items {
                flex-wrap: wrap;
                gap: 15px;
            }

            .section {
                padding: 70px 0;
            }

            .section-header {
                display: block;
            }

            .section-description {
                margin-top: 15px;
            }

            .section-title {
                font-size: 30px;
            }

            .process-grid {
                grid-template-columns: 1fr;
            }

            .process-step,
            .process-step:not(:first-child) {
                border-right: none;
                border-bottom: 1px solid var(--line);
                padding: 24px 0;
            }

            .process-step:last-child {
                border-bottom: none;
            }

            .process-step h3 {
                margin-top: 25px;
            }

            .cta {
                padding: 32px 24px;
            }

            .cta h2 {
                font-size: 30px;
            }

            .footer-inner {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

    <x-navbar />

    <main>

        <section class="hero">

            <div class="container hero-grid">

                <div>

                    <div class="eyebrow">
                        <span class="eyebrow-dot"></span>
                        Land intelligence for Ghana
                    </div>

                    <h1 class="hero-headline">

                        <span class="visually-hidden">
                            Know the land before you buy, lease, rent, or invest.
                        </span>

                        <span aria-hidden="true">
                            Know the land before you
                        </span>

                        <span class="ticker-slot" aria-hidden="true">

                            <span class="ticker-inner">

                                <span class="ticker-word">buy</span>
                                <span class="ticker-word">lease</span>
                                <span class="ticker-word">rent</span>
                                <span class="ticker-word">invest</span>

                                <!-- duplicate first word for a seamless loop -->
                                <span class="ticker-word">buy</span>

                            </span>

                        </span>

                    </h1>

                    <p class="hero-description">
                        LandSure helps you understand a piece of land
                        before you commit. Check its location, size,
                        potential risks and available land information
                        from one place.
                    </p>

                    <div class="hero-actions">

                        <a href="/map" class="button dark large">
                            Analyze a Property
                            <span>→</span>
                        </a>

                        <a href="/parcels" class="button large">
                            View My Parcels
                        </a>

                    </div>

                </div>


                <div class="hero-card">

                    <div class="hero-card-top">

                        <div class="window-dots">
                            <span class="window-dot"></span>
                            <span class="window-dot"></span>
                            <span class="window-dot"></span>
                        </div>

                        <span class="card-label">
                            LAND ANALYSIS / 001
                        </span>

                    </div>


                    <div class="map-preview">

                        <div class="map-road road-one"></div>
                        <div class="map-road road-two"></div>
                        <div class="map-road road-three"></div>

                        <div class="parcel-shape"></div>

                        <div class="parcel-point"></div>

                        <div class="map-tag">

                            <div class="map-tag-title">
                                Selected Parcel
                            </div>

                            <div class="map-tag-text">
                                5.603842, -0.187421
                            </div>

                        </div>

                    </div>


                    <div class="risk-summary">

                        <div class="risk-heading">

                            <strong>
                                Initial assessment
                            </strong>

                            <span>
                                PENDING
                            </span>

                        </div>

                        <div class="risk-items">

                            <div class="risk-item">

                                <div class="risk-item-label">
                                    Flood
                                </div>

                                <div class="risk-item-value">
                                    <span class="risk-indicator"></span>
                                    Checking
                                </div>

                            </div>

                            <div class="risk-item">

                                <div class="risk-item-label">
                                    Buffer
                                </div>

                                <div class="risk-item-value">
                                    <span class="risk-indicator"></span>
                                    Checking
                                </div>

                            </div>

                            <div class="risk-item">

                                <div class="risk-item-label">
                                    Parcel
                                </div>

                                <div class="risk-item-value">
                                    <span class="risk-indicator"></span>
                                    Pending
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <section class="trust-bar">

            <div class="container trust-inner">

                <span class="trust-label">
                    Built to make land due diligence easier.
                </span>

                <div class="trust-items">

                    <div class="trust-item">
                        <span class="trust-icon">⌖</span>
                        Location
                    </div>

                    <div class="trust-item">
                        <span class="trust-icon">◇</span>
                        Boundaries
                    </div>

                    <div class="trust-item">
                        <span class="trust-icon">!</span>
                        Risk
                    </div>

                    <div class="trust-item">
                        <span class="trust-icon">✓</span>
                        Verification
                    </div>

                </div>

            </div>

        </section>


        <section class="section">

            <div class="container">

                <div class="section-header">

                    <div>

                        <div class="section-kicker">
                            What LandSure checks
                        </div>

                        <h2 class="section-title">
                            Understand what you're buying.
                        </h2>

                    </div>

                    <p class="section-description">
                        LandSure brings important land information
                        together so you can identify potential problems
                        before they become expensive problems.
                    </p>

                </div>


                <div class="feature-grid">

                    <div class="feature-card">

                        <div class="feature-number">
                            01 / LOCATION
                        </div>

                        <div class="feature-icon">
                            ⌖
                        </div>

                        <h3>
                            Locate the land
                        </h3>

                        <p>
                            Identify the property's position on the map
                            and work with its coordinates and boundaries.
                        </p>

                    </div>


                    <div class="feature-card">

                        <div class="feature-number">
                            02 / RISK
                        </div>

                        <div class="feature-icon">
                            !
                        </div>

                        <h3>
                            Identify potential risks
                        </h3>

                        <p>
                            Check for issues such as flood exposure,
                            buffer zones and other environmental or
                            planning constraints.
                        </p>

                    </div>


                    <div class="feature-card">

                        <div class="feature-number">
                            03 / VERIFICATION
                        </div>

                        <div class="feature-icon">
                            ✓
                        </div>

                        <h3>
                            Support due diligence
                        </h3>

                        <p>
                            Keep parcel information and verification
                            status organized so important checks are
                            easier to track.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <section class="section process">

            <div class="container">

                <div class="section-header">

                    <div>

                        <div class="section-kicker">
                            Workflow
                        </div>

                        <h2 class="section-title">
                            From location to confidence.
                        </h2>

                    </div>

                    <p class="section-description">
                        A simple workflow designed around the way people
                        actually evaluate land.
                    </p>

                </div>


                <div class="process-grid">

                    <div class="process-step">

                        <div class="step-number">
                            01
                        </div>

                        <h3>
                            Locate
                        </h3>

                        <p>
                            Find the property or mark its location
                            directly on the LandSure map.
                        </p>

                    </div>


                    <div class="process-step">

                        <div class="step-number">
                            02
                        </div>

                        <h3>
                            Define
                        </h3>

                        <p>
                            Draw or provide the parcel boundary and
                            calculate its size and location.
                        </p>

                    </div>


                    <div class="process-step">

                        <div class="step-number">
                            03
                        </div>

                        <h3>
                            Analyze
                        </h3>

                        <p>
                            Compare the parcel against available
                            environmental and planning information.
                        </p>

                    </div>


                    <div class="process-step">

                        <div class="step-number">
                            04
                        </div>

                        <h3>
                            Verify
                        </h3>

                        <p>
                            Review the findings and identify what still
                            requires professional or official verification.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <section class="cta-section">

            <div class="container">

                <div class="cta">

                    <div>

                        <h2>
                            Before you buy the land, understand the land.
                        </h2>

                        <p>
                            Start with a location and let LandSure help
                            you build a clearer picture of the property.
                        </p>

                    </div>

                    <a href="/map" class="button">
                        Start checking land →
                    </a>

                </div>

            </div>

        </section>

    </main>


    <footer>

        <div class="container footer-inner">

            <div class="footer-brand">
                LandSure
            </div>

            <div class="footer-copy">
                Land intelligence & due diligence.
            </div>

            <div class="footer-links">
                <a href="/map">Check Land</a>
                <a href="/parcels">My Parcels</a>
            </div>

        </div>

    </footer>

</body>
</html>