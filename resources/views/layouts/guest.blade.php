<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LandSure') }}</title>

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

        /* AUTH SHELL */

        .ls-auth-shell {
            min-height: 100vh;

            display: flex;
            flex-direction: column;
        }

        .ls-auth-main {
            flex: 1 1 auto;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 60px 24px 80px;
        }

        .ls-auth-card {
            width: 100%;
            max-width: 440px;

            background: var(--paper-white);

            border: 1px solid var(--line);
            border-radius: var(--radius-xl);

            box-shadow: var(--shadow-subtle);

            padding: 32px 32px 28px;
        }

        .ls-auth-heading {
            margin-bottom: 24px;
        }

        .ls-auth-eyebrow {
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

        .ls-auth-title {
            margin: 0;

            font-size: 22px;
            line-height: 1.2;
            letter-spacing: -0.025em;
            font-weight: 600;
        }

        .ls-auth-subtitle {
            margin: 8px 0 0;

            color: var(--subtext);

            font-size: 14px;
            line-height: 1.55;
        }

        .ls-auth-footer {
            margin-top: 22px;
            padding-top: 18px;

            border-top: 1px solid var(--line);

            color: var(--subtext);

            font-size: 13px;
            line-height: 1.5;
        }

        .ls-auth-footer a {
            color: var(--ink);
            font-weight: 500;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .ls-auth-footer a:hover {
            opacity: 0.75;
        }

        /* Small resets for form rows */

        .ls-auth-card form > * + * {
            margin-top: 16px;
        }

        .ls-auth-inline {
            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 12px;

            margin-top: 16px;

            font-size: 13px;
        }

        .ls-auth-inline label {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            color: var(--subtext);

            cursor: pointer;
        }

        .ls-auth-inline input[type="checkbox"] {
            margin: 0;
        }

        .ls-auth-inline a {
            color: var(--ink);
            font-weight: 500;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .ls-auth-inline a:hover {
            opacity: 0.75;
        }

        @media (max-width: 600px) {

            .ls-auth-main {
                padding: 30px 16px 50px;
            }

            .ls-auth-card {
                padding: 24px 20px 22px;
            }

            .ls-auth-title {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="ls-auth-shell">

        <x-navbar />

        <main class="ls-auth-main">

            <div class="ls-auth-card">

                {{ $slot }}

            </div>

        </main>

    </div>

</body>
</html>