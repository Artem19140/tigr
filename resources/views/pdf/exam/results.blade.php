@extends('pdf.layouts.base')

@section('title')
    Результаты
@endsection

@push('style')
    
    th, td {
        border: 1px solid black;
        text-align:center;
        font-size:9px;
    }

    th {
    white-space: normal;
    word-break: normal;
    overflow-wrap: break-word;
}
    
@endpush

@section('content')

@include('pdf.exam.exam-statement', [
    'exam' => $exam, 
    'statementTable' => $statementTable
])

<div class="page-break"></div>

@include('pdf.exam.exam-marks', [
    'exam' => $exam, 
    'markTable' => $markTable
])

@endsection