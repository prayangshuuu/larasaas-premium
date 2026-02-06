<x-mail::message>
# Welcome to IELTS Band Booster!

Thank you for subscribing to the **{{ $planName }}** plan. We are thrilled to have you on board.

You now have full access to our premium features, including AI-powered writing evaluation, speaking practice, and more.

<x-mail::button :url="route('dashboard')">
Go to Dashboard
</x-mail::button>

If you have any questions, feel free to reply to this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
