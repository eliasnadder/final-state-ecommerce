<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PropertiesController;
use App\Http\Controllers\API\OfficeController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\UserOfficeController;

//------------------------Auth-------------------------------------------------
Route::post('/login', [UserController::class, 'login']);
Route::post('/registerUser', [UserController::class, 'registerUser']);
Route::post('/registerOffice', [OfficeController::class, 'registerOffice']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('api');


//---------------------user-------------------------------------------------------------
Route::group(['middleware' => ['api', 'jwt.auth'], 'prefix' => 'user'], function () {
    Route::get('/showOffice/{Id}', [UserOfficeController::class, 'showOffice']);
    Route::get('/getFollowersCount/{Id}', [UserOfficeController::class, 'getFollowersCount']);
    Route::get('/followOffice/{Id}', [UserOfficeController::class, 'followOffice']);
    Route::get('/is-followed/{Id}', [UserOfficeController::class, 'isFollowed']);
    Route::get('/getAllOfficePropertyVideos/{id}', [UserOfficeController::class, 'getAllOfficePropertyVideos']);
    Route::get('/getOfficePropertyCount/{id}', [UserOfficeController::class, 'getOfficePropertyCount']);
    Route::get('/getAllOfficeProperties/{Id}', [UserOfficeController::class, 'getAllOfficeProperties']);
    Route::get('/getOfficeViews/{Id}', [UserOfficeController::class, 'getOfficeViews']);

    Route::get('/properties/availability', [PropertiesController::class, 'availability']);
    Route::post('/payad', [PropertiesController::class, 'receiveCard']);

    Route::post('/updateProfile', [UserController::class, 'updateProfile']);
    Route::get('/getProfile', [UserController::class, 'getProfile']);

    Route::post('/addToFavorites', [FavoriteController::class, 'addToFavorites']);
    Route::post('/removeFromFavorites', [FavoriteController::class, 'removeFromFavorites']);
    Route::get('/getFavorites', [FavoriteController::class, 'getFavorites']);
    Route::get('/is-favorited', [FavoriteController::class, 'isFavorited']);

    Route::post('/rateOffice/{id}', [ReviewController::class, 'rateOffice']);
    Route::get('/getRating/{office_id}', [ReviewController::class, 'getRating']);

    Route::get('/showProperty/{Id}', [PropertiesController::class, 'showProperty']);
    Route::get('/properties/search/{ad_number}', [PropertiesController::class, 'searchByAdNumber']);
    Route::get('/properties/filter', [PropertiesController::class, 'filter']);
});

//--------------------------office--------------------------------------------------------
Route::group(['middleware' => ['auth:office-api', 'office'], 'prefix' => 'office'], function () {
    Route::get('/getOfficeFollowers', [OfficeController::class, 'getOfficeFollowers']);
    Route::get('/getOffice', [OfficeController::class, 'getOffice']);
    Route::get('/getOfficeViews', [OfficeController::class, 'getOfficeViews']);
    Route::get('/getRating/{office_id}', [ReviewController::class, 'getRating']);

    Route::get('/getAllProperties', [OfficeController::class, 'getAllProperties']);
    Route::post('/changePropertyStatus/{Id}', [PropertiesController::class, 'changePropertyStatus']);
    Route::get('/getAllOfficePropertyVideos', [OfficeController::class, 'getAllOfficePropertyVideos']);
    Route::get('/getOfficePropertyCount', [OfficeController::class, 'getOfficePropertyCount']);
    Route::post('/propertyStore', [PropertiesController::class, 'propertyStore']);

    Route::get('/getPendingRequestsOffice', [RequestController::class, 'getPendingRequestsOffice']);
    Route::get('/getAcceptedRequestsOffice', [RequestController::class, 'getacceptedRequestsOffice']);
    Route::get('/getRejectedRequestsOffice', [RequestController::class, 'getrejectedRequestsOffice']);

    Route::post('/requestSubscription', [OfficeController::class, 'requestSubscription']);
    Route::get('/getActiveSubscriptionsOffice', [RequestController::class, 'getActiveSubscriptionsOffice']);
    Route::get('/getRejectedSubscriptionsOffice', [RequestController::class, 'getRejectedSubscriptionsOffice']);
    Route::get('/getPendingSubscriptionsOffice', [RequestController::class, 'getPendingSubscriptionsOffice']);
});

//--------------------------Visitor-----------------------------------------------
Route::group(['prefix' => 'visitor'], function () {
    Route::get('/getRecentOffers', [PropertiesController::class, 'getRecentOffers']);
    Route::get('/getAllproperty', [PropertiesController::class, 'getAllproperty']);
    Route::get('/getPropertyVideos', [PropertiesController::class, 'getPropertyVideos']);
});

//----------------------------Admin-----------------------------------------------
Route::group(['middleware' => ['api', 'jwt.auth'], 'prefix' => 'admin'], function () {
    Route::get('/rejectOfficeRequest/{id}', [AdminController::class, 'rejectOfficeRequest']);
    Route::get('/approveOfficeRequest/{id}', [AdminController::class, 'approveOfficeRequest']);

    Route::post('/offices/{id}', [AdminController::class, 'rejectOffice']);

    Route::get('/pendingSubscription', [AdminController::class, 'pendingSubscription']);
    Route::get('/approveSubscription/{id}', [AdminController::class, 'approveSubscription']);
    Route::get('/rejectSubscription/{id}', [AdminController::class, 'rejectSubscription']);

    Route::get('/pendingRequest', [AdminController::class, 'pendingRequest']);
    Route::get('/approveProperty/{id}', [AdminController::class, 'approveProperty']);
    Route::get('/rejectProperty/{id}', [AdminController::class, 'rejectProperty']);

    Route::get('/getOfficesByViews', [AdminController::class, 'getOfficesByViews']);
    Route::get('/getOfficesByFollowers', [AdminController::class, 'getOfficesByFollowers']);

    // Office document management routes
    Route::get('/getPendingOfficeRequests', [AdminController::class, 'getPendingOfficeRequests']);
    Route::get('/getPendingOfficeRequestsWithDocuments', [AdminController::class, 'getPendingOfficeRequestsWithDocuments']);
    Route::get('/getOfficeWithDocument/{officeId}', [AdminController::class, 'getOfficeWithDocument']);
    Route::get('/downloadOfficeDocument/{officeId}', [AdminController::class, 'downloadOfficeDocument']);
});
