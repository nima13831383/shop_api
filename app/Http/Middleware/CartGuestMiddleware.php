<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class CartGuestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        if ($token) {
            $user = \App\Models\User::where('api_token', $token)->first();

            if (!$user) {
                return response()->json(['message' => 'Invalid token'], 401);
            }

            // Attach user to request
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
            // اگر کاربر لاگین است →‌ نیاز به guest token نیست
            if ($request->user()) {
                return $next($request);
            }
        } else {
            // گرفتن guest token از هدر درخواست
            $guestToken = $request->header('X-Guest-Token');

            // اگر guest token وجود ندارد → ساختن یک توکن جدید
            if (!$guestToken) {
                $guestToken = Str::uuid()->toString();

                // ذخیره در Redis برای ۳۰ روز
                Cache::put("guest:$guestToken", true, now()->addDays(30));
            }

            // اگر توکن موجود است ولی در Redis ثبت نشده → منقضی شده یا نامعتبر است
            if (!Cache::has("guest:$guestToken")) {
                abort(401, "Invalid or expired guest token");
            }

            // تزریق guest_id در request
            $request->merge([
                'guest_id' => $guestToken
            ]);

            // اجرای درخواست و ایجاد Response
            $response = $next($request);

            // اضافه کردن هدر به پاسخ (حتی اگر جدید ساخته نشده بود)
            $response->headers->set('X-Guest-Token', $guestToken);

            return $response;
        }
    }
}
