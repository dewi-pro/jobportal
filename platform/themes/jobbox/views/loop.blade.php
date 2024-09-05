<section class="section-box mt-50">
    <div class="container">
        @if ((theme_option('blog_page_template') === 'blog_gird_1'))
            {!! Theme::partial('blog.gird-1') !!}
        @else
            {!! Theme::partial('blog.gird-2') !!}
        @endif
    </div>
</section>
<section class="section-box mt-50">
    <div class="post-loop-grid">
        <div class="container">
            <div class="text-left">
                <h2 class="section-title mb-10 wow animate__animated animate__fadeInUp">{{ __('Latest Posts') }}</h2>
                <p class="font-lg color-text-paragraph-2 wow animate__animated animate__fadeInUp">{{ __('Don\'t miss the trending news') }}</p>
            </div>
            <div class="row mt-30">
                <div class="col-lg-8">
                    <div class="row">
                        @foreach ($posts as $post)
                            {!! Theme::partial('blog.box-post', ['post' => $post]) !!}
                        @endforeach
                    </div>
                    {!! $posts->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12 col-12 pl-40 pl-lg-15 mt-lg-30">
                    {!! dynamic_sidebar('blog_sidebar') !!}
                </div>
            </div>
        </div>
</section>

