<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const { t } = useI18n();

const props = defineProps({
    document:   Object,
    signers:    Array,
    audit_logs: Array,
});

const activeTab = ref('signers'); // 'signers' | 'audit'

function formatDate(dt) {
    if (!dt) return '—';
    return new Date(dt).toLocaleString(undefined, {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
}

const actionIcons = {
    document_created:    '📄',
    invitation_sent:     '✉️',
    document_viewed:     '👁️',
    signed:              '✅',
    rejected:            '❌',
    expired:             '⏰',
    signed_pdf_generated: '📎',
};
</script>

<template>
    <Head :title="document.title" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <Link :href="route('admin.documents.index')"
                          class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                        </svg>
                    </Link>
                    <h1 class="text-xl font-semibold text-slate-800">{{ document.title }}</h1>
                    <StatusBadge :status="document.status" size="md" />
                </div>
                <a v-if="document.has_signed_pdf"
                   :href="route('admin.documents.download', document.id)"
                   class="inline-flex items-center gap-2 bg-accent text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-accent-dark transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    {{ t('documents.download_signed') }}
                </a>
            </div>
        </template>

        <!-- Tabs -->
        <div class="flex gap-1 mb-6 border-b border-slate-200">
            <button v-for="tab in ['signers', 'audit']" :key="tab"
                    @click="activeTab = tab"
                    :class="['px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px',
                             activeTab === tab
                                ? 'border-primary text-primary'
                                : 'border-transparent text-slate-500 hover:text-slate-700']">
                {{ tab === 'signers' ? t('signers.title') : t('audit.title') }}
            </button>
        </div>

        <!-- Signers tab -->
        <div v-if="activeTab === 'signers'" class="space-y-3">
            <div v-for="signer in signers" :key="signer.id"
                 class="bg-white border border-slate-200 rounded-xl p-5 shadow-card flex items-start justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-semibold text-sm shrink-0">
                        {{ signer.signing_order }}
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800">{{ signer.name }}</p>
                        <p class="text-sm text-slate-500">{{ signer.email }}</p>
                        <p v-if="signer.rejection_reason"
                           class="mt-1 text-sm text-danger bg-danger/5 rounded px-2 py-1">
                            {{ signer.rejection_reason }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1.5">
                    <StatusBadge :status="signer.status" />
                    <p v-if="signer.signed_at" class="text-xs text-slate-400">
                        {{ t('signers.signed_at') }}: {{ formatDate(signer.signed_at) }}
                    </p>
                    <p v-if="signer.expires_at" class="text-xs text-slate-400">
                        {{ t('documents.expires_at') }}: {{ formatDate(signer.expires_at) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Audit log tab -->
        <div v-if="activeTab === 'audit'" class="space-y-3">
            <div class="relative ps-8">
                <!-- Timeline line -->
                <div class="absolute start-3.5 top-0 bottom-0 w-0.5 bg-slate-200"></div>

                <div v-for="log in audit_logs" :key="log.id"
                     class="relative mb-5">
                    <!-- Dot -->
                    <div class="absolute -start-8 w-5 h-5 rounded-full bg-white border-2 border-slate-300 flex items-center justify-center text-xs">
                        {{ actionIcons[log.action] || '•' }}
                    </div>

                    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-card">
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <div>
                                <p class="font-medium text-slate-800 text-sm">
                                    {{ t(`audit.actions.${log.action}`, log.action) }}
                                </p>
                                <p v-if="log.signer_name" class="text-xs text-slate-500 mt-0.5">
                                    {{ log.signer_name }}
                                </p>
                            </div>
                            <div class="text-end">
                                <p class="text-xs text-slate-400">{{ formatDate(log.performed_at) }}</p>
                                <p v-if="log.ip_address" class="text-xs text-slate-400 font-mono">
                                    {{ log.ip_address }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
