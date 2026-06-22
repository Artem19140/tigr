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
    examTypeId:number,
    enrollments?:Array<Enrollment>,
    enrollmentsCount:number,
    addressId:number,
    cancelledAt:string,
    permissions:ExamActionsPermissions,
    availability:availability,
    documents:{
        id:number
    }
}

export interface ExamIndex{
    id:number,
    name:string,
    shortName:string,
    beginTime:string,
    status:string
    enrollmentsCount:number,
}

export interface ExamCalendar{
    id:number,
    start:string,
    end:string,
    name:string,
    status:string
}

export interface ExamType{
    id:number,
    name:string
}

export interface ExamChecking{
    id:number,
    shortName:string,
    beginTime:string,
    enrollments:Enrollment[]
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

export interface ExamMonitoring  {
    id:number,
    name:string,
    shortName:string,
    beginTime:string,
    endTime:string,
    protocolComment:string,
    status:string,
    hasSpeakingTasks:boolean,
    enrollments:Array<Enrollment>,
    polling:boolean,
    availability:{
        protocolComment:boolean
    }
}
interface DocumentAvailble{
    available:boolean,
    reason:string | null,
    code:string | null
}

export interface ExamActionsPermissions {
  documents: {
    codes: boolean
    protocol: boolean
    results: boolean
    list: boolean
  }

  actions: {
    edit: boolean
    cancell: boolean
  }

  enrollments: {
    view: boolean
    statement: boolean
    payment: boolean
  }

  videos:{
    view:boolean
  }
}

interface availability{
    actions: {
        edit: boolean
        cancell: boolean
    }
    documents: {
        list:DocumentAvailble,
        protocol:DocumentAvailble,
        results:DocumentAvailble,
        codes:DocumentAvailble
    }
}

export interface ExamPagePermissions{
    create:boolean,
    frdo:boolean,
    flatTable:boolean
}