import { Enrollment } from "./Enrollment"

export interface ForeignNational{
  id:number,
  name:string,
  surname:string,
  patronymic:string | undefined,
  nameLatin:string
  surnameLatin:string
  patronymicLatin:string | undefined
  passportNumber:string | undefined
  passportSeries:string | undefined
  issuedBy:string | null
  issuedDate:string | null
  phone: string | null
  citizenship:string | null
  dateBirth:string | null
  comment:string | null
  gender:string | null
  addressReg:string,
}

export interface ForeignNationalView {
  id:number
  fullName:string
  fullNameLatin:string
  fullPassport:string
  enrollments:Array<Enrollment>,
  documents: Array<ForeignNationalDocument>
  creatorFullName:string
  issuedBy:string | null
  issuedDate:string | null
  phone: string | null
  dateBirth:string | null
  citizenship:string | null
}

export interface ForeignNationalEdit extends ForeignNational{
  id:number
  fullName:string
  fullNameLatin:string
  fullPassport:string
  issuedBy:string | null
  issuedDate:string | null
  phone: string | null
  dateBirth:string | null
  citizenship:string | null
  noPatronymic: boolean
  noPassportNumber: boolean
  noPassportSeries: boolean
  noPatronymicLatin:boolean,
  noPhone:boolean
}

export interface ForeignNationalEditForm extends Omit<ForeignNationalEdit, 
    'fullName' | 'fullNameLatin' | 'id' | 'fullPassport'
>{

}

export interface ForeignNationalCreate extends Omit<ForeignNational,
    'id' | 'fullName' | 'fullNameLatin'
>{
  issuedBy:string | null
  issuedDate:string | null
  phone: string | null
  dateBirth:string | null
  citizenship:string | null
  hasPayment:boolean 
  examId: number | null
  passportTranslate: File | null
  passport: File | null
  noPatronymic: boolean
  noPassportNumber: boolean
  noPassportSeries: boolean
  noPatronymicLatin:boolean,
  noPhone:boolean
}

export interface ForeignNationalEnrollment{
  id:number
  fullName:string
  fullPassport:string
}

export interface ForeignNationalIndex{
  id:number,
  fullName:string,
  fullPassport:string,
  showUrl: string
}

export type ForeignNationalFilters= {
  surname?: string ,
  name?: string ,
  patronymic?: string ,
  passportSeries?: string ,
  passportNumber?: string ,
  id?: number ,
}

export interface ForeignNationalDocument {
  id: number,
  updatedAt: string,
  type: string,
  actions: {
    downloadUrl: string,
    updateUrl: string
  }
}