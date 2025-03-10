<script setup>
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SelectInput from "@/Components/SelectInput.vue";
import ActionButton from "@/Components/ActionButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import {onUpdated, ref, computed} from "vue";
import {PencilIcon} from "@heroicons/vue/24/outline";

const emit = defineEmits(["open"]);
const show = ref(false);
const props = defineProps({
    title: String,
    pegawai: Object,
    statuses: Array,
    pegawaipegawai: Array,
});

const statusepe = computed(() => Object.entries(props.pegawaipegawai).map(([value, label]) => ({label, value})));
const statuse = computed(() => Object.entries(props.statuses).map(([value, label]) => ({label, value})));
const form = useForm({
    name: "",
    email: "",
    phone_number: "",
    jabatan_id: "",
    departemen_id: "",
    status_kepegawaian: "",
    status: "",
});
onUpdated(() => {
    if (show) {
        form.name = props.pegawai?.user?.first_name;
        form.email = props.pegawai?.user?.email;
        form.phone_number = props.pegawai?.user?.phone_number;
        form.jabatan_id = props.pegawai?.jabatan_id;
        form.departemen_id = props.pegawai?.departemen_id;
        form.status_kepegawaian = props.pegawai?.status_kepegawaian;
        form.status = props.pegawai?.status;
    }
});

const submit = () => {
    form.put(route("pegawai.update", props.pegawai?.id), {
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
        <ActionButton
                v-tooltip="lang().label.edit"
                @click.prevent="(show = true), emit('open')"
        >
            <PencilIcon class="w-4 h-auto"/>
        </ActionButton>
        <DialogModal :show="show" @close="closeModal">
            <template #title>
                {{ lang().label.edit }} {{ props.title }}
            </template>

            <template #content>
                <form class="space-y-2" @submit.prevent="submit">

                    <div class="space-y-1">
                        <InputLabel for="name" :value="lang().label.name"/>
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
                        <InputLabel for="email" :value="lang().label.email"/>
                        <TextInput
                            id="name"
                            v-model="form.email"
                            type="email"
                            class="block w-full"
                            autocomplete="email"
                            :placeholder="lang().label.email"
                            :error="form.errors.email"
                        />
                        <InputError :message="form.errors.email"/>
                    </div>
                    <div class="space-y-1">
                        <InputLabel for="email" :value="lang().label.phone_number"/>
                        <TextInput
                            id="phone_number"
                            v-model="form.phone_number"
                            type="text"
                            class="block w-full"
                            autocomplete="email"
                            :placeholder="lang().label.phone_number"
                            :error="form.errors.phone_number"
                        />
                        <InputError :message="form.errors.phone_number"/>
                    </div>
                    <div class="space-y-1">
                        <InputLabel :value="lang().label.jabatan" for="jabatan_id"/>
                        <DropdownLoader
                            v-model="form.jabatan_id"
                            apiUrl="/system/dropdown/jabatan"
                            placeholder="Pilih Kategori"
                            :error="form.errors.jabatan_id"
                        />
                        <InputError :message="form.errors.jabatan_id"/>
                    </div>
                    <div class="space-y-1">
                        <InputLabel :value="lang().label.departement" for="departemen_id"/>
                        <DropdownLoader
                            v-model="form.departemen_id"
                            apiUrl="/system/dropdown/department"
                            placeholder="Pilih Kategori"
                            :error="form.errors.departemen_id"
                        />
                        <InputError :message="form.errors.departemen_id"/>
                    </div>
                    <div class="space-y-1">
                        <InputLabel for="status" :value="lang().label.statusepe"/>
                        <SelectInput
                            id="statusepe"
                            v-model="form.status_kepegawaian"
                            :dataSet="statusepe"
                            class="block w-full"
                            :error="form.errors.status_kepegawaian"
                        />
                        <InputError :message="form.errors.status_kepegawaian"/>
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
