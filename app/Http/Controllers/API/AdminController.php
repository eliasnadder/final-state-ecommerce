<?php

namespace App\Http\Controllers\API;

use App\Models\Requestt;
use App\Models\Subscription;
use App\Models\Office;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{
    // عرض قائمة الطلبات المعلقة
    //? تمت اضافة احاضر مرفقات للمكتب من ملفات
    public function pendingRequest()
    {
        $requests = Requestt::where('status', 'pending')
            ->with([
                'requestable' => function ($morphTo) {
                    $morphTo->morphWith([
                        Office::class => ['document', 'image'], // Load document and image for offices
                        \App\Models\Property::class => ['images', 'owner'], // Load images and owner for properties
                    ]);
                }
            ])
            ->paginate(10);

        return response()->json($requests);
    }

    // قبول الطلب (تغيير الحالة إلى approved)
    public function approveProperty($id)
    {
        $request = Requestt::find($id);

        if (!$request) {
            return response()->json(['error' => 'الطلب غير موجود.'], 404);
        }

        $request->status = 'accepted';  // تأكد أنها نصية
        $request->save();

        // تفعيل العقار المرتبط عند الموافقة
        $property = $request->requestable;
        if ($property) {
            $property->update(['is_available' => true]); // تفعيل العقار
        }

        return response()->json(['message' => 'تم قبول الطلب وتفعيل العقار.']);
    }


    // رفض الطلب (تغيير الحالة إلى rejected)
    public function rejectProperty($id)
    {
        $requestt = Requestt::findOrFail($id);
        $requestt->status = 'rejected';
        $requestt->save();

        return response()->json(['message' => 'تم رفض الطلب.']);
    }

    // عرض الاشتراكات المعلقة
    public function pendingSubscription()
    {
        $pendingSubscriptions = Subscription::with('office')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pendingSubscriptions);
    }

    public function approveSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);

        if ($subscription->status !== 'pending') {
            return response()->json(['message' => 'الاشتراك تمت معالجته مسبقاً.'], 400);
        }

        // تعيين تاريخ البدء والانتهاء
        $start = now();
        $end = $subscription->subscription_type === 'monthly' ? $start->copy()->addMonth() : $start->copy()->addYear();

        $subscription->update([
            'starts_at' => $start,
            'expires_at' => $end,
            'status' => 'active',
        ]);

        return response()->json(['message' => 'تمت الموافقة على الاشتراك بنجاح.']);
    }

    public function rejectSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);

        if ($subscription->status !== 'pending') {
            return response()->json(['message' => 'الاشتراك تمت معالجته مسبقاً.'], 400);
        }

        $subscription->update(['status' => 'rejected']);

        return response()->json(['message' => 'تم رفض الاشتراك.']);
    }

    public function approveOfficeRequest($requestId)
    {
        $requestt = Requestt::findOrFail($requestId);
        $request = Office::findOrFail($requestt->office_id);

        if ($request->status !== 'pending') {
            return response()->json(['message' => 'تمت معالجة هذا الطلب سابقًا.'], 400);
        }

        // تحديث حالة الطلب إلى approved
        $requestt->update(['status' => 'accepted']);
        $request->update(['status' => 'approved']);
        $request->update(['free_ads' => 2]);

        return response()->json(['message' => 'تمت الموافقة على المكتب وإنشاؤه بنجاح.'], 200);
    }

    public function rejectOfficeRequest($requestId)
    {
        $requestt = Requestt::findOrFail($requestId);
        $office = Office::findOrFail($requestt->office_id);

        if ($office->status !== 'pending') {
            return response()->json(['message' => 'تمت معالجة هذا الطلب سابقًا.'], 400);
        }

        $requestt->update(['status' => 'rejected']);
        $office->delete(); // ← حذف المكتب بالكامل

        return response()->json(['message' => 'تم رفض الطلب وحذف المكتب بنجاح.'], 200);
    }

    public function getOfficesByViews()
    {
        $offices = \App\Models\Office::with(['image', 'document'])->orderBy('views', 'desc')->get();

        if ($offices->isEmpty()) {
            return response()->json([
                'message' => 'لم يتم إضافة مكاتب للتطبيق بعد.'
            ], 404);
        }

        return response()->json([
            'message' => 'تم جلب المكاتب حسب عدد المشاهدات.',
            'data' => $offices
        ], 200);
    }

    public function getOfficesByFollowers()
    {
        $offices = \App\Models\Office::with(['image', 'document'])->orderBy('followers_count', 'desc')->get();

        if ($offices->isEmpty()) {
            return response()->json([
                'message' => 'لم يتم إضافة مكاتب للتطبيق بعد.'
            ], 404);
        }

        return response()->json([
            'message' => 'تم جلب المكاتب حسب عدد المتابعين.',
            'data' => $offices
        ], 200);
    }

    /**
     * Get pending office requests with documents for admin review
     */
    public function getPendingOfficeRequests()
    {
        $pendingOffices = Office::with(['document', 'image'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($pendingOffices->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد طلبات مكاتب معلقة.'
            ], 404);
        }

        return response()->json([
            'message' => 'تم جلب طلبات المكاتب المعلقة بنجاح.',
            'data' => $pendingOffices
        ], 200);
    }

    /**
     * Get pending office requests through the Requestt model with documents
     */
    public function getPendingOfficeRequestsWithDocuments()
    {
        $requests = Requestt::where('status', 'pending')
            ->where('requestable_type', Office::class)
            ->with([
                'requestable' => function ($query) {
                    $query->with(['document', 'image']);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($requests->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد طلبات مكاتب معلقة.'
            ], 404);
        }

        return response()->json([
            'message' => 'تم جلب طلبات المكاتب المعلقة مع المستندات بنجاح.',
            'data' => $requests
        ], 200);
    }

    /**
     * Get specific office with document
     */
    public function getOfficeWithDocument($officeId)
    {
        $office = Office::with(['document', 'image'])->find($officeId);

        if (!$office) {
            return response()->json([
                'message' => 'المكتب غير موجود.'
            ], 404);
        }

        return response()->json([
            'message' => 'تم جلب بيانات المكتب بنجاح.',
            'data' => $office
        ], 200);
    }

    /**
     * Download office document
     */
    public function downloadOfficeDocument($officeId)
    {
        $office = Office::with('document')->find($officeId);

        if (!$office) {
            return response()->json([
                'message' => 'المكتب غير موجود.'
            ], 404);
        }

        if (!$office->document) {
            return response()->json([
                'message' => 'لا يوجد مستند مرفق لهذا المكتب.'
            ], 404);
        }

        // Extract the file path from the URL
        $documentUrl = $office->document->url;
        $parsedUrl = parse_url($documentUrl);
        $filePath = ltrim($parsedUrl['path'], '/');

        // Remove the base path to get the actual file path
        $filePath = str_replace(['storage/', 'pictures/'], '', $filePath);

        // Check if file exists in storage
        if (!Storage::disk('pictures')->exists($filePath)) {
            return response()->json([
                'message' => 'الملف غير موجود.'
            ], 404);
        }

        // Get the full file path
        $fullPath = Storage::disk('pictures')->path($filePath);

        // Return file download response
        return response()->download($fullPath);
    }
}
