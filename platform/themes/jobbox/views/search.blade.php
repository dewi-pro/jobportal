<div>
    @php
        Theme::set('pageTitle', __('Search'));
        Theme::set('pageDescription', '');
    @endphp
    {!! Theme::partial('breadcrumbs') !!}
</div>
<section class="section-box mt-50">
    <div class="post-loop-grid">
        <div class="container">
            <div class="row mt-30">
                <div class="col-lg-8">
                    <div class="row">
                        @forelse ($posts as $post)
                            {!! Theme::partial('blog.box-post', ['post' => $post]) !!}
                        @empty
                            <div class="job-empty">
                                <div class="text-center mt-2">
                                    <i class="fi fi-rr-sad text-3xl"></i>

                                    <h3 class="mt-2">{{ __('No Posts') }}</h3>

                                    <div class="mt-2 text-muted">
                                        {{ __('There are no posts found with your queries.') }}
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    @if ($posts->isNotEmpty())
                        {!! $posts->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) !!}
                    @endif
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12 col-12 pl-40 pl-lg-15 mt-lg-30">
                    {!! dynamic_sidebar('blog_sidebar') !!}
                </div>
            </div>
        </div>
</section>
