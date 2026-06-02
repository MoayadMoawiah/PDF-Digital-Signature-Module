<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const { t } = useI18n();

const props = defineProps({
    document:   Object,
    audit_logs: Array,
});

function formatDate(dt) {
    if (!dt) return '—';
    return new Date(dt).toLocaleString(undefined, {
        year: 'numeric', month: 'short', day: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
    });
}

const actionColors = {
    document_created:    'bg-slate-100 text-slate-600',
    invitation_sent:     'bg-blue-100 text-blue-700',
    document_viewed:     'bg-yellow-100 text-yellow-700',
    signed:              'bg-green-100 text-green-700',
    rejected:            'bg-red-100 text-red-700',
    expired:             'bg-gray-100 text-gray-600',
    signed_pdf_generated: 'bg-purple-100 text-purple-700',
};
</script>

<template>
    <Head :title="`${t('audit.title')} — ${document.title}`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link :href="route('admin.documents.show', document.id)"
                      class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-xl font-semibold text-slate-800">{{ t('audit.title') }}</h1>
                    <p class="text-sm text-slate-500">{{ document.title }}</p>
                </div>
            </div>
        </template>

        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-card">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-start px-5 py-3 font-semibold text-slate-600">{{ t('audit.action') }}</th>
                        <th class="text-start px-5 py-3 font-semibold text-slate-600">{{ t('audit.signer') }}</th>
                        <th class="text-start px-5 py-3 font-semibold text-slate-600">{{ t('audit.ip') }}</th>
                        <th class="text-start px-5 py-3 font-semibold text-slate-600">{{ t('audit.date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="log in audit_logs" :key="log.id" class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3">
                            <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                           actionColors[log.action] ?? 'bg-slate-100 text-slate-600']">
                                {{ t(`audit.actions.${log.action}`, log.action) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-700">
                            {{ log.signer_name || '—' }}
                            <span v-if="log.signer_email" class="text-slate-400 block text-xs">{{ log.signer_email }}</span>
                        </td>
                        <td class="px-5 py-3 font-mono text-slate-500 text-xs">{{ log.ip_address || '—' }}</td>
                        <td class="px-5 py-3 text-slate-500">{{ formatDate(log.performed_at) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
