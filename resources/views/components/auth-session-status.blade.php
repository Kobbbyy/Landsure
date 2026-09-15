@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'ls-form-status']) }}>
        {{ $status }}
    </div>
@endif

<style>
    .ls-form-status {
        padding: 10px 12px;

        margin-bottom: 16px;

        background: #f0fdf4;
        color: #166534;

        border: 1px solid #bbf7d0;
        border-radius: 8px;

        font-size: 13px;
        line-height: 1.5;
    }
</style>