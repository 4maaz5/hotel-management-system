<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم جدولة اجتماع جديد</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f5f7fa; line-height: 1.6; direction: rtl;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f5f7fa;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation"
                    style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); overflow: hidden;">

                    <!-- Header -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                            <div
                                style="background-color: rgba(255, 255, 255, 0.2); width: 64px; height: 64px; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                                <!-- Icon -->
                            </div>
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                تم جدولة اجتماع جديد
                            </h1>
                            <p style="margin: 10px 0 0; color: rgba(255, 255, 255, 0.9); font-size: 16px;">
                                تمت دعوتك لحضور اجتماع
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px; text-align: right;">

                            <!-- Meeting Title -->
                            <div style="margin-bottom: 30px;">
                                <h2 style="margin: 0 0 8px; color: #1a202c; font-size: 24px; font-weight: 600;">
                                    {{ $meeting->title }}
                                </h2>
                                <div
                                    style="width: 50px; height: 3px; background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); border-radius: 2px;">
                                </div>
                            </div>

                            <!-- Meeting Details -->
                            <table role="presentation" style="width: 100%; border-collapse: collapse;">

                                <!-- Start Time -->
                                <tr>
                                    <td style="padding: 16px 0; border-bottom: 1px solid #e2e8f0;">
                                        <strong style="color:#64748b; font-size:13px;">وقت بدء الاجتماع</strong>
                                        <div style="color:#1a202c; font-size:16px; font-weight:600; margin-top:6px;">
                                            {{ $meeting->start_time ?? '-' }}
                                        </div>
                                    </td>
                                </tr>

                                <!-- Duration -->
                                <tr>
                                    <td style="padding: 16px 0; border-bottom: 1px solid #e2e8f0;">
                                        <strong style="color:#64748b; font-size:13px;">مدة الاجتماع</strong>
                                        <div style="color:#1a202c; font-size:16px; font-weight:600; margin-top:6px;">
                                            {{ $meeting->duration ? $meeting->duration . ' دقيقة' : '-' }}
                                        </div>
                                    </td>
                                </tr>

                            </table>

                            <!-- CTA Button -->
                            <div style="margin-top: 35px; text-align: center;">
                                <a href="{{ route('meetings.join', $meeting->id) }}"
                                    style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 8px; font-weight: 600; font-size: 16px;">
                                    الانضمام إلى الاجتماع
                                </a>
                            </div>

                            <!-- Additional Info -->
                            <div
                                style="margin-top: 30px; padding: 20px; background-color: #f8fafc; border-radius: 8px; border-right: 4px solid #667eea;">
                                <p style="margin: 0; color: #64748b; font-size: 14px;">
                                    <strong style="color: #1a202c;">💡 ملاحظة:</strong>
                                    يرجى الانضمام قبل الموعد ببضع دقائق لاختبار إعدادات الصوت والفيديو.
                                </p>
                            </div>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="padding: 30px; background-color: #f8fafc; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 8px; color: #64748b; font-size: 14px;">مع أطيب التحيات،</p>
                            <p style="margin: 0; color: #1a202c; font-size: 16px; font-weight: 600;">
                                {{ config('app.name') }}
                            </p>
                            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                                <p style="margin: 0; color: #94a3b8; font-size: 12px;">
                                    هذه رسالة آلية، يرجى عدم الرد عليها.
                                </p>
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
