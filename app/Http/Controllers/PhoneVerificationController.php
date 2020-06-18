<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;

class PhoneVerificationController extends Controller
{
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedPhone()
                        ? redirect()->route('home')
                        : view('verifyphone');
    }

    public function verify(Request $request)
    {
        if ($request->user()->verification_code !== $request->code) {
            throw ValidationException::withMessages([
                'code' => ['The code your provided is wrong. Please try again or request another call.'],
            ]);
        }

        if ($request->user()->hasVerifiedPhone()) {
            return redirect()->route('home');
        }

        $request->user()->markPhoneAsVerified();

        return redirect()->route('home')->with('status', 'Your phone was successfully verified!');
    }

    public function buildTwiML($code)
    {
        /*$code = $this->formatCode($code);
        $response = new VoiceResponse();
        $response->say("Hi, thanks for Joining. This is your verification code. {$code}. I repeat, {$code}.");
        echo $response;*/
        echo $code;
    }

	public function formatCode($code)
	{
		$collection = collect(str_split($code));
		return $collection->reduce(
		    function ($carry, $item) {
		        return "{$carry}. {$item}";
		    }
		);
	}

}
