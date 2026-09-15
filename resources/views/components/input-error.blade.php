@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'ls-form-error']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif

<style>
    .ls-form-error {
        margin: 6px 0 0;
        padding: 0;

        list-style: none;

        color: #991b1b;

        font-size: 12px;
        line-height: 1.5;
    }
</style>