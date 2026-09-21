<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import { useConfirmationOptionsDialog } from '@/composables/useConfirmationOptionsDialog';
import { EmployeeIndex } from '@/interfaces/Employee';
import { mdiAccountCancel, mdiPencil } from '@mdi/js';

const props= defineProps<{
  employee:EmployeeIndex
}>()

const form = useForm()

const destroy = async () => {
  const useConfirmation = useConfirmationOptionsDialog()
  const ok = await useConfirmation.open(
    `Удалить ${props.employee.fullName}?
    У сотрудника больше не будет доступа к системе`
  )
  if(!ok) return
  form.delete(props.employee.destroyUrl)
}
</script>

<template>
    <div class="flex items-center justify-end gap-1">
        <v-tooltip
            v-if="employee.editUrl"
            text="Редактировать"
            location="top"
        >
            <template #activator="{ props }">
                <v-btn
                    v-bind="props"
                    :icon="mdiPencil"
                    variant="text"
                    density="comfortable"
                    size="small"
                    color="grey-darken-1"
                    @click="router.visit(employee.editUrl)"
                />
            </template>
        </v-tooltip>

        <v-tooltip
            v-if="employee.destroyUrl"
            text="Исключить"
            location="top"
        >
            <template #activator="{ props }">
                <v-btn
                    v-bind="props"
                    :icon="mdiAccountCancel"
                    variant="text"
                    density="comfortable"
                    size="small"
                    color="error"
                    :loading="form.processing"
                    :disabled="form.processing"
                    @click="destroy"
                />
            </template>
        </v-tooltip>
    </div>
</template>