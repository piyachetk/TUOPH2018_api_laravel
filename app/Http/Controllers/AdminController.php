<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use \App\Account;
use \App\Booth;
use \Exception;

class AdminController extends Controller
{
    public function login(Request $request){
        $password = $request->get('password');
        $validPasswords = explode(',', env('PASSWORDS'));

        if (in_array($password, $validPasswords)){
            session()->put('authenticated', true);
            session()->save();
            return redirect('/admin');
        }
        else {
            session()->flash('error', 'รหัสไม่ถูกต้อง');
            return redirect()->back();
        }
    }

    public function dirtySetData(Request $request){
        $id = $request->get('code');

        $user = Account::where('id', '=', $id)->first();

        if ($user == null){
            session()->flash('error', 'ไม่พบผู้ใช้');
            return redirect()->back();
        }

        $data = [];

        try{
            $data[$request->get('field')] = $request->get('value');

            $user->fill($data);

            $user->save();

            session()->flash('success', 'แก้ไขข้อมูลสำเร็จ');
            return redirect()->back();
        }
        catch(Exception $ex){
            session()->flash('error', 'ไม่พบช่องข้อมูล');
            return redirect()->back();
        }
    }
}