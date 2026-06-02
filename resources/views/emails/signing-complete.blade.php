<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>
    @if(app()->getLocale() === 'ar')
        {{ $isRejection ? 'تم رفض التوقيع' : 'اكتمل التوقيع' }}
    @else
        {{ $isRejection ? 'Signature Rejected' : 'Signing Complete' }}
    @endif
</title>
<style>
    body { margin:0; padding:0; background:#F8FAFC; font-family: Arial, sans-serif; color:#334155; }
    .wrapper { max-width:600px; margin:32px auto; background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }
    .header { background:{{ $isRejection ? '#C0392B' : '#1B4F72' }}; padding:28px 32px; text-align:center; }
    .header h1 { color:#fff; font-size:22px; margin:0; }
    .body { padding:32px; }
    .body p { line-height:1.7; margin:0 0 16px; }
    .status-box { border-radius:6px; padding:16px; margin:16px 0; }
    .status-rejected { background:#FEE2E2; border:1px solid #F87171; }
    .status-signed { background:#D1FAE5; border:1px solid #34D399; }
    .footer { background:#F1F5F9; padding:18px 32px; text-align:center; font-size:12px; color:#64748B; }
    .divider { border:none; border-top:1px solid #E2E8F0; margin:24px 0; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>
            @if(app()->getLocale() === 'ar')
                {{ $isRejection ? '❌ تم رفض التوقيع' : '✅ اكتمل التوقيع' }}
            @else
                {{ $isRejection ? '❌ Signature Rejected' : '✅ Signing Complete' }}
            @endif
        </h1>
    </div>

    <div class="body">
        @if(app()->getLocale() === 'ar')
            <p>
                @if($isRejection)
                    رفض <strong>{{ $signer->name }}</strong> التوقيع على وثيقة:
                    <strong>{{ $document->title }}</strong>
                @else
                    وقّع <strong>{{ $signer->name }}</strong> على وثيقة:
                    <strong>{{ $document->title }}</strong>
                @endif
            </p>

            @if($isRejection && $signer->rejection_reason)
                <div class="status-box status-rejected">
                    <strong>سبب الرفض:</strong><br>
                    {{ $signer->rejection_reason }}
                </div>
            @endif

            @if(!$isRejection)
                <div class="status-box status-signed">
                    تم التوقيع بتاريخ: <strong>{{ $signer->signed_at?->translatedFormat('Y/m/d H:i') }}</strong><br>
                    @if($signer->ip_address)
                        عنوان IP: {{ $signer->ip_address }}
                    @endif
                </div>
            @endif
        @else
            <p>
                @if($isRejection)
                    <strong>{{ $signer->name }}</strong> has rejected signing the document:
                    <strong>{{ $document->title }}</strong>
                @else
                    <strong>{{ $signer->name }}</strong> has successfully signed:
                    <strong>{{ $document->title }}</strong>
                @endif
            </p>

            @if($isRejection && $signer->rejection_reason)
                <div class="status-box status-rejected">
                    <strong>Reason for rejection:</strong><br>
                    {{ $signer->rejection_reason }}
                </div>
            @endif

            @if(!$isRejection)
                <div class="status-box status-signed">
                    Signed at: <strong>{{ $signer->signed_at?->format('F j, Y \a\t H:i') }}</strong><br>
                    @if($signer->ip_address)
                        IP Address: {{ $signer->ip_address }}
                    @endif
                </div>
            @endif
        @endif

        <hr class="divider">
        <p style="font-size:13px; color:#64748B;">
            @if(app()->getLocale() === 'ar')
                يمكنك الاطلاع على تفاصيل الوثيقة في لوحة التحكم.
            @else
                You can view the document details in your admin panel.
            @endif
        </p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.
    </div>
</div>
</body>
</html>
