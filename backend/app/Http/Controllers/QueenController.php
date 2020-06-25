<?php

namespace App\Http\Controllers;

use App\Queen;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QueenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public $inittime = 8; 
	public $endtime = 16; 
	public $initdate =  " 00:00:00";
	public $endate =  " 23:59:59";
	 
    public function index()
    {
		$queens = Queen::all();
		foreach($queens as $queen){
			$queen->start = $queen->queen_datetime;
			$queen->end = Carbon::parse($queen->start)->addHour();
			$queen->end = $queen->end->isoFormat('YYYY-MM-DD HH:mm:ss');
		}		
		$status = 200;
		$msg = "Cita obtenidas exitosamente";
        return response()->json(['msg' => $msg, 'available' => $queens], $status);
    }
	
    public function datesbydate($date)
    {
		$queens = Queen::where('queen_datetime','>=',$date . $this->initdate)->where('queen_datetime','<=',$date . $this->endate)->get();
		$status = 200;
		$msg = "Cita obtenidas exitosamente";
		foreach($queens as $queen){
			$queen->start = $queen->queen_datetime;
			$queen->end = Carbon::parse($queen->start)->addHour();
			$queen->end = $queen->end->isoFormat('YYYY-MM-DD HH:mm:ss');
		}
        return response()->json(['msg' => $msg, 'available' => $queens], $status);
    }	

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
		$status = 200;
		$msg = "Cita creada exitosamente";		
		if($request->has('queen_datetime') && $request->has('name') && $request->has('email')){
			//dd(is_null($request->queen_datetime));
			if(!is_null($request->queen_datetime) && !is_null($request->name) && !is_null($request->email)){
				$queen_datetime = new Carbon($request->queen_datetime);
				$available = $this->available($queen_datetime);
				if($available['available']){	
					$queen = new Queen;
					$queen->name = $request->name;
					$queen->email = $request->email;
					$queen->queen_datetime = $queen_datetime->toDateTimeString();
					$queen->save();
				} else {
					$status = $available['status'];
					$msg = $available['msg'];
				}
			} else {
				$status = 500;
				$msg = "Información insuficiente, la cita debe contener: Fech, hora, nombre y correo.";
			}
		} else {
			$status = 500;
			$msg = "Información insuficiente, la cita debe contener: Fech, hora, nombre y correo.";
		}
		return response()->json(['msg' => $msg], $status);
    }

    public function available($datetime)
    {
		$status = "";
		$msg = "";
		$available = true;
		$timeshr = (int)$datetime->format('H');	
		
		if($timeshr < $this->inittime || $timeshr > $this->endtime){
			$status = 500;
			$msg = "Cita fuera del rango permitido";
			$available = false;
		} else {
			$starttime = $datetime->toDateTimeString();
			$endtime = Carbon::parse($starttime)->addHour();
			$endtime = $endtime->isoFormat('YYYY-MM-DD HH:mm:ss');
			$beforetime = Carbon::parse($starttime)->subHour();
			$beforetime = $beforetime->isoFormat('YYYY-MM-DD HH:mm:ss');
			$queen = Queen::where('queen_datetime', '>=',$beforetime)->where('queen_datetime', '<=',$endtime)->count();
			$available = true;
			if($queen > 0){
				$available = false;
				$status = 500;
				$msg = "Cita en un horario no disponible";
			}
		}
        return  ['available' => $available, 'status' => $status, 'msg' => $msg];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Queen  $queen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Queen $queen)
    {
        //
		$status =200;
		$msg = "Cita actualizada exitosamente";
		if($request->has('queen_datetime') || $request->has('name') || $request->has('email')){
			if($request->has('queen_datetime')){
				if(!empty($request->queen_datetime)){
					$queen_datetime = new Carbon($request->queen_datetime);
					$available = $this->available($queen_datetime);
					if($available['available']){	
						$queen->queen_datetime = $queen_datetime->toDateTimeString();
						//$queen->save();
					} else {
						$status = $available['status'];
						$msg = $available['msg'];
					}				
				}				
			}
			if($request->has('name')){
				if(!empty($request->name)){
					$queen->name = $request->name;	
				}				
			}
			if($request->has('email')){
				if(!empty($request->email)){
					$queen->email = $request->email;
				}
			}
			$queen->save();
		} else {
			$status = 500;
			$msg = "Información insuficiente, la cita debe contener: Fecha y hora, Nombre y Correo.";
		}
	//	$queen->name = 'New Flight Name';

	//	$flight->save();
		return response()->json(['msg' => $msg], $status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Queen  $queen
     * @return \Illuminate\Http\Response
     */
    public function destroy(Queen $queen)
    {
        //
		$status = 200;
		$msg = "Cita eliminada exitosamente";			
		$queen->delete();
		return response()->json(['msg' => $msg], $status);
    }
}
