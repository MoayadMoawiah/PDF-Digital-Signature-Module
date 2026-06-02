<script setup>
import { ref, onMounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    pdfUrl:  { type: String, required: true },
});

const canvasRef    = ref(null);
const currentPage  = ref(1);
const totalPages   = ref(0);
const loading      = ref(true);
const error        = ref(null);
let   pdfDoc       = null;
let   renderTask   = null;

onMounted(async () => {
    await loadPdf();
});

watch(() => props.pdfUrl, async () => {
    await loadPdf();
});

async function loadPdf() {
    loading.value = true;
    error.value   = null;
    try {
        // Dynamically import pdfjs-dist to avoid SSR issues
        const pdfjsLib = await import('pdfjs-dist');

        // Point worker to the bundled worker file
        pdfjsLib.GlobalWorkerOptions.workerSrc = new URL(
            'pdfjs-dist/build/pdf.worker.mjs',
            import.meta.url
        ).toString();

        pdfDoc = await pdfjsLib.getDocument(props.pdfUrl).promise;
        totalPages.value  = pdfDoc.numPages;
        currentPage.value = 1;
        await renderPage(1);
    } catch (e) {
        error.value = e.message || 'Failed to load PDF';
    } finally {
        loading.value = false;
    }
}

async function renderPage(num) {
    if (!pdfDoc) return;

    if (renderTask) {
        renderTask.cancel();
    }

    const page     = await pdfDoc.getPage(num);
    const canvas   = canvasRef.value;
    const ctx      = canvas.getContext('2d');

    const desiredWidth = canvas.parentElement?.offsetWidth || 700;
    const viewport     = page.getViewport({ scale: 1 });
    const scale        = desiredWidth / viewport.width;
    const scaledViewport = page.getViewport({ scale });

    canvas.width  = scaledViewport.width;
    canvas.height = scaledViewport.height;

    renderTask = page.render({ canvasContext: ctx, viewport: scaledViewport });
    await renderTask.promise;
}

async function prevPage() {
    if (currentPage.value > 1) {
        currentPage.value--;
        await renderPage(currentPage.value);
    }
}

async function nextPage() {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
        await renderPage(currentPage.value);
    }
}
</script>

<template>
    <div class="flex flex-col h-full">
        <!-- Loading -->
        <div v-if="loading" class="flex-1 flex items-center justify-center">
            <div class="animate-spin w-8 h-8 border-4 border-primary/30 border-t-primary rounded-full"></div>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="flex-1 flex items-center justify-center text-danger text-sm p-4">
            {{ error }}
        </div>

        <!-- Canvas -->
        <div v-else class="flex-1 overflow-auto bg-slate-100 rounded-lg flex justify-center p-2">
            <canvas ref="canvasRef" class="shadow-md max-w-full"></canvas>
        </div>

        <!-- Pagination -->
        <div v-if="!loading && !error && totalPages > 1"
             class="flex items-center justify-center gap-4 pt-3 text-sm text-slate-600">
            <button @click="prevPage"
                    :disabled="currentPage <= 1"
                    class="px-3 py-1 rounded border border-slate-200 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                {{ t('signing.prev') }}
            </button>
            <span>{{ t('signing.page') }} {{ currentPage }} {{ t('signing.of') }} {{ totalPages }}</span>
            <button @click="nextPage"
                    :disabled="currentPage >= totalPages"
                    class="px-3 py-1 rounded border border-slate-200 hover:bg-slate-50 disabled:opacity-40 disabled:cursor-not-allowed">
                {{ t('signing.next') }}
            </button>
        </div>
    </div>
</template>
