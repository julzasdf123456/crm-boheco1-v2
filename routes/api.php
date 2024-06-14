<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ServiceConnectionInspectionsAPI;
use App\Http\Controllers\API\TelleringController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ServiceConnectionsEnergization;
use App\Http\Controllers\API\OtherData;
use App\Http\Controllers\API\TicketrepositoriesController;
use App\Http\Controllers\API\TicketsController;
use App\Http\Controllers\API\MeterReaderTracksAPI;
use App\Http\Controllers\API\ReadAndBillAPI;
use App\Http\Controllers\API\DisconnectionAPI;
use App\Http\Controllers\API\SMSNotificationsAPI;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// VERIFIES
// DOWNLOAD DATA
Route::get('get-service-connections/', [ServiceConnectionInspectionsAPI::class, 'getServiceConnections']);
Route::get('get-service-inspections/', [ServiceConnectionInspectionsAPI::class, 'getServiceInspections']);
Route::post('update-service-inspections/', [ServiceConnectionInspectionsAPI::class, 'updateServiceInspections']);
Route::post('notify-downloaded-inspections/', [ServiceConnectionInspectionsAPI::class, 'notifyDownloadedInspections']);

Route::get('get-payment-queues/', [TelleringController::class, 'fetchApprovedServiceConnections']);

// FOR ENERGIZATION ON CREW
Route::get('get-for-energization-data', [ServiceConnectionsEnergization::class, 'getForEnergizationData']);
Route::get('get-inspections-for-energization-data', [ServiceConnectionsEnergization::class, 'getInspectionsForEnergizationData']);
Route::post('update-energized', [ServiceConnectionsEnergization::class, 'updateEnergized']);
Route::post('create-timeframes', [ServiceConnectionsEnergization::class, 'createTimeFrames']);
Route::get('update-downloaded-service-connection-status', [ServiceConnectionsEnergization::class, 'updateDownloadedServiceConnectionStatus']);
Route::post('receive-mast-poles', [ServiceConnectionsEnergization::class, 'receiveMastPoles']);
Route::post('notify-downloaded', [ServiceConnectionsEnergization::class, 'notifyDownloaded']);
Route::get('get-account-master', [ServiceConnectionsEnergization::class, 'getAccountMaster']);

Route::get('get-towns', [OtherData::class, 'getTowns']);
Route::get('get-barangays', [OtherData::class, 'getBarangays']);
Route::get('get-all-crew', [OtherData::class, 'getAllCrew']);

Route::post('login', [UserController::class, 'login']);

// TICKETS
Route::get('test-connection', [TicketsController::class, 'testConnection']);
Route::get('get-ticket-types', [TicketrepositoriesController::class, 'getTicketTypes']);
Route::get('get-downloadable-tickets', [TicketsController::class, 'getDownloadableTickets']);
Route::get('update-downloaded-tickets-status', [TicketsController::class, 'updateDownloadedTicketsStatus']);
Route::post('upload-tickets', [TicketsController::class, 'uploadTickets']);
Route::get('get-crews-from-station', [TicketsController::class, 'getCrewsFromStation']);
Route::get('get-archive-tickets', [TicketsController::class, 'getArchiveTickets']);

// IMAGES
Route::post('save-uploaded-images', [ServiceConnectionsEnergization::class, 'saveUploadedImages']);

// TRACKS
Route::post('save-track-names', [MeterReaderTracksAPI::class, 'saveTrackNames']);
Route::post('save-tracks', [MeterReaderTracksAPI::class, 'saveTracks']);
Route::get('get-downloadable-tracknames', [MeterReaderTracksAPI::class, 'getDownloadableTrackNames']);
Route::get('get-downloadable-tracks', [MeterReaderTracksAPI::class, 'getDownloadableTracks']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// READ AND BILL
Route::get('get-undownloaded-schedules', [ReadAndBillAPI::class, 'getUndownloadedSchedules']);
Route::get('download-accounts', [ReadAndBillAPI::class, 'downloadAccounts']);
Route::get('download-rates', [ReadAndBillAPI::class, 'downloadRates']);
Route::get('update-downloaded-status', [ReadAndBillAPI::class, 'updateDownloadedStatus']);
Route::post('receive-readings', [ReadAndBillAPI::class, 'receiveReadings']);
Route::post('receive-bills', [ReadAndBillAPI::class, 'receiveBills']);
Route::post('save-reading-images', [ReadAndBillAPI::class, 'saveReadingImages']);
Route::get('get-bapa-list', [ReadAndBillAPI::class, 'getBapaList']);
Route::get('get-bapa-account-list', [ReadAndBillAPI::class, 'getBapaAccountList'])->name('readAndBillApi.get-bapa-account-list');
Route::get('get-available-bapa-schedule', [ReadAndBillAPI::class, 'getAvailableBapaSchedule']);
Route::get('update-bapa-schedule', [ReadAndBillAPI::class, 'updateBapaSchedule']);
Route::get('download-hv-accounts', [ReadAndBillAPI::class, 'downloadHvAccounts']);

// DISCONNECTION
Route::get('get-disconnection-list', [DisconnectionAPI::class, 'getDisconnectionList']);
Route::get('get-disconnection-list-schedule', [DisconnectionAPI::class, 'getDisconnectionListSchedule']);
Route::post('receive-disconnection-uploads', [DisconnectionAPI::class, 'receiveDisconnectionUploads']);
Route::get('update-downloaded-sched', [DisconnectionAPI::class, 'updateDownloadedSched']);


// SMS NOTIFICATIONS
Route::get('get-random-notification', [SMSNotificationsAPI::class, 'getRandomNotification']);
Route::get('update-sms', [SMSNotificationsAPI::class, 'updateSMSNotification']);

