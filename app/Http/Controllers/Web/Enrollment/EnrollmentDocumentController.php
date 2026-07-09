<?php

namespace App\Http\Controllers\Web\Enrollment;

use App\Models\Enrollment;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class EnrollmentDocumentController
{
    public function statement(
        Enrollment $enrollment,
    ): Response {
        
        $enrollment->load([
            'foreignNational',
            'exam.type',
            'creator'
        ]);

        $statementPdf = Pdf::loadView('pdf.enrollment.enrollment-full', [
            'enrollment' => $enrollment,
        ]);

        $fileName = "Заявление_согласие_{$enrollment->foreignNational->full_name}.pdf";
        
        return $statementPdf->stream($fileName);
    }
}
