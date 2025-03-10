<script setup>
import {computed, ref, watch} from "vue";

const mimeTypes = {
    pdf: "application/pdf",
    zip: "application/zip",
    rar: "application/vnd.rar",
    doc: "application/msword",
    docx: "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    xls: "application/vnd.ms-excel",
    xlsx: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    ppt: "application/vnd.ms-powerpoint",
    pptx: "application/vnd.openxmlformats-officedocument.presentationml.presentation",
    jpg: "image/jpeg",
    jpeg: "image/jpeg",
    png: "image/png",
    gif: "image/gif",
    mp4: "video/mp4",
    mp3: "audio/mpeg",
};

const props = defineProps({
    modelValue: Array,
    multiple: {type: Boolean, default: false},
    maxFiles: {type: Number, default: 5},
    maxFileSize: {type: Number, default: 5},
    allowedTypes: {type: Array, default: () => ["pdf", "png", "jpg"]},
    existingFiles: {type: Array, default: () => []}
});

const emit = defineEmits(["update:modelValue", "removeFile"]);

const fileInput = ref(null);
const files = ref([]);
const errorMessage = ref("");

const allowedMimeTypes = computed(() =>
    props.allowedTypes.map((ext) => mimeTypes[ext] || ext)
);

watch(() => props.modelValue, (newValue) => {
    files.value = newValue || [];
});

const selectFiles = (event) => {
    handleFiles(event.target.files);
};

const handleDrop = (event) => {
    event.preventDefault();
    handleFiles(event.dataTransfer.files);
};

const handleFiles = (selectedFiles) => {
    let newFiles = [...files.value, ...Array.from(selectedFiles)];

    if (newFiles.length + props.existingFiles.length > props.maxFiles) {
        errorMessage.value = `Maksimal ${props.maxFiles} file yang bisa diunggah.`;
        return;
    }

    for (let file of newFiles) {
        if (file.size / 1024 / 1024 > props.maxFileSize) {
            errorMessage.value = `File ${file.name} melebihi batas ${props.maxFileSize}MB.`;
            return;
        }

        const fileExtension = file.name.split('.').pop().toLowerCase();
        if (!props.allowedTypes.includes(fileExtension)) {
            errorMessage.value = `Jenis file ${file.name} tidak diizinkan.`;
            return;
        }
    }

    files.value = newFiles;
    emit("update:modelValue", files.value);
    errorMessage.value = "";
};
const removeFile = (index) => {
    files.value.splice(index, 1);
    emit("update:modelValue", files.value);
};

const removeExistingFile = (index) => {
    emit("removeFile", index);
};

const remainingFiles = computed(() => props.maxFiles - files.value.length);
</script>

<template>
    <div class="space-y-2">
        <div
                class="border-2 p-6 rounded-md text-center cursor-pointer transition-all"
                @dragover.prevent
                @drop="handleDrop"
                @click="fileInput.click()"
        >
            <input
                    ref="fileInput"
                    type="file"
                    :multiple="multiple"
                    class="hidden"
                    @change="selectFiles"
                    :accept="allowedMimeTypes.join(', ')"
            />
            <p class="text-gray-500">
                Drag & Drop files here or <span class="text-primary font-bold">Click to Upload</span>
            </p>
            <p v-if="multiple" class="text-xs text-gray-400">
                Maksimal {{ maxFiles }} file, sisa bisa unggah: {{ remainingFiles }}
            </p>
            <p class="text-xs text-gray-400">
                Maksimal ukuran per file: {{ maxFileSize }}MB
            </p>
            <p class="text-xs text-gray-400">
                Jenis file yang diperbolehkan: {{ props.allowedTypes.join(", ") }}
            </p>
        </div>
        <p v-if="errorMessage" class="text-red-500 text-sm">{{ errorMessage }}</p>


        <div v-if="props.existingFiles.length > 0 || files.length > 0" class="mt-2 max-h-60 overflow-y-auto border p-2 rounded-md">
            <ul class="grid grid-cols-3 gap-4">
                <li v-for="(file, idx) in [...props.existingFiles, ...files]" :key="file.id || idx" class="flex flex-col items-center">
                    <template v-if="file.name.match(/\.(jpeg|jpg|png|gif)$/i)">
                        <img :src="file.path || URL.createObjectURL(file)" :alt="'Lampiran ' + (idx + 1)" class="w-32 h-32 object-cover rounded shadow">
                    </template>
                    <template v-else>
                        <div class="w-32 h-32 flex items-center justify-center bg-gray-200 rounded shadow">
                            <span class="text-gray-700 font-semibold">{{ file.name.split('.').pop().toUpperCase() }}</span>
                        </div>
                        <a :href="file.path || URL.createObjectURL(file)" target="_blank" class="text-blue-500 hover:underline mt-2">
                            Lihat File {{ idx + 1 }}
                        </a>
                    </template>
                    <button @click="file.path ? removeExistingFile(idx) : removeFile(idx)" class="text-red-500 hover:text-red-700 mt-2">
                        ✕
                    </button>
                </li>
            </ul>
        </div>



    </div>
</template>
