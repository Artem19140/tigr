@extends('pdf.layouts.base')
@section('title')
    Коды
@endsection

@section('content')

<h3 class="text-center">{{ $exam->shortName() }} • {{ $exam->beginTimeLocal()->format('H:i, d.m.Y') }}</h3>
<table class="table">
    <tr>
        <th class="text-center border-black">ФИО</th>
        <th class="text-center border-black">Код</th>
    </tr>

    @foreach ($exam->enrollments as $enrollment)
    <tr>
        <td class="text-center border-black" style="font-size: medium;">
            {{  $enrollment->foreignNational->full_name }} 
        </td>
        
        <td class="text-center border-black" style="font-size: large;">
            <strong>
                {{ substr($enrollment?->exam_code, 0, 3) . "  " .  substr($enrollment?->exam_code, 3, 3) }}
            </strong>
        </td>
    </tr>
    @endforeach

</table>

@endsection