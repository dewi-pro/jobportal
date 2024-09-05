@if ($job->canShowApplyJob())
    @php($classButtonApply = $class ?? 'btn btn-apply-now')
    <div class="{{ $wrapClass ?? '' }}">
        @if ($job->is_applied)
            <button class="{{ $classButtonApply }} disabled" disabled>{{ __('Applied') }}</button>
        @elseif ($job->apply_url)
            <button class="{{ $classButtonApply }}"
                    data-bs-target="#ModalApplyExternalJobForm"
                    data-bs-toggle="modal"
                    data-job-name="{{ $job->name }}"
                    data-job-id="{{ $job->id }}"
            >
                {{ __('Apply Now') }}
            </button>
        @elseif (!auth('account')->check() && !JobBoardHelper::isGuestApplyEnabled())
            <a href="{{ route('public.account.login') }}">
                <div class="{{ $classButtonApply }}">{{ __('Apply Now') }}</div>
            </a>
        @else
            <button class="{{ $classButtonApply }}"
                    data-job-name="{{ $job->name }}"
                    data-job-id="{{ $job->id }}"
                    data-bs-toggle="modal"
                    data-bs-target="#ModalApplyJobForm"
            >
                {{ __('Apply Now') }}
            </button>
        @endif
    </div>
@endif
