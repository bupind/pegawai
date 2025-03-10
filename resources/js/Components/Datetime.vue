<script setup>
import {ref} from "vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

const date = ref(null);
</script>

<template>
    <Datepicker v-model="date" :enable-time-picker="true"/>
</template>


<style>
.dark .dp__input {
    background-color: rgb(15 23 42 / var(--tw-bg-opacity, 1)) !important;
    color: rgb(203 213 225 / var(--tw-text-opacity, 1)) !important;
}
</style>
