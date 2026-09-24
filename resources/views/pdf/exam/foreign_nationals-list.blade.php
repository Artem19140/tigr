@extends('pdf.layouts.base')
@section('title')
    Список
@endsection

@section('content')

<h2 class="text-center"> {{ $exam->type->short_name }} • {{ $exam->begin_time_local->format('H:i, d.m.Y') }}</h2>
@php
$headers = [
    'ФИО',
    'ФИО (лат.)',
    'Паспорт',
    'Дата рождения',
    'Гражданство'
];
@endphp
<table class="table text-center border-black">
    <tr>
        @foreach ($headers as $header)
            <td class="text-center border-black">{{ $header }}</td>
        @endforeach
    </tr>

    @php
        $countries = collect(json_decode(file_get_contents(storage_path('app/public/countries.json')), true));
    @endphp

    @foreach ($foreignNationals as $f)
        @php
            $countryName = $countries->firstWhere('value', $f->citizenship)['text'] ?? '';
        @endphp
        <tr>
            <td class="text-center border-black">{{ $f->full_name_short }}</td>
            <td class="text-center border-black">{{ $f->full_name_latin_short }}</td>
            <td class="text-center border-black">{{ $f->full_passport }}</td>
            <td class="text-center border-black">{{ $f->date_birth->format('d.m.Y') }}</td>
            <td class="text-center border-black">{{ $countryName }}</td>
        </tr>
    @endforeach

</table>

@endsection