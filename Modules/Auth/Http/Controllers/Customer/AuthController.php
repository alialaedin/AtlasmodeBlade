<?php

namespace Modules\Auth\Http\Controllers\Customer;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Http\Requests\Customer\CustomerSendTokenRequest;
use Modules\Customer\Entities\Customer;
use Modules\Customer\Events\SmsVerify;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Modules\Auth\Http\Requests\Customer\CustomerVerifyRequest;

class AuthController extends Controller
{
	public function showLoginForm()
	{
		return view('auth::customer.login');
	}

	public function sendToken(CustomerSendTokenRequest $request)
	{
		try {
			$result = event(new SmsVerify($request->mobile));
			if ($result[0]['status'] != 200) {
				throw new Exception($result[0]['message']);
			}
			$mobile = $request->mobile;
			return response()->success('بررسی وضعیت ثبت نام مشتری', compact('mobile'));
		} catch (Exception $exception) {
			Log::error($exception->getTraceAsString());
			return response()->error(
				'مشکلی در برنامه بوجود آمده است. لطفا با پشتیبانی تماس بگیرید: ' . $exception->getMessage(),
				$exception->getTrace(),
				422
			);
		}
	}

	public function login(CustomerVerifyRequest $request)
	{
		$request->smsToken->verified_at = now();
		$request->smsToken->save();

		if ($request->has('cookieCarts') && $request->filled('cookieCarts')) {
			Cookie::queue(Cookie::forget('productData'));
			$request->merge(['cookieCarts' => json_decode($request->cookieCarts, true)]);
		}
		
		$customer = Customer::where('mobile', $request->mobile)->first();
		$customer->login();	

		return response()->success('با موفقیت لاگین شدید', compact('customer'));
	}

	public function logout(Request $request) 
	{
		Auth::guard('customer')->logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return response()->success('با موفقیت خارج شد');
	}

	// public function registerLogin(CustomerRegisterLoginRequest $request): JsonResponse
	// {

	//     $request->validate([
	//         'sdlkjcvisl' => 'required|string'
	//     ]);

	//     if (!$request->has('sdlkjcvisl') || $request->sdlkjcvisl != 'uikjdknfs') {
	//         throw Helpers::makeValidationException('خطا در تایید کد کپچا');
	//     }

	//     try {
	//         $customer = Customer::where('mobile', $request->mobile)->first();
	//         if ($customer && !$customer->isActive()) {
	//             return response()->error('حساب شما غیر فعال است. لطفا با پشتیبانی تماس حاصل فرمایید.');
	//         }
	//         $status = ($customer && $customer->password) ? 'login' : 'register';

	//         if ($status === 'register') {
	//             $result = event(new SmsVerify($request->mobile));
	//             if ($result[0]['status'] != 200) {
	//                 return response()->error('ارسال کدفعال سازی ناموفق بود.لطفا دوباره تلاش کنید', null);
	//             }
	//         }

	//         $mobile = $request->mobile;

	//         return response()->success('بررسی وضعیت ثبت نام مشتری', compact('status', 'mobile'));
	//     } catch(Exception $exception) {
	//         Log::error($exception->getTraceAsString());
	//         return response()->error(
	//             'مشکلی در برنامه بوجود آمده است. لطفا با پشتیبانی تماس بگیرید: ' . $exception->getMessage(),
	//             $exception->getTrace(),
	//             500
	//         );
	//     }
	// }

	// public function sendToken(CustomerSendTokenRequest $request): JsonResponse
	// {
	//     $request->validate([
	//         'sdlkjcvisl' => 'required|string'
	//     ]);

	//     if (!$request->has('sdlkjcvisl') || $request->sdlkjcvisl != 'uikjdknfs') {
	//         throw Helpers::makeValidationException('خطا در تایید کد کپچا');
	//     }


	//     try {
	//         $result = event(new SmsVerify($request->mobile));

	//         if ($result[0]['status'] != 200) {
	//             throw new Exception($result[0]['message']);
	//         }
	//         $mobile = $request->mobile;
	//         return response()->success('بررسی وضعیت ثبت نام مشتری', compact('mobile'));
	//     } catch(Exception $exception) {
	//         Log::error($exception->getTraceAsString());
	//         return response()->error(
	//             'مشکلی در برنامه بوجود آمده است. لطفا با پشتیبانی تماس بگیرید: ' . $exception->getMessage(),
	//             $exception->getTrace(),
	//             422
	//         );
	//     }
	// }

	// public function verify(CustomerVerifyRequest $request): JsonResponse
	// {
	//     try {
	//         $request->smsToken->verified_at = now();
	//         $request->smsToken->save();
	//         $data['mobile'] = $request->mobile;

	//         $customer = $request->customer;
	//         if ($request->type === 'login' && $customer) {
	//             $customer->load(['listenCharges', 'carts' => function ($query) {
	//                 $query->withCommonRelations();
	//             }]);
	//             $token = $customer->createToken('authToken')->plainTextToken;
	//             $data['access_token'] = $token;
	//             $data['user'] = $customer;
	//             $data['token_type'] = 'Bearer';
	//             $notificationService = new NotificationService($customer);
	//             $data['notifications'] = [
	//                 'items' => $notificationService->get(),
	//                 'total_unread' => $notificationService->getTotalUnread()
	//             ];
	//             Helpers::actingAs($customer);
	//             $warnings = CartFromRequest::addToCartFromRequest($request);
	//             $data['cart_warnings'] = $warnings;
	//             $data['carts'] = $customer->carts()->withCommonRelations()->get();
	//             $customer->loadCommonRelations();
	//         }

	//         return response()->success('', compact('data'));
	//     } catch(Exception $exception) {
	//         Log::error($exception->getTraceAsString());
	//         return response()->error(
	//             'مشکلی در برنامه بوجود آمده است. لطفا با پشتیبانی تماس بگیرید: ' . $exception->getMessage(),
	//             $exception->getTrace(),
	//             500
	//         );
	//     }
	// }

	// // Fourth
	// public function register(CustomerRegisterRequest $request): JsonResponse
	// {
	//     /** @var Customer $customer */
	//     if (!($customer = Customer::query()->where('mobile', $request->mobile)->first())) {
	//         $customer = Customer::create($request->all());
	//     } else {
	//         if ($customer->password) {
	//             return response()->error('این شماره همراه قبلا انتخاب شده است');
	//         }
	//         $customer->password = $request->password;
	//         $customer->save();
	//     }
	//     if ($request->newsletter){
	//         UsersNewsletters::query()->firstOrCreate($request->only('email'));
	//     }

	//     $customer->load(['listenCharges', 'carts' => function ($query) {
	//         $query->withCommonRelations();
	//     }]);

	//     $token = $customer->createToken('authToken')->plainTextToken;

	//     Helpers::actingAs($customer);

	//     $warnings = CartFromRequest::addToCartFromRequest($request);
	//     $customer->loadCommonRelations();
	//     $notificationService = new NotificationService($customer);

	//     // لازمه
	//     $customer->getBalanceAttribute();
	//     $customer = Customer::query()->withCommonRelations()->whereKey($customer->id)->first();

	//     $data = [
	//         'access_token' => $token,
	//         'token_type' => 'Bearer',
	//         'cart_warnings' => $warnings,
	//         'user' => $customer,
	//         'carts' => $customer->carts()->withCommonRelations()->get(),
	//         'notifications' => [
	//             'items' => $notificationService->get(),
	//             'total_unread' => $notificationService->getTotalUnread()
	//         ]
	//     ];

	//     return response()->success('ثبت نام با موفقیت انجام شد', compact('data'));
	// }

	// // Second if registered
	// public function login(CustomerLoginRequest $request): JsonResponse
	// {
	//     $customer = $request->customer;

	//     if (! $customer || ! Hash::check($request->password, $customer->password)) {
	//         return response()->error('اطلاعات وارد شده اشتباه است.', [], 400);
	//     }

	//     $customer->load(['listenCharges', 'carts' => function ($query) {
	//         $query->withCommonRelations();
	//     }]);
	//     $token = $customer->createToken('authToken');
	//     $token->accessToken->device_token = $request->device_token;
	//     $token->accessToken->save();
	//     // اون ایدی کارت هایی که ساخته شده در کوکی رو تو دیتابیس میزاره
	//     Helpers::actingAs($customer);
	//     $warnings = CartFromRequest::addToCartFromRequest($request);
	//     $customer->loadCommonRelations();
	//     $notificationService = new NotificationService($customer);

	//     $data = [
	//         'user' => $customer,
	//         'access_token' => $token->plainTextToken,
	//         'token_type' => 'Bearer',
	//         'cart_warnings' => $warnings,
	//         'carts' => $customer->carts()->withCommonRelations()->get(),
	//         'notifications' => [
	//             'items' => $notificationService->get(),
	//             'total_unread' => $notificationService->getTotalUnread()
	//         ]
	//     ];

	//     return response()->success('کاربر با موفقیت وارد شد.', compact('data'));

	// }

	// public function logout(Request $request): JsonResponse
	// {
	//     /**
	//      * @var $user Customer
	//      */
	//     $user = $request->user();
	//     $user->currentAccessToken()->delete();

	//     return response()->success('خروج با موفقیت انجام شد');
	// }

	// public function resetPassword(CustomerResetPasswordRequest $request): JsonResponse
	// {
	//     $smsToken = SmsToken::where('mobile', $request->input('mobile'))->first();
	//     if ($smsToken->token !== $request->input('sms_token')) {
	//         throw Helpers::makeValidationException('توکن اشتباه است مججدا نلاش کنید');
	//     }
	//     $customer = $request->customer;
	//     $customer->update($request->only('password'));

	//     Helpers::actingAs($customer);
	//     $warnings = CartFromRequest::addToCartFromRequest($request);
	//     $customer->loadCommonRelations();

	//     $token = $customer->createToken('authToken')->plainTextToken;
	//     $customer->load(['listenCharges']);

	//     $notificationService = new NotificationService($customer);

	//     $data = [
	//         'user' => $customer,
	//         'access_token' => $token,
	//         'token_type' => 'Bearer',
	//         'cart_warnings' => $warnings,
	//         'carts' => $customer->carts()->withCommonRelations()->get(),
	//         'notifications' => [
	//             'items' => $notificationService->get(),
	//             'total_unread' => $notificationService->getTotalUnread()
	//         ]
	//     ];

	//     return response()->success('', compact('data'));
	// }

	// public function setDeviceToken(Request $request)
	// {
	//     /**
	//      * @var $user Customer
	//      */
	//     $user = auth()->user();
	//     $accessToken = $user->currentAccessToken();
	//     $accessToken->device_token = $request->device_token;
	//     $accessToken->save();
	// }
}
