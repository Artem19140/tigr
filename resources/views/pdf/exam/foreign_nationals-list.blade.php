@extends('pdf.layouts.base')
@section('title')
    Список
@endsection

@section('content')

<h3 class="text-center"> {{ $exam->shortName() }} • {{ $exam->beginTimeLocal()->format('H:i, d.m.Y') }}</h3>
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
            <td class="text-center border-black bold">{{ $header }}</td>
        @endforeach
    </tr>


    @foreach ($foreignNationals as $f)
        <tr>
            <td class="text-center border-black" style="text-align: left; padding-left: 5px;">{{ $f->full_name }}</td>
            <td class="text-center border-black" style="text-align: left; padding-left: 5px;">{{ $f->full_name_latin }}</td>
            <td class="text-center border-black" style="text-align: left; padding-left: 5px;">{{ $f->full_passport }}</td>
            <td class="text-center border-black">{{ $f->formattedDateBirth() }}</td>
            <td class="text-center border-black">{{ $f->citizenship }}</td>
        </tr>
    @endforeach

</table>

@endsection