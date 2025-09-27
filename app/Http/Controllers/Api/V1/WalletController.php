<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function wallet(Request $request){
        $userWallet = $request->user()->load('wallet');
        return response()->json([
            'status' => 'success',
            'data' => $userWallet
        ], 200);
    }
}