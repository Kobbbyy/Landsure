@props(['value'])

<label {{ $attributes->merge(['class' => 'ls-form-label']) }}>
    {{ $value ?? $slot }}
</label>

<style>
    .ls-form-label {
        display: block;

        margin-bottom: 6px;

        color: #171717;

        font-size: 13px;
        font-weight: 500;
    }
</style>