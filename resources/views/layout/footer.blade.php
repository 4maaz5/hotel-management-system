<div class="settingSidebar">
    <a href="javascript:void(0)" class="settingPanelToggle"> <i class="fa fa-spin fa-cog"></i>
    </a>
    <div class="settingSidebar-body ps-container ps-theme-default">
        <div class=" fade show active">
            <div class="setting-panel-header">{{ __('dashboard.setting_panel') }}
            </div>
            <div class="p-15 border-bottom">
                <h6 class="font-medium m-b-10">{{ __('dashboard.select_layout') }}</h6>
                <div class="selectgroup layout-color w-50">
                    <label class="selectgroup-item">
                        <input type="radio" name="value" value="1"
                            class="selectgroup-input-radio select-layout" checked>
                        <span class="selectgroup-button">{{ __('dashboard.light') }}</span>
                    </label>
                    <label class="selectgroup-item">
                        <input type="radio" name="value" value="2"
                            class="selectgroup-input-radio select-layout">
                        <span class="selectgroup-button">{{ __('dashboard.dark') }}</span>
                    </label>
                </div>
            </div>
            <div class="p-15 border-bottom">
                <h6 class="font-medium m-b-10">{{ __('dashboard.sidebar_color') }}</h6>
                <div class="selectgroup selectgroup-pills sidebar-color">
                    <label class="selectgroup-item">
                        <input type="radio" name="icon-input" value="1" class="selectgroup-input select-sidebar">
                        <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip"
                            data-original-title="Light Sidebar"><i class="fas fa-sun"></i></span>
                    </label>
                    <label class="selectgroup-item">
                        <input type="radio" name="icon-input" value="2" class="selectgroup-input select-sidebar"
                            checked>
                        <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip"
                            data-original-title="Dark Sidebar"><i class="fas fa-moon"></i></span>
                    </label>
                </div>
            </div>
            <div class="p-15 border-bottom">
                <h6 class="font-medium m-b-10">{{ __('dashboard.color_theme') }}</h6>
                <div class="theme-setting-options">
                    <ul class="choose-theme list-unstyled mb-0">
                        <li title="white" class="active">
                            <div class="white"></div>
                        </li>
                        <li title="cyan">
                            <div class="cyan"></div>
                        </li>
                        <li title="black">
                            <div class="black"></div>
                        </li>
                        <li title="purple">
                            <div class="purple"></div>
                        </li>
                        <li title="orange">
                            <div class="orange"></div>
                        </li>
                        <li title="green">
                            <div class="green"></div>
                        </li>
                        <li title="red">
                            <div class="red"></div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="p-15 border-bottom">
                <div class="theme-setting-options">
                    <label class="m-b-0">
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input"
                            id="mini_sidebar_setting">
                        <span class="custom-switch-indicator"></span>
                        <span class="control-label p-l-10">{{ __('dashboard.mini_sidebar') }}</span>
                    </label>
                </div>
            </div>
            <div class="p-15 border-bottom">
                <div class="theme-setting-options">
                    <label class="m-b-0">
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input"
                            id="sticky_header_setting">
                        <span class="custom-switch-indicator"></span>
                        <span class="control-label p-l-10">{{ __('dashboard.sticky_header') }}</span>
                    </label>
                </div>
            </div>
            <div class="mt-4 mb-4 p-3 align-center rt-sidebar-last-ele">
                <a href="#" class="btn btn-icon icon-left btn-primary btn-restore-theme">
                    <i class="fas fa-undo"></i> {{ __('dashboard.restore_default') }}
                </a>
            </div>
        </div>
    </div>
</div>
</div>
<input type="text" id="qrInput" style="opacity:0; position:absolute;" autofocus>
<audio id="thankYouAudio" src="{{ asset('audio/thankyou.wav') }}"></audio>
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script> --}}
<!-- General JS Scripts -->
<script src="{{ asset('js/app.min.js') }}"></script>
<!-- JS Libraies -->
{{-- <script src="{{ asset('bundles/apexcharts/apexcharts.min.js') }}"></script> --}}
<!-- Page Specific JS File -->
{{-- <script src="{{ asset('js/page/index.js') }}"></script> --}}
<!-- Template JS File -->
<script src="{{ asset('js/scripts.js') }}"></script>
<!-- Custom JS File -->
{{-- <script src="{{ asset('js/custom.js') }}"></script> --}}


<!-- JS Libraies -->
<!-- Page Specific JS File -->
{{-- <script src="{{ asset('bundles/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/buttons.flash.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/jszip.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/pdfmake.min.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/vfs_fonts.js') }}"></script>
<script src="{{ asset('bundles/datatables/export-tables/buttons.print.min.js') }}"></script>
<script src="{{ asset('js/page/datatables.js') }}"></script> --}}


<script>
    const qrInput = document.getElementById('qrInput');

    function keepFocus() {
        const active = document.activeElement;
        // Only focus if nothing else is active, or if active is the body
        if (active === document.body || active === qrInput) {
            qrInput.focus({
                preventScroll: true
            });
        }
    }

    setInterval(keepFocus, 500);

    qrInput.addEventListener('blur', () => setTimeout(keepFocus, 100));

    qrInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            const qrCode = qrInput.value.trim();
            qrInput.value = '';
            if (qrCode) submitQr(qrCode); // your POST function
            e.preventDefault();
        }
    });

    let isCooldown = false;

    async function submitQr(qrCode) {
        if (isCooldown) {
            console.log("Please wait before scanning again.");
            return;
        }

        try {
            const res = await fetch("{{ route('attendance.scan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    qr_code: qrCode
                })
            });

            const data = await res.json();
            console.log(data);

            if (data.status === 'success') {
                const audio = document.getElementById("thankYouAudio");
                if (audio) audio.play().catch(e => console.warn("Audio play failed:", e));

                isCooldown = true;
                setTimeout(() => {
                    isCooldown = false;
                }, 10000);
            }

        } catch (err) {
            console.error(err);
        }
    }


    //  Paste your notification
    function loadNotifications() {
        $.get('/notifications/unread').done(function(data) {
            const list = $('#notifList').empty();
            if (!data.length) {
                $('#notifCount').hide();
                $('#notifEmpty').show();
                return;
            }
            $('#notifEmpty').hide();
            $('#notifCount').text(data.length).show();

            data.forEach(n => {
                const html = `
                    <div class="notif-item p-2 border-bottom" data-id="${n.id}">
                        <div class="d-flex justify-content-between">
                            <div>${n.message}</div>
                            <small class="text-muted">${new Date(n.created_at).toLocaleString()}</small>
                        </div>
                        <div class="mt-1">
                            <a href="#" class="mark-read small text-primary">Mark read</a>
                        </div>
                    </div>`;
                list.append(html);
            });
        });
    }

    // Mark all notifications
    $(document).on('click', '#markAllRead', function(e) {
        e.preventDefault();

        $.post(`/notifications/mark-all-read`, {
            _token: $('meta[name="csrf-token"]').attr('content')
        }).done(function() {
            loadNotifications();
        }).fail(function(err) {
            console.error('Mark all read failed:', err.responseText);
        });
    });

    // Load notifications once when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadNotifications();
    });

    // setInterval(loadNotifications, 30000);
    $(document).ready(function() {
        setTimeout(function() {
            $(".top-right-alert").alert('close');
        }, 6000);
    });

    // Make sure this is in the main page, not in the partial
    function printBranchReport() {
        var reportContent = document.getElementById('branch-report').innerHTML;
        var originalContent = document.body.innerHTML;

        document.body.innerHTML = reportContent;
        window.print();
        document.body.innerHTML = originalContent;
        location.reload();
    }
</script>

</body>

</html>
