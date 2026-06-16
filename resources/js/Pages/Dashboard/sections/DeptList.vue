<script setup lang="ts">
import {ref} from "vue";
import PayBack from "@/Pages/Dashboard/sections/PayBack.vue";
import {useHelpers} from "@/Composables/helpers";
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import type {Company, User} from '@interfaces/dashboard';

defineProps<{
    company?: Company;
    users: User[];
    runner?: User | null;
    simulated?: boolean;
    userCount: number;
}>();

const {formatMoney} = useHelpers();

const isPaybackOpen = ref(false);

const shortenName = (name: string, start: number, end: number): string => {
    const lastSpaceIndex = name.lastIndexOf(" ", end);
    if (lastSpaceIndex > end - 10) {
        end = lastSpaceIndex + 1;
    }
    return name.substring(start, end);
};
</script>
<template>
    <div class="panel">
        <h2 class="panel-title">Runners</h2>
        <table v-if="userCount" class="table-brut dept-list__table">
            <tbody>
            <tr v-for="user in users" :key="user.id" class="dept-list__row">
                <td class="dept-list__name">
                    {{ shortenName(user.name, 0, 15) }}
                </td>
                <td class="dept-list__runner-cell">
                    <div v-if="runner && user.id === runner.id">
                        <FontAwesomeIcon
                            icon="fa-solid fa-paper-plane"
                            :class="simulated ? 'dept-list__runner-icon--simulated' : 'dept-list__runner-icon--selected'"
                            :title="simulated ? 'Simulated runner' : 'Selected runner'"
                            class="dept-list__runner-icon"
                        />
                    </div>
                </td>
                <td class="dept-list__dept-cell">
                    <template v-if="user.id === $page.props.auth.user.id">
                        <button
                            type="button"
                            @click="isPaybackOpen = true"
                            :class="user.dept > 0 ? 'tag--teal' : 'tag--sun'"
                            class="tag tag--bold">
                            {{ formatMoney(user.dept) }}
                        </button>
                        <PayBack :user="user" :users="users" :open="isPaybackOpen" @close="isPaybackOpen = false"></PayBack>
                    </template>
                    <template v-else>
                        <span
                            :class="user.dept > 0 ? 'tag--teal' : 'tag--sun'"
                            class="tag tag--bold">{{ formatMoney(user.dept) }}</span>
                    </template>
                </td>
            </tr>
            </tbody>
        </table>
        <div v-else>
            <div class="empty-action">
                <a :href="route('company.show')" class="chunk chunk--teal">
                    <FontAwesomeIcon icon="fa-solid fa-person-running" class="dept-list__cta-icon" />
                    Create your first runner
                </a>
            </div>
        </div>
    </div>
</template>

<style scoped>
@import "@css/pages/dashboard/sections/dept-list.css";
</style>
