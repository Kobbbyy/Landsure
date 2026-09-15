<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Page not found — LandSure</title>

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

            -webkit-font-smoothing: antialiased;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .ls-error-shell {
            min-height: 100vh;

            display: flex;
            flex-direction: column;
        }

        .ls-error-main {
            flex: 1 1 auto;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 60px 24px 80px;
        }

        .ls-error-card {
            width: 100%;
            max-width: 520px;

            background: var(--paper-white);

            border: 1px solid var(--line);
            border-radius: var(--radius-xl);

            box-shadow: var(--shadow-subtle);

            padding: 40px 36px 34px;

            text-align: center;
        }

        .ls-error-code {
            margin: 0 0 6px;

            color: var(--ink);

            font-family:
                ui-monospace,
                SFMono-Regular,
                Menlo,
                Monaco,
                Consolas,
                monospace;

            font-size: 44px;
            line-height: 1;
            letter-spacing: 0.02em;
            font-weight: 600;
        }

        .ls-error-eyebrow {
            margin-bottom: 16px;

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

        .ls-error-title {
            margin: 0;

            font-size: 22px;
            line-height: 1.25;
            letter-spacing: -0.025em;
            font-weight: 600;
        }

        .ls-error-text {
            max-width: 400px;

            margin: 12px auto 0;

            color: var(--subtext);

            font-size: 14px;
            line-height: 1.6;
        }

        .ls-error-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;

            gap: 10px;

            margin-top: 28px;
        }

        .ls-error-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            min-height: 40px;

            padding: 0 18px;

            border: 1px solid var(--line);
            border-radius: var(--radius-lg);

            background: var(--paper-white);
            color: var(--ink);

            font-size: 14px;
            font-weight: 500;

            transition:
                border-color 0.15s ease,
                box-shadow 0.15s ease,
                background 0.15s ease,
                color 0.15s ease;
        }

        .ls-error-button:hover {
            border-color: #d5d5d5;
            box-shadow:
                rgba(0, 0, 0, 0.04) 0px 2px 2px 0px,
                rgba(0, 0, 0, 0.04) 0px 8px 8px -8px;
        }

        .ls-error-button.dark {
            background: var(--ink);
            border-color: var(--ink);
            color: #ffffff;
        }

        .ls-error-button.dark:hover {
            background: var(--onyx);
            border-color: var(--onyx);
        }

        .ls-error-footer {
            margin-top: 26px;
            padding-top: 18px;

            border-top: 1px solid var(--line);

            color: var(--subtext);

            font-size: 12px;
            line-height: 1.5;
        }

        @media (max-width: 600px) {

            .ls-error-main {
                padding: 30px 16px 50px;
            }

            .ls-error-card {
                padding: 30px 22px 26px;
            }

            .ls-error-code {
                font-size: 36px;
            }

            .ls-error-title {
                font-size: 20px;
            }

            .ls-error-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .ls-error-button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="ls-error-shell">

        <x-navbar />

        <main class="ls-error-main">

            <div class="ls-error-card">

                <p class="ls-error-code">
                    404
                </p>

                <div class="ls-error-eyebrow">
                    Not found
                </div>

                <h1 class="ls-error-title">
                    This page does not exist.
                </h1>

                <p class="ls-error-text">
                    The page you were looking for could not be found. It may
                    have been moved, or the address may be incorrect. If you
                    were trying to open a parcel that belongs to another
                    account, LandSure will not reveal that it exists.
                </p>

                <div class="ls-error-actions">

                    <a
                        href="{{ route('home') }}"
                        class="ls-error-button dark"
                    >
                        Back to home
                    </a>

                    @auth

                        <a
                            href="{{ route('parcels.index') }}"
                            class="ls-error-button"
                        >
                            Go to my parcels
                        </a>

                    @else

                        <a
                            href="{{ route('login') }}"
                            class="ls-error-button"
                        >
                            Log in
                        </a>

                    @endauth

                </div>

                <div class="ls-error-footer">
                    LandSure · Ghana land intelligence
                </div>

            </div>

        </main>

    </div>

</body>
</html>