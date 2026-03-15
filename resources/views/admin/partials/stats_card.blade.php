<div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4" style="border-left-color: {{ $color ?? '#28A375' }}">
    <div class="flex-1">
        <p class="text-sm font-medium text-gray-500 mb-1">{{ $title }}</p>
        <h3 class="text-3xl font-bold text-gray-900 tracking-tight">{{ $value }}</h3>
        
        @if(isset($trendValue))
            <div class="flex items-center gap-1 mt-1">
                <i data-lucide="{{ $trendIcon ?? 'trending-up' }}" class="w-3 h-3 {{ $trendColor ?? 'text-green-500' }}"></i>
                <p class="text-[11px] font-bold {{ $trendColor ?? 'text-green-500' }}">{{ $trendValue }} 
                    @if(isset($subtitle))
                        <span class="text-gray-400 font-medium ml-1">{{ $subtitle }}</span>
                    @endif
                </p>
            </div>
        @elseif(isset($subtitle))
            <p class="text-[11px] font-bold text-gray-400 mt-1 uppercase tracking-wider">{{ $subtitle }}</p>
        @endif
    </div>
    <div class="w-12 h-12 {{ $iconBg ?? 'bg-[#28A375]' }} rounded-xl flex items-center justify-center shadow-lg {{ $iconShadow ?? 'shadow-green-100' }}">
        <i data-lucide="{{ $icon }}" class="w-6 h-6 text-white"></i>
    </div>
</div>
