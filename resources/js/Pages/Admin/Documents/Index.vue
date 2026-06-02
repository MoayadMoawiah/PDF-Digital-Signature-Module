<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const { t } = useI18n();

const props = defineProps({
    documents:       Object,
    current_status:  String,
    statuses:        Array,
});

function filterByStatus(status) {
    router.get(route('admin.documents.index'), status ? { status } : {}, { preserveState: true });
}

function confirmDelete(id) {
    if (confirm(t('confirm.delete_document'))) {
        router.delete(route('admin.documents.destroy', id));
    }
}

function formatDate(dt) {
    if (!dt) return '—';
    return new Date(dt).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>

<template>
    <Head :title="t('documents.title')" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-semibold text-slate-800">{{ t('documents.title') }}</h1>
                <Link :href="route('admin.documents.create')"
                      class="inline-flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-dark transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ t('documents.new_document') }}
                </Link>
            </div>
        </template>

        <!-- Status filter tabs -->
        <div class="flex gap-1 mb-6 flex-wrap">
            <button @click="filterByStatus(null)"
                    :class="['px-3 py-1.5 rounded-lg text-sm font-medium transition-colors',
                             !current_status ? 'bg-primary text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50']">
                {{ t('status.all', 'All') }}
            </button>
            <button v-for="s in statuses" :key="s"
                    @click="filterByStatus(s)"
                    :class="['px-3 py-1.5 rounded-lg text-sm font-medium transition-colors capitalize',
                             current_status === s ? 'bg-primary text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50']">
                {{ t(`status.${s}`) }}
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-card overflow-hidden">
            <table v-if="documents.data.length > 0" class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-start px-5 py-3 font-semibold text-slate-600">{{ t('documents.document_title') }}</th>
                        <th class="text-start px-5 py-3 font-semibold text-slate-600">{{ t('documents.signers') }}</th>
                        <th class="text-start px-5 py-3 font-semibold text-slate-600">{{ t('nav.status', 'Status') }}</th>
                        <th class="text-start px-5 py-3 font-semibold text-slate-600">{{ t('documents.created_at') }}</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="doc in documents.data" :key="doc.id"
                        class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-4 font-medium text-slate-800">
                            <Link :href="route('admin.documents.show', doc.id)"
                                  class="hover:text-primary transition-colors">
                                {{ doc.title }}
                            </Link>
                        </td>
                        <td class="px-5 py-4 text-slate-600">
                            <span class="text-accent-dark font-semibold">{{ doc.signed_count }}</span>
                            <span class="text-slate-400">/{{ doc.signers_count }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <StatusBadge :status="doc.status" />
                        </td>
                        <td class="px-5 py-4 text-slate-500">{{ formatDate(doc.created_at) }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <Link :href="route('admin.documents.show', doc.id)"
                                      class="text-primary hover:text-primary-dark text-sm font-medium">
                                    {{ t('documents.view') }}
                                </Link>
                                <a v-if="doc.status === 'completed'"
                                   :href="route('admin.documents.download', doc.id)"
                                   class="text-accent-dark hover:text-accent text-sm font-medium">
                                    {{ t('documents.download') }}
                                </a>
                                <button @click="confirmDelete(doc.id)"
                                        class="text-danger/70 hover:text-danger text-sm font-medium">
                                    {{ t('documents.delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Empty state -->
            <div v-else class="py-16 text-center text-slate-400">
                <svg class="w-12 h-12 mx-auto mb-3 text-slate-200" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <p class="font-medium">{{ t('documents.no_documents') }}</p>
                <Link :href="route('admin.documents.create')"
                      class="mt-3 inline-block text-sm text-primary hover:underline">
                    {{ t('documents.new_document') }}
                </Link>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="documents.last_page > 1"
             class="mt-6 flex items-center justify-between text-sm text-slate-500">
            <span>{{ documents.from }}–{{ documents.to }} / {{ documents.total }}</span>
            <div class="flex gap-1">
                <Link v-if="documents.prev_page_url" :href="documents.prev_page_url"
                      class="px-3 py-1.5 rounded border border-slate-200 hover:bg-slate-50">
                    ‹
                </Link>
                <Link v-if="documents.next_page_url" :href="documents.next_page_url"
                      class="px-3 py-1.5 rounded border border-slate-200 hover:bg-slate-50">
                    ›
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
