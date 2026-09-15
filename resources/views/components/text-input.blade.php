@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge(['class' => 'ls-form-input']) }}
>

<style>
    .ls-form-input {
        display: block;
        width: 100%;

        padding: 10px 12px;

        background: #ffffff;

        border: 1px solid #eaeaea;
        border-radius: 8px;

        color: #171717;

        font-size: 14px;
        line-height: 1.4;

        transition:
            border-color 0.15s ease,
            box-shadow 0.15s ease;

        outline: none;
    }

    .ls-form-input:focus {
        border-color: #171717;
        box-shadow: 0 0 0 3px rgba(23, 23, 23, 0.08);
    }

    .ls-form-input:disabled {
        background: #f5f5f5;
        color: #7d7d7d;
        cursor: not-allowed;
    }
</style>