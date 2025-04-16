<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import SelectInput from "@/Components/SelectInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {useForm} from "@inertiajs/vue3";
import TextAreaInput from "@/Components/TextAreaInput.vue";
import ImageInput from "@/Components/ImageInput.vue";
import {computed} from "vue";

const props = defineProps({
    title: String,
    setting: Object,
    canLogins: Object,
});
const canLogin = computed(() => Object.entries(props.canLogins).map(([value, label]) => ({label, value})));

const form = useForm({
    favicon: null,
    logo: null,
    name: props.setting?.name,
    employeecanlogin: props.setting?.employeecanlogin,
    short_name: props.setting?.short_name,
    description: props.setting?.description,
    _method: "PUT",
});
const update = () => {
    form.post(route("setting.update", props.setting?.id), {
        preserveScroll: true,
        onSuccess: () => null,
        onError: () => null,
        onFinish: () => null,
    });
};

const fileChange = (value) => {
    if (value.source === "favicon") {
        form.favicon = value.files[0];
    } else if (value.source === "logo") {
        form.logo = value.files[0];
    }
};
</script>

<template>
    <AppLayout :title="props.title">
        <template #title>
            <span>{{ props.title }}</span>
        </template>

        <div>
            <div class="max-w-full mx-auto py-10 px-2">
                <FormSection>
                    <template #title> {{ lang().label.web_setting }}</template>

                    <template #description>
                        {{ lang().label.web_setting_description }}
                    </template>

                    <template #form>
                        <div class="flex gap-1">
                            <div class="w-1/2">
                                <InputLabel :value="lang().label.favicon" for="favicon"/>
                                <ImageInput
                                    v-model="form.favicon"
                                    :preview="props.setting.full_path_favicon"
                                    class="mt-1 block w-50 h-50"
                                    source="favicon"
                                    tooltip="Click to select/change favicon"
                                    @fileChange="fileChange"
                                />
                                <InputError :message="form.errors.favicon"/>
                            </div>
                            <div class="w-1/2">
                                <InputLabel :value="lang().label.logo" for="logo"/>
                                <ImageInput
                                    v-model="form.logo"
                                    :preview="props.setting.full_path_logo"
                                    class="mt-1 block w-50 h-50"
                                    source="logo"
                                    tooltip="Click to select/change logo"
                                    @fileChange="fileChange"
                                />
                                <InputError :message="form.errors.logo"/>
                            </div>
                        </div>


                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel :value="lang().label.name" for="name"/>
                            <TextInput
                                id="name"
                                v-model="form.name"
                                :error="form.errors.name"
                                :placeholder="lang().placeholder.name"
                                class="mt-1 block w-full"
                                type="text"
                            />
                            <InputError
                                :message="form.errors.name"
                                class="mt-2"
                            />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel
                                :value="lang().label.short_name"
                                for="short_name"
                            />
                            <TextInput
                                id="short_name"
                                v-model="form.short_name"
                                :error="form.errors.short_name"
                                :placeholder="lang().placeholder.short_name"
                                class="mt-1 block w-full"
                                type="text"
                            />
                            <InputError
                                :message="form.errors.short_name"
                                class="mt-2"
                            />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel
                                :value="lang().label.description"
                                for="description"
                            />
                            <TextAreaInput
                                id="description"
                                v-model="form.description"
                                :error="form.errors.description"
                                :placeholder="lang().placeholder.description"
                                class="mt-1 block w-full"
                                rows="4"
                            />
                            <InputError
                                :message="form.errors.description"
                                class="mt-2"
                            />
                        </div>
                        <div class="space-y-1">
                            <InputLabel for="employeecanlogin" :value="lang().label.canLogin"/>
                            <SelectInput
                                id="employeecanlogin"
                                v-model="form.employeecanlogin"
                                :dataSet="canLogin"
                                class="block w-full"
                                :error="form.errors.employeecanlogin"
                            />
                            <InputError :message="form.errors.employeecanlogin"/>
                        </div>
                    </template>

                    <template #actions>
                        <ActionMessage
                            :on="form.recentlySuccessful"
                            class="mr-3"
                        >
                            {{ lang().label.saved }}
                        </ActionMessage>

                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            @click="update"
                        >
                            {{ lang().button.save }}
                            {{ form.processing ? "..." : "" }}
                        </PrimaryButton>
                    </template>

                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>
