@php
    $chatCopy = app()->getLocale() === 'ar'
        ? [
            'launcher' => 'المساعد الذكي',
            'title' => 'مساعد الحجز الذكي',
            'subtitle' => 'توفر الغرف، الحجز، الإلغاء، وسياسات الفندق',
            'placeholder' => 'اكتب رسالتك هنا...',
            'send' => 'إرسال',
            'close' => 'إغلاق',
            'welcome' => 'مرحباً، أستطيع مساعدتك في التوفر، إنشاء الحجز، إلغاء الحجز، أو الإجابة عن سياسات الفندق.',
            'typing' => 'المساعد يكتب...',
        ]
        : [
            'launcher' => 'AI Assistant',
            'title' => 'Smart Booking Assistant',
            'subtitle' => 'Availability, booking, cancellation, and hotel policies',
            'placeholder' => 'Type your message...',
            'send' => 'Send',
            'close' => 'Close',
            'welcome' => 'Hello, I can help with availability, booking, cancellation, and hotel policies.',
            'typing' => 'Assistant is typing...',
        ];

    $toolCopy = [
        'title' => __('dashboard.please_select_dates'),
        'check_in' => __('dashboard.check_in_date'),
        'check_out' => __('dashboard.check_out_date'),
        'submit' => $chatCopy['send'],
        'missing_check_in' => __('dashboard.please_select_check_in_date'),
        'missing_check_out' => __('dashboard.please_select_check_out_date'),
        'invalid_range' => app()->getLocale() === 'ar'
            ? 'يجب أن يكون تاريخ الخروج بعد تاريخ الدخول.'
            : 'Check-out date must be after check-in date.',
    ];
@endphp

<style>
    .dashboard-chatbot {
        position: fixed;
        right: 28px;
        bottom: 96px;
        z-index: 1200;
    }

    html[dir="rtl"] .dashboard-chatbot {
        right: auto;
        left: 28px;
    }

    .dashboard-chatbot__launcher {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: none;
        border-radius: 999px;
        padding: 14px 18px;
        color: #fff;
        background: linear-gradient(135deg, #1e40af, #2563eb);
        box-shadow: 0 16px 32px rgba(37, 99, 235, 0.28);
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .dashboard-chatbot__launcher:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px rgba(37, 99, 235, 0.35);
    }

    .dashboard-chatbot__panel {
        position: absolute;
        right: 0;
        bottom: 72px;
        width: min(380px, calc(100vw - 32px));
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.25);
        transform: translateY(18px) scale(0.96);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.22s ease, transform 0.22s ease;
    }

    html[dir="rtl"] .dashboard-chatbot__panel {
        right: auto;
        left: 0;
    }

    .dashboard-chatbot.is-open .dashboard-chatbot__panel {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .dashboard-chatbot__header {
        padding: 18px 20px;
        color: #fff;
        background: linear-gradient(135deg, #0f172a, #1d4ed8);
    }

    .dashboard-chatbot__title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
    }

    .dashboard-chatbot__subtitle {
        margin-top: 4px;
        font-size: 0.82rem;
        color: rgba(255, 255, 255, 0.8);
    }

    .dashboard-chatbot__close {
        border: none;
        background: rgba(255, 255, 255, 0.16);
        color: #fff;
        width: 34px;
        height: 34px;
        border-radius: 50%;
    }

    .dashboard-chatbot__body {
        display: flex;
        flex-direction: column;
        height: 500px;
        max-height: calc(100vh - 160px);
        background: linear-gradient(180deg, #f8fafc, #eef4ff);
    }

    .dashboard-chatbot__messages {
        flex: 1;
        overflow-y: auto;
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .dashboard-chatbot__bubble {
        max-width: 86%;
        padding: 12px 14px;
        border-radius: 18px;
        line-height: 1.5;
        font-size: 0.92rem;
        white-space: pre-wrap;
        word-break: break-word;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }

    .dashboard-chatbot__bubble--assistant {
        align-self: flex-start;
        background: #fff;
        color: #1e293b;
        border-top-left-radius: 8px;
    }

    .dashboard-chatbot__bubble--user {
        align-self: flex-end;
        background: #2563eb;
        color: #fff;
        border-top-right-radius: 8px;
    }

    .dashboard-chatbot__typing {
        align-self: flex-start;
        color: #475569;
        font-size: 0.82rem;
        display: none;
    }

    .dashboard-chatbot__typing.is-visible {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .dashboard-chatbot__tools {
        padding: 0 14px 12px;
    }

    .dashboard-chatbot__tool-card {
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid #dbe5f0;
        border-radius: 18px;
        padding: 12px;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    }

    .dashboard-chatbot__tool-title {
        font-size: 0.82rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 10px;
    }

    .dashboard-chatbot__tool-grid {
        display: grid;
        gap: 10px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .dashboard-chatbot__tool-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .dashboard-chatbot__tool-label {
        font-size: 0.78rem;
        color: #475569;
        font-weight: 600;
    }

    .dashboard-chatbot__tool-input {
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 0.88rem;
    }

    .dashboard-chatbot__tool-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
    }

    .dashboard-chatbot__tool-error {
        margin-top: 8px;
        color: #b91c1c;
        font-size: 0.76rem;
        min-height: 1rem;
    }

    .dashboard-chatbot__typing-dots {
        display: inline-flex;
        gap: 4px;
    }

    .dashboard-chatbot__typing-dots span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #2563eb;
        animation: chatbot-bounce 1s infinite ease-in-out;
    }

    .dashboard-chatbot__typing-dots span:nth-child(2) {
        animation-delay: 0.12s;
    }

    .dashboard-chatbot__typing-dots span:nth-child(3) {
        animation-delay: 0.24s;
    }

    .dashboard-chatbot__form {
        padding: 14px;
        border-top: 1px solid #dbe5f0;
        background: rgba(255, 255, 255, 0.95);
    }

    .dashboard-chatbot__textarea {
        min-height: 56px;
        max-height: 120px;
        resize: vertical;
        border-radius: 16px;
        border: 1px solid #cbd5e1;
        padding: 12px 14px;
    }

    .dashboard-chatbot__send {
        border-radius: 14px;
        min-width: 96px;
        font-weight: 600;
    }

    @keyframes chatbot-bounce {
        0%, 80%, 100% { transform: scale(0.7); opacity: 0.5; }
        40% { transform: scale(1); opacity: 1; }
    }

    @media (max-width: 767px) {
        .dashboard-chatbot {
            right: 16px;
            bottom: 84px;
        }

        html[dir="rtl"] .dashboard-chatbot {
            left: 16px;
        }

        .dashboard-chatbot__panel {
            width: min(360px, calc(100vw - 18px));
            bottom: 68px;
        }

        .dashboard-chatbot__tool-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div
    id="dashboardChatbot"
    class="dashboard-chatbot"
    data-session-route="{{ route('api.chat.session.current') }}"
    data-send-route="{{ route('api.chat.store') }}"
    data-messages-route-template="{{ route('api.chat.messages', ['chatSession' => '__SESSION__']) }}"
    data-poll-interval="{{ (int) config('chatbot.poll_interval_ms', 1800) }}"
    data-user-id="{{ auth()->id() }}"
>
    <button type="button" class="dashboard-chatbot__launcher" id="dashboardChatLauncher">
        <i class="bi bi-stars"></i>
        <span>{{ $chatCopy['launcher'] }}</span>
    </button>

    <div class="dashboard-chatbot__panel" id="dashboardChatPanel" aria-hidden="true">
        <div class="dashboard-chatbot__header d-flex align-items-start justify-content-between gap-3">
            <div>
                <h3 class="dashboard-chatbot__title">{{ $chatCopy['title'] }}</h3>
                <div class="dashboard-chatbot__subtitle">{{ $chatCopy['subtitle'] }}</div>
            </div>
            <button type="button" class="dashboard-chatbot__close" id="dashboardChatClose" aria-label="{{ $chatCopy['close'] }}">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="dashboard-chatbot__body">
            <div class="dashboard-chatbot__messages" id="dashboardChatMessages"></div>

            <div class="dashboard-chatbot__typing" id="dashboardChatTyping">
                <span>{{ $chatCopy['typing'] }}</span>
                <span class="dashboard-chatbot__typing-dots" aria-hidden="true">
                    <span></span><span></span><span></span>
                </span>
            </div>

            <div class="dashboard-chatbot__tools" id="dashboardChatTools"></div>

            <form class="dashboard-chatbot__form" id="dashboardChatForm">
                <div class="d-flex gap-2 align-items-end">
                    <textarea
                        class="form-control dashboard-chatbot__textarea"
                        id="dashboardChatInput"
                        placeholder="{{ $chatCopy['placeholder'] }}"
                    ></textarea>
                    <button type="submit" class="btn btn-primary dashboard-chatbot__send">{{ $chatCopy['send'] }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('dashboardChatbot');

    if (!root) {
        return;
    }

    const sessionRoute = root.dataset.sessionRoute;
    const sendRoute = root.dataset.sendRoute;
    const messagesRouteTemplate = root.dataset.messagesRouteTemplate;
    const pollInterval = Number(root.dataset.pollInterval || 1800);
    const userId = Number(root.dataset.userId || 0);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const launcher = document.getElementById('dashboardChatLauncher');
    const closeButton = document.getElementById('dashboardChatClose');
    const panel = document.getElementById('dashboardChatPanel');
    const form = document.getElementById('dashboardChatForm');
    const input = document.getElementById('dashboardChatInput');
    const messagesEl = document.getElementById('dashboardChatMessages');
    const typingEl = document.getElementById('dashboardChatTyping');
    const toolsEl = document.getElementById('dashboardChatTools');
    const welcomeText = @json($chatCopy['welcome']);
    const toolCopy = @json($toolCopy);

    let sessionId = null;
    let lastMessageId = 0;
    let pollTimer = null;
    let hasLoadedSession = false;
    let conversation = [];

    function setOpen(isOpen) {
        root.classList.toggle('is-open', isOpen);
        panel.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

        if (isOpen && !hasLoadedSession) {
            loadSession();
        }
    }

    function createBubble(role, message) {
        const bubble = document.createElement('div');
        bubble.className = 'dashboard-chatbot__bubble dashboard-chatbot__bubble--' + (role === 'user' ? 'user' : 'assistant');
        bubble.textContent = message;
        return bubble;
    }

    function renderWelcome() {
        conversation = [];
        messagesEl.innerHTML = '';
        messagesEl.appendChild(createBubble('assistant', welcomeText));
        renderTools(null);
        scrollToBottom();
    }

    function renderMessages(messages, append = false) {
        if (!append) {
            conversation = [];
            messagesEl.innerHTML = '';
        }

        if (!messages.length) {
            renderWelcome();
            return;
        }

        messages.forEach(function (message) {
            conversation.push(message);
            lastMessageId = Math.max(lastMessageId, Number(message.id || 0));
            messagesEl.appendChild(createBubble(message.role, message.message));
        });

        renderTools(conversation[conversation.length - 1] || null);
        scrollToBottom();
    }

    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function showTyping(show) {
        typingEl.classList.toggle('is-visible', show);
    }

    function renderTools(message) {
        toolsEl.innerHTML = '';

        if (!message || message.role !== 'assistant') {
            return;
        }

        const metadata = message.metadata || {};
        const dateFields = Array.isArray(metadata.date_fields) && metadata.date_fields.length
            ? metadata.date_fields
            : (Array.isArray(metadata.missing_fields)
                ? metadata.missing_fields.filter(function (field) {
                    return field === 'check_in_date' || field === 'check_out_date';
                })
                : []);

        const needsCheckIn = dateFields.includes('check_in_date');
        const needsCheckOut = dateFields.includes('check_out_date');

        if (!needsCheckIn && !needsCheckOut) {
            return;
        }

        const card = document.createElement('div');
        card.className = 'dashboard-chatbot__tool-card';

        const title = document.createElement('div');
        title.className = 'dashboard-chatbot__tool-title';
        title.textContent = toolCopy.title;
        card.appendChild(title);

        const formEl = document.createElement('form');
        const grid = document.createElement('div');
        grid.className = 'dashboard-chatbot__tool-grid';

        const checkInInput = needsCheckIn ? createDateInput(toolCopy.check_in, 'check-in-date') : null;
        const checkOutInput = needsCheckOut ? createDateInput(toolCopy.check_out, 'check-out-date') : null;

        if (checkInInput) {
            grid.appendChild(checkInInput.wrapper);
        }

        if (checkOutInput) {
            grid.appendChild(checkOutInput.wrapper);
        }

        if (checkInInput && checkOutInput) {
            checkInInput.input.addEventListener('change', function () {
                checkOutInput.input.min = checkInInput.input.value || '';
            });
        }

        const actions = document.createElement('div');
        actions.className = 'dashboard-chatbot__tool-actions';

        const submitButton = document.createElement('button');
        submitButton.type = 'submit';
        submitButton.className = 'btn btn-primary btn-sm dashboard-chatbot__send';
        submitButton.textContent = toolCopy.submit;
        actions.appendChild(submitButton);

        const errorEl = document.createElement('div');
        errorEl.className = 'dashboard-chatbot__tool-error';

        formEl.appendChild(grid);
        formEl.appendChild(actions);
        formEl.appendChild(errorEl);

        formEl.addEventListener('submit', async function (event) {
            event.preventDefault();

            const checkInValue = checkInInput ? checkInInput.input.value : '';
            const checkOutValue = checkOutInput ? checkOutInput.input.value : '';

            if (needsCheckIn && !checkInValue) {
                errorEl.textContent = toolCopy.missing_check_in;
                return;
            }

            if (needsCheckOut && !checkOutValue) {
                errorEl.textContent = toolCopy.missing_check_out;
                return;
            }

            if (checkInValue && checkOutValue && checkOutValue <= checkInValue) {
                errorEl.textContent = toolCopy.invalid_range;
                return;
            }

            errorEl.textContent = '';

            const payloadParts = [];
            const displayParts = [];

            if (checkInValue) {
                payloadParts.push('check_in_date: ' + checkInValue);
                displayParts.push(toolCopy.check_in + ': ' + checkInValue);
            }

            if (checkOutValue) {
                payloadParts.push('check_out_date: ' + checkOutValue);
                displayParts.push(toolCopy.check_out + ': ' + checkOutValue);
            }

            await submitMessage(payloadParts.join('\n'), displayParts.join('\n'));
        });

        card.appendChild(formEl);
        toolsEl.appendChild(card);
    }

    function createDateInput(labelText, name) {
        const wrapper = document.createElement('label');
        wrapper.className = 'dashboard-chatbot__tool-group';

        const label = document.createElement('span');
        label.className = 'dashboard-chatbot__tool-label';
        label.textContent = labelText;

        const input = document.createElement('input');
        input.type = 'date';
        input.name = name;
        input.className = 'dashboard-chatbot__tool-input';

        wrapper.appendChild(label);
        wrapper.appendChild(input);

        return { wrapper, input };
    }

    async function requestJson(url, options = {}) {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.headers || {}),
            },
            credentials: 'same-origin',
            ...options,
        });

        if (!response.ok) {
            throw new Error('Request failed');
        }

        return response.json();
    }

    async function loadSession() {
        try {
            const data = await requestJson(sessionRoute);
            sessionId = data.session_id;
            hasLoadedSession = true;
            renderMessages(data.messages || []);
        } catch (error) {
            renderWelcome();
        }
    }

    async function pollMessages() {
        if (!sessionId) {
            return;
        }

        try {
            const url = messagesRouteTemplate.replace('__SESSION__', String(sessionId)) + '?after=' + lastMessageId;
            const data = await requestJson(url);
            const messages = data.messages || [];

            if (messages.length) {
                renderMessages(messages, true);
            }

            if (messages.some(message => message.role === 'assistant')) {
                showTyping(false);
                stopPolling();
            }
        } catch (error) {
            showTyping(false);
            stopPolling();
        }
    }

    function startPolling() {
        stopPolling();
        pollMessages();
        pollTimer = window.setInterval(pollMessages, pollInterval);
    }

    function stopPolling() {
        if (pollTimer) {
            window.clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    async function submitMessage(text, displayText = text) {
        if (!hasLoadedSession) {
            await loadSession();
        }

        if (!messagesEl.children.length) {
            renderWelcome();
        }

        messagesEl.appendChild(createBubble('user', displayText));
        scrollToBottom();
        showTyping(true);
        renderTools(null);

        try {
            const response = await requestJson(sendRoute, {
                method: 'POST',
                body: JSON.stringify({
                    message: text,
                    session_id: sessionId,
                    user_id: userId,
                }),
            });

            sessionId = response.session_id;
            lastMessageId = Math.max(lastMessageId, Number(response.message?.id || 0));
            startPolling();
        } catch (error) {
            showTyping(false);
            messagesEl.appendChild(createBubble('assistant', 'Unable to send the message right now.'));
            scrollToBottom();
        }
    }

    async function handleSubmit(event) {
        event.preventDefault();

        const text = input.value.trim();

        if (!text) {
            return;
        }

        input.value = '';
        await submitMessage(text);
    }

    launcher?.addEventListener('click', function () {
        setOpen(!root.classList.contains('is-open'));
    });

    closeButton?.addEventListener('click', function () {
        setOpen(false);
    });

    form?.addEventListener('submit', handleSubmit);

    input?.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form?.requestSubmit();
        }
    });
});
</script>
@endpush
