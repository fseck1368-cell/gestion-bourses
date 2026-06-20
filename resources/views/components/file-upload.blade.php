@props(['name' => 'files', 'multiple' => true, 'accept' => '.pdf,.jpg,.jpeg,.png', 'maxSize' => 5])

<div x-data="fileUpload()" class="w-full">
    <div
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="handleDrop($event)"
        :class="dragging ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600'"
        class="border-2 border-dashed rounded-lg p-8 text-center transition cursor-pointer hover:border-indigo-400"
        @click="$refs.fileInput.click()">

        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
        </svg>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            <span class="font-medium text-indigo-600">Cliquez pour parcourir</span> ou glissez-déposez vos fichiers ici
        </p>
        <p class="mt-1 text-xs text-gray-500">{{ strtoupper(str_replace('.', '', $accept)) }} — Max {{ $maxSize }}MB par fichier</p>
    </div>

    <input type="file" x-ref="fileInput" name="{{ $name }}{{ $multiple ? '[]' : '' }}" {{ $multiple ? 'multiple' : '' }} accept="{{ $accept }}" class="hidden" @change="handleFiles($event)">

    <!-- Prévisualisation -->
    <div x-show="files.length > 0" class="mt-4 space-y-2">
        <template x-for="(file, index) in files" :key="index">
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-dark-800 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200" x-text="file.name"></p>
                        <p class="text-xs text-gray-500" x-text="formatSize(file.size)"></p>
                    </div>
                </div>
                <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </template>
    </div>

    <p x-show="error" x-text="error" class="mt-2 text-sm text-red-600"></p>
</div>

<script>
function fileUpload() {
    return {
        dragging: false,
        files: [],
        error: '',
        maxSize: {{ $maxSize }} * 1024 * 1024,
        handleDrop(e) {
            this.dragging = false;
            this.addFiles(e.dataTransfer.files);
        },
        handleFiles(e) {
            this.addFiles(e.target.files);
        },
        addFiles(fileList) {
            this.error = '';
            for (let f of fileList) {
                if (f.size > this.maxSize) {
                    this.error = `${f.name} dépasse la taille maximale.`;
                    continue;
                }
                this.files.push(f);
            }
            this.updateInput();
        },
        removeFile(index) {
            this.files.splice(index, 1);
            this.updateInput();
        },
        updateInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f));
            this.$refs.fileInput.files = dt.files;
        },
        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }
    }
}
</script>
