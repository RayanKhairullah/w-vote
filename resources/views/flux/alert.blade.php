@props(['class' => ''])
<div {{ $attributes->merge(['class' => 'rounded-md border border-amber-300 bg-amber-50 text-amber-800 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-200 p-3 '.$class]) }}>
    {{ $slot }}
</div>
