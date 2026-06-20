@props(['id' => 'datatable'])

<div x-data="dataTable_{{ $id }}()" class="w-full">
    <!-- Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div class="relative flex-1 max-w-sm">
            <input type="text" x-model="search" placeholder="Rechercher..." class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-dark-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
            <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-xs text-gray-500 dark:text-gray-400">Lignes:</label>
            <select x-model.number="perPage" class="text-sm rounded border-gray-300 dark:border-gray-600 dark:bg-dark-800 dark:text-white">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white dark:bg-dark-800 rounded-lg shadow">
        {{ $slot }}
    </div>

    <!-- Pagination info -->
    <div class="flex items-center justify-between mt-4 text-sm text-gray-500 dark:text-gray-400">
        <span x-text="`Affichage ${startIndex + 1} - ${Math.min(endIndex, filteredCount)} sur ${filteredCount}`"></span>
        <div class="flex gap-1">
            <button @click="prevPage()" :disabled="currentPage === 1" class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 hover:bg-gray-100 dark:hover:bg-dark-700">Préc</button>
            <template x-for="p in totalPages" :key="p">
                <button @click="currentPage = p" :class="currentPage === p ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-dark-700'" class="px-3 py-1 rounded border" x-text="p"></button>
            </template>
            <button @click="nextPage()" :disabled="currentPage === totalPages" class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 disabled:opacity-40 hover:bg-gray-100 dark:hover:bg-dark-700">Suiv</button>
        </div>
    </div>
</div>

<script>
function dataTable_{{ $id }}() {
    return {
        search: '',
        perPage: 10,
        currentPage: 1,
        sortCol: '',
        sortAsc: true,

        get rows() {
            return Array.from(document.querySelectorAll('#{{ $id }} tbody tr'));
        },
        get filteredRows() {
            let rows = this.rows;
            if (this.search) {
                const s = this.search.toLowerCase();
                rows = rows.filter(r => r.textContent.toLowerCase().includes(s));
            }
            return rows;
        },
        get filteredCount() { return this.filteredRows.length; },
        get totalPages() { return Math.max(1, Math.ceil(this.filteredCount / this.perPage)); },
        get startIndex() { return (this.currentPage - 1) * this.perPage; },
        get endIndex() { return this.startIndex + this.perPage; },

        init() {
            this.$watch('search', () => { this.currentPage = 1; this.applyFilter(); });
            this.$watch('perPage', () => { this.currentPage = 1; this.applyFilter(); });
            this.$watch('currentPage', () => this.applyFilter());
            this.applyFilter();
        },

        applyFilter() {
            const rows = this.rows;
            const filtered = this.filteredRows;
            rows.forEach(r => r.style.display = 'none');
            filtered.slice(this.startIndex, this.endIndex).forEach(r => r.style.display = '');
        },

        prevPage() { if (this.currentPage > 1) this.currentPage--; },
        nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },

        sort(colIndex) {
            const tbody = document.querySelector('#{{ $id }} tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            if (this.sortCol === colIndex) { this.sortAsc = !this.sortAsc; }
            else { this.sortCol = colIndex; this.sortAsc = true; }
            rows.sort((a, b) => {
                const aVal = a.cells[colIndex]?.textContent.trim() || '';
                const bVal = b.cells[colIndex]?.textContent.trim() || '';
                const cmp = aVal.localeCompare(bVal, 'fr', { numeric: true });
                return this.sortAsc ? cmp : -cmp;
            });
            rows.forEach(r => tbody.appendChild(r));
            this.applyFilter();
        }
    }
}
</script>
