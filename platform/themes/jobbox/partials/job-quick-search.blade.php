<div class="quick-search-result">
    @foreach($jobs as $job)
        <div class="quick-search-result__item">
            <div class="quick-search-result__item__image">
                <a href="{{ $job->company->url }}">
                    <img src="{{ RvMedia::getImageUrl($job->company->logo) }}" alt="{{ $job->company->name }}">
                </a>
            </div>
            <div class="quick-search-result__item__content text-truncate">
                <h3 class="quick-search-result__item__content__title text-truncate">
                    <a href="{{ $job->url }}">{{ $job->name }}</a>
                </h3>
                <div class="quick-search-result__item__content__location">
                    <i class="fa fa-map-marker"></i>
                    {{ $job->location }}
                </div>
            </div>
        </div>
    @endforeach
</div>
