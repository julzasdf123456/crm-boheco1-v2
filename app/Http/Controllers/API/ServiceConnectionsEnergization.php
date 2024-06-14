<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\Models\ServiceConnections;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceConnectionTimeframes;
use App\Models\ServiceConnectionImages;
use App\Models\ServiceConnectionCrew;
use App\Models\MastPoles;
use App\Models\IDGenerator;
use App\Models\SMSNotifications;
use App\Models\Notifiers;

class ServiceConnectionsEnergization extends Controller {

    public $successStatus = 200;

    public function getForEnergizationData(Request $request) {
        $crew = $request['Crew'];

        $serviceConnections = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_ServiceConnectionInspections', 'CRM_ServiceConnections.id', '=', 'CRM_ServiceConnectionInspections.ServiceConnectionId')
            ->leftJoin('CRM_ServiceConnectionMeterAndTransformer', 'CRM_ServiceConnections.id', '=', 'CRM_ServiceConnectionMeterAndTransformer.ServiceConnectionId')
            ->leftJoin('users', 'users.id', '=', 'CRM_ServiceConnectionInspections.Inspector')
            ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
            ->whereRaw("CRM_ServiceConnections.Status IN ('Approved', 'Not Energized')")
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Trash', 'No')
                    ->orWhereNull('CRM_ServiceConnections.Trash');
            })
            ->whereRaw("CRM_ServiceConnections.id IN (SELECT DISTINCT ServiceConnectionId FROM CRM_ServiceConnectionMeterAndTransformer WHERE ServiceConnectionId IS NOT NULL)")
            ->whereRaw("StationCrewAssigned='" . $crew . "'")
            ->select('CRM_ServiceConnections.*', 
                'CRM_ServiceConnectionMeterAndTransformer.MeterSerialNumber', 
                'CRM_ServiceConnectionMeterAndTransformer.MeterBrand',
                'CRM_ServiceConnectionMeterAndTransformer.MeterSealNumber',
                'users.name AS Verifier',
                'CRM_ServiceConnectionAccountTypes.AccountType AS AccountTypeWord')
            ->orderBy('CRM_ServiceConnections.ServiceAccountName')
            ->get();

        if ($serviceConnections == null) {
            return response()->json(['error' => 'No data'], 404); 
        } else {
            return response()->json($serviceConnections, $this->successStatus); 
        } 
    }

    public function updateDownloadedServiceConnectionStatus(Request $request) {
        $serviceConnections = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_ServiceConnectionInspections', 'CRM_ServiceConnections.id', '=', 'CRM_ServiceConnectionInspections.ServiceConnectionId')
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Status', 'Approved')
                    ->orWhere('CRM_ServiceConnections.Status', 'Not Energized');
            })
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Trash', 'No')
                    ->orWhereNull('CRM_ServiceConnections.Trash');
            })
            ->whereIn('CRM_ServiceConnections.id', DB::table('CRM_ServiceConnectionMeterAndTransformer')->pluck('ServiceConnectionId'))
            ->select('CRM_ServiceConnections.*')
            ->orderBy('CRM_ServiceConnections.ServiceAccountName')
            ->get();

        $crew = ServiceConnectionCrew::find($request['CrewAssigned']);

        $dateTimeDownloaded = date('Y-m-d H:i:s');

        foreach($serviceConnections as $item) {
            // CREATE LOG
            $timeFrame = new ServiceConnectionTimeframes;
            $timeFrame->id = IDGenerator::generateIDandRandString();
            $timeFrame->ServiceConnectionId = $item->id;
            $timeFrame->UserId = $request['User'];
            $timeFrame->Status = $request['Status'];
            $timeFrame->Notes = 'Application downloaded by crew ' . $request['CrewAssigned'];

            $timeFrame->save();
        }

        $serviceConnections = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_ServiceConnectionInspections', 'CRM_ServiceConnections.id', '=', 'CRM_ServiceConnectionInspections.ServiceConnectionId')
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Status', 'Approved')
                    ->orWhere('CRM_ServiceConnections.Status', 'Not Energized');
            })
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Trash', 'No')
                    ->orWhereNull('CRM_ServiceConnections.Trash');
            })
            ->update(['Status' => 'Downloaded by Crew', 'EnergizationOrderIssued' => 'Yes']);
        
        return response()->json(['res' => 'ok'], $this->successStatus);   
    }

    public function getInspectionsForEnergizationData(Request $request) {
        $crew = $request['Crew'];

        $serviceConnections = DB::table('CRM_ServiceConnections')
            ->leftJoin('CRM_ServiceConnectionInspections', 'CRM_ServiceConnections.id', '=', 'CRM_ServiceConnectionInspections.ServiceConnectionId')
            ->leftJoin('CRM_ServiceConnectionMeterAndTransformer', 'CRM_ServiceConnections.id', '=', 'CRM_ServiceConnectionMeterAndTransformer.ServiceConnectionId')
            ->leftJoin('users', 'users.id', '=', 'CRM_ServiceConnectionInspections.Inspector')
            ->leftJoin('CRM_ServiceConnectionAccountTypes', 'CRM_ServiceConnections.AccountType', '=', 'CRM_ServiceConnectionAccountTypes.id')
            ->whereRaw("CRM_ServiceConnections.Status IN ('Approved', 'Not Energized')")
            ->where(function ($query) {
                $query->where('CRM_ServiceConnections.Trash', 'No')
                    ->orWhereNull('CRM_ServiceConnections.Trash');
            })
            ->whereRaw("CRM_ServiceConnections.id IN (SELECT DISTINCT ServiceConnectionId FROM CRM_ServiceConnectionMeterAndTransformer WHERE ServiceConnectionId IS NOT NULL)")
            ->whereRaw("StationCrewAssigned='" . $crew . "'")
            ->select('CRM_ServiceConnectionInspections.*')
            ->orderBy('CRM_ServiceConnections.ServiceAccountName')
            ->get();

        if ($serviceConnections == null) {
            return response()->json(['error' => 'No data'], 404); 
        } else {
            return response()->json($serviceConnections, $this->successStatus); 
        } 
    }

    public function updateEnergized(Request $request) {
        $serviceConnections = ServiceConnections::find($request['id']);
        $serviceConnections->Status = $request['Status'];
        $serviceConnections->DateTimeLinemenArrived = $request['DateTimeLinemenArrived'];
        $serviceConnections->DateTimeOfEnergization = $request['DateTimeOfEnergization'];
        $serviceConnections->LinemanCrewExecuted = $request['LinemanCrewExecuted'];
        $serviceConnections->Notes = $request['Notes'];

        if ($serviceConnections->save()) {
            return response()->json(['success' => 'Upload Success'], $this->successStatus);             
        } else {
            return response()->json(['error' => 'Error Uploading Data ID ' . $request['id']], 404); 
        }
    }

    public function createTimeFrames(Request $request) {
        // CREATE Timeframes
        $timeFrame = new ServiceConnectionTimeframes;
        $timeFrame->id = $request['id'];
        $timeFrame->ServiceConnectionId = $request['ServiceConnectionId'];
        $timeFrame->UserId = $request['User'];
        $timeFrame->Status = $request['Status'];
        $timeFrame->created_at = $request['created_at'];
        $timeFrame->updated_at = $request['updated_at'];
        $timeFrame->Notes = 'Crew arrived at ' . date('F d, Y h:i:s A', strtotime($request['ArrivalDate'])) . '<br>' . 'Performed energization attempt at ' . date('F d, Y h:i:s A', strtotime($request['EnergizationDate'])) . '<br>' . $request['Reason'];
            
        if ($timeFrame->save()) {
            return response()->json(['success' => 'Upload Success'], $this->successStatus);             
        } else {
            return response()->json(['error' => 'Error Uploading Data ID ' . $request['ServiceConnectionId']], 404); 
        }
    }

    public function saveUploadedImages(Request $request) {
        if ($files = $request->file('file')) {
            
            $path = $request->file->storeAs('public/documents/' . $request['svcId'] . '/images', $request->file->getClientOriginalName() . '.' . $request->file->extension());
    
            $imgs = new ServiceConnectionImages;
            $imgs->id = IDGenerator::generateRandString(90);
            $imgs->Photo = $request->file->getClientOriginalName() . '.' . $request->file->extension();
            $imgs->ServiceConnectionId = $request['svcId'];
            $imgs->save();
                
            return response()->json([
                "success" => true,
                "file" => $path
            ], 200);
    
        } else {
            return response()->json([
                "success" => false,
                "file" => ''
          ], 401);
        }
    }

    public function receiveMastPoles(Request $request) {
        $mastPole = MastPoles::where('ServiceConnectionId', $request['ServiceConnectionId'])
            ->where('Latitude', $request['Latitude'])
            ->where('Longitude', $request['Longitude'])
            ->first();

        if ($mastPole != null) {

        } else {
            $mastPole = new MastPoles;
            $mastPole->id = $request['id'];
            $mastPole->ServiceConnectionId = $request['ServiceConnectionId'];
            $mastPole->Latitude = $request['Latitude'];
            $mastPole->Longitude = $request['Longitude'];
            $mastPole->PoleRemarks = $request['PoleRemarks'];
            $mastPole->DateTimeTaken = $request['DateTimeTaken'];
            $mastPole->save();
        }

        return response()->json($mastPole, 200);
    }

    public function notifyDownloaded(Request $request) {
        $name = $request['ServiceAccountName'];
        $contactNumber = $request['ContactNumber'];
        // CREATE NOTIFICATION
        if ($contactNumber != null) {
            if (strlen($contactNumber) > 10 && strlen($contactNumber) < 13) {
                $msg = "Hello " . $name . ", \nBOHECO I linemen are on its way to energize your house within 24 hours. Please ensure to have a representative in your house during the energization activity.\nHave a great day!";
                        SMSNotifications::createFreshSms($contactNumber, $msg, 'SERVICE CONNECTION ENERGIZATION', $request['id']);
            }
        }

        return response()->json('ok', 200);
    }

    public function getAccountMaster(Request $request) {
        $data = DB::connection('sqlsrvbilling')
            ->table('AccountMaster')
            ->select(
                'AccountNumber',
                'ConsumerName',
                'ConsumerAddress',
                'Item1 AS Coordinates',
                'ConsumerType',
                'Pole',
                'Route',
                'SequenceNumber', 
                'AccountStatus',  
                'MeterNumber', 
            )
            ->get();

        return response()->json($data, 200);
    }
}