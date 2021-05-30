<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit {{defName}}
            </h2>
        </template>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <form-section @submitted="submit">
                    <template #title>
                        Tournament details
                    </template>
                    <template #form>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="name" value="Name" required="1"/>
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" ref="name" autocomplete="name" required />
                            <input-error :message="form.errors.name" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4" >
                            <jet-label for="place" value="Place" required="1"/>
                            <jet-input id="place" type="text" class="mt-1 block w-full" v-model="form.place" ref="place" autocomplete="place" required />
                            <input-error :message="form.errors.place" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="latitude" value="Latitude" :required="form.longitude != null" />
                            <jet-input id="latitude" type="number" class="mt-1 block w-full" v-model="form.latitude" ref="latitude" autocomplete="latitude" :required="form.longitude != null"/>
                            <input-error :message="form.errors.latitude" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="longitude" value="Longitude" :required="form.latitude != null"/>
                            <jet-input id="longitude" type="number" class="mt-1 block w-full" v-model="form.longitude" ref="longitude" autocomplete="longitude" :required="form.latitude != null"/>
                            <input-error :message="form.errors.longitude" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="date" value="Date" required="1" />
                            <jet-input id="date" type="date" class="mt-1 block w-full" v-model="form.date" ref="date" autocomplete="date" required />
                            <input-error :message="form.errors.date" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="max_participants" value="Max participants" required="1" />
                            <jet-input id="max_participants" type="number" class="mt-1 block w-full" v-model="form.max_participants" ref="max_participants" autocomplete="max_participants" />
                            <input-error :message="form.errors.max_participants" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="ranked_players" value="Ranked players" required="1" />
                            <jet-input id="ranked_players" type="number" class="mt-1 block w-full" v-model="form.ranked_players" ref="ranked_players" autocomplete="ranked_players" />
                            <input-error :message="form.errors.ranked_players" class="mt-2" />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="category_id" value="Category" required="1" />
                            <select id="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="form.category_id" autocomplete="category_id" ref="category_id" required>
                                <option v-for="category in categories" :value="category.id" :key="category.id">{{category.name}}</option>
                            </select>
                            <input-error :message="form.errors.category_id" class="mt-2" />
                        </div>

                    </template>
                    <template #actions>
                        <action-message :on="form.recentlySuccessful" class="mr-3">
                            Updated.
                        </action-message>
                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Update tournament
                        </jet-button>
                    </template>
                </form-section>
            </div>

    </app-layout>

</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetButton from "@/Jetstream/Button";
import FormSection from "@/Jetstream/FormSection";
import ActionMessage from "@/Jetstream/ActionMessage";
import JetLabel from "@/Jetstream/Label";
import JetInput from "@/Jetstream/Input";
import InputError from "@/Jetstream/InputError";
import {useForm} from "@inertiajs/inertia-vue3";

export default {
    components: {
        InputError,
        JetInput,
        JetLabel,
        ActionMessage,
        FormSection,
        JetButton,
        AppLayout
    },
    props: {
        tournament: Object,
        categories: Object
    },
    data() {
        return {
            form: useForm({
                name: this.tournament.name,
                place: this.tournament.place,
                latitude: this.tournament.latitude,
                longitude: this.tournament.longitude,
                date: this.tournament.date,
                max_participants: this.tournament.max_participants,
                ranked_players: this.tournament.ranked_players,
                category_id: this.tournament.category_id,
                _token: this.$page.props.csrf_token,
            }),
            defName: this.tournament.name
        }
    },
    methods: {
        submit() {
            this.form
                .transform(data => ({
                    ...data,
                    latitude: data.latitude === '' ? null : data.latitude,
                    longitude: data.longitude === '' ? null : data.longitude,
                }))
                .patch(this.route('tournament.update', this.tournament.id))
        }
    },
    watch: {
        form: {
            handler: function () {
                if (this.form.latitude === '')
                    this.form.latitude = null
                if (this.form.longitude === '')
                    this.form.longitude = null

            },
            deep: true
        }
    }
}
</script>

<style scoped>

</style>
