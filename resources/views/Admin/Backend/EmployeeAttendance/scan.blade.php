<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance Scanner</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon svg {
            width: 30px;
            height: 30px;
            fill: white;
        }

        #reader {
            width: 100% !important;
            border-radius: 12px;
            overflow: hidden;
            margin: 20px 0;
            border: 3px solid #f0f0f0;
        }

        /* Style the Start Scanning button */
        #reader button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border: none !important;
            padding: 12px 30px !important;
            border-radius: 8px !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4) !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        #reader button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6) !important;
        }

        #reader button:active {
            transform: translateY(0) !important;
        }

        /* Style the file input button if present */
        #reader input[type="file"] {
            display: none;
        }

        #reader label {
            background: #f8f9fa !important;
            color: #666 !important;
            border: 2px dashed #ddd !important;
            padding: 10px 20px !important;
            border-radius: 8px !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }

        #reader label:hover {
            border-color: #667eea !important;
            color: #667eea !important;
        }

        .status {
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            font-size: 14px;
            display: none;
        }

        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        .status.scanning {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
            display: block;
        }

        .instructions {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            text-align: left;
        }

        .instructions h3 {
            color: #333;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .instructions ul {
            list-style: none;
            padding: 0;
        }

        .instructions li {
            padding: 8px 0;
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .instructions li:before {
            content: "✓";
            color: #667eea;
            font-weight: bold;
            margin-right: 10px;
        }

        .loader {
            display: none;
            margin: 20px auto;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        /* Popup Modal Styles */
        .popup-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            animation: popIn 0.3s ease-out;
        }

        .popup-icon {
            font-size: 48px;
            margin-bottom: 20px;
        }

        .popup-success .popup-icon {
            color: #28a745;
        }

        .popup-error .popup-icon {
            color: #dc3545;
        }

        .popup-info .popup-icon {
            color: #17a2b8;
        }

        .popup-content h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 24px;
        }

        .popup-content p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .popup-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .popup-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .logout-btn {
            padding: 10px 14px;
            border-radius: 6px;
            font-weight: 600;
            display: flex;
            align-items: center;
            /* vertically centered */
            justify-content: center;
            /* horizontally centered */
            gap: 10px;
            color: #dc3545;
            transition: 0.25s ease-in-out;
            background: transparent;
            border: none;
            width: 100%;
        }

        .logout-btn i {
            font-size: 15px;
            opacity: 0.9;
            transition: 0.25s ease;
        }

        .logout-btn:hover {
            background: #ffe6e6;
            color: #b02a37;
        }

        .logout-btn:hover i {
            transform: translateX(-3px);
            opacity: 1;
        }



        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 25px;
            }

            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">
            <svg viewBox="0 0 24 24">
                <path
                    d="M3,11H5V13H3V11M11,5H13V9H11V5M9,11H13V15H11V13H9V11M15,11H17V13H19V11H21V13H19V15H21V19H19V21H17V19H13V21H11V17H15V15H17V13H15V11M19,19V15H17V19H19M15,3H21V9H15V3M17,5V7H19V5H17M3,3H9V9H3V3M5,5V7H7V5H5M3,15H9V21H3V15M5,17V19H7V17H5Z" />
            </svg>
        </div>

        <div class="header">
            <h1>{{ __('dashboard.employee_attendance') }}</h1>
            <p>{{ __('dashboard.scan_qr_code') }}</p>
        </div>

        <div id="reader"></div>

        <div class="loader" id="loader"></div>

        <div class="status" id="status"></div>

        <div class="instructions">
            <h3>{{ __('dashboard.instructions') }}:</h3>
            <ul>
                <li>{{ __('dashboard.allow_camera_access') }}</li>
                <li>{{ __('dashboard.position_qr_code') }}</li>
                <li>{{ __('dashboard.hold_steady') }}</li>
                <li>{{ __('dashboard.wait_for') }}</li>
            </ul>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="dropdown-divider"></div>

            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                {{ __('dashboard.logout') }}
            </button>
        </form>


    </div>
    <input type="text" id="qrInput" style="opacity:0; position:absolute;" autofocus>

    <!-- Popup Modal -->
    <div class="popup-modal" id="popupModal">
        <div class="popup-content" id="popupContent">
            <div class="popup-icon" id="popupIcon">✓</div>
            <h3 id="popupTitle">{{ __('dashboard.success') }}</h3>
            <p id="popupMessage">{{ __('dashboard.attendance_marked') }}</p>
            <button class="popup-btn" onclick="closePopup()">{{ __('dashboard.ok') }}</button>
        </div>
    </div>
    <audio id="thankYouAudio" src="/audio/thankyou.wav"></audio>

    <script>
        // Replace the entire script section with this simplified version:

        const statusDiv = document.getElementById('status');
        const loader = document.getElementById('loader');
        const popupModal = document.getElementById('popupModal');
        const popupContent = document.getElementById('popupContent');
        const popupIcon = document.getElementById('popupIcon');
        const popupTitle = document.getElementById('popupTitle');
        const popupMessage = document.getElementById('popupMessage');

        let isProcessing = false;

        function showPopup(title, message, type = 'success') {
            popupTitle.textContent = title;
            popupMessage.textContent = message;
            popupContent.className = `popup-content popup-${type}`;

            if (type === 'success') {
                popupIcon.textContent = '✓';
                popupIcon.style.color = '#28a745';
            } else if (type === 'error') {
                popupIcon.textContent = '✕';
                popupIcon.style.color = '#dc3545';
            } else {
                popupIcon.textContent = 'ℹ';
                popupIcon.style.color = '#17a2b8';
            }

            popupModal.style.display = 'flex';

            // Automatically hide after 5 seconds
            setTimeout(() => {
                closePopup();
            }, 3000);
        }

        function closePopup() {
            popupModal.style.display = 'none';
        }


        function showStatus(message, type) {
            statusDiv.textContent = message;
            statusDiv.className = `status ${type}`;
            statusDiv.style.display = 'block';

            if (type === 'success' || type === 'error') {
                setTimeout(() => {
                    statusDiv.style.display = 'none';
                }, 5000);
            }
        }

        function onScanSuccess(decodedText) {
            if (isProcessing) return;
            isProcessing = true;

            loader.style.display = 'block';
            showStatus('Processing QR code...', 'scanning');

            fetch("{{ route('attendance.scan') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        qr_code: decodedText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    loader.style.display = 'none';

                    if (data.status === 'success') {
                        const title = data.type === 'check_in' ? 'Check-in Successful' : 'Check-out Successful';
                        showPopup(title, data.message, 'success');
                        //  Play Thank You Audio
                        document.getElementById("thankYouAudio").play();

                        //  Show Thank You Message
                        showStatus('Thank you! Attendance recorded successfully.', 'success');
                    } else if (data.status === 'already_marked') {
                        showPopup('Attendance Already Marked', data.message, 'info');
                    } else if (data.status === 'error') {
                        showPopup('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Scan error:', error);
                    loader.style.display = 'none';
                    showPopup('Error', 'Failed to process QR code', 'error');
                })
                .finally(() => {
                    setTimeout(() => {
                        isProcessing = false;
                    }, 2000);
                });
        }

        function onScanError(error) {
            // Ignore common scanning errors
            if (!error.includes('No MultiFormat Readers') &&
                !error.includes('NotFoundException') &&
                !error.includes('QR code parse error')) {
                console.warn('Scan error:', error);
            }
        }

        // Initialize scanner
        document.addEventListener('DOMContentLoaded', function() {
            showStatus('Initializing camera...', 'scanning');

            const scanner = new Html5QrcodeScanner(
                "reader", {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                },
                false
            );

            scanner.render(onScanSuccess, onScanError);

            setTimeout(() => {
                showStatus('Ready to scan', 'scanning');
            }, 2000);

            // Close popup when clicking outside
            popupModal.addEventListener('click', function(e) {
                if (e.target === popupModal) {
                    closePopup();
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const qrInput = document.getElementById('qrInput');

            qrInput.addEventListener('change', function() {
                const qrCode = this.value.trim();
                if (!qrCode) return;

                // Clear input for next scan
                this.value = '';

                // Call your existing function directly
                onScanSuccess(qrCode);
            });

            // Keep input focused all the time
            qrInput.focus();
            qrInput.addEventListener('blur', () => qrInput.focus());
        });
    </script>
</body>

</html>
