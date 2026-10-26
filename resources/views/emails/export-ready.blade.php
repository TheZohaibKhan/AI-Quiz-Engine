<x-mail::message>
# Your PDF export is ready

@if ($downloadUrl)
Your download link is valid for **24 hours**@if ($expiresAt) and will expire on {{ $expiresAt->format('d M Y \a\t H:i') }}@endif.

<x-mail::button :url="$downloadUrl">
Download PDF
</x-mail::button>

The file will be deleted automatically once downloaded or when the link expires.
@else
Your PDF is attached to this email.
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>