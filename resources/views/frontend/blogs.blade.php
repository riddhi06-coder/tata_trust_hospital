
<!DOCTYPE html>
<html lang="en">
  <head>

    @include('components.frontend.head')

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
                        <div class="row">
                            @forelse($listings as $listing)
                                @php $detailHref = route('frontend.blog_details', $listing->slug); @endphp
                                <div class="col-md-6">
                                    <div class="blog__post-item-three blog__post-item-five shine-animate-item">
                                        <div class="blog__post-thumb-three blog__post-thumb-five shine-animate">
                                            <a href="{{ $detailHref }}">
                                                @if($listing->thumbnail)
                                                    <img src="{{ asset('home/blog/thumbnails/'.$listing->thumbnail) }}" alt="{{ $listing->title }}">
                                                @else
                                                    <img src="{{ asset('frontend/assets/img/gallery/blog-new-img-1.webp') }}" alt="{{ $listing->title }}">
                                                @endif
                                            </a>
                                        </div>
                                        <div class="blog__post-content-three">
                                            <h2 class="title"><a href="{{ $detailHref }}">{{ $listing->title }}</a></h2>
                                            <div class="blog-excerpt">
                                                {!! Str::limit(strip_tags($listing->short_description ?? ''), 180) !!}
                                            </div>
                                            <a href="{{ $detailHref }}" class="btn">Read More <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                                                    alt="" class="injectable"></a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-center text-muted py-5">No blog posts published yet. Please check back soon.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4">
                        <aside class="blog-sidebar">
                            <div class="blog-widget">
                                <h4 class="widget-title">Search</h4>
                                <div class="sidebar-search-form">
                                    <form action="#">
                                        <input type="text" placeholder="Type Keywords. . .">
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
                                                <li>
                                                    <a href="#">
                                                        {{ $category->name }} ({{ str_pad($category->listings_count, 2, '0', STR_PAD_LEFT) }})
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
                                            @foreach($tags as $tag)
                                                <li><a href="#">{{ $tag }}</a></li>
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

  </body>
</html>
