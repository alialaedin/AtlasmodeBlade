<?php

namespace Modules\Newsletters\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Newsletters\Entities\UsersNewsletters;
class UsersNewslettersController extends Controller
{
  public function index()
  {
    $userNewsletters = UsersNewsletters::query()->filters()->latest()->paginate();

    return view('newsletters::admin.user.index', compact('userNewsletters'));
  }

  public function destroy(UsersNewsletters $usersNewsletters)
  {
    $usersNewsletters->delete();

    return redirect()->route('admin.newsletters.index')->with(['success' => 'کاربر از لیست خبرنامه با موفقیت ارسال شد']);
  }
}
