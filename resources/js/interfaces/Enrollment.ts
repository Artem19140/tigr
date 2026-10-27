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
    cancelledAt: string | null
    actions:{
        payment: Action | undefined
        statement: Action | undefined,
        approval: Action | undefined,
        changeRegNumber: Action,
        delete: Action | undefined
    }
}

type ExamStatus = 'absent' | 'annulled' | 'failed' | 'passed'

export interface EnrollmentConduct{
    id:number,
    foreignNational:ForeignNationalEnrollment,
    hasPayment:boolean,
    attempt:AttemptConduct | null,
    actions: {
        recoveryCode: {
            url: string | null
        }
    }
}

interface Action{
    url: string,
    disabled: boolean | undefined
}