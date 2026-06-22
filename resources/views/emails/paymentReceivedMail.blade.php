<x-mail::message>
# Hallo {{ $receiverName }},

Goed nieuws! **{{ $payerName }}** heeft zijn Sendwich-schuld bij jou vereffend.

<x-mail::table>
| Van | Voor | Bedrag |
| :--- | :--- | ---: |
| {{ $payerName }} | {{ $receiverName }} | €{{ number_format($balance, 2) }} |
</x-mail::table>

Smakelijk!
</x-mail::message>
