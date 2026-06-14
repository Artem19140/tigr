import { ForeignNational } from "@/interfaces/ForeignNational";
import { ref } from "vue";

const foreignNationals = ref<{}>({})

export const useForeignNationals = () => {
    const set = (foreignNational :ForeignNational) => {
        foreignNationals.value[foreignNational.id] = foreignNational
    }

    const get = (foreignNationalId : number) => {
        return foreignNationals.value[foreignNationalId]
    }
    
    const remove = (foreignNationalId : number) => {
        foreignNationals.delete(foreignNationalId)
    }
    return {set, get, remove, foreignNationals}
}