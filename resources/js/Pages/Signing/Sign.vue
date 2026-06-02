<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PdfViewer from '@/Components/PdfViewer.vue';
import SignatureCanvas from '@/Components/SignatureCanvas.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const { t } = useI18n();

const props = defineProps({
    token:    String,
    signer:   Object,
    document: Object,
    pdf_url:  String,
});

const signatureData  = ref(null);
const showRejectModal = ref(false);

const signForm = useForm({ signature_data: '' });
const rejectForm = useForm({ rejection_reason: '' });

const canSign = computed(() => !!signatureData.value);

// Expiry countdown
const expiryText = ref('');
onMounted(() => {
    if (props.signer.expires_at) {
        const update = () => {
            const diff = new Date(props.signer.expires_at) - Date.now();
            if (diff <= 0) {
                expiryText.value = t('signing.expired_on') + ' ' + new Date(props.signer.expires_at).toLocaleString();
                return;
            }
            const h = Math.floor(diff / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            expiryText.value = `${h}h ${m}m`;
            setTimeout(update, 60000);
        };
        update();
    }
});

function submitSign() {
    signForm.signature_data = signatureData.value;
    signForm.post(route('sign.submit', { token: props.token }));
}

function submitReject() {
    rejectForm.post(route('sign.reject', { token: props.token }), {
        onSuccess: () => { showRejectModal.value = false; },
    });
}
</script>

<template>
    <Head :title="t('signing.title')" />

    <div class="min-h-screen bg-surface flex flex-col" dir="auto">
        <!-- Top bar -->
        <header class="bg-primary text-white px-4 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-white/80" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                </svg>
                <div>
                    <p class="font-semibold text-sm leading-none">{{ document.title }}</p>
                    <p class="text-xs text-white/60 mt-0.5">{{ t('signing.signer') }}: {{ signer.name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span v-if="expiryText" class="text-xs text-yellow-200 hidden sm:block">
                    ⏱ {{ t('signing.expires_in') }}: {{ expiryText }}
                </span>
                <LanguageSwitcher />
            </div>
        </header>

        <!-- Main split layout -->
        <div class="flex flex-1 overflow-hidden flex-col lg:flex-row">
            <!-- PDF Viewer — 60% -->
            <div class="flex-1 lg:w-3/5 overflow-auto p-4 border-b lg:border-b-0 lg:border-e border-slate-200 bg-slate-100"
                 style="min-height: 50vh;">
                <PdfViewer :pdf-url="pdf_url" class="h-full min-h-[400px]" />
            </div>

            <!-- Signing panel — 40% -->
            <div class="lg:w-2/5 flex flex-col p-6 gap-6 overflow-y-auto">
                <div>
                    <h2 class="text-base font-semibold text-slate-800 mb-1">{{ t('signing.draw_signature') }}</h2>
                    <p class="text-sm text-slate-500 mb-4">{{ signer.name }} · {{ signer.email }}</p>
                    <SignatureCanvas v-model="signatureData" />
                </div>

                <!-- Sign button -->
                <div class="space-y-3">
                    <button @click="submitSign"
                            :disabled="!canSign || signForm.processing"
                            class="w-full inline-flex items-center justify-center gap-2 bg-primary text-white py-3 px-6 rounded-xl font-semibold text-sm hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-sm">
                        <svg v-if="signForm.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        {{ t('signing.sign') }}
                    </button>
                    <p v-if="!canSign" class="text-xs text-center text-slate-400">{{ t('signing.draw_first') }}</p>

                    <!-- Reject -->
                    <div class="text-center pt-2 border-t border-slate-100">
                        <button @click="showRejectModal = true"
                                class="text-sm text-danger/70 hover:text-danger font-medium transition-colors">
                            {{ t('signing.reject') }}
                        </button>
                    </div>
                </div>

                <!-- Signer info box -->
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-xs text-slate-500 space-y-1">
                    <p><span class="font-medium text-slate-600">{{ t('signing.document') }}:</span> {{ document.title }}</p>
                    <p><span class="font-medium text-slate-600">{{ t('signing.signer') }}:</span> {{ signer.name }}</p>
                    <p v-if="signer.expires_at">
                        <span class="font-medium text-slate-600">{{ t('documents.expires_at') }}:</span>
                        {{ new Date(signer.expires_at).toLocaleDateString() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <Teleport to="body">
        <div v-if="showRejectModal"
             class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">{{ t('signing.reject_title') }}</h3>
                <form @submit.prevent="submitReject">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">
                            {{ t('signing.reject_reason') }} <span class="text-danger">*</span>
                        </label>
                        <textarea v-model="rejectForm.rejection_reason" rows="4"
                                  class="w-full rounded-lg border-slate-300 text-sm focus:ring-danger focus:border-danger"
                                  :placeholder="t('signing.reject_reason')">
                        </textarea>
                        <p v-if="rejectForm.errors.rejection_reason" class="mt-1 text-xs text-danger">
                            {{ rejectForm.errors.rejection_reason }}
                        </p>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="showRejectModal = false"
                                class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium">
                            {{ t('signing.cancel') }}
                        </button>
                        <button type="submit"
                                :disabled="rejectForm.processing"
                                class="px-5 py-2 bg-danger text-white text-sm font-semibold rounded-lg hover:bg-danger-dark disabled:opacity-60 transition-colors">
                            {{ t('signing.reject_confirm') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>
