<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str; // Asegúrate de importar la clase Str
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // CAMBIO: Se valida 'dni' en lugar de 'email'.
            'dni' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Validate the request's credentials and return the user without logging them in.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateCredentials(): User
    {
        $this->ensureIsNotRateLimited();

        /** @var User|null $user */
        // CAMBIO: Se buscan las credenciales usando 'dni' en lugar de 'email'.
        $user = Auth::getProvider()->retrieveByCredentials($this->only('dni', 'password'));

        // CAMBIO: Se valida la contraseña por separado.
        if (!$user || !Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                // CAMBIO: El mensaje de error se asocia al campo 'dni'.
                'dni' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        return $user;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            // CAMBIO: El mensaje de error de 'throttle' se asocia al campo 'dni'.
            'dni' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate-limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        // CAMBIO: La clave para el rate limit ahora se basa en el DNI.
        return Str::transliterate(Str::lower($this->input('dni')) . '|' . $this->ip());
    }
}
