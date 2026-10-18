@extends('pdf.layouts.base')

@section('title')
    Результаты
@endsection

@section('content')

@include('pdf.exam.statement-body', [
    'exam' => $exam, 
    'headers' => $statementTable['headers'],
    'rows' => $statementTable['rows'],
])

<div class="page-break"></div>

@include('pdf.exam.scores-body', [
    'exam' => $exam, 
    'headers' => $markTable['headers'],
    'rows' => $markTable['rows'],
])

@endsection