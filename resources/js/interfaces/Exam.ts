import { Employee } from "./Employee"
import { Enrollment } from "./Enrollment"
import { ForeignNational } from "./ForeignNational"

export interface Exam{
    id:number,
    name:string,
    shortName:string,
    beginTime:string,
    endTime:string,
    capacity:number,
    group:number | null,
    sessionNumber:number | null,
    comment:string,
    examiners:Array<Employee>,
    address:string,
    creator:Employee | null,
    createdAt:string | null,
    cancelledReason:string | null,
    status:string
    foreignNationals:Array<ForeignNational>,
    enrollments: Array<Enrollment>,
    enrollmentsCount:number,
    cancelledAt:string,
    documents:{
        id:number
    },
    hasEnrollment:boolean
}

export interface ExamIndex{
    id:number,
    name:string,
    shortName:string,
    beginTime:string,
    status:string
    enrollmentsCount:number,
}

export interface ExamType{
    id:number,
    name:string
}

export interface ExamReview{
    id:number,
    shortName:string,
    beginTime:string,
    enrollments:Enrollment[],
    cancelledAt:string
}

export interface ExamForm{
    examTypeId: number | null,
    addressId: number | null,
    comment:string,
    examiners: Array<number | Employee>,
    time:string | null,
    date:string | null,
    capacity:number | null
}

export interface ExamFilters  {
    dateFrom: string | undefined,
    cancelled: boolean | null,
    examTypeId: number | null,
    dateTo: string | null,
    id:number | null
}

export interface ExamConduct  {
    id:number,
    name:string,
    shortName:string,
    beginTime:string,
    endTime:string,
    protocolComment:string,
    status:string,
    hasSpeakingTasks:boolean,
    enrollments:Array<Enrollment>,
    cancelledAt:string,
}

export interface ExamDocument {
    url:string,
    availability:{
        disabled: boolean,
        code: string | null
    }
}