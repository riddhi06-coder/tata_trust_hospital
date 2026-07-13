{{-- Shared, high-contrast styling for appointment status badges + history.
     Uses !important to win over the admin theme's default .badge / text colours. --}}
<style>
    .status-badge{
        display:inline-block;
        padding:5px 12px;
        font-size:12px;
        font-weight:600;
        line-height:1.3;
        border-radius:6px;
        color:#ffffff !important;
        white-space:nowrap;
        letter-spacing:.2px;
    }
    .status-badge--soft{
        color:#374151 !important;
        background:#f3f4f6 !important;
        border:1px solid #d1d5db;
    }
    .status-note{
        margin-top:8px;
        padding:10px 12px;
        border-radius:8px;
        background:#f8fafc !important;
        border:1px solid #e5e7eb;
        color:#1f2937 !important;
        font-size:13px;
        line-height:1.55;
    }
    .status-hist{
        display:flex;
        gap:14px;
        padding-bottom:16px;
        margin-bottom:16px;
        border-bottom:1px solid #eef0f3;
    }
    .status-hist:last-child{border-bottom:0;margin-bottom:0;padding-bottom:0;}
    .status-hist__dot{width:11px;height:11px;border-radius:50%;margin-top:5px;flex:0 0 11px;}
    .status-hist__by{margin-top:6px;font-size:13px;color:#4b5563 !important;}
    .status-hist__by strong{color:#111827 !important;font-weight:600;}
    .status-hist__arrow{margin:0 8px;color:#9ca3af !important;font-weight:600;}
    .status-hist__time{font-size:12px;color:#6b7280 !important;white-space:nowrap;}

    .appt-filter-panel{
        background:#f8fafc;
        border:1px solid #e9edf2;
        border-radius:10px;
        padding:14px 16px;
    }
    .appt-filter-panel .form-label{color:#475569 !important;}
    #apptResults{transition:opacity .15s ease;}
    #apptResultsWrap{min-height:120px;}
    .appt-loader{
        position:absolute;
        inset:0;
        display:flex;
        align-items:center;
        justify-content:center;
        background:rgba(255,255,255,.7);
        z-index:5;
        border-radius:8px;
    }
</style>
