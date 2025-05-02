<?php

namespace Modules\Contact\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\Contact\Entities\Contact;
use Modules\Contact\Http\Requests\ContactRequest;
use Modules\Setting\Entities\Setting;

class ContactController extends Controller
{
	public function index()
	{
		$address = Setting::getFromName('shop_address');
		$mobile = Setting::getFromName('shop_telephone');
		$email = Setting::getFromName('email');
		$shopTitle = Setting::getFromName('title');
		$aboutUsText = Setting::getFromName('about_us_site');

		return view('contact::front.contact', compact(['address', 'mobile', 'email', 'aboutUsText', 'shopTitle']));
	}

	public function store(ContactRequest $request)
	{
		Contact::create($request->validated());
		return redirect()->back()->with('success', 'پیام شما با موفقیت ارسال شد');
	}

	public function about()
	{
		return view('contact::front.about');
	}
}
