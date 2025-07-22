<?php

namespace App\Http\Controllers\API;

use App\Models\Requestt;
use App\Models\Office;
use Illuminate\Http\Request;
use App\Traits\UploadImagesTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class OfficeController extends Controller
{
    use UploadImagesTrait;

    //? رفع ملفات المكتب الى جدول منفصل مثل الصور
    public function registerOffice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:3,50',
            'phone' => 'required|unique:offices,phone',
            'type' => 'required',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'document' => 'required|file|mimes:pdf|max:2048',
            'url' => 'nullable|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        DB::beginTransaction();

        try {
            $office = Office::create([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'description' => $request->description,
                'location' => $request->location,
                'status' => 'pending',
                'free_ads' => 0,
                'followers_count' => 0,
                'views' => 0,
            ]);
            $docPath = $this->uploadDocument($request->file('document'), 'offices');
            $office->document()->create([
                'url' => $docPath,
            ]);

            if ($request->hasFile('url')) {
                $imageUrl = $this->uploadImage($request->file('url'), 'offices');
                $office->image()->create([
                    'url' => $imageUrl,
                ]);
            }

            Requestt::create([
                'office_id' => $office->id,
                'requestable_id' => $office->id,
                'requestable_type' => \App\Models\Office::class,
                'status' => 'pending',
            ]);

            DB::commit();
            return response()->json([
                'message' => 'تم إرسال طلب فتح مكتب بنجاح. سيتم مراجعته من قبل الإدارة.',
                'status' => 'pending',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    public function requestSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscription_type' => 'required|in:monthly,yearly',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $office = auth('office-api')->user();

        // تحقق إذا عنده اشتراك نشط حالي
        $activeSubscription = $office->subscriptions()
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>', now())
            ->first();

        if ($activeSubscription) {
            return response()->json([
                'message' => 'لديك اشتراك نشط حالياً. لا يمكنك إرسال طلب جديد حتى ينتهي الاشتراك الحالي.',
            ], 400);
        }

        // تحقق إذا عنده طلب اشتراك معلق
        $pendingRequest = $office->subscriptions()
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            return response()->json([
                'message' => 'لقد قمت بالفعل بإرسال طلب اشتراك قيد الانتظار. الرجاء انتظار موافقة الإدارة.',
            ], 400);
        }

        // تحديد السعر
        $price = $request->subscription_type === 'monthly' ? 50 : 500;

        // إنشاء طلب الاشتراك
        $subscription = $office->subscriptions()->create([
            'subscription_type' => $request->subscription_type,
            'price' => $price,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'تم إرسال طلب الاشتراك، بانتظار موافقة الإدارة.',
            'subscription' => $subscription,
        ]);
    }

    public function getOffice()
    {
        $office = auth('office-api')->user();
        if (!$office) {
            return response()->json(['message' => 'لا يوجد هذا المكتب .'], 404);
        }

        // Load the office with its document and image
        $office->load(['document', 'image']);

        return response()->json([
            'message' => 'تم جلب معلومات المكتب بنجاح.',
            'data' => $office,
        ], 200);
    }

    public function getAllProperties()
    {
        try {
            $office = auth('office-api')->user();
            if (!$office) {
                return response()->json(['message' => 'لا يوجد هذا المكتب .'], 404);
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

    public function getOfficePropertyCount()
    {
        try {
            $office = auth('office-api')->user();
            if (!$office) {
                return response()->json(['message' => 'لا يوجد هذا المكتب .'], 404);
            }

            $count = $office->properties()->count();
            $activeCount = $office->properties()->where('position', 'available')->count();
            return response()->json([
                'message' => 'تم جلب عدد العقارات بنجاح.',
                'count' => $count,
                'activeCount' => $activeCount,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء جلب عدد العقارات.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getOfficeViews()
    {
        $office = auth('office-api')->user();
        if (!$office) {
            return response()->json(['message' => 'لا يوجد هذا المكتب .'], 404);
        }
        return response()->json([
            'message' => 'تم جلب عدد المشاهدين بنجاح.',
            'views' => $office->views,
        ], 200);
    }

    public function getOfficeFollowers()
    {
        $office = auth('office-api')->user();
        if (!$office) {
            return response()->json(['message' => 'لا يوجد هذا المكتب .'], 404);
        }
        $office_id = $office->id;
        $followers = DB::table('office_followers')
            ->join('users', 'office_followers.user_id', '=', 'users.id')
            ->where('office_followers.office_id', $office_id)
            ->select('users.*')
            ->get();

        if ($followers->isEmpty()) {
            return response()->json(['message' => 'this office don\'t have followers', 'followers' => []], 200);
        }
        return response()->json(['followers' => $followers], 200);
    }


    public function getAllOfficePropertyVideos()
    {
        try {
            $office = auth('office-api')->user();
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
