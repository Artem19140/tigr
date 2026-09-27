import { Attempt, AttemptConduct } from "./Attempt";
import { Exam } from "./Exam";
import { ForeignNationalEnrollment } from "./ForeignNational";

export interface Enrollment{
    id:number,
    foreignNational:ForeignNationalEnrollment,
    hasPayment:boolean,
    isLoading?: boolean,
    exam: Exam,
    attempt:Attempt | null,
    examResult:ExamStatus,
    actions:{
        payment:{
            url: string | null
            disabled: boolean
        }
        statement:{
            url: string | null
        }
    }
}

type ExamStatus = 'absent' | 'annulled' | 'failed' | 'passed'

export interface EnrollmentConduct{
    id:number,
    foreignNational:ForeignNationalEnrollment,
    hasPayment:boolean,
    isLoading?: boolean,
    attempt:AttemptConduct | null,
    availability:{
        payment:boolean
    }
}