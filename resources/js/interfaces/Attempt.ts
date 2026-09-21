import { Exam } from "./Exam";
import { ForeignNational } from "./ForeignNational";
import { Task } from "./Task";

export interface Attempt{
    id:number,
    startedAt:number,
    finishedAt:string | null,
    isPassed:boolean | null,
    status:string,
    exam:Exam,
    expiredAt:string,
    tasks: Task[],
    foreignNational: ForeignNational,
    examName:string,
    endsAt:number,
    serverNow:number,
    minDurationMinutes:number,
    tasksCount:number,
    checkedAt:string,
    checkUrl: string
}

export interface AttemptReview{
    id:number,
    status:string,
    expiredAt:string,
    tasks: Task[],
    checkedAt:string
}

export interface AttemptConduct{
    id:number, 
    startedAt:string,
    finishedAt:string | null,
    status:string,
    foreignNational: ForeignNational,
    endsAt:number,
    serverNow:number,
    speakingFinishedAt: string | null,
    speakingStartedAt: string | null,
    tasks: Task[],
    checkedAt:string,
    actions:{
        destroy:{
            url: string,
            disabled:boolean
        },
        speaking:{
            url: string,
            disabled:boolean
        },
    }
}