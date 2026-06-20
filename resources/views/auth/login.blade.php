<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-dark-900">Connexion</h2>
        <p class="text-dark-500 text-sm mt-1">Accédez à votre espace</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-dark-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="mt-1 block w-full rounded-lg border-dark-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 transition">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-dark-700">Mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="mt-1 block w-full rounded-lg border-dark-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 transition">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-dark-300 text-primary-600 shadow-sm focus:ring-primary-500" name="remember">
                <span class="ms-2 text-sm text-dark-600">Se souvenir</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm text-secondary-600 hover:text-secondary-800" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            @endif
        </div>

        <button type="submit" class="mt-6 w-full py-3 px-4 bg-dark-900 hover:bg-dark-800 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition duration-200 border-2 border-primary-500">
            Se connecter
        </button>

        <p class="mt-4 text-center text-sm text-dark-500">
            Pas encore inscrit ?
            <a href="{{ route('register') }}" class="text-secondary-600 hover:text-secondary-800 font-medium">Créer un compte</a>
        </p>
    </form>
</x-guest-layout>
