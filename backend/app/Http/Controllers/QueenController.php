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
	public $endtime = 17; 
	 
    public function index()
    {
		$queens = Queen::all();
		$status = "OK";
		$msg = "Cita obtenidas exitosamente";
        return response()->json(['status' => $status, 'msg' => $msg, 'data' => $queens]);
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
		$status = "OK";
		$msg = "Cita creada exitosamente";		
		if($request->has('queen_datetime') && $request->has('name') && $request->has('email')){
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
			$status = "error";
			$msg = "Información insuficiente, la cita debe contener: Fecha y hora, Nombre y Correo.";
		}
		return response()->json(['status' => $status, 'msg' => $msg]);
    }

    public function available($datetime)
    {
		$status = "";
		$msg = "";
		$available = true;
		$timeshr = (int)$datetime->format('H');	
		if($timeshr < $this->inittime || $timeshr > $this->endtime){
			$status = "error";
			$msg = "Cita fuera del rango permitido";
			$available = false;
		} else {
			$queen = Queen::where('queen_datetime', $datetime->toDateTimeString())->count();
			$available = true;
			if($queen > 0){
				$available = false;
				$status = "error";
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
		$status = "OK";
		$msg = "Cita actualizada exitosamente";
		if($request->has('queen_datetime') || $request->has('name') || $request->has('email')){
			if($request->has('queen_datetime')){
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
			if($request->has('name')){
				$queen->name = $request->name;		
			}
			if($request->has('email')){
				$queen->email = $request->email;
			}
			$queen->save();
		} else {
			$status = "error";
			$msg = "Información insuficiente, la cita debe contener: Fecha y hora, Nombre y Correo.";
		}
	//	$queen->name = 'New Flight Name';

	//	$flight->save();
		return response()->json(['status' => $status, 'msg' => $msg]);
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
		$status = "OK";
		$msg = "Cita eliminada exitosamente";			
		$queen->delete();
		return response()->json(['status' => $status, 'msg' => $msg]);
    }
}
