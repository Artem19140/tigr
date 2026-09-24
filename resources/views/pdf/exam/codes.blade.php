@extends('pdf.layouts.base')
@section('title')
    Коды
@endsection

@section('content')

<h2 class="text-center">{{ $exam->type->short_name }} • {{ $exam->begin_time_local->format('H:i, d.m.Y') }}</h2>
<table class="table">
    <tr>
        <th class="text-center border-black">ФИО</th>
        <th class="text-center border-black">Код</th>
    </tr>

    @foreach ($exam->enrollments as $enrollment)
    <tr>
        <td class="py-5 text-center border-black">{{  $enrollment->foreignNational->full_name }} </td>
        <td class="py-5 text-center border-black">{{ substr($enrollment?->exam_code, 0, 3) . "  " .  substr($enrollment?->exam_code, 3, 3) }}</td>
    </tr>
    @endforeach

</table>

@endsection