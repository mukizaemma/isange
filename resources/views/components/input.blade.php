@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-neutral-200 focus:border-[#e69138] focus:ring-[#e69138] rounded-lg shadow-sm']) !!}>
