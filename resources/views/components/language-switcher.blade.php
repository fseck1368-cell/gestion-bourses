<div class="flex items-center gap-1">
    <form method="POST" action="{{ route('langue.switch', 'fr') }}" class="inline">
        @csrf
        <button type="submit" class="px-2 py-1 text-xs rounded {{ app()->getLocale() === 'fr' ? 'bg-primary-500 text-dark-900 font-bold' : 'text-dark-400 hover:text-white' }}">FR</button>
    </form>
    <form method="POST" action="{{ route('langue.switch', 'en') }}" class="inline">
        @csrf
        <button type="submit" class="px-2 py-1 text-xs rounded {{ app()->getLocale() === 'en' ? 'bg-primary-500 text-dark-900 font-bold' : 'text-dark-400 hover:text-white' }}">EN</button>
    </form>
</div>
