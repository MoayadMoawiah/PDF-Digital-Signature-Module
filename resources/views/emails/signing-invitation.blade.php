<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>
    @if(app()->getLocale() === 'ar')
        طلب توقيع وثيقة
    @else
        Document Signature Request
    @endif
</title>
<style>
    body { margin:0; padding:0; background:#F8FAFC; font-family: Arial, sans-serif; color:#334155; }
    .wrapper { max-width:600px; margin:32px auto; background:#fff; border-radius:8px; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }
    .header { background:#1B4F72; padding:28px 32px; text-align:center; }
    .header img { height:48px; }
    .header h1 { color:#fff; font-size:22px; margin:12px 0 0; }
    .body { padding:32px; }
    .body p { line-height:1.7; margin:0 0 16px; }
    .btn { display:inline-block; background:#1B4F72; color:#fff !important; text-decoration:none; padding:14px 32px; border-radius:6px; font-size:15px; font-weight:600; margin:16px 0; }
    .expiry { background:#FEF3C7; border:1px solid #F59E0B; border-radius:6px; padding:12px 16px; font-size:13px; margin:16px 0; }
    .footer { background:#F1F5F9; padding:18px 32px; text-align:center; font-size:12px; color:#64748B; }
    .divider { border:none; border-top:1px solid #E2E8F0; margin:24px 0; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $companyName }}">
        @endif
        <h1>{{ $companyName }}</h1>
    </div>

    <div class="body">
        @if(app()->getLocale() === 'ar')
            <p>السيد/السيدة <strong>{{ $signer->name }}</strong>،</p>
            <p>
                تلقيتَ هذا البريد لأنه تمت مشاركة وثيقة معك تستلزم توقيعك الرقمي.
            </p>
            <p>
                <strong>اسم الوثيقة:</strong> {{ $document->title }}
            </p>
            <p>
                يُرجى النقر على الزر أدناه لمراجعة الوثيقة والتوقيع عليها:
            </p>
            <p style="text-align:center;">
                <a href="{{ $signingUrl }}" class="btn">مراجعة الوثيقة والتوقيع</a>
            </p>
            @if($signer->expires_at)
                <div class="expiry">
                    ⚠️ ينتهي رابط التوقيع بتاريخ: <strong>{{ $signer->expires_at->translatedFormat('Y/m/d H:i') }}</strong>
                </div>
            @endif
            <hr class="divider">
            <p style="font-size:13px; color:#64748B;">
                إذا لم تتوقع استلام هذا البريد، يمكنك تجاهله بأمان.
                لا تشارك رابط التوقيع مع أي شخص آخر.
            </p>
        @else
            <p>Dear <strong>{{ $signer->name }}</strong>,</p>
            <p>
                You have been requested to review and digitally sign the following document.
            </p>
            <p>
                <strong>Document:</strong> {{ $document->title }}
            </p>
            <p>
                Please click the button below to review and sign the document:
            </p>
            <p style="text-align:center;">
                <a href="{{ $signingUrl }}" class="btn">Review &amp; Sign Document</a>
            </p>
            @if($signer->expires_at)
                <div class="expiry">
                    ⚠️ This signing link expires on: <strong>{{ $signer->expires_at->format('F j, Y \a\t H:i') }}</strong>
                </div>
            @endif
            <hr class="divider">
            <p style="font-size:13px; color:#64748B;">
                If you were not expecting this email, you can safely ignore it.
                Do not share this signing link with anyone else.
            </p>
        @endif
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.
    </div>
</div>
</body>
</html>
