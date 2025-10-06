<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    employees: Object,
    companies: Array,
    errors: Object
});

const pagination = ref({
    current: props.employees.meta.current_page,
    pageSize: props.employees.meta.per_page,
    total: props.employees.meta.total,
});

const columns = [
    { title: '#', key: 'index' },
    { title: 'First Name', dataIndex: 'first_name', key: 'first_name' },
    { title: 'Last Name', dataIndex: 'last_name', key: 'last_name' },
    { title: 'Company', key: 'company' },
    { title: 'Email', dataIndex: 'email', key: 'email' },
    { title: 'Phone', dataIndex: 'phone', key: 'phone' },
    { title: 'Action', key: 'action' },
];

const handleTableChange = (paginationData) => {
    router.get('/employees', { page: paginationData.current }, { preserveState: true });
};

const selectedCompany = ref(null);
const companyModal = ref(null);
const showModal = ref(false);
const form = ref({
    id: null,
    first_name: "",
    last_name: "",
    email: "",
    phone: "",
    company_id: null
});
const isEdit = ref(false);
const showDeleteModal = ref(false);
const deleteId = ref(null);

const openEditModal = (employee) => {
    isEdit.value = true;
    form.value = {
        id: employee.id,
        first_name: employee.first_name,
        last_name: employee.last_name,
        company_id: employee.company_id,
        email: employee.email,
        phone: employee.phone,
    };
    showModal.value = true;
};

const openAddModal = () => {
    isEdit.value = false;
    resetForm();
    showModal.value = true;
};

const resetForm = () => {
    form.value = {
        id: null,
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        company_id: null,
    };
};

const handleSubmit = () => {
    const data = new FormData();
    data.append("first_name", form.value.first_name);
    data.append("last_name", form.value.last_name);
    data.append("email", form.value.email);
    data.append("phone", form.value.phone);
    data.append("company_id", form.value.company_id);

  if (isEdit.value) {
    data.append('_method', 'PUT');
    router.post(route('employees.update', form.value.id), data, {
      onSuccess: () => (showModal.value = false),
      onError: (err) => { console.log(err) },
    });
  } else {
    router.post(route('employees.store'), data, {
      onSuccess: () => (showModal.value = false),
      onError: (err) => { console.log(err) },
    });
  }
};


//delete 
const confirmDelete = (id) => {
  deleteId.value = id;
  showDeleteModal.value = true;
};

const deleteEmployee = () => {
  router.delete(`/employees/${deleteId.value}`, {
    onSuccess: () => {
      showDeleteModal.value = false;
    },
  });
};
const getCompanyName = (company_id) => {
    const company = props.companies.data.find(item => item.id === company_id);
    return company ? company.name : '-';
}
const showCompany = (company_id) => {
  const company = props.companies.data.find(item => item.id === company_id);
  selectedCompany.value = company;
  companyModal.value = true;
};
</script>

<template>

    <Head title="Employees" />
    <AuthenticatedLayout>
        <div class="p-6 bg-gray-50 min-h-screen">
            <a-card title="Employees">
                <a-button type="primary" class="mb-4" @click="openAddModal">
                    Add Employee
                </a-button>
                <a-table :columns="columns" :data-source="employees.data" :pagination="pagination" row-key="id"
                    @change="handleTableChange">
                    <template #bodyCell="{ column, record, index }">
                        <template v-if="column.key === 'index'">
                            {{ (pagination.current - 1) * pagination.pageSize + index + 1 }}
                        </template>
                        <template v-else-if="column.key === 'company'">
                            <a @click="showCompany(record.company_id)">{{ getCompanyName(record.company_id) }}</a>
                        </template>
                        <template v-else-if="column.key === 'action'">
                            <a-space>
                                <a-button type="link" @click="openEditModal(record)">Edit</a-button>
                                <a-button type="link" danger @click="confirmDelete(record.id)">
                                    Delete
                                </a-button>
                            </a-space>
                        </template>
                    </template>
                </a-table>

                <a-modal
                    v-model:open="companyModal"
                    title="Company Details"
                    :footer="null"
                    >
                    <p><strong>Name:</strong> {{ selectedCompany?.name }}</p>
                    <p><strong>Email:</strong> {{ selectedCompany?.email }}</p>
                    <p>
                        <strong>Website:</strong>
                        <a :href="selectedCompany?.website" target="_blank">{{ selectedCompany?.website }}</a>
                    </p>
                    <img v-if="selectedCompany?.logo" :src="selectedCompany.logo" class="h-20" />
                    </a-modal>

                <!--Modal Add & EDit -->
                <a-modal v-model:open="showModal" :title="isEdit ? 'Edit Employee' : 'Add Employee'" @ok="handleSubmit"
                    ok-text="Save">
                    <a-alert
                    v-if="Object.keys(props.errors).length"
                        type="error"
                        show-icon
                        message="Please fix the following errors:"
                        >
                        <template #description>
                            <ul>
                            <li v-for="(msg, field) in props.errors" :key="field">{{ msg }}</li>
                            </ul>
                        </template>
                    </a-alert>
                    <a-form layout="vertical">
                        <a-form-item
                        label="Company"
                        :validate-status="errors.company_id ? 'error' : ''"
                        :help="errors.company_id"
                        >
                            <a-select
                                v-model:value="form.company_id"
                                placeholder="Select company"
                                allow-clear
                            >
                                <a-select-option
                                    v-for="company in props.companies"
                                    :key="company.id"
                                    :value="company.id"
                                >
                                {{ company.name }}
                                </a-select-option>
                            </a-select>
                        </a-form-item>
                        <a-form-item label="First Name">
                            <a-input v-model:value="form.first_name" placeholder="Employee first name" />
                        </a-form-item>
                        <a-form-item label="Last Name">
                            <a-input v-model:value="form.last_name" placeholder="Employee last name" />
                        </a-form-item>
                        <a-form-item label="Email">
                            <a-input v-model:value="form.email" placeholder="Employee email" />
                        </a-form-item>
                        <a-form-item label="Phone">
                            <a-input v-model:value="form.phone" placeholder="Employee Phone" />
                        </a-form-item>
                    </a-form>
                </a-modal>

                <!-- Delete Confirmation Modal -->
                <a-modal v-model:open="showDeleteModal" title="Confirm Delete" ok-text="Delete" ok-type="danger"
                    @ok="deleteEmployee">
                    <p>Are you sure you want to delete this employee?</p>
                </a-modal>
            </a-card>
        </div>
    </AuthenticatedLayout>
</template>
