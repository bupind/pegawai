<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SelectInput from "@/Components/SelectInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import {PlusIcon} from "@heroicons/vue/24/outline";

const show = ref(false);
const props = defineProps({
    title: String,
    statuses: Object,
    genders: Object,
});

const form = useForm({
    code: "",
    name: "",
    gender: "",
    status: "",
});

const statuse = computed(() => Object.entries(props.statuses).map(([value, label]) => ({label, value})));
const gender = computed(() => Object.entries(props.genders).map(([value, label]) => ({label, value})));

const submit = () => {
    form.post(route("employee.store"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => null,
        onFinish: () => null,
    });
};
const closeModal = () => {
    show.value = false;
    form.errors = {};
    form.reset();
};
</script>
<template>
    <div>
        <PrimaryButton
                class="flex rounded-none items-center justify-start gap-2"
                @click.prevent="show = true"
        >
            <PlusIcon class="w-4 h-auto"/>
            <span class="hidden md:block">{{ lang().button.add }}</span>
        </PrimaryButton>
        <DialogModal :show="show" @close="closeModal">
            <template #title>
                {{ lang().label.add }} {{ props.title }}
            </template>

            <template #content>
                <form class="space-y-2" @submit.prevent="submit">
                    <div class="space-y-1">
                        <InputLabel for="name" :value="lang().label.Code"/>
                        <TextInput
                                id="name"
                                v-model="form.code"
                                type="text"
                                class="block w-full"
                                autocomplete="code"
                                :placeholder="lang().label.Code"
                                :error="form.errors.Code"
                        />
                        <InputError :message="form.errors.code"/>
                    </div>
                    <div class="space-y-1">
                        <InputLabel for="name" :value="lang().label.Employee"/>
                        <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="block w-full"
                                autocomplete="name"
                                :placeholder="lang().label.name"
                                :error="form.errors.name"
                        />
                        <InputError :message="form.errors.name"/>
                    </div>
                    <div class="space-y-1">
                        <InputLabel for="gender" :value="lang().label.Gender"/>
                        <SelectInput
                                id="status"
                                v-model="form.gender"
                                :dataSet="gender"
                                class="block w-full"
                                :error="form.errors.gender"
                        />
                        <InputError :message="form.errors.status"/>
                    </div>
                    <div class="space-y-1">
                        <InputLabel for="status" :value="lang().label.Status"/>
                        <SelectInput
                                id="status"
                                v-model="form.status"
                                :dataSet="statuse"
                                class="block w-full"
                                :error="form.errors.status"
                        />
                        <InputError :message="form.errors.status"/>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    {{ lang().button.cancel }}
                </SecondaryButton>

                <PrimaryButton
                        class="ml-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="submit"
                >
                    {{ lang().button.save }} {{ form.processing ? "..." : "" }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>
