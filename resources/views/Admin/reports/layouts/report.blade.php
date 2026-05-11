@extends('layouts.app')

@php
$reportTitle = $reportTitle ?? 'Report';
$reportRoute = Route::currentRouteName();
$reportKey = str_replace(['dashboard.reports.', '.report', '_report'], '', $reportRoute);
$reportKeyHyphen = str_replace('_', '-', $reportKey);
@endphp

@section('title', $reportTitle)

@section('content')
    <main class="u-white-bg bg-white p-3" style="border-radius:15px;">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-0">{{ $reportTitle }}</h2>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="openPrintModal('{{ $reportKeyHyphen }}')" class="btn btn-primary">
                        <i class="fas fa-print"></i> {{ __('dashboard.print') }}
                    </button>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.date_from') }}</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('dashboard.date_to') }}</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> {{ __('dashboard.search') }}
                            </button>
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @yield('report_content')
        </div>
    </main>

    <!-- Print Modal -->
    <div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header no-print">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('en')">English</button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('ar')">العربية</button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchPrintLang('both')">Both</button>
                    </div>
                    <div class="btn-group ms-3" role="group">
                        <button type="button" class="btn btn-primary" onclick="printFromIframe()"><i class="fas fa-print"></i> Print</button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="min-height: 500px;">
                    <iframe id="printIframe" src="" style="width: 100%; height: 70vh; border: 1px solid #ccc;" onerror="showIframeError('Iframe failed to load')"></iframe>
                    <div id="iframeError" class="alert alert-danger m-3 d-none"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const reportPrintRouteTemplate = @json(route('dashboard.reports.print', ['reportType' => '__REPORT__'], false));

    function showIframeError(msg) {
        const errDiv = document.getElementById('iframeError');
        if (errDiv) {
            errDiv.textContent = msg;
            errDiv.classList.remove('d-none');
        }
    }

    function openPrintModal(reportKey) {
        const currentUrl = new URL(window.location.href);
        const printUrl = reportPrintRouteTemplate.replace('__REPORT__', reportKey) + '?' + currentUrl.searchParams.toString();
        console.log('Opening print modal with URL:', printUrl);
        
        const iframe = document.getElementById('printIframe');
        const errDiv = document.getElementById('iframeError');
        if (errDiv) errDiv.classList.add('d-none');
        
        iframe.src = printUrl;
        
        var modal = new bootstrap.Modal(document.getElementById('printModal'));
        modal.show();

        iframe.onload = function() {
            console.log('Iframe loaded');
            setTimeout(function() {
                switchPrintLang('en');
            }, 500);
        };

        iframe.onerror = function() {
            showIframeError('Failed to load print content');
        };
    }

    function switchPrintLang(lang) {
        const iframe = document.getElementById('printIframe');
        console.log('Switching language to:', lang);
        try {
            if (iframe && iframe.contentWindow) {
                if (typeof iframe.contentWindow.switchLanguage === 'function') {
                    iframe.contentWindow.switchLanguage(lang);
                } else {
                    console.log('switchLanguage function not found in iframe');
                }
            }
        } catch(e) {
            console.log('Error switching language:', e);
        }
    }

    function printFromIframe() {
        const iframe = document.querySelector('#printIframe');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.print();
        }
    }
</script>
@endpush
