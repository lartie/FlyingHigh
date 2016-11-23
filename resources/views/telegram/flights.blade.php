@if($flag == 1)
Woohoo! I've just found your flights! Time of the flight is always local.
@else
I’ve just found your flight. Time of the flight is local.
@endif

I will track your flights and send you all the updated information as soon as it appears.

✈️ – I have all information about the flight
✈️🕒 – I have incomplete flight information yet, but will try to receive it. Usually it happens no later than 5 days before the flight.

@foreach($flights as $key => $flight)
@if($flight['identified'])
✈️ {{ $flight['departure']['city'] }} ({{ $flight['departure']['iata'] }}) → {{ $flight['arrival']['city'] }} ({{ $flight['arrival']['iata'] }})
{{ $flight['departure']['date']->format('d.m l, \d\e\p\a\r\t\u\r\e \a\t H:i') }}
@if(isset($flight['departure']['terminal']))
Terminal {{ $flight['departure']['terminal'] }}
@endif
@if(isset($flight['departure']['gate']))
Gate {{ $flight['departure']['gate'] }}
@endif
@else
✈️🕒 {{ $flight['departure']['city'] }} → {{ $flight['arrival']['city'] }}
{{ $flight['departure']['date']->format('d.m l, \d\e\p\a\r\t\u\r\e \a\t G:i') }}
@endif

@endforeach