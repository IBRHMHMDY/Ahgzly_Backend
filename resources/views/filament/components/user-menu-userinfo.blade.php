<div class="leading-tight">
    <div class="text-xl color-black font-semibold">
        {{ auth()->user()->name }}({{ auth()->user()->getRoleNames()->first() ?? 'بدون دور' }})
    </div>
</div>
