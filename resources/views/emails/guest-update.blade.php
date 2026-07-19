<!doctype html>
<html lang="en">
<body style="margin:0;background:#f4f6f4;font-family:Arial,sans-serif;color:#24352c">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:32px 16px">
    <tr><td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #dce8df">
            <tr><td style="background:#106b38;padding:24px;text-align:center;color:#fff">
                <h1 style="margin:0;font-size:24px">{{ $hotel }}</h1>
            </td></tr>
            @if ($coverUrl)
                <tr><td><img src="{{ $coverUrl }}" alt="" width="640" style="display:block;width:100%;height:auto"></td></tr>
            @endif
            <tr><td style="padding:32px">
                <p style="margin-top:0">Hello {{ $user->name }},</p>
                <h2 style="color:#106b38;font-size:28px;margin:10px 0 18px">{{ $update->title }}</h2>
                <div style="font-size:16px;line-height:1.7;white-space:pre-line">{{ $update->description }}</div>
                <p style="margin:28px 0 0"><a href="{{ route('booking.checkout') }}" style="display:inline-block;background:#e85e26;color:#fff;text-decoration:none;padding:13px 22px;border-radius:999px;font-weight:700">Book your stay</a></p>
            </td></tr>
            <tr><td style="padding:20px 32px;background:#f7faf8;font-size:12px;color:#66756c;text-align:center">
                You received this because you opted in to updates from {{ $hotel }}.
                <a href="{{ $unsubscribeUrl }}" style="color:#106b38">Unsubscribe</a>
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
