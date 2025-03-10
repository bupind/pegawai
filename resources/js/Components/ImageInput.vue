<script setup>
import { reactive, watchEffect } from "vue";
import { PhotoIcon, XMarkIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    images: { type: [String, Array], default: null },
    preview: { type: [String, Array], default: null },
    multiple: { type: Boolean, default: false },
    maxFiles: { type: Number, default: 5 },
    maxSize: { type: Number, default: 2048 }, // KB
    allowedTypes: { type: Array, default: () => ["image/jpeg", "image/png", "image/gif"] },
    source: { type: String, default: "image" },
});

const emit = defineEmits(["fileChange"]);

const data = reactive({
    images: [],
    files: [],
    error: null,
});

// Menampilkan pratinjau dari props jika ada gambar sebelumnya
watchEffect(() => {
    data.images = [];
    if (props.preview) {
        data.images = Array.isArray(props.preview) ? [...props.preview] : [props.preview];
    } else if (props.images) {
        data.images = Array.isArray(props.images) ? [...props.images] : [props.images];
    }
});

// Fungsi memilih gambar
const selectImage = async (e) => {
    const files = Array.from(e.target.files);

    // Cek batas maksimal file
    if (props.multiple && data.files.length + files.length > props.maxFiles) {
        data.error = `Maksimal hanya bisa memilih ${props.maxFiles} gambar`;
        return;
    }

    data.error = null;
    const validFiles = [];

    for (const file of files) {
        if (file.size > props.maxSize * 1024) {
            data.error = `Ukuran file ${file.name} melebihi ${props.maxSize} KB`;
            return;
        }
        if (!props.allowedTypes.includes(file.type)) {
            data.error = `Format file ${file.name} tidak diperbolehkan`;
            return;
        }

        validFiles.push(file);
    }

    const base64Images = await Promise.all(validFiles.map(toBase64));

    if (props.multiple) {
        data.images = [...data.images, ...base64Images];
        data.files = [...data.files, ...validFiles];
    } else {
        data.images = [base64Images[0]]; // Ganti gambar lama jika mode single
        data.files = [validFiles[0]];
    }

    emit("fileChange", { files: [...data.files], source: props.source }); // Hanya update file, tidak submit
};

// Fungsi menghapus gambar
const removeImage = (index) => {
    if (!confirm("Apakah Anda yakin ingin menghapus gambar ini?")) {
        return; // Batalkan penghapusan jika user menekan 'Batal'
    }

    // Hapus gambar dari array tanpa menyebabkan submit
    data.images.splice(index, 1);
    data.files.splice(index, 1);

    // Emit perubahan untuk memperbarui tampilan, tanpa submit
    emit("fileChange", { files: [...data.files], source: props.source });
};

// Konversi file ke Base64
const toBase64 = (file) =>
    new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = (error) => reject(error);
    });
</script>

<template>
    <div>
        <!-- Mode single image -->
        <div v-if="!multiple && data.images.length" class="relative w-full">
            <img class="w-full h-64 object-cover rounded-md" :src="data.images[0]" alt="Selected Image" />
            <button
                @click.stop="removeImage(0)"
                class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded opacity-75 hover:opacity-100"
            >
                <XMarkIcon class="w-5 h-5" />
            </button>
        </div>

        <!-- Mode multiple images -->
        <div v-if="multiple" class="grid grid-cols-3 gap-4">
            <div v-for="(img, index) in data.images" :key="index" class="relative group">
                <img class="w-full h-24 object-cover rounded-md" :src="img" alt="Selected Image" />
                <button
                    @click.stop="removeImage(index)"
                    class="absolute top-1 right-1 bg-red-500 text-white p-1 rounded opacity-75 hover:opacity-100"
                >
                    <XMarkIcon class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Input File -->
        <label
            v-if="data.images.length < maxFiles"
            class="flex items-center justify-center w-full h-24 border-2 border-dashed rounded-md cursor-pointer mt-2"
        >
            <PhotoIcon class="w-10 h-10 text-gray-400" />
            <input
                class="hidden"
                type="file"
                accept="image/jpeg,image/png,image/gif"
                :multiple="multiple"
                @change="selectImage"
            />
        </label>

        <p v-if="data.error" class="text-red-500 text-sm mt-2">{{ data.error }}</p>
    </div>
</template>
