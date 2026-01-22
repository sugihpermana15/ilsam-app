@php
    $name = (string) ($data['name'] ?? '');
    $tel = (string) ($data['tel'] ?? '');
    $email = (string) ($data['email'] ?? '');
    $inquiries = (string) ($data['inquiries'] ?? '');
    $messageBody = (string) ($data['textarea'] ?? '');
@endphp

New message from ILSAM website contact form

Name: {{ $name }}
Email: {{ $email }}
Phone: {{ $tel !== '' ? $tel : '-' }}
Work Inquiries: {{ $inquiries !== '' ? $inquiries : '-' }}

Message:
{{ $messageBody }}

---
Meta
IP: {{ $ip ?: '-' }}
User Agent: {{ $userAgent ?: '-' }}
