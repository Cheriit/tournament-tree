<template xmlns="http://www.w3.org/1999/html">
    <div :class="$attrs.class">
        <label v-if="label" class="mb-2 block text-gray-700 select-none" :for="id">{{ label }}:</label>
        <input
            :id="id"
            ref="input"
            v-bind="{...$attrs, class: null }"
            class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
            :class="{error: 'border-red-600'}"
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.targa.value)"
        />
        <div v-if="error" class="text-red-700 mt-2 text-sm">
            {{error}}
        </div>
    </div>
    <div v-show="message">
        <p class="text-sm text-red-600">
            {{ message }}
        </p>
    </div>
</template>

<script>
    export default {
        props: {
            id: {
                type: String,
                default() {
                    return `text-input-${Math.random() * 1000}`;
                }
            },
            type: {
                type: String,
                default: 'text'
            },
            modelValue: String,
            label: String,
            error: String
        },

        emits: ['update:modelValue'],

        methods: {
            focus() {
                this.$refs.input.focus()
            },
            select() {
                this.$refs.input.select()
            },
            setSelectionRange(start, end) {
                this.$refs.input.setSelectionRange(start, end)
            }
        }
    }
</script>

