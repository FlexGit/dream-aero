<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Спасибо за регистрацию! Подтвердите свой адрес электронной почты, перейдя по ссылке, которая была отправлена Вам. Если Вы не получили письмо, мы вышлем Вам другое.') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('На E-mail, который Вы указали при регистрации, была отправлена новая ссылка для подтверждения.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button>
                        {{ __('Выслать повторно письмо для подтверждения') }}
                    </x-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Выход') }}
                </button>
            </form>
        </div>
    </x-auth-card>
</x-guest-layout>
