{{--
    LandSure shared navbar.

    Rendered on every page via <x-navbar />.

    Responsibilities:
    - Renders the brand, centre links, and right-side actions.
    - Detects the current page to highlight the active link.
    - Shows login and register buttons when logged out.
    - Shows the user's email and a log out button when logged in.
    - Owns its own CSS, so pages do not need to carry navbar rules.

    The markup uses the standardised 64px full-width layout that
    replaced the previously inconsistent per-page navbars.
--}}

<header class="ls-navbar">

    <div class="ls-nav-inner">

        <a href="{{ route('home') }}" class="ls-brand">

            <span class="ls-brand-mark">
                L
            </span>

            <span>
                LandSure
            </span>

        </a>


        <nav class="ls-nav-center">

            <a
                href="{{ route('home') }}"
                class="ls-nav-link @if (request()->routeIs('home')) active @endif"
            >
                Home
            </a>

            <a
                href="{{ route('methodology') }}"
                class="ls-nav-link @if (request()->routeIs('methodology')) active @endif"
            >
                How it works
            </a>

            @auth

                <a
                    href="{{ route('map') }}"
                    class="ls-nav-link @if (request()->routeIs('map')) active @endif"
                >
                    Check Land
                </a>

                <a
                    href="{{ route('parcels.index') }}"
                    class="ls-nav-link @if (request()->routeIs('parcels.*')) active @endif"
                >
                    My Parcels
                </a>

            @endauth

        </nav>


        <div class="ls-nav-right">

            @auth

                <span class="ls-nav-user">
                    {{ auth()->user()->email }}
                </span>

                <a
                    href="{{ route('map') }}"
                    class="ls-nav-button"
                >
                    Check Land
                </a>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="ls-nav-logout-form"
                >
                    @csrf

                    <button
                        type="submit"
                        class="ls-nav-button dark"
                    >
                        Log out
                    </button>

                </form>

            @else

                <a
                    href="{{ route('login') }}"
                    class="ls-nav-button"
                >
                    Log in
                </a>

                @if (Route::has('register'))

                    <a
                        href="{{ route('register') }}"
                        class="ls-nav-button dark"
                    >
                        Register
                    </a>

                @endif

            @endauth

        </div>

    </div>


    {{-- Navbar styles are scoped to this component. --}}
    <style>

        .ls-navbar {
            height: 64px;
            width: 100%;

            display: flex;
            align-items: center;

            padding: 0 32px;

            background: rgba(255, 255, 255, 0.94);

            border-bottom: 1px solid #eaeaea;

            position: sticky;
            top: 0;
            z-index: 1000;

            backdrop-filter: blur(12px);

            font-family: inherit;
        }

        .ls-nav-inner {
            width: 100%;

            display: flex;
            align-items: center;
            justify-content: space-between;

            gap: 24px;
        }

        .ls-brand {
            display: flex;
            align-items: center;
            gap: 9px;

            font-size: 16px;
            font-weight: 600;

            letter-spacing: -0.02em;

            color: #171717;
            text-decoration: none;

            white-space: nowrap;
        }

        .ls-brand-mark {
            width: 26px;
            height: 26px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            border-radius: 7px;

            background: #000000;
            color: #ffffff;

            font-size: 12px;
            font-weight: 700;
        }

        .ls-nav-center {
            display: flex;
            align-items: center;
            gap: 4px;

            flex: 1 1 auto;
            justify-content: center;

            min-width: 0;
        }

        .ls-nav-link {
            padding: 8px 12px;

            border-radius: 6px;

            color: #666666;

            font-size: 13px;
            font-weight: 500;

            text-decoration: none;

            transition:
                background 0.15s ease,
                color 0.15s ease;

            white-space: nowrap;
        }

        .ls-nav-link:hover {
            background: #f5f5f5;
            color: #171717;
        }

        .ls-nav-link.active {
            color: #171717;
            background: #f3f3f3;
        }

        .ls-nav-right {
            display: flex;
            align-items: center;
            gap: 8px;

            flex-shrink: 0;
        }

        .ls-nav-user {
            max-width: 220px;

            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;

            color: #666666;

            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 11px;
        }

        .ls-nav-button {
            height: 34px;

            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: 0 13px;

            border: 1px solid #eaeaea;
            border-radius: 6px;

            background: #ffffff;
            color: #171717;

            font-size: 13px;
            font-weight: 500;

            text-decoration: none;

            cursor: pointer;

            transition:
                border-color 0.15s ease,
                box-shadow 0.15s ease,
                background 0.15s ease,
                color 0.15s ease;

            white-space: nowrap;
        }

        .ls-nav-button:hover {
            border-color: #d5d5d5;
        }

        .ls-nav-button.dark {
            background: #171717;
            border-color: #171717;
            color: #ffffff;
        }

        .ls-nav-button.dark:hover {
            background: #000000;
        }

        .ls-nav-logout-form {
            display: inline-flex;
            margin: 0;
        }


        /* -------------------------------------
           Mobile
        ------------------------------------- */

        @media (max-width: 800px) {

            .ls-navbar {
                padding: 0 16px;
            }

            .ls-nav-user {
                display: none;
            }

            .ls-nav-center {
                display: none;
            }
        }

        @media (max-width: 480px) {

            .ls-nav-button {
                height: 32px;
                padding: 0 10px;

                font-size: 12px;
            }

            .ls-brand {
                font-size: 15px;
            }
        }

    </style>

</header>