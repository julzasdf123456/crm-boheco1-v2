<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SAPObject extends Model
{
    use HasFactory;

    public static function getSIBatchIdDaily() {
        return "SI-" . date('Ymd');
    }

    public static function getIPBatchIdDaily() {
        return "INPAY-" . date('Ymd');
    }

    public static function sumAll($arr) {
        $sum = 0;

        for ($i=0; $i<count($arr); $i++) {
            $sum += ($arr[$i] != null ? floatval($arr[$i]) : 0);
        }

        return $sum;
    }

    public static function getAccountCodeByConsumerType($type) {
        if ($type == null) {
            return "41040410000";
        } else {
            if (in_array($type, ['B', 'E', 'R', 'RC', 'RI', 'RM', 'RS'])) {
                return "41040410000";
            } elseif (in_array($type, ['C', 'CL', 'CM', 'CS', 'P', 'S'])) {
                return "41040411000";
            } elseif (in_array($type, ['H'])) {
                return "41040412000";
            } else {
                return "41040410000";
            }
        }
    }
}
