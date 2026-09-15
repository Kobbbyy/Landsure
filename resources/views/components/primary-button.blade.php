<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ls-form-button']) }}>
    {{ $slot }}
</button>

<style>
    .ls-form-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        width: 100%;

        min-height: 44px;

        padding: 0 18px;

        background: #171717;
        color: #ffffff;

        border: 1px solid #171717;
        border-radius: 8px;

        font-size: 14px;
        font-weight: 500;

        cursor: pointer;

        transition: background 0.15s ease;
    }

    .ls-form-button:hover {
        background: #000000;
    }

    .ls-form-button:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(23, 23, 23, 0.15);
    }

    .ls-form-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>