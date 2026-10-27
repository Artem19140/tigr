@extends('pdf.layouts.base')

@section('title')
    Согласие
@endsection

@section('content')
    @include(
        'pdf.enrollment.enrollment-approval-body', [
            'foreignNational' => $enrollment->foreignNational, 
            'enrollment' => $enrollment
        ])
@endsection