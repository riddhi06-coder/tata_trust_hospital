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
                                                            <li><a href="#">{{ $tag->tag }}</a></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-5">
                                            @if(optional($listing->detail) && $listing->detail->socialLinks->count())
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


                        <div class="comment-respond">
                            <h3 class="comment-reply-title">Post a comment</h3>
                            <form action="#" class="comment-form">
                                <p class="comment-notes">Your email address will not be published. Required fields are
                                    marked *</p>
                                <div class="form-grp">
                                    <textarea name="comment" placeholder="Comment"></textarea>
                                </div>
                                <div class="row gutter-20">
                                    <div class="col-md-4">
                                        <div class="form-grp">
                                            <input type="text" placeholder="Name">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-grp">
                                            <input type="email" placeholder="Email">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-grp">
                                            <input type="url" placeholder="Website">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn">Read More <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                                        alt="" class="injectable"></button>
                            </form>
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
                                                <li><a href="#">{{ $category->name }} ({{ str_pad($category->listings_count, 2, '0', STR_PAD_LEFT) }})</a></li>
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
        <!-- blog-details-area-end -->

    </main>

    @include('components.frontend.footer')
    @include('components.frontend.main-js')
  </body>
</html>
