@props(['name' => 'signature', 'width' => 500, 'height' => 200])

<div x-data="signaturePad()" class="w-full">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Signature</label>

    <div class="border-2 border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden bg-white" style="max-width: {{ $width }}px;">
        <canvas
            x-ref="canvas"
            width="{{ $width }}"
            height="{{ $height }}"
            class="cursor-crosshair w-full"
            @mousedown="startDrawing($event)"
            @mousemove="draw($event)"
            @mouseup="stopDrawing()"
            @mouseleave="stopDrawing()"
            @touchstart.prevent="startDrawing($event)"
            @touchmove.prevent="draw($event)"
            @touchend="stopDrawing()">
        </canvas>
    </div>

    <div class="flex items-center gap-3 mt-2">
        <button type="button" @click="clear()" class="px-3 py-1 text-sm bg-gray-200 dark:bg-dark-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300">
            Effacer
        </button>
        <span x-show="signed" class="text-xs text-green-600">Signature enregistrée</span>
    </div>

    <input type="hidden" name="{{ $name }}" x-ref="signatureInput" :value="signatureData">
</div>

<script>
function signaturePad() {
    return {
        drawing: false,
        signed: false,
        signatureData: '',
        ctx: null,

        init() {
            this.ctx = this.$refs.canvas.getContext('2d');
            this.ctx.strokeStyle = '#1e293b';
            this.ctx.lineWidth = 2;
            this.ctx.lineCap = 'round';
            this.ctx.lineJoin = 'round';
        },

        getPos(e) {
            const rect = this.$refs.canvas.getBoundingClientRect();
            const scaleX = this.$refs.canvas.width / rect.width;
            const scaleY = this.$refs.canvas.height / rect.height;
            let clientX, clientY;
            if (e.touches) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }
            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        },

        startDrawing(e) {
            this.drawing = true;
            const pos = this.getPos(e);
            this.ctx.beginPath();
            this.ctx.moveTo(pos.x, pos.y);
        },

        draw(e) {
            if (!this.drawing) return;
            const pos = this.getPos(e);
            this.ctx.lineTo(pos.x, pos.y);
            this.ctx.stroke();
        },

        stopDrawing() {
            if (this.drawing) {
                this.drawing = false;
                this.signed = true;
                this.signatureData = this.$refs.canvas.toDataURL('image/png');
            }
        },

        clear() {
            this.ctx.clearRect(0, 0, this.$refs.canvas.width, this.$refs.canvas.height);
            this.signed = false;
            this.signatureData = '';
        }
    }
}
</script>
