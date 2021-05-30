<template>
    <teleport to="head">
        <title>Tournament details</title>
    </teleport>

    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🏆 Tournament details
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <action-section>
                <template #title>
                    {{ tournament.name }}
                </template>
                <template #description>
                    {{ tournament.date }}
                </template>
                <template #content>
                    <div class="-mx-6 px-6">
                        <p class="leading-6 block w-100 mb-3 text-2xl">
                            <span class="text-lg">
                                Organisator:
                            </span>
                            {{ tournament.organisator.name }}
                        </p>
                        <p class="leading-6 block w-100 mb-3 text-2xl">
                            <span class="text-lg">
                                Category:
                            </span>
                            {{ tournament.category.name }}
                        </p>
                        <p class="leading-6 block w-100 mb-3 text-2xl">
                            <span class="text-lg">
                                Place:
                            </span>
                            <span v-if="tournament.latitude != null">
                                <a :href="`http://www.google.com/maps/place/${tournament.latitude},${tournament.longitude}`" target="_blank" class="underline text-blue-600">{{ tournament.place }}</a>
                            </span>
                            <span v-else>
                                {{ tournament.place }}
                            </span>
                        </p>
                        <p class="leading-6 block w-100 mb-3 text-2xl">
                            <span class="text-lg">
                                Participants:
                            </span>
                            {{tournament.entries.length}}/{{ tournament.max_participants }}
                        </p>
                        <p class="leading-6 block w-100 mb-3 text-2xl">
                            <span class="text-lg">
                                Ranked players:
                            </span>
                            {{tournament.ranked_players}}
                        </p>

                        <template v-if="tournament.current_stage <= tournament.max_stage_number">
                            <p v-if="tournament.current_stage > 0" class="leading-6 block w-100 mb-3 text-2xl">
                            <span class="text-lg">
                                Stage:
                            </span>
                                {{ tournament.current_stage }} / {{ tournament.max_stage_number }}
                            </p>
                        </template>
                        <template v-else>
                            <p class="leading-6 block w-100 mb-3 text-2xl">
                            <span class="text-lg">
                                Tournament has finished
                            </span>
                            </p>
                        </template>
                        <template v-if="tournament.logos.length > 0">
                            <p  class="leading-6 block w-100 mb-3 text-2xl">
                            <span class="text-lg">
                                Sponsors:
                            </span> <br/>
                            </p>
                            <div class="flex flex-wrap -mx-4 -mb-8">
                                <div v-for="logo in tournament.logos" :key="logo.id" class="md:w-1/4 px-4 mb-8"><img class="rounded shadow-md" :src="logo.path" alt=""></div>
                            </div>
                        </template>
                    </div>
                </template>
            </action-section>
            <section-border />
            <template v-if="!is_enrolled && !is_organisator && tournament.current_stage == 0">
                <form-section @submitted="submitEntry" >
                    <template #title>
                        Enroll to tournament
                    </template>
                    <template v-if="$page.props.user" #form >
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="licence_number" value="Licence number" required="1"/>
                            <jet-input id="licence_number" type="text" class="mt-1 block w-full" v-model="entryForm.licence_number" ref="licence_number" autocomplete="licence_number" required />
                            <input-error :message="entryForm.errors.licence_number" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4" >
                            <jet-label for="current_ranking" value="Current ranking" required="1"/>
                            <jet-input id="current_ranking" type="number" class="mt-1 block w-full" v-model="entryForm.current_ranking" ref="current_ranking" autocomplete="current_ranking" required />
                            <input-error :message="entryForm.errors.current_ranking" class="mt-2" />
                        </div>
                    </template>
                    <template v-else #actions>
                        Log in to enroll to tournament
                    </template>
                    <template #actions>
                        <action-message :on="entryForm.recentlySuccessful" class="mr-3">
                            Saved.
                        </action-message>
                        <jet-button :class="{ 'opacity-25': entryForm.processing }" :disabled="entryForm.processing">
                            Enroll to tournament
                        </jet-button>
                    </template>
                </form-section>
                <section-border />
            </template>
            <action-section v-if="is_organisator">
                <template #title>
                    Manage tournament
                </template>
                <template #content>
                    <form @submit.prevent="submitLogo" class="mb-6">
                        <input type="file" @input="logoForm.file = $event.target.files[0]" />
                        <input-error :message="logoForm.errors.file" class="mt-2" /><br/>
                        <jet-button type="submit" class="mt-4">➕ Add logo</jet-button>
                    </form>

                    <inertia-link class="mr-4" :href="route('tournament.edit', tournament.id)">
                        <jet-secondary-button>
                            Edit tournament
                        </jet-secondary-button>
                    </inertia-link>
                    <jet-danger-button class="mr-4" @click="toggleModal">
                        Cancel tournament
                    </jet-danger-button>
                </template>
            </action-section>
            <section-border v-if="is_organisator"/>

            <action-section>
                <template #title>
                    Tournament participants
                </template>
                <template #content>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Ranking
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Licence plate
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Position
                            </th>
                            <th class="pr-4 py-3 bg-gray-50"></th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="entry in tournament.entries" :key="entry.id" class="hover:bg-gray-200">
                            <td class="px-6 py-4 whitespace-no-wrap">
                                <div class="text-sm leading-5 font-medium text-gray-900">
                                    {{ entry.player.name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ entry.current_ranking }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ entry.licence_number }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap">
                                <div class="text-sm leading-5 text-gray-900">
                                    {{ entry.current_position }}
                                </div>
                            </td>
                            <td class="pr-4 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
<!--                                <inertia-link :href="route('tournament.show', tournament.id)" class="text-sm text-gray-700 underline">Details</inertia-link>-->
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </template>
            </action-section>
            <section-border />


        </div>
    </app-layout>

    <dialog-modal :show="showDeleteModal" @close="toggleModal">
        <template #title>
            Cancel this tournament
        </template>

        <template #content>
            Are you sure you want to cancel this tournament?
        </template>

        <template #footer>
            <jet-secondary-button @click="toggleModal">
                No
            </jet-secondary-button>

            <inertia-link method="delete" :href="route('tournament.destroy', tournament.id)">
                <jet-danger-button class="ml-2" >
                    Cancel this tournament
                </jet-danger-button>
            </inertia-link>
        </template>
    </dialog-modal>

</template>

<script>
import { useForm } from '@inertiajs/inertia-vue3'

import AppLayout from '@/Layouts/AppLayout';
import ActionSection from "../../Jetstream/ActionSection";
import FormSection from "@/Jetstream/FormSection";
import JetButton from "../../Jetstream/Button";
import JetDangerButton from "../../Jetstream/DangerButton";
import JetSecondaryButton from '../../Jetstream/SecondaryButton'
import DialogModal from "../../Jetstream/DialogModal";
import InputError from "../../Jetstream/InputError";
import SectionBorder from '../../Jetstream/SectionBorder';
import JetLabel from '../../Jetstream/Label';
import JetInput from '../../Jetstream/Input';
import ActionMessage from "../../Jetstream/ActionMessage";

export default {
    name: "Details.vue",
    components: {
        JetButton,
        JetDangerButton,
        JetSecondaryButton,
        DialogModal,
        ActionSection,
        AppLayout,
        InputError,
        FormSection,
        SectionBorder,
        JetInput,
        JetLabel,
        ActionMessage
    },
    props: {
        tournament: Object,
        is_enrolled: Boolean,
        is_organisator: Boolean,
    },
    data() {
        return {
            showDeleteModal: false,
            logoForm: useForm({
                file: null,
            }),
            entryForm: useForm({
                tournament_id: this.tournament.id,
                licence_number: null,
                current_ranking: null
            })
        }
    },
    methods: {
        toggleModal() {
            this.showDeleteModal = ! this.showDeleteModal;
        },
        submitLogo() {
            this.logoForm.post(this.route('tournament.logo.store', this.tournament.id), {
                preserveScroll: true,
                onSuccess: () => logoForm.reset()
            })
        },
        submitEntry() {
            this.entryForm.post(this.route('tournament.enroll'))
        }
    }
}
</script>

<style scoped>

</style>
