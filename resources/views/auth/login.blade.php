<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="flex items-center flex-col">
            <img class="w-32 h-32" src="{{ url('/asset/Tamkang_University_logo.svg.png') }}" />
            <div class="mt-5 text-center font-black text-3xl">{{ $title }}</div>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <x-jet-validation-errors class="mb-4" />

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="text-sm text-gray-600 mb-3">※ 登入帳號為學號，密碼預設學號後六碼。</div>
                <div>
                    <x-jet-label for="username" value="{{ '帳號' }}" />
                    <x-jet-input id="username" class="block mt-1 w-full" type="text" name="username"
                        :value="old('username')" required autofocus autocomplete="username" />
                </div>

                <div class="mt-4">
                    <x-jet-label for="password" value="{{ '密碼' }}" />
                    <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required
                        autocomplete="current-password" />
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="flex items-center">
                        <input id="remember_me" type="checkbox" class="form-checkbox" name="remember">
                        <span class="ml-2 text-sm text-gray-600">{{ __('記住此次登入') }}</span>
                    </label>
                </div>
                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900"
                            href="{{ route('password.request') }}">
                            {{ __('忘記密碼?') }}
                        </a>
                    @endif

                    <x-jet-button class="ml-4">
                        {{ '登入' }}
                    </x-jet-button>
                </div>
            </form>
        </div>

        <div class="mt-8 grid md:grid-rows-1 grid-rows-3 grid-flow-col gap-2">
            <a href="{{ route('find_pdf', ['name' => '公告1']) }}" target="_blank"
                class="text-center text-green-600 hover:text-purple-700">{{ '@' }}{{ \App\Models\Config::getPdfName('pdf_a') }}</a>
            <a href="{{ route('find_pdf', ['name' => '公告2']) }}" target="_blank"
                class="text-center text-green-600 hover:text-purple-700">{{ '@' }}{{ \App\Models\Config::getPdfName('pdf_b') }}</a>
            <a href="{{ route('find_pdf', ['name' => '公告3']) }}" target="_blank"
                class="text-center text-green-600 hover:text-purple-700">{{ '@' }}{{ \App\Models\Config::getPdfName('pdf_c') }}</a>
        </div>
    </div>
</x-guest-layout>
