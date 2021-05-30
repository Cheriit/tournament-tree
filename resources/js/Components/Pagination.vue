<template>
    <div class="mt-6 -mb-1 flex flex-wrap justify-center">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm border rounded text-gray-400" :class="{ 'ml-auto': link.label === 'Next' }" ><span v-html="link.label"></span></div>
            <inertia-link v-else :key="key" class="mr-1 mb-1 px-4 py-3 text-sm border rounded hover:bg-white focus:border-indigo-500 focus:text-indigo-500" :class="{ 'bg-white': link.active, 'ml-auto': link.label === 'Next' }" :href="link.url" preserve-scroll><span v-html="link.label"></span></inertia-link>
        </template>
    </div>
</template>

<script>
export default {
    name: "Pagination",
    props: {
        links: Array,
    }
}
</script>
