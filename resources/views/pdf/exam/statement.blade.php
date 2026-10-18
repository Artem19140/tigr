@extends('pdf.layouts.base')
@section('title')
    Ведомость
@endsection

@section('content')

@include('pdf.exam.statement-body',[
    'exam' => $exam, 
    'headers' => $headers,
    'rows' => $rows,
])

@endsection