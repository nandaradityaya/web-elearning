<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'occupation' => ['required', 'string', 'max:255'],
            'avatar' => ['required', 'image', 'mimes:png,jpg,jpeg'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // process upload photo to our laravel project
        // jika request atau form telah menerima sebuah file dari yg input typenya sebuah file yg memiliki name="avatar"
        if($request->hasFile('avatar')) {
            // ambil pathnya dan simpan dalam folder avatars dan simpan secara public
            $avatarPath = $request->file('avatar')->store('avatars', 'public'); 
        } else {
            $avatarPath = 'images/avatar-default.png'; // default image jika tdk ada image dr user
        }

        // jalankan model User dan jalankan perintah create
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'occupation' => $request->occupation,
            'avatar' => $avatarPath, // tidak usah menggunakan request karna sudah di handle di atas
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
