<script setup lang="ts">
import Authenticated from "@/Layouts/Authenticated.vue";
import {useForm, router} from '@inertiajs/vue3';
import {ref} from 'vue';
import Modal from '@/Components/ui/modal-component.vue';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import type {Company} from '@interfaces/dashboard';
import type {CompanyUser} from '@interfaces/company';

defineProps<{
    user?: CompanyUser;
    company?: Company;
    users: CompanyUser[];
}>();

const toggle = ref(false);

const form = useForm<{ name: string | null; email: string | null }>({
    name: null,
    email: null,
});

const invite = (): void => {
    form.post(route('invite'), {
        onSuccess: () => toggle.value = false,
    });
};

const togglePermission = (type: string, user: CompanyUser): void => {
    router.post(route('user.permissions'), {
        user_id: user.id,
        type: type,
    }, {
        preserveState: true,
        replace: true,
        only: ['users']
    });
};

const deleteUser = (user: CompanyUser): void => {
    if (confirm("Do you really want to delete this user?")) {
        router.post(route('user.delete'), {
            user_id: user.id,
        }, {
            preserveState: true,
            replace: true,
            only: ['users']
        });
    }
};
</script>
<template>
    <Authenticated>
        <div class="app-page">
            <div class="page">
                <div class="page-container">
                    <div class="company__grid">
                        <div class="panel">
                            <div class="company__head">
                                <h1 class="company__title">Users</h1>
                                <button type="button" @click="toggle = true" class="chunk chunk--teal">
                                    Invite new user
                                </button>
                            </div>

                            <div class="company__scroll">
                                <table class="table-brut">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Edit stores</th>
                                        <th>Edit users</th>
                                        <th class="company__num">Remove</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="user in users" :key="user.id">
                                        <td class="company__cell-name">{{ user.name }}</td>
                                        <td class="company__cell-email">{{ user.email }}</td>
                                        <td>
                                            <input type="checkbox" v-model="user.canEditStore"
                                                   class="field-checkbox"
                                                   @change="togglePermission('edit-store', user)"/>
                                        </td>
                                        <td>
                                            <input type="checkbox" v-model="user.canEditCompany"
                                                   class="field-checkbox"
                                                   @change="togglePermission('edit-company', user)"/>
                                        </td>
                                        <td class="company__num">
                                            <div class="company__remove">
                                                <button v-if="$page.props.auth.user?.id !== user.id"
                                                        type="button"
                                                        class="icon-btn icon-btn--danger"
                                                        @click="deleteUser(user)">
                                                    <FontAwesomeIcon icon="fa-solid fa-xmark" class="icon-btn__icon" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <Modal :open="toggle" title="Invite new user" @close="toggle = false">
                        <form id="invite-user-form" @submit.prevent="invite">
                            <div class="company__form">
                                <div>
                                    <span class="field-label">Name</span>
                                    <input type="text"
                                           v-model="form.name"
                                           placeholder="Name"
                                           class="field-input"
                                           required
                                    />
                                    <div v-if="form.errors.name" class="field-error">{{ form.errors.name }}</div>
                                </div>

                                <div>
                                    <span class="field-label">Email</span>
                                    <input type="email"
                                           v-model="form.email"
                                           placeholder="Email"
                                           class="field-input"
                                           required
                                    />
                                    <div v-if="form.errors.email" class="field-error">{{ form.errors.email }}</div>
                                </div>
                            </div>
                        </form>
                        <template #actions>
                            <button type="submit"
                                    form="invite-user-form"
                                    class="chunk chunk--teal"
                                    :disabled="form.processing"
                            >Send Invitation
                            </button>
                            <button type="button" @click="toggle = false" class="chunk chunk--ghost">Cancel</button>
                        </template>
                    </Modal>
                </div>
            </div>
        </div>
    </Authenticated>
</template>

<style scoped>
@import "@css/pages/company.css";
</style>
