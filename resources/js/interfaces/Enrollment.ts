import { Attempt, AttemptConduct } from "./Attempt";
import { Exam } from "./Exam";
import { ForeignNationalEnrollment } from "./ForeignNational";

export interface Enrollment{
    id:number,
    foreignNational:ForeignNationalEnrollment,
    hasPayment:boolean,
    exam: Exam,
    attempt:Attempt | null,
    examResult:ExamStatus,
    regNumber: string,
    actions:{
        payment:{
            url: string | null
            disabled: boolean
        }
        statement:{
            url: string | null
        },
        changeRegNumber:{
            url: string | null
        }
    }
}

type ExamStatus = 'absent' | 'annulled' | 'failed' | 'passed'

export interface EnrollmentConduct{
    id:number,
    foreignNational:ForeignNationalEnrollment,
    hasPayment:boolean,
    attempt:AttemptConduct | null,
    availability:{
        payment:boolean
    }
}