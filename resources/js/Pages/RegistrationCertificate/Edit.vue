<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SelectInput from "@/Components/SelectInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DropdownLoader from "@/Components/DropdownLoader.vue";
import ActionButton from "@/Components/ActionButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import {computed, ref, onUpdated} from "vue";
import {PencilIcon} from "@heroicons/vue/24/outline";

const emit = defineEmits(["open"]);
const show = ref(false);
const props = defineProps({
    title: String,
    statuses: Object,
    types: Object,
    registrationcertificate: Object,
});

const statuse = computed(() => Object.entries(props.statuses).map(([value, label]) => ({label, value})));
const typee = computed(() => Object.entries(props.types).map(([value, label]) => ({label, value})));
const form = useForm({
    employeeId: "",
    type: "",
    registrationNumber: "",
    competence: "",
    certificateOfCompetenceNumber: "",
    validFrom: "",
    validUntil: "",
    status: "",
});
onUpdated(() => {
    if (show) {
        form.employeeId = props.registrationcertificate?.employeeId;
        form.type = props.registrationcertificate?.type;
        form.registrationNumber = props.registrationcertificate?.registrationNumber;
        form.competence = props.registrationcertificate?.competence;
        form.certificateOfCompetenceNumber = props.registrationcertificate?.certificateOfCompetenceNumber;
        form.validFrom = props.registrationcertificate?.validFrom;
        form.validUntil = props.registrationcertificate?.validUntil;
        form.status = props.registrationcertificate?.status;
    }
});

const submit = () => {
    form.put(route("registrationcertificate.update", props.registrationcertificate?.id), {
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
        <ActionButton v-tooltip="lang().label.edit" @click.prevent="(show = true), emit('open')">
            <PencilIcon class="w-4 h-auto"/>
        </ActionButton>
        <DialogModal :show="show" @close="closeModal">
            <template #title>
                {{ lang().label.edit }} {{ props.title }}
            </template>
            <template #content>

                <form class="space-y-2" @submit.prevent="submit">
                    <div class="space-y-1">
                        <InputLabel :value="lang().label.employeeId" for="employeeId"/>
                        <DropdownLoader
                                v-model="form.employeeId"
                                apiUrl="dropdown/employees"
                                placeholder="Pilih Salah Satu"
                                :error="form.errors.employeeId"
                        />
                        <InputError :message="form.errors.employeeId"/>
                    </div>
                    <div class="space-y-1">
                        <InputLabel for="type" :value="lang().label.type"/>
                        <SelectInput id="type" v-model="form.type"
                                     :dataSet="typee"
                                     class="block w-full"
                                     :error="form.errors.type"
                        />
                        <InputError :message="form.errors.type"/>
                    </div>
                    <div class="space-y-1">
                        <InputLabel for="registrationNumber" :value="lang().label.registrationNumber"/>
                        <TextInput id="registrationNumber" v-model="form.registrationNumber"
                                   :error="form.errors.registrationNumber"
                                   :placeholder="lang().placeholder.registrationNumber"
                                   autocomplete="off" class="block w-full"
                                   type="text"
                        />
                        <InputError :message="form.errors.registrationNumber"/>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-1/2">
                            <InputLabel for="competence" :value="lang().label.competence"/>
                            <TextInput id="competence" v-model="form.competence"
                                       :error="form.errors.competence"
                                       :placeholder="lang().placeholder.competence"
                                       autocomplete="off" class="block w-full"
                                       type="text"
                            />
                            <InputError :message="form.errors.competence"/>
                        </div>
                        <div class="w-1/2">
                            <InputLabel for="certificateOfCompetenceNumber"
                                        :value="lang().label.certificateOfCompetenceNumber"/>
                            <TextInput id="certificateOfCompetenceNumber" v-model="form.certificateOfCompetenceNumber"
                                       :error="form.errors.certificateOfCompetenceNumber"
                                       :placeholder="lang().placeholder.certificateOfCompetenceNumber"
                                       autocomplete="off" class="block w-full"
                                       type="text"
                            />
                            <InputError :message="form.errors.certificateOfCompetenceNumber"/>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <div class="w-1/2">
                            <InputLabel :value="lang().label.validFrom" for="validFrom"/>
                            <TextInput id="validUntil" v-model="form.validFrom"
                                       :error="form.errors.validFrom"
                                       :placeholder="lang().placeholder.validFrom"
                                       autocomplete="off" class="block w-full"
                                       type="date"
                            />
                            <InputError :message="form.errors.validFrom"/>
                        </div>
                        <div class="w-1/2">
                            <InputLabel :value="lang().label.validUntil" for="validUntil"/>
                            <TextInput id="validUntil" v-model="form.validUntil"
                                       :error="form.errors.validUntil"
                                       :placeholder="lang().placeholder.validUntil"
                                       autocomplete="off" class="block w-full"
                                       type="date"
                            />
                            <InputError :message="form.errors.validUntil"/>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <InputLabel for="status" :value="lang().label.status"/>
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
