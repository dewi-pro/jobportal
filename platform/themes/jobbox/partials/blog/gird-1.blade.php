<div class="row">
    @foreach (get_featured_posts(3) as $post)
        <div class="col-lg-4 col-md-6 col-sm-12 col-12">
            <div class="card-grid-5">
                <div class="card-grid-5 hover-up" style="background-image: url('{{ RvMedia::getImageUrl($post->image, null, false, RvMedia::getDefaultImage()) }}')">
                    <a href="{{ $post->url }}">
                        <div class="box-cover-img">
                            <div class="content-bottom">
                                <h3 class="color-white mb-20">{{ $post->name }}</h3>
                                <div class="author d-flex align-items-center mr-20">
                                    @if ($post->author && $post->author->id)
                                        <img class="mr-10" alt="{{ $post->author->name }}" src="{{ $post->author->avatar_url }}">
                                        <span class="color-white font-sm mr-25">{{ $post->author->name }}</span>
                                    @endif
                                    <span class="color-white font-sm">{{ $post->created_at->translatedFormat('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
