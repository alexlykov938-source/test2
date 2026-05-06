<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AuthController — авторизация для страницы статистики.
 *
 * Почему не Breeze/Jetstream:
 * Для одной защищённой страницы это overkill — npm-сборка, компоненты, лишние вью.
 * Здесь: 2 метода, ноль лишних зависимостей.
 *
 * Создать пользователя через tinker:
 *   php artisan tinker
 *   User::create(['name'=>'Admin','email'=>'admin@admin.com','password'=>bcrypt('secret')]);
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('stats.index');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate(); // защита от session fixation
            return redirect()->intended(route('stats.index'));
        }

        return back()
            ->withErrors(['email' => 'Неверный email или пароль.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}