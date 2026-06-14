import { ForeignNational } from "@/interfaces/ForeignNational";
import { reactive, ref } from "vue";

const foreignNationals = reactive<Map<number,ForeignNational>>(new Map)

export const useForeignNationals = () => {
    const set = (foreignNational :ForeignNational) => {
        foreignNationals.set(foreignNational.id, {... foreignNational})
    }

    const get = (foreignNationalId : number) => {
        return foreignNationals.get(foreignNationalId)
    }
    
    const remove = (foreignNationalId : number) => {
        foreignNationals.delete(foreignNationalId)
    }
    return {set, get, remove, foreignNationals}
}