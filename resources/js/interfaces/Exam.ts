import { Employee } from "./Employee"
import { Enrollment } from "./Enrollment"

export interface Exam{
    id:number,
    shortName:string,
    beginTime:string,
    endTime:string,
    capacity:number,
    group:number | null,
    sessionNumber:number | null,
    comment:string,
    examiners:Array<Employee>,
    address:string,
    createdAt:string | null,
    cancelledReason:string | null,
    status:string
    enrollments: Array<Enrollment>,
    enrollmentsCount:number,
    cancelledAt:string,
    documents:{
        id:number
    }
}

export interface ExamEdit{
    examTypeId: number
    addressId: number
    comment: string
    examiners: Array<Employee>
    beginTime: string,
    capacity: number,
    hasEnrollment: boolean
}

export interface ExamIndex{
    id: number,
    name: string,
    shortName: string,
    beginTime: string,
    status: string
    enrollmentsCount: number,
    showUrl: string
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
    comment: string,
    examiners: Array<number | Employee>,
    time: string | null,
    date: string | null,
    capacity: number | null
}

export interface ExamFilters  {
    dateFrom?: string ,
    cancelled?: boolean,
    examTypeId?: number,
    dateTo?: string,
    id?:number 
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