@extends('pdf.layouts.base')
@section('title')
    Список
@endsection
@push('style')
    table, th, td {
        border: 1px solid black;
        text-align:center;
    }
@endpush
@section('content')

<h2 class="text-center">Список</h2>
<div>Экзамен: {{ $exam->type->short_name }}</div>
<div class="mb-10">Дата: {{ $exam->begin_time_local->format('H:i, d.m.Y') }}</div>
<table class="table">
    <tr>
        <th>ФИО</th>
        <th>Паспорт</th>
        <th>Дата рождения</th>
        <th>Гражданство</th>
    </tr>
    
    @foreach ($foreignNationals as $f)
    @php
        $countries = collect(json_decode(file_get_contents(storage_path('app/public/countries.json')), true));
        $countryName = $countries->firstWhere('value', $f->citizenship)['text'] ?? '';
    @endphp
        <tr>
            <td>{{ $f->full_name_latin }}</td>
            <td>{{ $f->full_passport }}</td>
            <td>{{ $f->date_birth->format('d.m.Y') }}</td>
            <td>{{ $countryName }}</td>
        </tr>
    @endforeach

</table>

@endsection