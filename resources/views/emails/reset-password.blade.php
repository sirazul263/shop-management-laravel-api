<p>You requested a password reset. Click the link below to reset your password:</p>

<a href="{{ env('FRONTEND_URL') }}/reset-password?token={{ $token }}">
    Reset Password
</a>

<p>If you did not request this, please ignore this email.</p>
