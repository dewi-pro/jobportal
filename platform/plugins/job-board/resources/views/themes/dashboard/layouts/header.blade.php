@php
    $account = auth('account')->user();
@endphp

{!! SeoHelper::render() !!}

@include('core/base::components.layouts.header')

<link href="{{ asset('vendor/core/plugins/job-board/css/dashboard/style.css') }}" rel="stylesheet">

@if (BaseHelper::isRtlEnabled())
    <link href="{{ asset('vendor/core/plugins/job-board/css/dashboard/style-rtl.css') }}" rel="stylesheet">
@endif
