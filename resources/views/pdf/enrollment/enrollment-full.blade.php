@extends('pdf.layouts.base')
@section('title')
    Заявление
@endsection
@section('content')
    @include('pdf.enrollment.enrollment-statement-body', ['enrollment' => $enrollment])
        <div class="page-break"></div>
    @include('pdf.enrollment.enrollment-approval-body', ['foreignNational' => $enrollment->foreignNational])
@endsection