{{-- Reusable period filters: Year, Month, and an optional custom From/To range.
     Custom range (if set) takes precedence over Year/Month in the controller. --}}
<div class="col-lg-2 col-md-4 col-6">
    <label class="form-label small fw-semibold mb-1">Year</label>
    <select name="year" class="form-select form-select-sm js-auto-filter">
        <option value="">All</option>
        @foreach($years as $y)
            <option value="{{ $y }}" {{ (string) $filters['year'] === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
    </select>
</div>
<div class="col-lg-2 col-md-4 col-6">
    <label class="form-label small fw-semibold mb-1">Month</label>
    <select name="month" class="form-select form-select-sm js-auto-filter">
        <option value="">All</option>
        @foreach(range(1, 12) as $m)
            <option value="{{ $m }}" {{ (string) $filters['month'] === (string) $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}</option>
        @endforeach
    </select>
</div>
<div class="col-lg-2 col-md-4 col-6">
    <label class="form-label small fw-semibold mb-1">From (custom)</label>
    <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control form-control-sm js-auto-filter">
</div>
<div class="col-lg-2 col-md-4 col-6">
    <label class="form-label small fw-semibold mb-1">To (custom)</label>
    <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control form-control-sm js-auto-filter">
</div>
