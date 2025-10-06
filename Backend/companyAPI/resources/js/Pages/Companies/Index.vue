<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    companies: Object,
    errors: Object,
});

const pagination = ref({
    current: props.companies.meta.current_page,
    pageSize: props.companies.meta.per_page,
    total: props.companies.meta.total,
});

const columns = [
    { title: '#', key: 'index' },
    { title: 'Name', dataIndex: 'name', key: 'name' },
    { title: 'Email', dataIndex: 'email', key: 'email' },
    { title: 'Logo', key: 'logo' },
    { title: 'Website', key: 'website' },
    { title: 'Action', key: 'action' },
];

const showModal = ref(false);
const form = ref({
    id: null,
    name: "",
    email: "",
    website: "",
    logo: null,
    logoPreview: null,
});
const isEdit = ref(false);
const showDeleteModal = ref(false);
const deleteId = ref(null);

//handlers
const openAddModal = () => {
    isEdit.value = false;
    resetForm();
    showModal.value = true;
};
const resetForm = () => {
    form.value = {
        id: null,
        name: "",
        email: "",
        website: "",
        logo: null,
        logoPreview: null,
    };
};
const openEditModal = (company) => {
    isEdit.value = true;
    form.value = {
        id: company.id,
        name: company.name,
        email: company.email,
        website: company.website,
        logo: null,
        logoPreview: company.logo,
    };
    showModal.value = true;
};
const confirmDelete = (id) => {
  deleteId.value = id;
  showDeleteModal.value = true;
};

const handleSubmit = () => {
  const data = new FormData();
  data.append("name", form.value.name);
  data.append("email", form.value.email);
  data.append("website", form.value.website);
  if (form.value.logo) {
    data.append("logo", form.value.logo);
  }

  if (isEdit.value) {
    data.append('_method', 'PUT');
    router.post(route('companies.update', form.value.id), data, {
      onSuccess: () => (showModal.value = false),
      onError: (err) => { console.log(err) },
    });
  } else {
    router.post(route('companies.store'), data, {
      onSuccess: () => (showModal.value = false),
      onError: (err) => { console.log(err) },
    });
  }
};

const handleFileUpload = (e) => {
  const file = e.target.files[0];
  form.value.logo = file;
  form.value.logoPreview = URL.createObjectURL(file);
};


const handleTableChange = (paginationData) => {
    router.get('/companies', { page: paginationData.current }, { preserveState: true });
};

const deleteCompany = () => {
  router.delete(`/companies/${deleteId.value}`, {
    onSuccess: () => {
      showDeleteModal.value = false;
    },
  });
};
</script>

<template>
    <Head title="Companies" />
    <AuthenticatedLayout>
        <div class="p-6 bg-gray-50 min-h-screen">
            <a-card title="Companies">
                <a-button type="primary" class="mb-4" @click="openAddModal">
                    Add Company
                </a-button>
                <a-table :columns="columns" :data-source="companies.data" :pagination="pagination" row-key="id"
                    @change="handleTableChange">
                    <template #bodyCell="{ column, record, index }">
                        <template v-if="column.key === 'index'">
                            {{ (pagination.current - 1) * pagination.pageSize + index + 1 }}
                        </template>
                        <template v-else-if="column.key === 'logo'">
                            <img v-if="record.logo" :src="record.logo" class="h-10" />
                        </template>
                        <template v-else-if="column.key === 'website'">
                            <a :href="record.website" target="_blank">{{ record.website }}</a>
                        </template>
                        <template v-else-if="column.key === 'action'">
                            <a-space>
                                <a-button type="link" @click="openEditModal(record)">Edit</a-button>
                                <a-button type="link" @click="confirmDelete(record.id)" danger>Delete</a-button>
                            </a-space>
                        </template>
                    </template>
                </a-table>
            </a-card>

            <!--Modal Add & EDit -->
            <a-modal v-model:open="showModal" :title="isEdit ? 'Edit Company' : 'Add Company'" @ok="handleSubmit"
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
                    <a-form-item label="Name">
                        <a-input v-model:value="form.name" placeholder="Company name" />
                    </a-form-item>
                    <a-form-item label="Email">
                        <a-input v-model:value="form.email" placeholder="Company email" />
                    </a-form-item>
                    <a-form-item label="Website">
                        <a-input v-model:value="form.website" placeholder="https://example.com" />
                    </a-form-item>
                    <a-form-item label="Logo">
                        <input type="file" @change="handleFileUpload" />
                        <div v-if="form.logoPreview" class="mt-2">
                            <img :src="form.logoPreview" class="h-16 rounded" />
                        </div>
                    </a-form-item>
                </a-form>
            </a-modal>

            <!-- Delete Confirmation Modal -->
            <a-modal v-model:open="showDeleteModal" title="Confirm Delete" ok-text="Delete" ok-type="danger"
                @ok="deleteCompany">
                <p>Are you sure you want to delete this company?</p>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>