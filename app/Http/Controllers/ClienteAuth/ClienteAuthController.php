<?php

namespace App\Http\Controllers\ClienteAuth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ClienteAuthController extends Controller
{
    /**
     * Display the client login view.
     */
    public function create(): Response|RedirectResponse
    {
        // Redirect if already authenticated
        if (Auth::guard('cliente')->check()) {
            return redirect()->route('cliente.dashboard');
        }

        return Inertia::render('ClienteAuth/Login', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming client authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::guard('cliente')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('Las credenciales proporcionadas no coinciden con nuestros registros.'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('cliente.dashboard'));
    }

    /**
     * Destroy an authenticated client session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('cliente')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('cliente.login');
    }
}
