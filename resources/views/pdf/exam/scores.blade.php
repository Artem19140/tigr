@extends('pdf.layouts.base')

@section('title')
    Баллы
@endsection

@section('content')
@include('pdf.exam.scores-body', [
    'exam' => $exam, 
    'headers' => $headers,
    'rows' => $rows,
])

@endsection