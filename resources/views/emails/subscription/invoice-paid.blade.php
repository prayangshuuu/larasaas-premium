<x-mail::message>
# Payment Successful

Your payment of **{{ $amount }}** for **IELTS Band Booster** was successful on {{ $date }}.

You can download your invoice for your records below.

<x-mail::button :url="$pdfUrl">
Download Invoice
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
