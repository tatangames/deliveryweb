<?php

namespace App\Http\Controllers\Backend\api\MetodoPago;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ApiMetodoPagoController extends Controller
{
    public function verMetodoPago(Request $request){

        $rules = array(
            'clienteid' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){return ['success' => 0]; }

        if($info = Cliente::where('id', $request->clienteid)->first()){

            return ['success' => 1, 'credito' => $info->monedero];
        }else{
            return ['success' => 2];
        }
    }



}
