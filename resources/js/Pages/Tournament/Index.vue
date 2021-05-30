<template>
    <teleport to="head">
        <title>List of tournaments</title>
    </teleport>

    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tournament panel
            </h2>
        </template>

        <div class="flex flex-wrap justify-center">

            <div class="my-6 w-full  lg:w-1/2 max-w-xl">
                <div class=" mx-auto sm:px-6 ">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <section-title class="pt-5 pl-5 pb-3">
                            <template #title>
                                🏆 Find the tournament you're looking for 😀
                            </template>
                        </section-title>

                        <div class="grid grid-cols-6 gap-6 p-3">
                            <div class="col-span-6 sm:col-span-3">
                                <jet-label for="filter" value="Filter" />
                                <jet-input id="filter" type="text" class="mt-1 block w-full" v-model="searchBar.filter" autofocus />
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <jet-label for="category" value="Category"/>
                                <select id="category" class="mt-1 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm" v-model="searchBar.category_id" autocomplete="category_id" ref="category_id" required>
                                    <option></option>
                                    <option v-for="category in categories" :value="category.id" :key="category.id">{{category.name}}</option>
                                </select>
                            </div>

                        </div>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Organisator
                                </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                    Starting date
                                </th>
                                <th class="pr-4 py-3 bg-gray-50"></th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="tournament in tournaments.data" :key="tournament.id" class="hover:bg-gray-200">
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 font-medium text-gray-900">
                                        {{ tournament.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 text-gray-900">
                                        {{ tournament.organisator_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 text-gray-900">
                                        {{ tournament.start_date }}
                                    </div>
                                </td>
                                <td class="pr-4 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                    <inertia-link :href="route('tournament.show', tournament.id)" class="text-sm text-gray-700 underline">Details</inertia-link>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="pb-3 flex flex-wrap justify-center">
                            <pagination class="content-center" :links="tournaments.links" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="my-6 w-full lg:w-1/2 max-w-xl">
                <div class=" mx-auto sm:px-6">
                    <div class="bg-white shadow-xl sm:rounded-lg pb-9">
                        <section-title class="pt-5 pl-5 pb-3">
                            <template #title class="text-center">
                                <template v-if="$page.props.user">
                                    🏆 Your tournament panel 😀
                                </template>
                                <template v-else>
                                    Log in to see your tournament panel 😥
                                </template>
                            </template>
                        </section-title>

                        <template v-if="$page.props.user">

                            <section-title class="pt-5 pl-5 pb-3">
                                <template #description class="text-center">
                                    <template v-if="$page.props.user">
                                        Tournaments you've enrolled
                                    </template>
                                </template>
                            </section-title>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Organisator
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Starting date
                                    </th>
                                    <th class="pr-4 py-3 bg-gray-50"></th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="tournament in enrolled_tournaments" :key="tournament.id" class="hover:bg-gray-200">
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        <div class="text-sm leading-5 font-medium text-gray-900">
                                            {{ tournament.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        <div class="text-sm leading-5 text-gray-900">
                                            {{ tournament.organisator_name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        <div class="text-sm leading-5 text-gray-900">
                                            {{ tournament.start_date }}
                                        </div>
                                    </td>
                                    <td class="pr-4 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                        <inertia-link :href="route('tournament.show', tournament.id)" class="text-sm text-gray-700 underline">Details</inertia-link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <section-title class="pt-5 pl-5 pb-3">
                                <template #description class="text-center">
                                    <template v-if="$page.props.user">
                                        Tournaments you're organising
                                    </template>
                                </template>
                            </section-title>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                        Starting date
                                    </th>
                                    <th class="pr-4 py-3 bg-gray-50"></th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="tournament in organised_tournaments" :key="tournament.id" class="hover:bg-gray-200">
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        <div class="text-sm leading-5 font-medium text-gray-900">
                                            {{ tournament.name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-no-wrap">
                                        <div class="text-sm leading-5 text-gray-900">
                                            {{ tournament.start_date }}
                                        </div>
                                    </td>
                                    <td class="pr-4 py-4 whitespace-no-wrap text-right text-sm leading-5 font-medium">
                                        <inertia-link :href="route('tournament.show', tournament.id)" class="text-sm text-gray-700 underline">Details</inertia-link>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </template>
                    </div>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-2">

        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout';
import JetInput from "@/Jetstream/Input";
import JetLabel from "@/Jetstream/Label";
import Pagination from "@/Components/Pagination";
import throttle from 'lodash/throttle';
import SectionTitle from "../../Jetstream/SectionTitle";

export default {
    components: {
        SectionTitle,
        Pagination,
        JetLabel,
        JetInput,
        AppLayout,
    },
    props: {
        search: String,
        tournaments: Object,
        category: Number,
        categories: Object,
        organised_tournaments: Object,
        enrolled_tournaments: Object
    },
    data() {
        return {
            searchBar: {
                filter : this.search,
                category_id : this.category
            }
        }
    },
    watch: {
        searchBar: {
            handler: throttle(function () {
                let route = this.route('tournament.index', {search: this.searchBar.filter, category: this.searchBar.category_id})
                this.$inertia.get(route, {}, { preserveState: true })
            }, 150),
            deep: true
        }
    }
}
</script>

<style scoped>

</style>
