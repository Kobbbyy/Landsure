<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        LandSure — How it works
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

            --radius-md: 4px;
            --radius-lg: 8px;
            --radius-xl: 12px;

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

        .page {
            min-height: 100vh;
        }

        /* MAIN */

        .main {
            width: 100%;
            max-width: 820px;
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

        .subtitle {
            margin: 12px 0 0;
            color: var(--subtext);
            font-size: 15px;
            line-height: 1.6;
        }

        .card {
            background: var(--paper-white);
            border: 1px solid var(--line);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-subtle);
            padding: 26px 28px;
            margin-top: 22px;
        }

        .card h2 {
            margin: 0 0 14px;
            font-size: 17px;
            font-weight: 600;
            letter-spacing: -0.02em;
        }

        .card h3 {
            margin: 22px 0 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .card h3:first-of-type {
            margin-top: 12px;
        }

        .card p {
            margin: 10px 0;
            color: var(--subtext);
            font-size: 14px;
            line-height: 1.65;
        }

        .card ul {
            margin: 10px 0;
            padding-left: 22px;
            color: var(--subtext);
            font-size: 14px;
            line-height: 1.65;
        }

        .card li {
            margin: 6px 0;
        }

        .card strong {
            color: var(--ink);
            font-weight: 600;
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

        /* MOBILE */

        @media (max-width: 850px) {

            .main {
                padding: 30px 18px 55px;
            }

            h1 {
                font-size: 27px;
            }

            .card {
                padding: 20px 20px;
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
            href="/"
            class="back-link"
        >
            <span class="back-arrow">←</span>
            Back to home
        </a>

        <div class="eyebrow">
            How LandSure works
        </div>

        <h1>
            Methodology
        </h1>

        <p class="subtitle">
            This page explains how LandSure decides the overall status of a
            parcel, what each status means, and what LandSure does not claim.
        </p>


        <section class="card">

            <h2>
                The five possible overall statuses
            </h2>

            <p>
                Every parcel analysis ends in one of these five statuses:
            </p>

            <ul>

                <li>
                    <strong>High Risk</strong>:
                    At least one connected check found a high-risk condition
                    with supporting evidence.
                </li>

                <li>
                    <strong>Moderate Risk</strong>:
                    No check found a high-risk condition, but at least one
                    check found a moderate-risk condition.
                </li>

                <li>
                    <strong>Low Risk</strong>:
                    Every check LandSure can currently run returned a low-risk
                    result. This does not mean the land is safe. It means that
                    no known modeled risk was detected by the checks LandSure
                    can perform.
                </li>

                <li>
                    <strong>Unknown</strong>:
                    One or more checks are still unknown, so LandSure cannot
                    yet rule out a serious issue on this parcel.
                </li>

                <li>
                    <strong>Not Assessed</strong>:
                    No checks have been run for this parcel yet.
                </li>

            </ul>

        </section>


        <section class="card">

            <h2>
                How the individual checks combine
            </h2>

            <p>
                LandSure runs each check independently. The overall status
                reflects the most serious finding LandSure can support with
                evidence. It is not an average, and it is not a sum.
            </p>

            <p>
                The rule is applied in this order:
            </p>

            <ul>

                <li>
                    If any connected check returns <strong>High</strong> or
                    <strong>Very High</strong>, the overall is
                    <strong>High Risk</strong>.
                </li>

                <li>
                    Otherwise, if any connected check returns
                    <strong>Moderate</strong>, the overall is
                    <strong>Moderate Risk</strong>.
                </li>

                <li>
                    Otherwise, if any connected check is still
                    <strong>Unknown</strong>, the overall is
                    <strong>Unknown</strong>.
                </li>

                <li>
                    Only if every connected check returns <strong>Low</strong>
                    does the overall show <strong>Low Risk</strong>.
                </li>

            </ul>

            <p>
                Note the deliberate placement of Unknown above Low. LandSure
                will never claim Low Risk while any check LandSure can
                currently run is still unknown. This is what keeps the tool
                honest.
            </p>

        </section>


        <section class="card">

            <h2>
                Boundary is a data quality check, not a risk finding
            </h2>

            <p>
                The parcel boundary check verifies that the polygon you drew
                is structurally valid: it is a closed shape, it has a usable
                center, and its coordinates are within valid ranges.
            </p>

            <p>
                It does not verify that this is the legal boundary of the
                land, or that it matches any record held by the Lands
                Commission. A structurally valid polygon never makes the
                overall status Low on its own, and it never makes the overall
                status High on its own. It is displayed as a separate
                data quality result.
            </p>

        </section>


        <section class="card">

            <h2>
                Why there is no overall numeric score
            </h2>

            <p>
                LandSure does not produce a single number to represent the
                overall risk of a parcel. Combining several unrelated
                measurements, such as modeled flood depth, distance to a
                mapped waterway, and the status of future planning checks,
                into one number would imply a precision that LandSure does
                not have and cannot defend.
            </p>

            <p>
                Individual checks may still show their own scores for their
                own narrow purpose, but those are never combined into an
                overall score.
            </p>

        </section>


        <section class="card">

            <h2>
                What LandSure does not claim
            </h2>

            <p>
                LandSure is a screening and due diligence aid. It is not a
                replacement for:
            </p>

            <ul>
                <li>A licensed surveyor</li>
                <li>A lawyer</li>
                <li>A Lands Commission search</li>
                <li>Official title verification</li>
                <li>Planning authority approval</li>
                <li>An environmental assessment</li>
                <li>A professional valuation</li>
                <li>Government certification</li>
            </ul>

            <p>
                Where LandSure does not have data, it says so. It does not
                guess, and it does not turn missing data into a low-risk
                result.
            </p>

        </section>


        <footer class="footer">

            <span>
                LandSure · Ghana land intelligence
            </span>

            <span>
                Methodology v1.0
            </span>

        </footer>

    </main>

</div>

</body>
</html>