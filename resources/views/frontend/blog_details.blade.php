<!DOCTYPE html>
<html lang="en">
  <head>
    @include('components.frontend.head')
  </head>
  <body>

    @include('components.frontend.header')

    <main class="fix">


    <!-- Breadcrumb -->
        <section class="breadcrumb contact-us-breadcrumb-bg">
            <div class="breadcrumb-img-custom-sec">
                @if($settings && $settings->banner_image)
                    <img src="{{ asset('home/blog/banner/'.$settings->banner_image) }}" alt="">
                @else
                    <img src="{{ asset('frontend/assets/img/banner/contact-new-banner.webp') }}" alt="">
                @endif
            </div>
            <div class="container">
                <h1 class="breadcrumb-title">{{ $settings->banner_heading ?? 'Latest Blog' }}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li><a href="{{ route('frontend.blogs') }}">Blog</a></li>
                </ul>
            </div>
        </section>

        <!-- blog-details-area -->
        <section class="blog__details-area pt-100 pb-100">
            <div class="container">
                <div class="row">

                    <div class="col-xl-9 col-lg-8">
                        <div class="blog__details-wrap">
                            <div class="blog__details-thumb">
                                @if(optional($listing->detail)->image)
                                    <img src="{{ asset('home/blog/details/'.$listing->detail->image) }}" alt="{{ $listing->title }}">
                                @elseif($listing->thumbnail)
                                    <img src="{{ asset('home/blog/thumbnails/'.$listing->thumbnail) }}" alt="{{ $listing->title }}">
                                @else
                                    <img src="{{ asset('frontend/assets/img/gallery/event-gallery-img-1.webp') }}" alt="img">
                                @endif
                            </div>
                            <div class="blog__details-content">
                                <h2 class="title">{{ $listing->title }}</h2>

                                @if(optional($listing->detail)->information)
                                    {!! $listing->detail->information !!}
                                @elseif($listing->short_description)
                                    {!! $listing->short_description !!}
                                @else
                                    <p class="text-muted"><em>The detail content for this blog hasn't been added yet.</em></p>
                                @endif

                                <div class="blog__details-content-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-7">
                                            @if($listing->tags->count())
                                                <div class="post-tags">
                                                    <h5 class="title">Tags:</h5>
                                                    <ul class="list-wrap">
                                                        @foreach($listing->tags as $tag)
                                                            <li><a href="{{ route('frontend.blogs') }}" class="js-detail-filter" data-tag="{{ $tag->tag }}">{{ $tag->tag }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-5">
                                            @if($listing->detail && $listing->detail->socialLinks->count())
                                                <div class="blog-post-share">
                                                    <h5 class="title">Share:</h5>
                                                    <ul class="list-wrap">
                                                        @foreach($listing->detail->socialLinks as $link)
                                                            <li><a href="{{ $link->url }}" target="_blank" title="{{ $link->platform_label }}"><i class="{{ $link->icon_class }}"></i></a></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- Existing comments --}}
                        <div class="comments-list-wrap" id="comments">
                            <h3 class="comment-reply-title">
                                <span id="commentCount">{{ $listing->comments->count() }}</span>
                                <span id="commentCountLabel">Comment{{ $listing->comments->count() === 1 ? '' : 's' }}</span>
                            </h3>

                            <div id="commentFlash" class="comment-flash" style="display:none;" role="alert"></div>

                            <div id="commentList">
                                @forelse($listing->comments as $c)
                                    @include('frontend.partials.blog_comment', ['c' => $c])
                                @empty
                                    <p class="comment-empty" id="commentEmpty">Be the first to comment on this post.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- New comment form --}}
                        <div class="comment-respond mt-5">
                            <h3 class="comment-reply-title">Post a comment</h3>
                            <form id="blogCommentForm" action="{{ route('frontend.blog_comment.store', $listing->slug) }}" method="POST" class="comment-form" novalidate>
                                @csrf
                                <p class="comment-notes">Your email address will not be published. Required fields are marked *</p>

                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert" style="margin-bottom:16px;">
                                        <ul style="margin:0; padding-left:18px;">
                                            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="form-grp">
                                    <textarea name="comment" id="bc_comment" placeholder="Comment*">{{ old('comment') }}</textarea>
                                    <small class="error-msg" id="bcerr_comment"></small>
                                </div>
                                <div class="row gutter-20">
                                    <div class="col-md-4">
                                        <div class="form-grp">
                                            <input type="text" name="name" id="bc_name" placeholder="Name*" value="{{ old('name') }}" maxlength="100">
                                            <small class="error-msg" id="bcerr_name"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-grp">
                                            <input type="email" name="email" id="bc_email" placeholder="Email*" value="{{ old('email') }}" maxlength="150">
                                            <small class="error-msg" id="bcerr_email"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-grp">
                                            <input type="url" name="website" id="bc_website" placeholder="Website (optional)" value="{{ old('website') }}" maxlength="255">
                                            <small class="error-msg" id="bcerr_website"></small>
                                        </div>
                                    </div>
                                </div>

                                {{-- Honeypot: real users won't see this; bots often auto-fill every input. --}}
                                <div style="position:absolute; left:-9999px; top:-9999px;" aria-hidden="true">
                                    <label>Do not fill this out<input type="text" name="website_url" tabindex="-1" autocomplete="off"></label>
                                </div>

                                <button type="submit" class="btn" id="blogCommentBtn">Post Comment <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}" alt="" class="injectable"></button>
                            </form>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-4">
                        <aside class="blog-sidebar">
                            <div class="blog-widget">
                                <h4 class="widget-title">Search</h4>
                                <div class="sidebar-search-form">
                                    <form id="js-detail-search-form" action="{{ route('frontend.blogs') }}" method="GET">
                                        <input type="text" id="js-detail-search-input" placeholder="Type Keywords. . .">
                                        <button type="submit"><i class="flaticon-loupe"></i></button>
                                    </form>
                                </div>
                            </div>
                            @if($categories->count())
                                <div class="blog-widget">
                                    <h4 class="widget-title">Categories</h4>
                                    <div class="sidebar-cat-list">
                                        <ul class="list-wrap">
                                            @foreach($categories as $category)
                                                <li><a href="{{ route('frontend.blogs') }}" class="js-detail-filter" data-category="{{ $category->slug }}">{{ $category->name }} ({{ str_pad($category->listings_count, 2, '0', STR_PAD_LEFT) }})</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                            @if($recentPosts->count())
                                <div class="blog-widget">
                                    <h4 class="widget-title">Recent Post</h4>
                                    <div class="rc-post-wrap">
                                        @foreach($recentPosts as $post)
                                            @php $postHref = route('frontend.blog_details', $post->slug); @endphp
                                            <div class="rc-post-item">
                                                <div class="thumb">
                                                    <a href="{{ $postHref }}">
                                                        @if($post->thumbnail)
                                                            <img src="{{ asset('home/blog/thumbnails/'.$post->thumbnail) }}" alt="{{ $post->title }}">
                                                        @else
                                                            <img src="{{ asset('frontend/assets/img/gallery/event-gallery-img-1.webp') }}" alt="img">
                                                        @endif
                                                    </a>
                                                </div>
                                                <div class="content">
                                                    <h4 class="title"><a href="{{ $postHref }}">{{ $post->title }}</a></h4>
                                                    <span class="date"><i class="flaticon-calendar"></i>{{ optional($post->blog_date)->format('M d, Y') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if($tags->count())
                                <div class="blog-widget">
                                    <h4 class="widget-title">Tags</h4>
                                    <div class="sidebar-tag-list">
                                        <ul class="list-wrap">
                                            @foreach($tags as $tag)
                                                <li><a href="{{ route('frontend.blogs') }}" class="js-detail-filter" data-tag="{{ $tag }}">{{ $tag }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </aside>
                    </div>



                </div>
            </div>
        </section>
        <!-- blog-details-area-end -->

    </main>

    @include('components.frontend.footer')
    @include('components.frontend.main-js')

    <script>
        (function () {
            var el = document.getElementById('commentSuccessAlert');
            if (!el) return;
            setTimeout(function () { el.classList.add('is-hiding'); }, 4000);
            setTimeout(function () { el.remove(); }, 4600);
        })();
    </script>

    <script>
        (function () {
            // Filter clicks: stash the intent in sessionStorage, then let the anchor navigate to /blog.
            // The listing page reads this on load and applies the filter without ever touching the URL.
            document.addEventListener('click', function (e) {
                var el = e.target.closest('.js-detail-filter');
                if (!el) return;
                var filter = {};
                if (el.dataset.category) { filter.category = el.dataset.category; }
                if (el.dataset.tag)      { filter.tag      = el.dataset.tag;      }
                if (Object.keys(filter).length) {
                    try { sessionStorage.setItem('pendingBlogFilter', JSON.stringify(filter)); } catch (err) {}
                }
                // href already points to /blog — allow default navigation.
            });

            // Search form: same treatment, but intercept to prevent ?search=... appearing in the URL.
            var $form  = document.getElementById('js-detail-search-form');
            var $input = document.getElementById('js-detail-search-input');
            if ($form && $input) {
                $form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var v = ($input.value || '').trim();
                    try {
                        if (v) sessionStorage.setItem('pendingBlogFilter', JSON.stringify({ search: v }));
                        else   sessionStorage.removeItem('pendingBlogFilter');
                    } catch (err) {}
                    window.location.href = @json(route('frontend.blogs'));
                });
            }
        })();
    </script>

    <script>
    (function () {
        var form = document.getElementById('blogCommentForm');
        if (!form) return;
        var btn = document.getElementById('blogCommentBtn');
        var btnHtml = btn ? btn.innerHTML : '';
        var $list  = document.getElementById('commentList');
        var $flash = document.getElementById('commentFlash');
        var $count = document.getElementById('commentCount');
        var $cLbl  = document.getElementById('commentCountLabel');

        function show(id, msg) {
            var e = document.getElementById('bcerr_' + id);
            var i = form.querySelector('[name="' + id + '"]');
            if (e) e.textContent = msg;
            if (i) i.classList.add('is-invalid');
        }
        function clearErr(id) {
            var e = document.getElementById('bcerr_' + id);
            var i = form.querySelector('[name="' + id + '"]');
            if (e) e.textContent = '';
            if (i) i.classList.remove('is-invalid');
        }
        function clearAllErrors() { ['name','email','website','comment'].forEach(clearErr); }

        ['name','email','website','comment'].forEach(function (id) {
            var i = form.querySelector('[name="' + id + '"]');
            if (i) i.addEventListener('input', function () { clearErr(id); });
        });

        function validateClient() {
            clearAllErrors();
            var v = function (n) { var i = form.querySelector('[name="' + n + '"]'); return i ? (i.value || '').trim() : ''; };
            var errs = 0;

            var name = v('name');
            if (name === '') { show('name', 'Please enter your name.'); errs++; }
            else if (!/^[A-Za-z\s.'\-]+$/.test(name)) { show('name', 'Name cannot contain numbers or special characters.'); errs++; }

            var email = v('email');
            if (email === '') { show('email', 'Please enter your email.'); errs++; }
            else if (!/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/.test(email)) { show('email', 'Please enter a valid email address.'); errs++; }

            var website = v('website');
            if (website !== '' && !/^https?:\/\/.+\..+/i.test(website)) {
                show('website', 'Website must start with http:// or https://'); errs++;
            }

            var comment = v('comment');
            if (comment === '') { show('comment', 'Please enter a comment.'); errs++; }
            else if (comment.length < 3) { show('comment', 'Comment is too short.'); errs++; }

            return errs === 0;
        }

        function showFlash(msg, type) {
            if (!$flash) return;
            $flash.className = 'comment-flash comment-flash--' + (type || 'success');
            $flash.textContent = msg;
            $flash.style.display = 'block';
            $flash.classList.remove('is-hiding');
            // Auto-dismiss after 4s
            setTimeout(function () { $flash.classList.add('is-hiding'); }, 4000);
            setTimeout(function () { $flash.style.display = 'none'; }, 4600);
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!validateClient()) return;

            if (btn) { btn.disabled = true; btn.innerHTML = 'Posting…'; }

            var fd = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: fd
            })
            .then(function (r) {
                return r.json().then(function (data) { return { status: r.status, data: data }; });
            })
            .then(function (res) {
                if (res.status === 422 && res.data && res.data.errors) {
                    // Server-side validation errors — mirror to fields.
                    var errs = res.data.errors;
                    Object.keys(errs).forEach(function (k) {
                        show(k, Array.isArray(errs[k]) ? errs[k][0] : String(errs[k]));
                    });
                    return;
                }
                if (res.status === 429) {
                    showFlash('Too many comments in a short time. Please wait a few minutes and try again.', 'error');
                    return;
                }
                if (res.status >= 200 && res.status < 300 && res.data && res.data.success) {
                    // Silent honeypot hit: just reset the form.
                    if (res.data.silent) {
                        form.reset();
                        return;
                    }
                    // Insert new comment at top of the list.
                    if (res.data.html && $list) {
                        var wrap = document.createElement('div');
                        wrap.innerHTML = res.data.html.trim();
                        var node = wrap.firstElementChild;
                        // Remove the empty state if present.
                        var empty = document.getElementById('commentEmpty');
                        if (empty) empty.remove();
                        if (node) {
                            node.classList.add('is-just-added');
                            $list.insertBefore(node, $list.firstChild);
                        }
                    }
                    // Update count.
                    if (typeof res.data.count === 'number' && $count) {
                        $count.textContent = res.data.count;
                        if ($cLbl) $cLbl.textContent = res.data.count === 1 ? 'Comment' : 'Comments';
                    }
                    // Reset form + flash.
                    form.reset();
                    clearAllErrors();
                    showFlash('Thanks for your comment — it’s live below.', 'success');
                    // Scroll to the flash so the user sees confirmation.
                    if ($flash) $flash.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
                showFlash('Something went wrong. Please try again.', 'error');
            })
            .catch(function () {
                showFlash('Network error. Please check your connection and try again.', 'error');
            })
            .finally(function () {
                if (btn) { btn.disabled = false; btn.innerHTML = btnHtml; }
            });
        });
    })();
    </script>
  </body>
</html>
