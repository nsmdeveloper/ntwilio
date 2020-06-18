<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;
use Twilio\Rest\Client;
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
            'phone_verified_at' => 'datetime',
        ];

    public function hasVerifiedPhone()
    {
        return ! is_null($this->phone_verified_at);
    }

    public function markPhoneAsVerified()
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function callToVerify()
    {
        $code = random_int(100000, 999999);
        $account_sid = getenv('TWILIO_SID');
        $auth_token = getenv('TWILIO_AUTH_TOKEN');
        
        $this->forceFill([
            'verification_code' => $code
        ])->save();

        $client = new Client($account_sid, $auth_token);

        $twilio_number_from = getenv('TWILIO_NUMBER_FROM');

        $twilio_number_to = getenv('TWILIO_NUMBER_TO');


        $client = new Client($account_sid, $auth_token);
        $client->messages->create(
        // Where to send a text message (your cell phone?)
        $twilio_number_to,
        array(
            'from' => $twilio_number_from,
            'body' => 'Your verification code is : ' . $code
        )
        );

        /*$client->calls->create("+1".$this->phone,
            //$this->phone,
            "+12058522814", // REPLACE WITH YOUR TWILIO NUMBER
            ["url" => "http://0c201d1194f9.ngrok.io/build-twiml/{$code}"]
        );*/
    }

        public function resendSMS($uid)
        {
            
            $code = random_int(100000, 999999);
            $account_sid = getenv('TWILIO_SID');
            $auth_token = getenv('TWILIO_AUTH_TOKEN');


                    

            $this->where('id',$uid)->update(['verification_code'=>$code]);

            

            $client = new Client($account_sid, $auth_token);

           $twilio_number_from = getenv('TWILIO_NUMBER_FROM');

           $twilio_number_to = getenv('TWILIO_NUMBER_TO');

            $client = new Client($account_sid, $auth_token);
            $client->messages->create(
            // Where to send a text message (your cell phone?)
            $twilio_number_to,
            array(
                'from' => $twilio_number_from,
                'body' => 'Your verification code is : ' . $code
            )
            );

            /*$client->calls->create("+1".$this->phone,
                //$this->phone,
                "+12058522814", // REPLACE WITH YOUR TWILIO NUMBER
                ["url" => "http://0c201d1194f9.ngrok.io/build-twiml/{$code}"]
            );*/

            return $code;
        }

        public function resendWS($uid)
        {
            
            $code = random_int(100000, 999999);
            
            $account_sid = getenv('TWILIO_SID');
            $auth_token = getenv('TWILIO_AUTH_TOKEN');
            
            $whatsapp_number_from = getenv('TWILIO_WHATSAPP_NUMBER_FROM');
           
            $whatsapp_number_to = getenv('TWILIO_WHATSAPP_NUMBER_TO');


            $twilio = new Client($account_sid, 
                $auth_token);
                        
            
            $this->where('id',$uid)->update(['verification_code'=>$code]);

            $message = "Your *Verification Code* is: " . $code;

            $twilio->messages->create($whatsapp_number_to,
               [
                   "from" => $whatsapp_number_from,
                   "body" => $message
               ]
            );

            return $code;
        }

}
