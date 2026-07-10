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
                    {!! $listing->short_description ?? '' !!}
                </div>
                <a href="{{ $detailHref }}" class="btn">Read More <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                        alt="" class="injectable"></a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <p class="text-center text-muted py-5">No blog posts found. Try a different filter or clear the search.</p>
    </div>
@endforelse
