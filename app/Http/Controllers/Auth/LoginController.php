<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;



class LoginController extends Controller
{
    public function showLoginForm(){
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validate($request,[
            'usuario'=> 'required|string',
            'password'=>'required|string',
        ]);
        
       if($request->usuario=='admin' && $request->password=='123')
            return redirect()->route('main');
        else
        return back()
        ->withErrors(['usuario' => trans('auth.failed')])
        ->withInput(request(['usuario'])); 

            

        
        /*if (Auth::attempt(['usuario' => $request->usuario,'password' => $request->password,'estado'=>1])){
            return redirect()->route('main');
        }

        return back()
        ->withErrors(['usuario' => trans('auth.failed')])
        ->withInput(request(['usuario']));*/
    }


}
