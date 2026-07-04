import { Attempt } from "./Attempt"
import { Employee } from "./Employee"
import { Enrollment } from "./Enrollment"

export interface ForeignNational{
  id:number,
  name:string,
  surname:string,
  patronymic:string | undefined,
  nameLatin:string,
  surnameLatin:string,
  patronymicLatin:string | undefined,
  passportNumber:string | undefined,
  passportSeries:string | undefined,
  issuedBy:string,
  issuedDate:string,
  phone: string | null,
  creator?:Employee | null,
  passport?:string | null,
  citizenship:string | null,
  dateBirth:string,
  attempts?:Array<Attempt> | null,
  fullName:string,
  fullNameLatin:string,
  fullPassport:string,
  passportTranslate?:string | null,
  comment:''
  gender:string | null,
  enrollments:Array<Enrollment>,
  creatorFullName:string,
  addressReg:string,
  documents: {
    id:number
  },
  permissions: ForeignNationalActionsPermissions
}

export interface ForeignNationalEnrollment{
  id:number,
  fullName:string,
  fullPassport:string,
  isLoading?: boolean,
}

export interface ForeignNationalIndex{
  id:number,
  fullName:string,
  fullPassport:string,
}

export type ForeignNationalFormI = Omit<
  ForeignNational,
  'id' | 'creator' | 'exams' | 'createdAt' | 'attempts' | 'exam' | 'fullName' | 'fullPassport' | 
  'passportTranslate' | 'passport' | 'creatorFullName' | 'enrollments'  | 'fullNameLatin' | 'permissions' | 'documents'
> & {
  passport: File | null
  passportTranslate: File | null
  noPassportNumber: boolean
  noPassportSeries: boolean
  noPatronymic: boolean
  noPatronymicLatin:boolean,
  noPhone:boolean
}

export type ForeignNationalEditForm = Omit<
  ForeignNational,
  'id' | 'creator' | 'exams' | 'createdAt' | 'attempts' | 'exam' | 'fullName' | 'fullPassport' | 
  'passportTranslate' | 'passport' | 'creatorFullName' | 'enrollments'  | 'fullNameLatin' | 'permissions' | 'documents'
> & {
  noPassportNumber: boolean
  noPassportSeries: boolean
  noPatronymic: boolean
  noPatronymicLatin:boolean,
  noPhone:boolean
}

export type ForeignNationalFilters= {
  surname: string | null,
  name: string | null,
  patronymic: string | null,
  passportSeries: string | null,
  passportNumber: string | null,
  id: number | null,
}

export interface ForeignNationalActionsPermissions{
  edit: boolean,
  enroll: boolean,
  documents:boolean,
  enrollments:boolean
}