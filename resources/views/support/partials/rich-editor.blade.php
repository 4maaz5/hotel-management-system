@php
    $editorId = $editorId ?? 'support-editor';
    $fieldName = $fieldName ?? 'body';
    $editorValue = \App\Support\SupportTicketRichText::sanitize(old($fieldName, $value ?? ''));
@endphp

<textarea
    id="{{ $editorId }}"
    name="{{ $fieldName }}"
    class="form-control js-support-summernote"
    data-placeholder="{{ __('support.placeholder_write_message') }}"
>{!! $editorValue !!}</textarea>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('bundles/summernote/summernote-bs4.css') }}">

        <style>
            form.card .form-label,
            form.card label {
                color: #111827 !important;
                font-weight: 600;
            }

            .note-editor.note-frame {
                display: block;
                width: 100%;
                border: 1px solid #d8dee9;
                border-radius: 8px;
                overflow: hidden;
                background: #fff;
                color: #111827;
                box-shadow: none;
            }

            .note-editor .note-toolbar {
                background: #f8fafc;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                padding: 8px;
            }

            .note-editor .note-btn-group {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                margin: 0;
            }

            .note-editor .note-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 34px;
                min-height: 32px;
                padding: 6px 9px;
                color: #334155;
                background: #fff;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                line-height: 1;
                box-shadow: none;
            }

            .note-editor .note-icon-bold,
            .note-editor .note-icon-italic,
            .note-editor .note-icon-underline,
            .note-editor .note-icon-eraser,
            .note-editor .note-icon-unorderedlist,
            .note-editor .note-icon-orderedlist,
            .note-editor .note-icon-align-left,
            .note-editor .note-icon-align-center,
            .note-editor .note-icon-align-right,
            .note-editor .note-icon-align-justify,
            .note-editor .note-icon-link,
            .note-editor .note-icon-code,
            .note-editor .note-icon-caret,
            .note-editor .note-btn i,
            .note-editor .note-btn span {
                color: #334155 !important;
            }

            .note-editor .note-btn .note-icon-caret {
                margin-left: 4px;
            }

            .note-editor .note-btn:hover,
            .note-editor .note-btn:focus,
            .note-editor .note-btn.active {
                color: #0f172a;
                background: #e0f2fe;
                border-color: #38bdf8;
            }

            .note-editor .note-dropdown-menu {
                display: none;
                min-width: 180px;
                padding: 6px;
                background: #fff;
                border: 1px solid #d8dee9;
                border-radius: 8px;
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.16);
            }

            .note-editor .note-btn-group.show .note-dropdown-menu,
            .note-editor .note-dropdown-menu.show {
                display: block;
            }

            .note-editor .note-editing-area .note-editable {
                min-height: 160px;
                padding: 14px;
                line-height: 1.6;
                color: #111827;
                background: #fff;
            }

            .note-editor .note-editing-area .note-placeholder {
                color: #94a3b8;
                padding: 14px;
            }

            .note-editor .note-editing-area,
            .note-editor .note-editable,
            .note-editor .note-editable p,
            .note-editor .note-editable li {
                color: #111827 !important;
            }

            .note-editor .note-statusbar {
                background: #f8fafc;
                border-top: 1px solid #e5e7eb;
            }

            .note-modal .modal-dialog {
                margin-top: 80px;
            }

            .support-thread {
                display: grid;
                gap: 16px;
            }

            .support-message {
                max-width: 820px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 14px;
                background: #fff;
            }

            .support-message--admin {
                margin-left: auto;
                background: #f0f7ff;
                border-color: #cfe5ff;
            }

            .support-message--tenant {
                margin-right: auto;
            }

            .support-message__body p:last-child,
            .support-message__body ul:last-child,
            .support-message__body ol:last-child {
                margin-bottom: 0;
            }

            .support-attachment-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 10px;
            }

            .support-attachment {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border: 1px solid #d8dee9;
                border-radius: 8px;
                padding: 8px 10px;
                text-decoration: none;
                background: #fff;
            }

            .support-attachment img {
                width: 72px;
                height: 54px;
                object-fit: cover;
                border-radius: 6px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('bundles/summernote/summernote-bs4.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!window.jQuery || !jQuery.fn.summernote) {
                    return;
                }

                jQuery('.js-support-summernote').each(function () {
                    const editor = jQuery(this);

                    if (editor.data('supportSummernoteReady')) {
                        return;
                    }

                    editor.summernote({
                        height: 170,
                        minHeight: 140,
                        dialogsInBody: true,
                        disableDragAndDrop: true,
                        placeholder: editor.data('placeholder') || '',
                        toolbar: [
                            ['style', ['bold', 'italic', 'underline', 'clear']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['insert', ['link']],
                            ['view', ['codeview']]
                        ],
                        callbacks: {
                            onChange: function (contents) {
                                editor.val(contents);
                            }
                        }
                    });

                    editor.data('supportSummernoteReady', true);
                });

                jQuery(document).on('submit', 'form', function () {
                    jQuery(this).find('.js-support-summernote').each(function () {
                        const editor = jQuery(this);

                        if (editor.data('supportSummernoteReady')) {
                            editor.val(editor.summernote('code'));
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce
