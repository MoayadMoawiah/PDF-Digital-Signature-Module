<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import SignaturePad from 'signature_pad';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const emit = defineEmits(['update:modelValue', 'signed']);

const canvasRef = ref(null);
let   signaturePad = null;

const isEmpty = ref(true);

onMounted(() => {
    signaturePad = new SignaturePad(canvasRef.value, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor:        '#1B4F72',
        minWidth:        1,
        maxWidth:        3,
    });

    signaturePad.addEventListener('endStroke', () => {
        isEmpty.value = signaturePad.isEmpty();
        if (!isEmpty.value) {
            emit('update:modelValue', signaturePad.toDataURL('image/png'));
            emit('signed');
        }
    });

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
});

onUnmounted(() => {
    window.removeEventListener('resize', resizeCanvas);
    signaturePad?.off();
});

function resizeCanvas() {
    const canvas = canvasRef.value;
    if (!canvas) return;
    const ratio  = Math.max(window.devicePixelRatio || 1, 1);
    const data   = signaturePad.toData();
    canvas.width  = canvas.offsetWidth  * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext('2d').scale(ratio, ratio);
    signaturePad.clear();
    signaturePad.fromData(data);
    isEmpty.value = signaturePad.isEmpty();
}

function clear() {
    signaturePad.clear();
    isEmpty.value = true;
    emit('update:modelValue', null);
}

defineExpose({ clear, isEmpty });
</script>

<template>
    <div class="flex flex-col gap-3">
        <div class="relative border-2 border-dashed border-primary/40 rounded-lg bg-white overflow-hidden"
             style="height: 160px;">
            <canvas ref="canvasRef"
                    class="absolute inset-0 w-full h-full touch-none cursor-crosshair"
                    aria-label="Signature pad">
            </canvas>
            <div v-if="isEmpty"
                 class="absolute inset-0 flex items-center justify-center pointer-events-none select-none">
                <span class="text-slate-300 text-sm font-medium">{{ t('signing.draw_signature') }}</span>
            </div>
        </div>

        <button type="button"
                @click="clear"
                :disabled="isEmpty"
                class="self-start text-sm text-slate-500 hover:text-danger disabled:opacity-40 disabled:cursor-not-allowed transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
            {{ t('signing.clear') }}
        </button>
    </div>
</template>
