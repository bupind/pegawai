<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DropdownLoader from "@/Components/DropdownLoader.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import {PlusIcon} from "@heroicons/vue/24/outline";

const show = ref(false);
const props = defineProps({
    title: String,
    statuses: Object,
    types: Object,
});

const form = useForm({
    employeeId: "",
    registrationNumber: "",
    recommendationNumber: "",
    validFrom: "",
    validUntil: "",
    status: "",
});

const statuse = computed(() => Object.entries(props.statuses).map(([value, label]) => ({label, value})));
const submit = () => {
    form.post(route("license.store"), {
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
        <PrimaryButton class="flex rounded-none items-center justify-start gap-2" @click.prevent="show = true">
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
                        <InputLabel :value="lang().label.employee" for="employeeId"/>
                        <DropdownLoader
                                v-model="form.employeeId"
                                apiUrl="dropdown/employees"
                                placeholder="Pilih Salah Satu"
                                :error="form.errors.employeeId"
                        />
                        <InputError :message="form.errors.employeeId"/>
                    </div>
                    <div class="space-y-1">
                        <InputLabel for="registrationNumber" :value="lang().label.registrationNumber"/>
                        <TextInput id="registrationNumber" v-model="form.registrationNumber"
                                   :error="form.errors.registrationNumber"
                                   :placeholder="lang().placeholder.cluster"
                                   autocomplete="off" class="block w-full"
                                   type="text"
                        />
                        <InputError :message="form.errors.registrationNumber"/>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-1/2">
                            <InputLabel :value="lang().label.validFrom" for="validFrom"/>
                            <TextInput id="registrationNumber" v-model="form.validFrom"
                                       :error="form.errors.validFrom"
                                       :placeholder="lang().placeholder.validFrom"
                                       autocomplete="off" class="block w-full"
                                       type="date"
                            />
                            <InputError :message="form.errors.validFrom"/>
                        </div>
                        <div class="w-1/2">
                            <InputLabel :value="lang().label.validUntil" for="validUntil"/>
                            <TextInput id="registrationNumber" v-model="form.validUntil"
                                       :error="form.errors.validUntil"
                                       :placeholder="lang().placeholder.validUntil"
                                       autocomplete="off" class="block w-full"
                                       type="date"
                            />
                            <InputError :message="form.errors.validUntil"/>
                        </div>
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
