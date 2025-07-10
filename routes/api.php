<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PropertiesController;
use App\Http\Controllers\API\WantedPropertyController;
use App\Http\Controllers\API\OfficeController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\RequestController;
use App\Http\Controllers\API\FavoriteController;
use App\Http\Controllers\API\OfficeFollowerController as APIOfficeFollowerController;
use App\Http\Controllers\OfficeFollowerController;

//------------------------Auth-------------------------------------------------
Route::post('/login', [UserController::class, 'login']);
Route::post('/registerUser', [UserController::class, 'registerUser']);
Route::post('/registerOffice', [OfficeController::class, 'registerOffice']);
Route::post('/logout', [UserController::class, 'logout']);


//---------------------user-------------------------------------------------------------

Route::group(['middleware' => ['api', 'jwt.auth'], 'prefix' => 'user'], function () {


    Route::get('/showOffice/{Id}', [OfficeController::class, 'showOffice']);
    Route::get('/getFollowersCount/{Id}', [OfficeController::class, 'getFollowersCount']);
    Route::get('/followOffice/{Id}', [OfficeController::class, 'followOffice']);
    Route::get('/getAllOfficePropertyVideos/{id}', [OfficeController::class, 'getAllOfficePropertyVideos']);
    Route::get('/getOfficePropertyCount/{id}', [OfficeController::class, 'getOfficePropertyCount']);
    Route::get('/properties/availability', [PropertiesController::class, 'availability']);
    Route::post('/payad', [PropertiesController::class, 'receiveCard']);
    Route::post('/updateProfile', [UserController::class, 'updateProfile']);
    Route::get('/getProfile', [UserController::class, 'getProfile']);
    Route::post('/addToFavorites', [FavoriteController::class, 'addToFavorites']);
    Route::post('/removeFromFavorites', [FavoriteController::class, 'removeFromFavorites']);
    Route::get('/getFavorites', [FavoriteController::class, 'getFavorites']);
    Route::get('/getOfficeViews/{Id}', [OfficeController::class, 'getOfficeViews']);
    Route::post('/rateOffice/{id}', [ReviewController::class, 'rateOffice']);
     Route::get('/getRating/{office_id}', [ReviewController::class, 'getRating']);
      Route::get('/showProperty/{Id}', [PropertiesController::class, 'showProperty']);
     Route::get('/properties/search/{ad_number}', [PropertiesController::class, 'searchByAdNumber']);
     Route::get('/properties/filter', [PropertiesController::class, 'filter']);
      Route::get('/getAllOfficeProperties/{Id}', [OfficeController::class, 'getAllOfficeProperties']);

});
//--------------------------office--------------------------------------------------------
Route::group(['middleware' => ['auth:office-api','office'], 'prefix' => 'office'], function () {
    Route::get('/GetOfficeFollowers/{id}', [OfficeController::class, 'GetOfficeFollowers']);
    Route::post('/changePropertyStatus/{Id}', [PropertiesController::class, 'changePropertyStatus']);
    Route::get('/showOffice/{Id}', [OfficeController::class, 'showOffice']);
    Route::get('/getFollowersCount/{Id}', [OfficeController::class, 'getFollowersCount']);
    Route::post('/propertyStore', [PropertiesController::class, 'propertyStore']);
    Route::post('/requestSubscription', [OfficeController::class, 'requestSubscription']);
    Route::get('/getPendingRequestsOffice', [RequestController::class, 'getPendingRequestsOffice']);
    Route::get('/getacceptedRequestsOffice', [RequestController::class, 'getacceptedRequestsOffice']);
    Route::get('/getrejectedRequestsOffice', [RequestController::class, 'getrejectedRequestsOffice']);
    Route::get('/getOfficePropertyCount/{id}', [OfficeController::class, 'getOfficePropertyCount']);
    Route::get('/getAllOfficePropertyVideos/{id}', [OfficeController::class, 'getAllOfficePropertyVideos']);
    Route::get('/getOfficeViews/{Id}', [OfficeController::class, 'getOfficeViews']);
    Route::get('/getPendingRequestsOffice', [RequestController::class, 'getPendingRequestsOffice']);
    Route::get('/getAcceptedRequestsOffice', [RequestController::class, 'getAcceptedRequestsOffice']);
    Route::get('/getRejectedRequestsOffice', [RequestController::class, 'getRejectedRequestsOffice']);
    Route::get('/getRating/{office_id}', [ReviewController::class, 'getRating']);
    Route::get('/getActiveSubscriptionsOffice', [RequestController::class, 'getActiveSubscriptionsOffice']);
    Route::get('/getRejectedSubscriptionsOffice', [RequestController::class, 'getRejectedSubscriptionsOffice']);
    Route::get('/getPendingSubscriptionsOffice', [RequestController::class, 'getPendingSubscriptionsOffice']);
     Route::get('/showProperty/{Id}', [PropertiesController::class, 'showProperty']);
      Route::get('/getAllOfficeProperties/{Id}', [OfficeController::class, 'getAllOfficeProperties']);



});
//--------------------------Visitor-----------------------------------------------
    Route::group(['prefix' => 'visitor'], function () {

    Route::get('/getRecentOffers', [PropertiesController::class, 'getRecentOffers']);
    Route::get('/getAllproperty', [PropertiesController::class, 'getAllproperty']);
    Route::get('/getPropertyVideos', [PropertiesController::class, 'getPropertyVideos']);

});

//----------------------------Admin-----------------------------------------------
Route::group(['middleware' => ['jwt.auth','admin'], 'prefix' => 'admin'], function () {
Route::get('/rejectOfficeRequest/{id}', [AdminOfficeController::class, 'rejectOfficeRequest']);
Route::get('/approveOfficeRequest/{id}', [AdminOfficeController::class, 'approveOfficeRequest']);
Route::post('/offices/{id}', [AdminController::class, 'rejectOffice']);
Route::get('/pendingSubscription', [AdminController::class, 'pendingSubscription']);
Route::get('/rejectSubscription/{id}', [Adminontroller::class, 'rejectSubscription']);
Route::get('/pandingRequest', [AdminController::class, 'pandingRequest']);
Route::get('/approveProperty/{id}', [AdminController::class, 'approveProperty']);
Route::get('/rejectProperty/{id}', [AdminController::class, 'rejectProperty']);
Route::get('/approveSubscription/{id}', [AdminController::class, 'approveSubscription']);
Route::get('getOfficesByViews', [AdminController::class, 'getOfficesByViews']);
Route::get('getOfficesByFollowers', [AdminController::class, 'getOfficesByFollowers']);
});


