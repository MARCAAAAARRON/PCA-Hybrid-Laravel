@php
    $forecast = $this->getForecastData();
@endphp

<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-calendar-days"
        icon-color="success"
        collapsible
        collapsed
    >
        <x-slot name="heading">
            🌴 Harvest Forecast
        </x-slot>

        <x-slot name="description">
            Projected harvests from hybridization records — plan your monthly harvest data collection.
        </x-slot>

        {{-- ── Summary Stats ─────────────────────────────────── --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.25rem;">
            {{-- Overdue --}}
            <div style="
                background: linear-gradient(135deg, rgba(239,68,68,0.10) 0%, rgba(239,68,68,0.05) 100%);
                border: 1px solid rgba(239,68,68,0.25);
                border-radius: 0.75rem;
                padding: 1rem;
                text-align: center;
            ">
                <div style="font-size: 1.75rem; font-weight: 800; color: #ef4444; line-height: 1;">
                    {{ $forecast['overdue'] }}
                </div>
                <div style="font-size: 0.7rem; font-weight: 600; color: #f87171; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem;">
                    Overdue
                </div>
            </div>

            {{-- Ready Soon (≤30 days) --}}
            <div style="
                background: linear-gradient(135deg, rgba(245,158,11,0.10) 0%, rgba(245,158,11,0.05) 100%);
                border: 1px solid rgba(245,158,11,0.25);
                border-radius: 0.75rem;
                padding: 1rem;
                text-align: center;
            ">
                <div style="font-size: 1.75rem; font-weight: 800; color: #f59e0b; line-height: 1;">
                    {{ $forecast['ready_soon'] }}
                </div>
                <div style="font-size: 0.7rem; font-weight: 600; color: #fbbf24; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem;">
                    ≤ 30 Days
                </div>
            </div>

            {{-- Upcoming (>30 days) --}}
            <div style="
                background: linear-gradient(135deg, rgba(34,197,94,0.10) 0%, rgba(34,197,94,0.05) 100%);
                border: 1px solid rgba(34,197,94,0.25);
                border-radius: 0.75rem;
                padding: 1rem;
                text-align: center;
            ">
                <div style="font-size: 1.75rem; font-weight: 800; color: #22c55e; line-height: 1;">
                    {{ $forecast['upcoming'] }}
                </div>
                <div style="font-size: 0.7rem; font-weight: 600; color: #4ade80; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem;">
                    Upcoming
                </div>
            </div>

            {{-- Total --}}
            <div style="
                background: linear-gradient(135deg, rgba(99,102,241,0.10) 0%, rgba(99,102,241,0.05) 100%);
                border: 1px solid rgba(99,102,241,0.25);
                border-radius: 0.75rem;
                padding: 1rem;
                text-align: center;
            ">
                <div style="font-size: 1.75rem; font-weight: 800; color: #6366f1; line-height: 1;">
                    {{ $forecast['total'] }}
                </div>
                <div style="font-size: 0.7rem; font-weight: 600; color: #818cf8; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem;">
                    Total Pending
                </div>
            </div>
        </div>

        {{-- ── Month Cards / Timeline ────────────────────────── --}}
        @if(empty($forecast['months']))
            <div style="text-align: center; padding: 2rem; color: #9ca3af;">
                <x-heroicon-o-calendar style="width: 2.5rem; height: 2.5rem; margin: 0 auto 0.5rem; opacity: 0.5;" />
                <p style="font-weight: 600;">No upcoming harvests</p>
                <p style="font-size: 0.8rem;">All hybridization records are either harvested or have no planting date.</p>
            </div>
        @else
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
                @foreach($forecast['months'] as $month)
                    @php
                        if ($month['is_past']) {
                            $borderColor = 'rgba(239,68,68,0.40)';
                            $bgGradient = 'linear-gradient(135deg, rgba(239,68,68,0.08) 0%, rgba(239,68,68,0.02) 100%)';
                            $labelColor = '#ef4444';
                            $badgeBg = '#fef2f2';
                            $badgeColor = '#dc2626';
                            $badgeText = 'OVERDUE';
                            $icon = '🔴';
                        } elseif ($month['is_current']) {
                            $borderColor = 'rgba(245,158,11,0.40)';
                            $bgGradient = 'linear-gradient(135deg, rgba(245,158,11,0.10) 0%, rgba(245,158,11,0.03) 100%)';
                            $labelColor = '#f59e0b';
                            $badgeBg = '#fffbeb';
                            $badgeColor = '#d97706';
                            $badgeText = 'THIS MONTH';
                            $icon = '🟡';
                        } else {
                            $borderColor = 'rgba(34,197,94,0.25)';
                            $bgGradient = 'linear-gradient(135deg, rgba(34,197,94,0.06) 0%, rgba(34,197,94,0.02) 100%)';
                            $labelColor = '#22c55e';
                            $badgeBg = '#f0fdf4';
                            $badgeColor = '#16a34a';
                            $badgeText = 'UPCOMING';
                            $icon = '🟢';
                        }
                    @endphp

                    <div style="
                        flex: 1 1 200px;
                        max-width: 280px;
                        border: 1px solid {{ $borderColor }};
                        border-radius: 0.75rem;
                        padding: 1rem;
                        background: {{ $bgGradient }};
                        transition: transform 0.15s ease, box-shadow 0.15s ease;
                    "
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)';"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';"
                    >
                        {{-- Header --}}
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <div style="display: flex; align-items: center; gap: 0.4rem;">
                                <span style="font-size: 0.85rem;">{{ $icon }}</span>
                                <span style="font-weight: 700; font-size: 0.95rem; color: {{ $labelColor }};">
                                    {{ $month['label'] }}
                                </span>
                            </div>
                            <span style="
                                font-size: 0.6rem;
                                font-weight: 700;
                                text-transform: uppercase;
                                letter-spacing: 0.05em;
                                padding: 0.15rem 0.45rem;
                                border-radius: 9999px;
                                background: {{ $badgeBg }};
                                color: {{ $badgeColor }};
                            ">{{ $badgeText }}</span>
                        </div>

                        {{-- Count --}}
                        <div style="font-size: 2rem; font-weight: 800; color: {{ $labelColor }}; line-height: 1; margin-bottom: 0.5rem;">
                            {{ $month['total'] }}
                            <span style="font-size: 0.7rem; font-weight: 500; opacity: 0.7;">
                                record{{ $month['total'] !== 1 ? 's' : '' }}
                            </span>
                        </div>

                        {{-- Growth Status Breakdown --}}
                        @if(!empty($month['status']))
                            <div style="display: flex; flex-wrap: wrap; gap: 0.3rem; margin-bottom: 0.5rem;">
                                @foreach($month['status'] as $status => $count)
                                    @php
                                        $statusColors = [
                                            'seedling'   => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                                            'vegetative' => ['bg' => '#eff6ff', 'text' => '#3b82f6'],
                                            'flowering'  => ['bg' => '#fffbeb', 'text' => '#d97706'],
                                            'fruiting'   => ['bg' => '#f0fdf4', 'text' => '#16a34a'],
                                        ];
                                        $sc = $statusColors[$status] ?? ['bg' => '#f3f4f6', 'text' => '#6b7280'];
                                    @endphp
                                    <span style="
                                        font-size: 0.6rem;
                                        font-weight: 600;
                                        padding: 0.15rem 0.4rem;
                                        border-radius: 0.375rem;
                                        background: {{ $sc['bg'] }};
                                        color: {{ $sc['text'] }};
                                        text-transform: capitalize;
                                    ">{{ $status }}: {{ $count }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Sites --}}
                        @if(!empty($month['sites']))
                            <div style="border-top: 1px solid rgba(0,0,0,0.06); padding-top: 0.4rem;">
                                @foreach(array_slice($month['sites'], 0, 3) as $siteName => $count)
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.1rem 0; font-size: 0.7rem;">
                                        <span style="color: #6b7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 140px;" title="{{ $siteName }}">
                                            📍 {{ $siteName }}
                                        </span>
                                        <span style="font-weight: 700; color: #374151; font-size: 0.7rem;">{{ $count }}</span>
                                    </div>
                                @endforeach
                                @if(count($month['sites']) > 3)
                                    <div style="font-size: 0.65rem; color: #9ca3af; margin-top: 0.15rem;">
                                        +{{ count($month['sites']) - 3 }} more site{{ count($month['sites']) - 3 > 1 ? 's' : '' }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
