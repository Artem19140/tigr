<script setup lang="ts">
import { useModals } from '@composables/useModals';
import ExamCreateModal from '@pages/Exam/Components/Modals/ExamCreateModal.vue';
import FrdoModal from '@/pages/Exam/Components/FrdoModal.vue';
import FlatTableModal from '@/pages/Exam/Components/FlatTableModal.vue';
import EmployeeCreateModal from '@pages/Center/Components/Employees/EmployeeCreateModal.vue';
import ExamEditModal from '@pages/Exam/Components/Modals/ExamEditModal.vue';
import ForeignNationalExportModal from '@/pages/ForeignNationals/Components/ForeignNationalExportModal.vue';
import StatisticsModal from '@/pages/ForeignNationals/Components/StatisticsModal.vue';
import AddressCreateModal from '@/pages/Center/Components/Addresses/AddressCreateModal.vue';
import MinistryEducationReportModal from '@/pages/ForeignNationals/Components/MinistryEducationReportModal.vue';
import ViolationModal from '@/pages/ExamMonitoring/Components/Violations/ViolationModal.vue';
import EmployeeEditModal from '@/pages/Center/Components/Employees/EmployeeEditModal.vue';
import LogoutAllDevicesModal from './LogoutAllDevicesModal.vue';
import CenterCreateModal from '@/pages/PlatformAdmin/Components/CenterCreateModal.vue';
import ForeignNationalEditModal from '@/pages/ForeignNationals/Components/ForeignNationalEditModal.vue';

const {modals, close} = useModals()

export type ModalName = keyof typeof modalMap

const modalComponent = (name: ModalName) => modalMap[name] ?? null

const modalMap = {
    examCreate: ExamCreateModal,
    frdo: FrdoModal,
    flatTable: FlatTableModal,
    employeeCreate: EmployeeCreateModal,
    foreignNationalEdit: ForeignNationalEditModal,
    examEdit: ExamEditModal,
    foreignNationalExport: ForeignNationalExportModal,
    statistics: StatisticsModal,
    addressCreate: AddressCreateModal,
    ministryEducationReport: MinistryEducationReportModal,
    violation: ViolationModal,
    employeeEdit:EmployeeEditModal,
    logoutAll:LogoutAllDevicesModal,
    centerCreate:CenterCreateModal
} as const

const closeModal = (id: number) => {
    close(id)
}
</script>

<template>
    <component
        v-for="modal in modals"
        :key="modal.id"
        v-bind="modal.data"
        :is="modalComponent(modal.name)"
        v-model = modal.isOpen
        @update:modelValue="closeModal(modal.id)"
    />
</template>