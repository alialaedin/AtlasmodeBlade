<?php

namespace Modules\Contact\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Admin\Classes\ActivityLogHelper;
use Modules\Contact\Entities\Contact;
use Modules\Contact\Entities\Repository;

class ContactController extends Controller
{
  private $repository;

  public function __construct(Repository $repository)
  {
    $this->repository = $repository;
  }

  public function index()
  {
    return view('contact::admin.index', ['contacts' => $this->repository->paginate()]);
  }

  public function read(Request $request)
  {
    $contact = Contact::query()->find($request->contact_id);
    $contact->status = 1;
    $contact->save();

    return response()->success('وضعیت به خوانده شده تغییر کرد', ['contact' => $contact]);
  }

  public function destroy(Contact $contact)
  {
    $this->repository->delete($contact);
    ActivityLogHelper::deletedModel('پیام حذف شد', $contact);

    return redirect()->route('admin.contacts.index')->with('success', 'پیام با موفقیت حذف شد.');
  }
}
