{{-- Renders KPI stat cards from an array of ['label','value','variant','icon']. --}}
<div class="row g-3 mb-1">
    @foreach($kpis as $kpi)
        <div class="col-xl-3 col-sm-6">
            <div class="dash-stat dash-stat--{{ $kpi['variant'] }}">
                <div>
                    <div class="dash-stat__num">{{ $kpi['value'] }}</div>
                    <div class="dash-stat__label">{{ $kpi['label'] }}</div>
                </div>
                <div class="dash-stat__icon"><i class="fa {{ $kpi['icon'] }}"></i></div>
            </div>
        </div>
    @endforeach
</div>
