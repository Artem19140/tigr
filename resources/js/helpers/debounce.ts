export const debounce = (func:Function, delay:number) => {
    let timeoutId: ReturnType<typeof setTimeout> | null = null
    return (...args: any) => {
        if(timeoutId){
            clearTimeout(timeoutId)
        }
        timeoutId = setTimeout(() => {
            func(...args)
            timeoutId = null;
        }, delay)
    }
}

export const autosave = (func:Function, period:number) => {
    let timeoutSet: boolean = false
    return (...args: any) => {

    
        if (timeoutSet) {
            return 
        }
        timeoutSet = true

        setTimeout(async () => {
            timeoutSet = false
            func(...args)
        }, period)
    }
}