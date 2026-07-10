
<!DOCTYPE html>
<html lang="en">
  <head>

    @include('components.frontend.head')

    <style>
        .blog-grid-container { position: relative; min-height: 240px; }
        .blog-loader-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.75);
            z-index: 5;
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
        .blog-loader-overlay.is-loading { display: flex; }
        .blog-spinner {
            width: 56px; height: 56px;
            border: 6px solid #e2e8f0;
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: blog-spin 0.9s linear infinite;
        }
        @keyframes blog-spin { to { transform: rotate(360deg); } }

        .sidebar-cat-list li a.is-active,
        .sidebar-tag-list li a.is-active,
        .blog__post-tag li a.is-active { font-weight: 700; color: #6366f1; }
        .filter-chip {
            display: inline-flex; align-items: center; gap: 6px;
            background: #eef2ff; color: #1e3a8a;
            padding: 4px 10px; border-radius: 999px;
            font-size: 0.85rem; font-weight: 600;
        }
        .filter-chip .chip-clear { cursor: pointer; opacity: 0.7; }
        .filter-chip .chip-clear:hover { opacity: 1; }
    </style>

  </head>
  <body>

    @include('components.frontend.header')


    <!-- main-area -->
    <main class="fix">


     <!-- Breadcrumb -->
        <section class="breadcrumb contact-us-breadcrumb-bg">
            <div class="breadcrumb-img-custom-sec">
                @if($settings && $settings->banner_image)
                    <img src="{{ asset('home/blog/banner/'.$settings->banner_image) }}" alt="">
                @else
                    <img src="{{ asset('assets/img/banner/contact-new-banner.webp1') }}" alt="">
                @endif
            </div>
            <div class="container">
                <h1 class="breadcrumb-title">{{ $settings->banner_heading ?? 'Latest Blog' }}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>Blog</li>
                </ul>
            </div>
        </section>



        <!-- blog-area -->
        <section class="blog__area pt-100 pb-100">
            <div class="container">
                <div class="row">
                    <div class="col-xl-9 col-lg-8 order-0 order-lg-2">

                        {{-- Active filter chip / clear-all --}}
                        <div id="active-filter-bar" class="mb-3" style="{{ ($search || $category || $tag) ? '' : 'display:none;' }}">
                            <span class="text-muted me-2">Showing:</span>
                            <span id="active-filter-chip" class="filter-chip">
                                <span id="active-filter-label">
                                    @if($search) Search: “{{ $search }}”
                                    @elseif($category) Category: {{ optional($categories->firstWhere('slug', $category))->name ?? $category }}
                                    @elseif($tag) Tag: {{ $tag }}
                                    @endif
                                </span>
                                <a href="#" class="chip-clear" id="js-filter-reset" title="Clear filter">&times;</a>
                            </span>
                        </div>

                        <div class="blog-grid-container">
                            <div id="blog-loader" class="blog-loader-overlay">
                                <div class="blog-spinner"></div>
                            </div>

                            <div id="blog-grid" class="row">
                                @include('frontend.partials.blog_cards', ['listings' => $listings])
                            </div>
                        </div>

                    </div>
                    <div class="col-xl-3 col-lg-4">
                        <aside class="blog-sidebar">
                            <div class="blog-widget">
                                <h4 class="widget-title">Search</h4>
                                <div class="sidebar-search-form">
                                    <form id="js-blog-search-form" action="{{ route('frontend.blogs') }}" method="GET">
                                        <input type="text" name="search" id="js-blog-search-input"
                                               placeholder="Type Keywords. . ." value="{{ $search }}">
                                        <button type="submit"><i class="flaticon-loupe"></i></button>
                                    </form>
                                </div>
                            </div>
                            @if($categories->count())
                                <div class="blog-widget">
                                    <h4 class="widget-title">Categories</h4>
                                    <div class="sidebar-cat-list">
                                        <ul class="list-wrap">
                                            @foreach($categories as $cat)
                                                <li>
                                                    <a href="#" class="js-category-filter {{ $category === $cat->slug ? 'is-active' : '' }}"
                                                       data-category="{{ $cat->slug }}">
                                                        {{ $cat->name }} ({{ str_pad($cat->listings_count, 2, '0', STR_PAD_LEFT) }})
                                                    </a>
                                                </li>
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
                                                            <img src="{{ asset('frontend/assets/img/gallery/blog-new-img-1.webp') }}" alt="{{ $post->title }}">
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
                                            @foreach($tags as $t)
                                                <li>
                                                    <a href="#" class="js-tag-filter {{ $tag === $t ? 'is-active' : '' }}"
                                                       data-tag="{{ $t }}">{{ $t }}</a>
                                                </li>
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
        <!-- blog-area-end -->





    </main>
    <!-- main-area-end -->

    @include('components.frontend.footer')

    @include('components.frontend.main-js')

    <script>
        (function () {
            var BLOGS_URL = @json(route('frontend.blogs'));
            var $grid    = document.getElementById('blog-grid');
            var $loader  = document.getElementById('blog-loader');
            var $bar     = document.getElementById('active-filter-bar');
            var $label   = document.getElementById('active-filter-label');
            var $searchForm  = document.getElementById('js-blog-search-form');
            var $searchInput = document.getElementById('js-blog-search-input');

            var state = {
                search:   @json($search),
                category: @json($category),
                tag:      @json($tag),
            };

            function activeFilterText() {
                if (state.search)   return 'Search: “' + state.search + '”';
                if (state.category) {
                    var el = document.querySelector('.js-category-filter[data-category="' + state.category + '"]');
                    var label = el ? el.textContent.trim() : state.category;
                    // Strip trailing "(NN)" count from label.
                    label = label.replace(/\s*\(\d+\)\s*$/, '');
                    return 'Category: ' + label;
                }
                if (state.tag)      return 'Tag: ' + state.tag;
                return '';
            }

            function refreshFilterBar() {
                var text = activeFilterText();
                if (text) {
                    $label.textContent = text;
                    $bar.style.display = '';
                } else {
                    $bar.style.display = 'none';
                }
            }

            function refreshActiveClasses() {
                document.querySelectorAll('.js-category-filter').forEach(function (el) {
                    el.classList.toggle('is-active', el.dataset.category === state.category);
                });
                document.querySelectorAll('.js-tag-filter').forEach(function (el) {
                    el.classList.toggle('is-active', el.dataset.tag === state.tag);
                });
            }

            function updateURL() {
                if (!window.history || !window.history.replaceState) return;
                var qs = new URLSearchParams();
                if (state.search)   qs.set('search', state.search);
                if (state.category) qs.set('category', state.category);
                if (state.tag)      qs.set('tag', state.tag);
                var url = BLOGS_URL + (qs.toString() ? ('?' + qs.toString()) : '');
                window.history.replaceState({}, '', url);
            }

            function fetchBlogs() {
                $loader.classList.add('is-loading');

                var qs = new URLSearchParams({ partial: 1 });
                if (state.search)   qs.set('search', state.search);
                if (state.category) qs.set('category', state.category);
                if (state.tag)      qs.set('tag', state.tag);

                fetch(BLOGS_URL + '?' + qs.toString(), {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                    credentials: 'same-origin'
                })
                .then(function (r) { return r.text(); })
                .then(function (html) {
                    $grid.innerHTML = html;
                })
                .catch(function (err) {
                    console.error(err);
                    $grid.innerHTML = '<div class="col-12"><p class="text-center text-muted py-5">Something went wrong. Please try again.</p></div>';
                })
                .finally(function () {
                    $loader.classList.remove('is-loading');
                    refreshFilterBar();
                    refreshActiveClasses();
                    updateURL();
                    if (typeof AOS !== 'undefined' && AOS.refresh) { AOS.refresh(); }
                });
            }

            // Category clicks (event delegation).
            document.addEventListener('click', function (e) {
                var catEl = e.target.closest('.js-category-filter');
                if (catEl) {
                    e.preventDefault();
                    state.category = catEl.dataset.category || '';
                    state.tag      = '';
                    state.search   = '';
                    if ($searchInput) $searchInput.value = '';
                    fetchBlogs();
                    return;
                }
                var tagEl = e.target.closest('.js-tag-filter');
                if (tagEl) {
                    e.preventDefault();
                    state.tag      = tagEl.dataset.tag || '';
                    state.category = '';
                    state.search   = '';
                    if ($searchInput) $searchInput.value = '';
                    fetchBlogs();
                    return;
                }
                if (e.target.closest('#js-filter-reset')) {
                    e.preventDefault();
                    state = { search: '', category: '', tag: '' };
                    if ($searchInput) $searchInput.value = '';
                    fetchBlogs();
                    return;
                }
            });

            // Search form.
            if ($searchForm) {
                $searchForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    state.search   = ($searchInput.value || '').trim();
                    state.category = '';
                    state.tag      = '';
                    fetchBlogs();
                });
            }
        })();
    </script>

  </body>
</html>
