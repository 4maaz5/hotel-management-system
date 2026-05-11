@php
    $chatCopy = app()->getLocale() === 'ar'
        ? [
            'launcher' => 'مساعد الحجز',
            'title' => 'مساعد الحجز الذكي',
            'subtitle' => 'التوفر، طلب الحجز، وسياسات الإقامة',
            'placeholder' => 'اكتب سؤالك هنا...',
            'send' => 'إرسال',
            'close' => 'إغلاق',
            'welcome' => 'مرحباً، أستطيع مساعدتك في التوفر، أسعار البداية، طلبات الحجز المباشر، وسياسات الإقامة.',
            'typing' => 'المساعد يكتب...',
            'send_error' => 'تعذر إرسال الرسالة الآن. حاول مرة أخرى بعد قليل.',
        ]
        : [
            'launcher' => 'Booking Assistant',
            'title' => 'Smart Booking Assistant',
            'subtitle' => 'Availability, booking requests, and stay policies',
            'placeholder' => 'Ask about your stay...',
            'send' => 'Send',
            'close' => 'Close',
            'welcome' => 'Hello, I can help with room availability, starting rates, booking requests, and stay policies.',
            'typing' => 'Assistant is typing...',
            'send_error' => 'Unable to send the message right now. Please try again shortly.',
        ];

    $toolCopy = [
        'title' => app()->getLocale() === 'ar' ? 'اختر التواريخ للمتابعة' : 'Select stay dates to continue',
        'check_in' => __('dashboard.check_in_date'),
        'check_out' => __('dashboard.check_out_date'),
        'submit' => $chatCopy['send'],
        'missing_check_in' => __('dashboard.please_select_check_in_date'),
        'missing_check_out' => __('dashboard.please_select_check_out_date'),
        'invalid_range' => app()->getLocale() === 'ar'
            ? 'يجب أن يكون تاريخ المغادرة بعد تاريخ الوصول.'
            : 'Check-out date must be after check-in date.',
    ];
@endphp

<style>
    .booking-chatbot {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 1200;
    }

    html[dir="rtl"] .booking-chatbot {
        right: auto;
        left: 22px;
    }

    .booking-chatbot__launcher {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        border: 0;
        border-radius: 999px;
        padding: 0.9rem 1.1rem;
        color: white;
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
        box-shadow: 0 18px 40px rgba(20, 33, 61, 0.18);
        font-weight: 800;
    }

    .booking-chatbot__panel {
        position: absolute;
        right: 0;
        bottom: 72px;
        width: min(390px, calc(100vw - 20px));
        border-radius: 26px;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 28px 70px rgba(20, 33, 61, 0.22);
        transform: translateY(16px) scale(0.97);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.22s ease, transform 0.22s ease;
    }

    html[dir="rtl"] .booking-chatbot__panel {
        right: auto;
        left: 0;
    }

    .booking-chatbot.is-open .booking-chatbot__panel {
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .booking-chatbot__header {
        padding: 1rem 1.1rem;
        color: white;
        background:
            linear-gradient(135deg, rgba(24, 49, 83, 0.96), rgba(179, 138, 61, 0.92)),
            radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 45%);
    }

    .booking-chatbot__title {
        margin: 0;
        font-size: 1rem;
        font-weight: 800;
    }

    .booking-chatbot__subtitle {
        margin-top: 0.2rem;
        color: rgba(255, 255, 255, 0.82);
        font-size: 0.83rem;
    }

    .booking-chatbot__close {
        width: 2.2rem;
        height: 2.2rem;
        border: 0;
        border-radius: 50%;
        color: white;
        background: rgba(255, 255, 255, 0.16);
    }

    .booking-chatbot__body {
        display: flex;
        flex-direction: column;
        height: 520px;
        max-height: calc(100vh - 150px);
        background:
            linear-gradient(180deg, rgba(255, 253, 248, 1), rgba(248, 244, 236, 0.95));
    }

    .booking-chatbot__messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .booking-chatbot__bubble {
        max-width: 88%;
        padding: 0.8rem 0.95rem;
        border-radius: 18px;
        line-height: 1.55;
        font-size: 0.92rem;
        white-space: pre-wrap;
        word-break: break-word;
        box-shadow: 0 10px 22px rgba(20, 33, 61, 0.06);
    }

    .booking-chatbot__bubble--assistant {
        align-self: flex-start;
        color: var(--ink);
        background: white;
        border-top-left-radius: 8px;
    }

    .booking-chatbot__bubble--user {
        align-self: flex-end;
        color: white;
        background: linear-gradient(135deg, var(--brand-primary), var(--brand-accent));
        border-top-right-radius: 8px;
    }

    .booking-chatbot__typing {
        display: none;
        align-items: center;
        gap: 0.5rem;
        padding: 0 1rem 0.7rem;
        color: var(--muted);
        font-size: 0.82rem;
    }

    .booking-chatbot__typing.is-visible {
        display: inline-flex;
    }

    .booking-chatbot__typing-dots {
        display: inline-flex;
        gap: 0.25rem;
    }

    .booking-chatbot__typing-dots span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--brand-primary);
        animation: booking-chatbot-bounce 1s infinite ease-in-out;
    }

    .booking-chatbot__typing-dots span:nth-child(2) {
        animation-delay: 0.12s;
    }

    .booking-chatbot__typing-dots span:nth-child(3) {
        animation-delay: 0.24s;
    }

    .booking-chatbot__tools {
        padding: 0 0.9rem 0.8rem;
    }

    .booking-chatbot__tool-card {
        padding: 0.85rem;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid rgba(20, 33, 61, 0.08);
        box-shadow: 0 12px 24px rgba(20, 33, 61, 0.06);
    }

    .booking-chatbot__tool-title {
        margin-bottom: 0.7rem;
        color: var(--ink);
        font-size: 0.82rem;
        font-weight: 800;
    }

    .booking-chatbot__tool-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .booking-chatbot__tool-group {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .booking-chatbot__tool-label {
        color: var(--muted);
        font-size: 0.78rem;
        font-weight: 700;
    }

    .booking-chatbot__tool-input {
        min-height: 44px;
        padding: 0.7rem 0.8rem;
        border-radius: 14px;
        border: 1px solid rgba(20, 33, 61, 0.14);
        background: white;
    }

    .booking-chatbot__tool-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 0.75rem;
    }

    .booking-chatbot__tool-error {
        min-height: 1rem;
        margin-top: 0.45rem;
        color: var(--danger);
        font-size: 0.76rem;
    }

    .booking-chatbot__form {
        padding: 0.9rem;
        background: rgba(255, 255, 255, 0.95);
        border-top: 1px solid rgba(20, 33, 61, 0.08);
    }

    .booking-chatbot__textarea {
        min-height: 56px;
        max-height: 120px;
        resize: vertical;
        border-radius: 16px;
        border: 1px solid rgba(20, 33, 61, 0.14);
        padding: 0.8rem 0.9rem;
    }

    .booking-chatbot__send {
        border-radius: 14px;
        min-width: 96px;
        font-weight: 700;
    }

    @keyframes booking-chatbot-bounce {
        0%, 80%, 100% { transform: scale(0.7); opacity: 0.5; }
        40% { transform: scale(1); opacity: 1; }
    }

    @media (max-width: 767px) {
        .booking-chatbot {
            right: 14px;
            bottom: 14px;
        }

        html[dir="rtl"] .booking-chatbot {
            left: 14px;
        }

        .booking-chatbot__panel {
            width: min(360px, calc(100vw - 12px));
            bottom: 66px;
        }

        .booking-chatbot__tool-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div
    id="bookingChatbot"
    class="booking-chatbot"
    data-session-route="{{ route('booking.chat.session.current', $bookingPropertyQuery ?? []) }}"
    data-send-route="{{ route('booking.chat.store', $bookingPropertyQuery ?? []) }}"
    data-messages-route-template="{{ route('booking.chat.messages', array_merge(['chatSession' => '__SESSION__'], $bookingPropertyQuery ?? [])) }}"
    data-poll-interval="{{ (int) config('chatbot.poll_interval_ms', 1800) }}"
>
    <button type="button" class="booking-chatbot__launcher" id="bookingChatLauncher">
        <i class="fas fa-comments"></i>
        <span>{{ $chatCopy['launcher'] }}</span>
    </button>

    <div class="booking-chatbot__panel" id="bookingChatPanel" aria-hidden="true">
        <div class="booking-chatbot__header d-flex align-items-start justify-content-between gap-3">
            <div>
                <h3 class="booking-chatbot__title">{{ $chatCopy['title'] }}</h3>
                <div class="booking-chatbot__subtitle">{{ $chatCopy['subtitle'] }}</div>
            </div>
            <button type="button" class="booking-chatbot__close" id="bookingChatClose" aria-label="{{ $chatCopy['close'] }}">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        <div class="booking-chatbot__body">
            <div class="booking-chatbot__messages" id="bookingChatMessages"></div>

            <div class="booking-chatbot__typing" id="bookingChatTyping">
                <span>{{ $chatCopy['typing'] }}</span>
                <span class="booking-chatbot__typing-dots" aria-hidden="true">
                    <span></span><span></span><span></span>
                </span>
            </div>

            <div class="booking-chatbot__tools" id="bookingChatTools"></div>

            <form class="booking-chatbot__form" id="bookingChatForm">
                <div class="d-flex gap-2 align-items-end">
                    <textarea
                        class="form-control booking-chatbot__textarea"
                        id="bookingChatInput"
                        placeholder="{{ $chatCopy['placeholder'] }}"
                    ></textarea>
                    <button type="submit" class="btn btn-primary booking-chatbot__send">{{ $chatCopy['send'] }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('bookingChatbot');

    if (!root) {
        return;
    }

    const sessionRoute = root.dataset.sessionRoute;
    const sendRoute = root.dataset.sendRoute;
    const messagesRouteTemplate = root.dataset.messagesRouteTemplate;
    const pollInterval = Number(root.dataset.pollInterval || 1800);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const launcher = document.getElementById('bookingChatLauncher');
    const closeButton = document.getElementById('bookingChatClose');
    const panel = document.getElementById('bookingChatPanel');
    const form = document.getElementById('bookingChatForm');
    const input = document.getElementById('bookingChatInput');
    const messagesEl = document.getElementById('bookingChatMessages');
    const typingEl = document.getElementById('bookingChatTyping');
    const toolsEl = document.getElementById('bookingChatTools');
    const welcomeText = @json($chatCopy['welcome']);
    const sendErrorText = @json($chatCopy['send_error']);
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
        bubble.className = 'booking-chatbot__bubble booking-chatbot__bubble--' + (role === 'user' ? 'user' : 'assistant');
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
        card.className = 'booking-chatbot__tool-card';

        const title = document.createElement('div');
        title.className = 'booking-chatbot__tool-title';
        title.textContent = toolCopy.title;
        card.appendChild(title);

        const formEl = document.createElement('form');
        const grid = document.createElement('div');
        grid.className = 'booking-chatbot__tool-grid';

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
        actions.className = 'booking-chatbot__tool-actions';

        const submitButton = document.createElement('button');
        submitButton.type = 'submit';
        submitButton.className = 'btn btn-primary btn-sm booking-chatbot__send';
        submitButton.textContent = toolCopy.submit;
        actions.appendChild(submitButton);

        const errorEl = document.createElement('div');
        errorEl.className = 'booking-chatbot__tool-error';

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
        wrapper.className = 'booking-chatbot__tool-group';

        const label = document.createElement('span');
        label.className = 'booking-chatbot__tool-label';
        label.textContent = labelText;

        const input = document.createElement('input');
        input.type = 'date';
        input.name = name;
        input.className = 'booking-chatbot__tool-input';

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
                }),
            });

            sessionId = response.session_id;
            lastMessageId = Math.max(lastMessageId, Number(response.message?.id || 0));
            startPolling();
        } catch (error) {
            showTyping(false);
            messagesEl.appendChild(createBubble('assistant', sendErrorText));
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

    launcher.addEventListener('click', function () {
        setOpen(!root.classList.contains('is-open'));
    });

    closeButton.addEventListener('click', function () {
        setOpen(false);
    });

    form.addEventListener('submit', handleSubmit);
    renderWelcome();
});
</script>
@endpush
