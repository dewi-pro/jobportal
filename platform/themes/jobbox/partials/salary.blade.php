@if ($job->hide_salary)
    <span class="text-muted">{{ __('Attractive') }}</span>
@elseif ($job->salary_from || $job->salary_to)
    @if ($job->salary_from && $job->salary_to)
        <span class="card-text-price" title="{{ format_price($job->salary_from) }} - {{ format_price($job->salary_to) }}">
            {{ format_price($job->salary_from) }} - {{ format_price($job->salary_to) }}
        </span>
    @elseif ($job->salary_from)
        <span class="card-text-price" title="{{ __('From :price', ['price' => format_price($job->salary_from)]) }}">
            {{ __('From :price', ['price' => format_price($job->salary_from)]) }}
        </span>
    @elseif ($job->salary_to)
        <span class="card-text-price" title="{{ __('Upto :price', ['price' => format_price($job->salary_to)]) }}">
            {{ __('Upto :price', ['price' => format_price($job->salary_to)]) }}
        </span>
    @endif
    <span class="text-muted">/{{ $job->salary_range->label() }}</span>
@else
    <span class="text-muted">{{ __('Attractive') }}</span>
@endif
