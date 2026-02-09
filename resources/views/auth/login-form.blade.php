<x-panel class="tw:rounded-2xl tw:overflow-hidden tw:shadow-2xl tw:p-8 tw:bg-white tw:max-w-lg tw:mx-auto tw:border-0">

    {{-- Login Form --}}
    <form class="form-horizontal" role="form" action="{{ url('login') }}" method="post" name="logonform">
        {{ csrf_field() }}

        {{-- Username --}}
        <div class="tw:mb-6">
            <input type="text" name="username" id="username"
                value="{{ old('username') }}"
                class="form-control tw:rounded-lg tw:h-14 tw:text-lg tw:px-4"
                placeholder="{{ __('Username') }}" required autofocus />
        </div>

        {{-- Password --}}
        <div class="tw:mb-6 tw:relative">
            <input type="password" name="password" id="password"
                autocomplete="off"
                class="form-control tw:rounded-lg tw:h-14 tw:text-lg tw:px-4 tw:pr-12"
                placeholder="{{ __('Password') }}" />

            
            {{-- Remember Me --}}
<div class="tw:mt-10 tw:mb-6">
    <label class="tw:flex tw:items-center">
        <!-- Bigger Checkbox with right margin -->
        <input type="checkbox" name="remember" id="remember"
               class="tw:w-9 tw:h-9 tw:mr-3" />
        <!-- Bigger Text -->
        <span class="tw:text-xl">
            {{ __('Remember Me') }}
        </span>
    </label>
</div>




        {{-- Login Button --}}
        <div class="tw:mb-6">
            <button type="submit"
                    id="login"
                    class="btn btn-primary btn-block tw:h-14 tw:text-lg tw:rounded-lg tw:w-full">
                <i class="fa-solid fa-right-to-bracket tw:mr-1"></i> {{ __('Login') }}
            </button>
        </div>
    </form>
</x-panel>
<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const input = document.getElementById('password');
    const icon = document.getElementById('togglePassword');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});
</script>

