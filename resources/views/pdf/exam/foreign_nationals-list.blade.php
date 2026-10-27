@extends('pdf.layouts.base')
@section('title')
    Список
@endsection

@section('content')

<h3 class="text-center"> {{ $exam->shortName() }} • {{ $exam->beginTimeLocal()->format('H:i, d.m.Y') }}</h3>
@php

$leftAlign = [
    'fullName',
    'fullNameLatin',
    'fullPassport'
]

@endphp
<table class="table text-center border-black">
    <tr>
        @foreach ($headers as $header)
            <td class="text-center border-black bold">{{ $header }}</td>
        @endforeach
    </tr>


    @foreach ($rows as $key => $row)
        <tr>
            @foreach ($row as $key => $data)
            <td 
                class="text-center border-black" 
                style="
                    {{  in_array( $key, $leftAlign ) 
                        ? 'text-align: left; padding-left: 5px;' 
                        : '' 
                    }}
                "
            >
                {{ $data }}
            </td>
            @endforeach
        </tr>
    @endforeach

</table>

@endsection