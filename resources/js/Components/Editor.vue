<script setup>
import { ref } from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
//import '@vueup/vue-quill/dist/vue-quill.snow.css';
import '@vueup/vue-quill/dist/vue-quill.snow.prod.css';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    contentType: {
        type: String,
        default: 'delta',
        validator: (value) => {
            return ['delta', 'html', 'text'].includes(value);
        },
    },
    toolbar: {
        type: [String, Array, Object],
        required: false,
        validator: (value) => {
            if (typeof value === 'string' && value !== '') {
                if (value.charAt(0) !== '#') {
                    const toolbarOptions = {
                        essential: [
                            [{header: [1, 2, 3, 4, 5, 6, false]}],
                            ['bold', 'italic', 'underline'],
                            [{list: 'ordered'}, {list: 'bullet'}, {align: []}],
                            ['blockquote', 'code-block', 'link'],
                            [{color: []}, 'clean'],
                        ],
                        minimal: [
                            [{header: 1}, {header: 2}],
                            ['bold', 'italic', 'underline'],
                            [{list: 'ordered'}, {list: 'bullet'}, {align: []}],
                        ],
                        full: [
                            ['bold', 'italic', 'underline', 'strike'],
                            ['blockquote', 'code-block'],
                            [{header: 1}, {header: 2}],
                            [{list: 'ordered'}, {list: 'bullet'}],
                            [{script: 'sub'}, {script: 'super'}],
                            [{indent: '-1'}, {indent: '+1'}],
                            [{direction: 'rtl'}],
                            [{size: ['small', false, 'large', 'huge']}],
                            [{header: [1, 2, 3, 4, 5, 6, false]}],
                            [{color: []}, {background: []}],
                            [{font: []}],
                            [{align: []}],
                            ['link', 'video', 'image'],
                            ['clean'],
                        ],
                    };
                    return Object.keys(toolbarOptions).indexOf(value) !== -1;
                }
                return true;
            }
            return true;
        }
    },
    placeholder: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'on-editor-ready']);
const input = ref(null);

const onUpdate = () => {
    const content = input.value.getContents();
    emit('update:modelValue', content);
}

const onReady = (event) => {
    emit('on-editor-ready', event);
};
</script>

<template>
    <QuillEditor
        ref="input"
        theme="snow"
        :toolbar="props.toolbar"
        :contentType="props.contentType"
        :content="props.modelValue"
        :placeholder="props.placeholder"
        @ready="onReady($event)"
        @update:content="onUpdate"
    />
</template>