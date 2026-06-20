<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-dark-900">Inscription</h2>
        <p class="text-dark-500 text-sm mt-1">Créez votre compte étudiant</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="prenom" class="block text-sm font-medium text-dark-700">Prénom</label>
                <input id="prenom" type="text" name="prenom" value="{{ old('prenom') }}" required autofocus
                    class="mt-1 block w-full rounded-lg border-dark-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <x-input-error :messages="$errors->get('prenom')" class="mt-1" />
            </div>
            <div>
                <label for="nom" class="block text-sm font-medium text-dark-700">Nom</label>
                <input id="nom" type="text" name="nom" value="{{ old('nom') }}" required
                    class="mt-1 block w-full rounded-lg border-dark-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                <x-input-error :messages="$errors->get('nom')" class="mt-1" />
            </div>
        </div>

        <div class="mt-4">
            <label for="email" class="block text-sm font-medium text-dark-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="mt-1 block w-full rounded-lg border-dark-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="mt-4">
            <label for="numero_etudiant" class="block text-sm font-medium text-dark-700">N° étudiant <span class="text-dark-400">(optionnel)</span></label>
            <input id="numero_etudiant" type="text" name="numero_etudiant" value="{{ old('numero_etudiant') }}"
                class="mt-1 block w-full rounded-lg border-dark-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
        </div>

        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-dark-700">Mot de passe</label>
            <input id="password" type="password" name="password" required
                class="mt-1 block w-full rounded-lg border-dark-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="mt-4">
            <label for="password_confirmation" class="block text-sm font-medium text-dark-700">Confirmer</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="mt-1 block w-full rounded-lg border-dark-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
        </div>

        <button type="submit" class="mt-6 w-full py-3 px-4 bg-secondary-600 hover:bg-secondary-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition duration-200">
            S'inscrire
        </button>

        <p class="mt-4 text-center text-sm text-dark-500">
            Déjà inscrit ?
            <a href="{{ route('login') }}" class="text-secondary-600 hover:text-secondary-800 font-medium">Se connecter</a>
        </p>
    </form>
</x-guest-layout>
