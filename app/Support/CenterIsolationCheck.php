<?php

namespace App\Support;

use App\Domain\Center\CenterContext;
use Illuminate\Support\Facades\Log;

class CenterIsolationCheck
{

    public static function check( $collection):void
    {
        if(auth()->user()->isPlatformAdmin()){
            return ;
        }

        $centerContext = new CenterContext();
        $centerId = $centerContext->id();

        $collection->each(function($model) use( &$hasIsolationError, $centerId){
            self::centerBelongs($model, $centerId);
        });
    }
    public static function centerBelongs($model, int | null $centerId){
        if(!$centerId ){
            return ;
        }
        if($model->center_id !== $centerId){
            Log::warning('center isolation error', [
                'model_id' => $model->id,
                'model_center_id' => $model->center_id,
                'employee_id' => auth()->user()->id,
                "employee_center_id" => auth()->user()->center_id
            ]);
            abort(400, 'Нарушение изоляции центра');
        } 
    }
}