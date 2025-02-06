<?php

namespace Modules\Auth\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Admin\Entities\Admin;
use Modules\Core\Helpers\Helpers;

class AuthController
{
  public function showLoginForm()
  {
    return view('auth::admin.login');
  }

  public function login(Request $request)
  {
    
    $credentials = $request->validate([
      'username' => ['required', 'max:20'],
      'password' => ['required', 'min:3'],
    ]);

    $admin = Admin::where('username', $request->username)->first();
    if (!$admin || !Hash::check($request->password, $admin->password)) {
      throw Helpers::makeValidationException('نام کاربری یا رمز عبور نادرست است');
    }

    if (Auth::guard('admin')->attempt($credentials, 1)) {
      Auth::login($admin);
      return redirect()->route('admin.dashboard');
    } 
    throw Helpers::makeValidationException('خطا در لاگین');
  }
  
  public function logout(Request $request)
  {
    Auth::guard('admin')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('admin.form');
  }
}
