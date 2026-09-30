<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php $waDigits = preg_replace('/\D+/', '', $query->wa_id); @endphp

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"><h4>WhatsApp Query</h4></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('manage-whatsapp-queries.index') }}">WhatsApp Queries</a></li>
                            <li class="breadcrumb-item active">#{{ $query->id }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid enq-page">
            <div class="enq-header">
                <div class="enq-avatar"><i class="fab fa-whatsapp"></i></div>
                <div class="enq-header-meta">
                    <h1>{{ $query->name ?: 'WhatsApp User' }}</h1>
                    <p class="enq-header-sub">
                        <span>Talk to our team</span>
                        <span class="enq-sub-dot">·</span>
                        <span>{{ optional($query->created_at)->format('d M Y, h:i A') }}</span>
                    </p>
                </div>
                <div class="enq-header-ref">#{{ str_pad($query->id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>

            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="enq-card">
                        <div class="enq-card-title">Message</div>
                        <p class="enq-message-body">{{ $query->message }}</p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="enq-card">
                        <div class="enq-card-title">Quick Actions</div>
                        <div class="enq-actions d-grid gap-2">
                            <a href="https://wa.me/{{ $waDigits }}" target="_blank" class="btn btn-success">Reply on WhatsApp</a>
                            <a href="tel:{{ $waDigits }}" class="btn btn-outline-secondary">Call Sender</a>
                            <a href="{{ route('manage-whatsapp-queries.index') }}" class="btn btn-outline-secondary">Back to List</a>
                        </div>
                    </div>

                    <div class="enq-card">
                        <div class="enq-card-title">Metadata</div>
                        <ul class="enq-meta-list">
                            <li><span class="k">WhatsApp</span><span class="v enq-mono">{{ $query->wa_id }}</span></li>
                            <li><span class="k">Status</span><span class="v text-capitalize">{{ $query->status }}</span></li>
                            <li><span class="k">Received</span><span class="v">{{ optional($query->created_at)->format('d M Y, h:i A') }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')
</body>
</html>
