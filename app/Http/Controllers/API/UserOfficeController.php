<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Office;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserOfficeController extends Controller
{

    public function getAllOfficeProperties($id)
    {
        try {
            $office = Office::find($id);

            if (!$office) {
                return response()->json(['message' => 'لا يوجد هذا المكتب.'], 404);
            }

            // جلب العقارات الخاصة بالمكتب بدون علاقة الفيديو
            $properties = $office->properties()->with(['owner', 'images'])->get();

            return response()->json([
                'message' => 'تم جلب العقارات الخاصة بالمكتب بنجاح.',
                'data' => $properties,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب العقارات.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOfficePropertyCount($id)
    {
        try {
            $office = Office::find($id);
            if (!$office) {
                return response()->json(['message' => 'لا يوجد هذا المكتب .'], 404);
            }

            $count = $office->properties()->count();
            return response()->json([
                'message' => 'تم جلب عدد العقارات بنجاح.',
                'count' => $count,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب عدد العقارات.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function followOffice($officeId)
    {
        try {
            $authUser = JWTAuth::parseToken()->authenticate();

            if (!$authUser) {
                return response()->json(['message' => 'غير مصرح, يجب تسجيل الخول اولا'], 401);
            }

            $office = Office::find($officeId);

            if (!$office) {
                return response()->json(['message' => 'المكتب غير موجود'], 404);
            }

            // تحقق إذا المتابعة موجودة
            $alreadyFollowing = $office->followers()->where('user_id', $authUser->id)->exists();

            if ($alreadyFollowing) {
                return response()->json(['message' => 'أنت تتابع هذا المكتب مسبقًا'], 409);
            }

            // تنفيذ المتابعة
            $office->followers()->attach($authUser->id);

            // زيادة عدد المتابعين
            $office->increment('followers_count');

            return response()->json(['message' => 'تمت متابعة المكتب بنجاح'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء المتابعة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function isFollowed($id)
    {
        $authUser = JWTAuth::parseToken()->authenticate();

        if (!$authUser) {
            return response()->json(['message' => 'غير مصرح, يجب تسجيل الخول اولا'], 401);
        }

        $office = Office::find($id);

        if (!$office) {
            return response()->json(['message' => 'المكتب غير موجود'], 404);
        }

        // تحقق إذا المتابعة موجودة
        $alreadyFollowing = $office->followers()->where('user_id', $authUser->id)->exists();

        if ($alreadyFollowing) {
            return response()->json(['message' => true], 200);
        }
        return response()->json(['message' => false], 200);
    }

    public function getFollowersCount($officeId)
    {
        $office = Office::find($officeId);

        if (!$office) {
            return response()->json(['message' => 'المكتب غير موجود'], 404);
        }

        $count = $office->followers()->count();

        return response()->json([
            'message' => 'عدد المتابعين للمكتب',
            'followers_count' => $count,
        ], 200);
    }

    //تابع يجلب معلومات المكتب
    public function showOffice($id)
    {
        try {
            // جلب المكتب أو إرجاع 404 إذا غير موجود
            $office = Office::findOrFail($id);

            // زيادة عدد المشاهدات بشكل أوتوماتيكي
            $office->increment('views');

            // إرجاع بيانات المكتب
            return response()->json([
                'message' => 'تم جلب بيانات المكتب بنجاح.',
                'data' => $office,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب بيانات المكتب.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //تابع يجلب عدد المشاهدين
    public function getOfficeViews($office_id)
    {
        $office = Office::find($office_id);

        if (!$office) {
            return response()->json([
                'message' => 'المكتب غير موجود.',
            ], 404);
        }

        return response()->json([
            'message' => 'تم جلب عدد المشاهدين بنجاح.',
            'office_id' => $office_id,
            'views' => $office->views,
        ], 200);
    }

    //تابع يجيب المتابعين
    public function GetOfficeFollowers($id)
    {
        $office_id = $id;
        if (!Office::find($id)) {
            return response()->json(['message' => 'this office doesnt exist'], 404);
        }
        $followers = DB::table('office_followers')
            ->join('users', 'office_followers.user_id', '=', 'users.id')
            ->where('office_followers.office_id', $office_id)
            ->select('users.*')
            ->get();

        if ($followers->isEmpty()) {
            return response()->json(['message' => 'this office dont have followers'], 404);
        }
        return response()->json(['followers' => $followers], 200);
    }


    public function getAllOfficePropertyVideos($id)
    {
        try {
            $office = Office::find($id);

            if (!$office) {
                return response()->json(['message' => 'لا يوجد هذا المكتب .'], 404);
            }

            $propertiesWithVideos = $office->properties()->with('video')->get();

            // هون رح يعمل فلتره العقارات يلي ما الها فيديوهات بشيلا وبيرجع برتبا
            $videos = $propertiesWithVideos->pluck('video')->filter()->values();

            return response()->json([
                'message' => 'تم جلب فيديوهات العقارات بنجاح.',
                'data' => $videos,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب الفيديوهات.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
