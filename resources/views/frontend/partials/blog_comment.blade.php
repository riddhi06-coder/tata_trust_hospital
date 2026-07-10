<div class="comment-item">
    <div class="comment-avatar">{{ strtoupper(substr(trim($c->name), 0, 1)) }}</div>
    <div class="comment-body">
        <div class="comment-head">
            <h5 class="comment-author">
                @if($c->website)
                    <a href="{{ $c->website }}" target="_blank" rel="nofollow noopener">{{ $c->name }}</a>
                @else
                    {{ $c->name }}
                @endif
            </h5>
            <span class="comment-date">{{ optional($c->created_at)->format('M d, Y') }}</span>
        </div>
        <p class="comment-text">{{ $c->comment }}</p>
    </div>
</div>
