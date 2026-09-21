import { Roles } from "@/constants/Roles"

export interface Employee{
    id:number,
    surname:string,
    name:string,
    patronymic:string | null,
    email:string,
    fullName:string,
    
}

export interface EmployeeFormI extends Omit<Employee, 'id' | 'roles' | 'fullName'>{
    roles:Array<number | undefined>
}

export interface EmployeeEdit extends Omit
    <Employee, 'fullName'>
{

}

export interface EmployeeIndex extends Omit<Employee, 'surname' | 'name' | 'patronymic'>{
    destroyUrl:string,
    editUrl:string
}

export interface Role{
    id:number,
    name:Roles
}