<?php

namespace Modules\Newsletters\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Newsletters\Entities\Newsletters;
use Modules\Newsletters\Entities\UsersNewsletters;
use Modules\Newsletters\Http\Requests\Admin\NewslettersSendRequest;
use Modules\Newsletters\Jobs\SendNewslettersJob;

class NewslettersController extends Controller
{
  public function index()
  {
    $newsletters = Newsletters::query()->filters()->latest()->paginate();

    return view('newsletters::admin.index', compact('newsletters'));
  }
  public function create()
  {
    return view('newsletters::admin.create');
  }

  public function show(Newsletters $newsletters)
  {
    return response()->success('', compact('newsletters'));
  }

  public function send(NewslettersSendRequest $request)
  {
    $newsletters = Newsletters::query()->create($request->all());

    if ($request->send_all) {
      $emails = UsersNewsletters::getUsersEmails(true);
    } else {
      $emails = UsersNewsletters::getUsersEmails($request->users);
    }
    $response = [];
    foreach ($emails as $email) {
      $response[] = SendNewslettersJob::dispatch($email, $newsletters, $request->send_at);
    }
    ActivityLogHelper::storeModel('خبرنامه ثبت شد', $newsletters);

    return redirect()->route('admin.newsletters.index')->with([
      'success' => 'خبرنامه با موفقیت ارسال شد'
    ]);
  }

  public function destroy($id)
  {
    $newsletters = Newsletters::find($id);
    $newsletters->delete();
    ActivityLogHelper::deletedModel('خبرنامه حذف شد', $newsletters);

    return redirect()->route('admin.newsletters.index')->with([
      'success' => 'خبرنامه با موفقیت ارسال شد'
    ]);
  }
}
