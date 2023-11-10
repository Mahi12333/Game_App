<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    //send Email for Already Existing User.

    public function generateOTP($email,$otp)
    {
        // $email="mahitoshgiri287@gmail.com";
        // $otp=132234;

        try {
            $data = ['otpString' => $otp];
            $user['to'] = $email;
            $orgname = 'Kaunsituency';
            $fromemail = env('MAIL_FROM_ADDRESS');
            // dd($fromemail);
            Mail::send('Otp_send.otp', $data, function ($message) use ($fromemail, $orgname, $user) {
                $message->from($fromemail, $orgname);
                $message->to($user['to'], $orgname);
                $message->subject('Sign OTP for Kaunsituency');
            });
        } catch (\Exception $e) {
            // Log or print the exception message for debugging
            //dd($e->getMessage());
        }
    }
}
