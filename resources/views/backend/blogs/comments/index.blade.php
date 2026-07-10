<!doctype html>
<html lang="en">
<head>
    @include('components.backend.head')
</head>
<body>
    @include('components.backend.header')
    @include('components.backend.sidebar')

    @php
        $grouped = $comments->groupBy(fn ($c) => optional($c->blogListing)->id ?? 0);
    @endphp

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
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                        <li class="breadcrumb-item">Blog</li>
                                        <li class="breadcrumb-item active">Comments</li>
                                    </ol>
                                </nav>

                                <form method="GET" class="d-flex align-items-center gap-2" style="min-width:260px;">
                                    <label for="blog-filter" class="form-label mb-0 text-muted small">Filter by blog:</label>
                                    <select class="form-select form-select-sm" id="blog-filter" name="blog" onchange="this.form.submit()">
                                        <option value="">All blogs</option>
                                        @foreach($blogs as $b)
                                            <option value="{{ $b->id }}" {{ (string) $blogFilter === (string) $b->id ? 'selected' : '' }}>
                                                {{ \Illuminate\Support\Str::limit($b->title, 60) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>

                                <span class="badge bg-primary">{{ $comments->count() }} Total</span>
                            </div>

                            @forelse($grouped as $blogId => $group)
                                @php $blog = $group->first()->blogListing ?? null; @endphp

                                <div class="comment-group-card mb-4">
                                    <div class="comment-group-head">
                                        <div class="comment-group-head-text">
                                            <h5 class="comment-group-title">
                                                @if($blog)
                                                    {{ $blog->title }}
                                                @else
                                                    <span class="text-muted">Blog removed</span>
                                                @endif
                                            </h5>
                                            @if($blog && !$blog->trashed())
                                                <a href="{{ route('frontend.blog_details', $blog->slug) }}" target="_blank" class="comment-group-link">View on site &rarr;</a>
                                            @elseif($blog && $blog->trashed())
                                                <span class="comment-group-link text-muted">Post was deleted</span>
                                            @endif
                                        </div>
                                        <span class="comment-group-count">{{ $group->count() }} {{ $group->count() === 1 ? 'comment' : 'comments' }}</span>
                                    </div>

                                    <div class="comment-group-list">
                                        @foreach($group as $c)
                                            <div class="comment-row">
                                                <div class="comment-row-avatar">{{ strtoupper(substr(trim($c->name), 0, 1)) ?: '?' }}</div>
                                                <div class="comment-row-main">
                                                    <div class="comment-row-head">
                                                        <div class="comment-row-meta">
                                                            <strong class="comment-row-name">{{ $c->name }}</strong>
                                                            <a href="mailto:{{ $c->email }}" class="comment-row-email">{{ $c->email }}</a>
                                                            <span class="comment-row-date">{{ optional($c->created_at)->format('d M Y · h:i A') }}</span>
                                                            @if($c->is_active)
                                                                <span class="badge bg-success comment-row-badge">Live</span>
                                                            @else
                                                                <span class="badge bg-secondary comment-row-badge">Hidden</span>
                                                            @endif
                                                        </div>
                                                        <div class="comment-row-actions">
                                                            <form action="{{ route('manage-blog-comments.toggle', $c->id) }}" method="POST" class="m-0">
                                                                @csrf @method('PATCH')
                                                                <button type="submit" class="btn btn-sm {{ $c->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                                                    {{ $c->is_active ? 'Hide' : 'Show' }}
                                                                </button>
                                                            </form>
                                                            <form action="{{ route('manage-blog-comments.destroy', $c->id) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this comment permanently?');">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <div class="comment-row-body">{{ $c->comment }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">No comments posted yet.</div>
                            @endforelse

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
