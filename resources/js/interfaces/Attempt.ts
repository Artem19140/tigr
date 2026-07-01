import { Exam } from "./Exam";
import { ForeignNational } from "./ForeignNational";
import { Task } from "./Task";
import { Violation } from "./Violation";

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
    checkedAt:string
}

export interface AttemptChecking{
    id:number,
    status:string,
    expiredAt:string,
    tasks: Task[],
    checkedAt:string
}

export interface AttemptMonitoring{
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
    availability:{
        annul:boolean,
        violations:boolean,
        speaking:boolean
    },
    violations:Array<Violation>
}