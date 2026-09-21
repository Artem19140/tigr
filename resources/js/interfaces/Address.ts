export interface Address{
    id:number,
    address:string,
    capacity:number,
    isActive:boolean,
    examsExists:boolean
}

export interface AddressIndex extends Omit<Address, ''> {
    destroyUrl: string,
    editUrl: string
}

export interface AddressEdit extends Omit<Address, ''> {
    backUrl: string,
    updateUrl: string
}

export interface AddressCreate extends Omit<Address, 'isActive' | 'examsExists' | 'id'> {
    storeUrl: string,
    backUrl: string
}