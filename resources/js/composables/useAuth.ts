import { usePage } from "@inertiajs/vue3"

export const useAuth = () => {
    const page = usePage()
    const user  = page.props?.auth?.user ?? null
    const roles = user?.roles ?? [];

    return {user}
}