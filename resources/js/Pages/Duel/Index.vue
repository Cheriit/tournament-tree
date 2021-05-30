<template>
    <teleport to="head">
        <title>List of duels</title>
    </teleport>

    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Your duels
            </h2>
        </template>

        <div class="flex flex-wrap justify-center">
            <div class="my-6 w-full max-w-6xl">
                <div class="mx-auto sm:px-6">
                    <div class="bg-white shadow-xl sm:rounded-lg pb-10">
                        <section-title class="pt-5 pl-5 pb-3">
                            <template #title class="text-center">
                                🏆 Your duel menu 😀
                            </template>
                            <template #description class="text-center">
                                Duels that requires your action
                            </template>
                        </section-title>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Tournament name
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Tournament date
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Opponent name
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase "></th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="duel in unfinished_duels" :key="duel.id" class="hover:bg-gray-200">
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 font-medium text-gray-900">
                                        <inertia-link :href="route('tournament.show', duel.tournament_id)" class="underline text-blue-600">{{ duel.name }}</inertia-link>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 text-gray-900">
                                        {{ duel.date }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 text-gray-900">
                                        {{ duel.opponent_name }}
                                    </div>
                                </td>

                                <td class="pr-4 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                    <jet-secondary-button @click="()=>openModal(duel.id, duel.opponent_name)">
                                        Enter results
                                    </jet-secondary-button>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <section-title class="pt-5 pl-5 pb-3">
                            <template #description class="text-center">
                                Your past duel results
                            </template>
                        </section-title>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Tournament name
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Tournament date
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Opponent name
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase">
                                Your result
                            </th>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="duel in finished_duels" :key="duel.id" class="hover:bg-gray-200">
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 font-medium text-gray-900">
                                        <inertia-link :href="route('tournament.show', duel.tournament_id)" class="underline text-blue-600">{{ duel.name }}</inertia-link>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 text-gray-900">
                                        {{ duel.date }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 text-gray-900">
                                        {{ duel.opponent_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 text-gray-900">
                                        {{ duel.my_result == true ? "Winner" : "Looser" }}
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <dialog-modal :show="isModalOpen" @close="closeModal">
            <template #title>
                Enter results of your match against {{currentDuelOpponent}}
            </template>
            <template #content>
                <div class="mt-4">
                    <jet-label for="duel_state" :value="`Did you won your duel against ${currentDuelOpponent}?`" required="1" />
                    <select id="duel_state" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="form.state" autocomplete="duel_state" ref="duel_state" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                    <input-error :message="form.errors.state" class="mt-2" />
                    <input-error :message="form.errors.id" class="mt-2" />
                </div>
            </template>
            <template #footer>
                <jet-secondary-button @close="closeModal">
                    Cancel
                </jet-secondary-button>

                <jet-button class="ml-2" @click="submitResult" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Submit results
                </jet-button>
            </template>
        </dialog-modal>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import JetInput from "@/Jetstream/Input";
import JetLabel from "@/Jetstream/Label";
import Pagination from "@/Components/Pagination";
import SectionTitle from "../../Jetstream/SectionTitle";
import JetButton from "@/Jetstream/Button";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import DialogModal from "../../Jetstream/DialogModal";
import Checkbox from "../../Jetstream/Checkbox";

export default {
    components: {
        Checkbox,
        DialogModal,
        SectionTitle,
        Pagination,
        JetButton,
        JetLabel,
        JetInput,
        AppLayout,
        JetSecondaryButton
    },
    props: {
        unfinished_duels: Array,
        finished_duels: Array
    },
    data() {
        return {
            isModalOpen: false,
            form: this.$inertia.form({
                id: null,
                state: null,
            }),
            currentDuelOpponent: null
        }
    },
    methods: {
        openModal(id, opponent) {
            this.isModalOpen = true
            this.form.id = id;
            this.currentDuelOpponent = opponent;
        },
        submitResult() {
            this.form.post(
                route('tournament.duel.update'), {
                    preserveScroll: true,
                    onSuccess: () => this.closeModal(),
                    onFinish: () => this.form.reset()
                }
            )
        },
        closeModal() {
            this.isModalOpen = false;
            this.form().reset();
        }
    }
}
</script>

<style scoped>

</style>
