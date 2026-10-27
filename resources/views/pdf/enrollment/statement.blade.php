@extends('pdf.layouts.base')
@section('title')
    Заявление
@endsection
@section('content')
    @include(
        'pdf.enrollment.enrollment-statement-body', 
        ['enrollment' => $enrollment]
    )
@endsection