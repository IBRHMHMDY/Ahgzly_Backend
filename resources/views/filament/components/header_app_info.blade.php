@php
    // جلب المستخدم الحالي
    $user = auth()->user();

    $restaurantName = $user?->restaurants->first()?->name ?? "الفرع الرئيسى";
@endphp
<div class="flex flex-col items-center justify-center gap-3 h-full">
    
    {{-- السطر الأول: اسم التطبيق --}}
    <div class="text-xl font-bold text-gray-900 dark:text-white">
        {{ config('app.name') }}
    </div>

    {{-- السطر الثاني: اسم المطعم --}}
    <div class="text-gray-500 dark:text-gray-400">
        {{ $restaurantName }}
    </div>
    
</div>