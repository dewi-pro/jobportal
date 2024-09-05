<div>
    @php
        Theme::set('pageTitle', $category->name);
        Theme::set('pageDescription', Str::limit($category->description, 50));
    @endphp
    {!! Theme::partial('breadcrumbs') !!}
</div>
<section class="section-box mt-50">
    <div class="post-loop-grid">
        <div class="container">
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

