<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    <div class="page-body">
        <div class="container-fluid">
            <div class="page-title">
                <div class="row">
                    <div class="col-6"></div>
                    <div class="col-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">
                                    <svg class="stroke-icon">
                                        <use href="../assets/svg/icon-sprite.svg#stroke-home"></use>
                                    </svg>
                                </a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                        <li class="breadcrumb-item">Form Enquiries</li>
                                        <li class="breadcrumb-item active">Appointment Enquiries</li>
                                    </ol>
                                </nav>
                                <span class="badge bg-primary">{{ $enquiries->count() }} Total</span>
                            </div>

                            <div class="table-responsive custom-scrollbar">
                                <table class="display" id="basic-1">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Submitted</th>
                                            <th>Owner</th>
                                            <th>Mobile</th>
                                            <th>Pet</th>
                                            <th>Consultation</th>
                                            <th>Appt. Date</th>
                                            <th class="text-end" style="min-width:120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($enquiries as $i => $enquiry)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ optional($enquiry->created_at)->format('d M Y, h:i A') }}</td>
                                                <td>{{ $enquiry->owner_name }}</td>
                                                <td>+91 {{ $enquiry->mobile }}</td>
                                                <td>{{ $enquiry->pet_name }} ({{ ucfirst($enquiry->pet_type) }})</td>
                                                <td>{{ $enquiry->consult_type === 'first' ? 'First-time' : 'Follow-up' }}</td>
                                                <td>{{ optional($enquiry->appointment_date)->format('d M Y') }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('manage-appointment-enquiries.show', $enquiry->id) }}" class="btn btn-sm btn-primary">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8" class="text-center text-muted py-4">No appointment enquiries received yet.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.backend.footer')
    @include('components.backend.main-js')
</body>
</html>
