<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SignerForm from '@/Components/SignerForm.vue';

const { t } = useI18n();

const props = defineProps({
    default_expiry_days: { type: Number, default: 7 },
});

const form = useForm({
    title:      '',
    pdf:        null,
    expires_at: '',
    signers: [
        { name: '', email: '', expires_at: '' },
    ],
});

const dragOver    = ref(false);
const filePreview = ref(null);

function onFileDrop(e) {
    dragOver.value = false;
    const file = e.dataTransfer?.files?.[0];
    if (file && file.type === 'application/pdf') {
        form.pdf      = file;
        filePreview.value = file.name;
    }
}

function onFileInput(e) {
    const file = e.target.files?.[0];
    if (file) {
        form.pdf      = file;
        filePreview.value = file.name;
    }
}

function submit() {
    form.post(route('admin.documents.store'), {
        forceFormData: true,
    });
}
</script>

<template>
    <Head :title="t('documents.new_document')" />
    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-semibold text-slate-800">{{ t('documents.new_document') }}</h1>
        </template>

        <form @submit.prevent="submit" class="space-y-8 max-w-2xl">
            <!-- Document info -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-card space-y-5">
                <h2 class="text-base font-semibold text-slate-700">Document Details</h2>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        {{ t('documents.document_title') }} <span class="text-danger">*</span>
                    </label>
                    <input v-model="form.title" type="text"
                           class="w-full rounded-lg border-slate-300 text-sm focus:ring-primary focus:border-primary"
                           :placeholder="t('documents.document_title')" />
                    <p v-if="form.errors.title" class="mt-1 text-xs text-danger">{{ form.errors.title }}</p>
                </div>

                <!-- PDF upload -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        {{ t('documents.upload_pdf') }} <span class="text-danger">*</span>
                    </label>
                    <div @dragover.prevent="dragOver = true"
                         @dragleave="dragOver = false"
                         @drop.prevent="onFileDrop"
                         :class="['border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-colors',
                                  dragOver ? 'border-primary bg-primary/5' : 'border-slate-300 hover:border-primary/50 hover:bg-slate-50']"
                         @click="$refs.fileInput.click()">
                        <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <p v-if="filePreview" class="text-sm font-medium text-primary">📄 {{ filePreview }}</p>
                        <p v-else class="text-sm text-slate-500">{{ t('documents.drop_pdf') }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ t('documents.pdf_only') }} · {{ t('documents.max_size', { size: 20 }) }}</p>
                        <input ref="fileInput" type="file" accept=".pdf" class="hidden" @change="onFileInput" />
                    </div>
                    <p v-if="form.errors.pdf" class="mt-1 text-xs text-danger">{{ form.errors.pdf }}</p>
                </div>

                <!-- Document expiry -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        {{ t('documents.expires_at') }}
                        <span class="text-slate-400 font-normal">{{ t('documents.optional') }}</span>
                    </label>
                    <input v-model="form.expires_at" type="datetime-local"
                           class="w-full rounded-lg border-slate-300 text-sm focus:ring-primary focus:border-primary" />
                </div>
            </div>

            <!-- Signers -->
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-card">
                <SignerForm :signers="form.signers"
                            :errors="form.errors"
                            @update:signers="form.signers = $event" />
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-4">
                <button type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 bg-primary text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-dark disabled:opacity-60 disabled:cursor-not-allowed transition-colors shadow-sm">
                    <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    {{ t('documents.new_document') }}
                </button>
                <a :href="route('admin.documents.index')"
                   class="text-sm text-slate-500 hover:text-slate-700">
                    {{ t('signing.cancel') }}
                </a>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
