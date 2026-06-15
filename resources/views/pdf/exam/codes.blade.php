@extends('pdf.layouts.base')
@section('title')
    Коды
@endsection
@push('style')
    table, th, td {
        border: 1px solid black;
        text-align:center;
    }
    td{
        padding:9px;
    }
@endpush
@section('content')

<h4 class="text-center">Коды ИГ</h4>
<div>Экзамен: {{ $exam->type->short_name }}</div>
<div class="mb-10">Дата: {{ $exam->begin_time_local->format('H:i, d.m.Y') }}</div>
<table class="table">
    <tr>
        <th>ФИО</th>
        <th>Паспорт</th>
        <th>Код</th>
    </tr>

    @foreach ($exam->enrollments as $enrollment)
    <tr>
        <td>{{  $enrollment->foreignNational->surname }} {{ $enrollment->foreignNational->name }} {{ $enrollment->foreignNational->patronymic }}</td>
        <td>{{  $enrollment->foreignNational->passport_series }} {{  $enrollment->foreignNational->passport_number }}</td>
        <td>{{ substr($enrollment?->exam_code, 0, 3) . "  " .  substr($enrollment?->exam_code, 3, 3) }}</td>
    </tr>
    @endforeach

</table>

@endsection