<!doctype html>
<html lang="en">
<body style="margin:0;background:#f4f6f4;font-family:Arial,sans-serif;color:#24352c">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:32px 16px">
    <tr><td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#fff;border-radius:16px;overflow:hidden;border:1px solid #dce8df">
            <tr><td style="background:#106b38;padding:24px;text-align:center;color:#fff">
                <h1 style="margin:0;font-size:24px">{{ $hotel }}</h1>
            </td></tr>
            <tr><td style="padding:32px">
                <h2 style="margin:0 0 12px;color:#106b38">Unlock your direct-booking discount</h2>
                <p>Hello {{ $user->name }}, enter this one-time code to confirm your email:</p>
                <div style="margin:28px 0;text-align:center;font-size:38px;letter-spacing:12px;font-weight:800;color:#106b38">{{ $code }}</div>
                <p style="font-size:14px;color:#647269">This code expires in 10 minutes. If you did not request it, you can ignore this email.</p>
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
