import { Attempt, AttemptMonitoring } from "./Attempt";
import { Exam } from "./Exam";
import { ForeignNationalEnrollment } from "./ForeignNational";

export interface Enrollment{
    id:number,
    foreignNational:ForeignNationalEnrollment,
    hasPayment:boolean,
    isLoading?: boolean,
    exam:Exam,
    attempt:Attempt | null,
    examResult:string,
    availability:EnrollmentAvailability
}

export interface EnrollmentAvailability{
    payment:boolean,
    ban:boolean,
    violations:boolean,
    speaking:boolean
}

export interface EnrollmentMonitoring{
    id:number,
    foreignNational:ForeignNationalEnrollment,
    hasPayment:boolean,
    isLoading?: boolean,
    attempt:AttemptMonitoring | null,
    availability:{
        payment:boolean
    }
}