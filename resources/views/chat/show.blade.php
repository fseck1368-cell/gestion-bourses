<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight">Chat - {{ $dossier->reference }}</h2>
            <span class="text-sm text-dark-400">avec <strong class="text-white">{{ $otherUser?->name ?? 'Non assigné' }}</strong></span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div x-data="chatApp()" x-init="init()" class="bg-white dark:bg-dark-800 rounded-xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-dark-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($otherUser?->prenom ?? 'U', 0, 1)) }}{{ strtoupper(substr($otherUser?->nom ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $otherUser?->name ?? 'Non assigné' }}</p>
                            <p class="text-xs text-gray-500">Dossier {{ $dossier->reference }}</p>
                        </div>
                    </div>
                    <div x-show="unreadCount > 0" class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                        <span x-text="unreadCount"></span> non lu(s)
                    </div>
                </div>

                <!-- Messages -->
                <div x-ref="messagesContainer" class="h-[450px] overflow-y-auto px-6 py-4 space-y-3">
                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.user_id === currentUserId ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="msg.user_id === currentUserId ? 'bg-indigo-600 text-white rounded-tl-xl rounded-tr-xl rounded-bl-xl' : 'bg-gray-100 dark:bg-dark-700 text-gray-900 dark:text-gray-100 rounded-tl-xl rounded-tr-xl rounded-br-xl'" class="max-w-xs lg:max-w-md px-4 py-2.5 shadow-sm">
                                <p class="text-sm whitespace-pre-wrap" x-text="msg.contenu"></p>
                                <div class="flex items-center justify-end gap-2 mt-1" :class="msg.user_id === currentUserId ? 'text-indigo-200' : 'text-gray-400'">
                                    <span class="text-xs" x-text="msg.timestamp"></span>
                                    <template x-if="msg.user_id === currentUserId && msg.lu">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="messages.length === 0 && !loading" class="flex flex-col items-center justify-center h-full text-gray-400">
                        <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        <p class="text-sm">Aucun message. Commencez la conversation !</p>
                    </div>
                </div>

                <!-- Input -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-dark-700 bg-gray-50 dark:bg-dark-900">
                    <form @submit.prevent="sendMessage()" class="flex items-end gap-3">
                        <textarea x-model="newMessage" @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()" rows="2" maxlength="2000" placeholder="Votre message..." class="flex-1 rounded-lg border-gray-300 dark:border-dark-600 dark:bg-dark-800 dark:text-white text-sm resize-none focus:ring-indigo-500" :disabled="sending"></textarea>
                        <button type="submit" :disabled="sending || newMessage.trim() === ''" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition">
                            <svg x-show="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            <svg x-show="sending" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function chatApp() {
        return {
            messages: [],
            newMessage: '',
            loading: true,
            sending: false,
            unreadCount: 0,
            currentUserId: {{ $currentUser->id }},
            dossierId: {{ $dossier->id }},
            interval: null,

            init() {
                this.fetchMessages();
                this.interval = setInterval(() => this.fetchMessages(), 5000);
                this.markAsRead();
            },

            async fetchMessages() {
                try {
                    const res = await axios.get(`/chat/${this.dossierId}/messages`);
                    this.messages = res.data.messages;
                    this.unreadCount = res.data.unread_count;
                    this.loading = false;
                    if (this.unreadCount > 0) this.markAsRead();
                    this.$nextTick(() => this.scrollBottom());
                } catch(e) { this.loading = false; }
            },

            async sendMessage() {
                if (!this.newMessage.trim() || this.sending) return;
                this.sending = true;
                const contenu = this.newMessage.trim();
                this.newMessage = '';
                try {
                    const res = await axios.post(`/chat/${this.dossierId}/send`, { contenu });
                    if (res.data.success) {
                        this.messages.push(res.data.message);
                        this.$nextTick(() => this.scrollBottom());
                    }
                } catch(e) { this.newMessage = contenu; }
                this.sending = false;
            },

            async markAsRead() {
                try { await axios.post(`/chat/${this.dossierId}/read`); this.unreadCount = 0; } catch(e) {}
            },

            scrollBottom() {
                const c = this.$refs.messagesContainer;
                if (c) c.scrollTop = c.scrollHeight;
            },

            destroy() { if (this.interval) clearInterval(this.interval); }
        }
    }
    </script>
    @endpush
</x-app-layout>
