@php
    $formOptionLists = $formOptionLists ?? [];
@endphp

<datalist id="department-options">
    @foreach(($formOptionLists['departments'] ?? []) as $option)
        <option value="{{ $option }}"></option>
    @endforeach
</datalist>

<datalist id="name-options">
    @foreach(($formOptionLists['names'] ?? []) as $option)
        <option value="{{ $option }}"></option>
    @endforeach
</datalist>

<datalist id="position-options">
    @foreach(($formOptionLists['positions'] ?? []) as $option)
        <option value="{{ $option }}"></option>
    @endforeach
</datalist>

<datalist id="sector-options">
    @foreach(($formOptionLists['sectors'] ?? []) as $option)
        <option value="{{ $option }}"></option>
    @endforeach
</datalist>

<datalist id="location-options">
    @foreach(($formOptionLists['locations'] ?? []) as $option)
        <option value="{{ $option }}"></option>
    @endforeach
</datalist>

<datalist id="bay-options">
    @foreach(($formOptionLists['bays'] ?? []) as $option)
        <option value="{{ $option }}"></option>
    @endforeach
</datalist>

