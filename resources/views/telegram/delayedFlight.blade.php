Flight to {{ $flight['arrival']['city'] }} is delayed. I will continue informing you if something changes.

Previous info
✈️ {{ $flight['departure']['city'] }} ({{ $flight['departure']['iata'] }}) → {{ $flight['arrival']['city'] }} ({{ $flight['arrival']['iata'] }})
{{ $flight['departure']['date']['old']->format('d.m l, \d\e\p\a\r\t\u\r\e \a\t G:i') }}
{{ $flight['arrival']['date']['old']->format('d.m l, \a\r\r\i\v\a\l \a\t G:i') }}
@if(isset($flight['departure']['gate']))
Gate {{ $flight['departure']['gate'] }}
@endif
@if(isset($flight['departure']['terminal']))
Terminal {{ $flight['departure']['terminal'] }}
@endif

Actual info
✈️ {{ $flight['departure']['city'] }} ({{ $flight['departure']['iata'] }}) → {{ $flight['arrival']['city'] }} ({{ $flight['arrival']['iata'] }})
{{ $flight['departure']['date']['new']->format('d.m l, \d\e\p\a\r\t\u\r\e \a\t G:i') }}
{{ $flight['arrival']['date']['new']->format('d.m l, \a\r\r\i\v\a\l \a\t G:i') }}
@if(isset($flight['departure']['gate']))
Gate {{ $flight['departure']['gate'] }}
@endif
@if(isset($flight['departure']['terminal']))
Terminal {{ $flight['departure']['terminal'] }}
@endif