<?php
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;
use Twilio\Rest\Client;
use App\User;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home')->middleware('verifiedphone'); 

Route::get('phone/verify', 'PhoneVerificationController@show')->name('phoneverification.notice');

Route::post('phone/verify', 'PhoneVerificationController@verify')->name('phoneverification.verify');

Route::post('build-twiml/{code}', 'PhoneVerificationController@buildTwiMl')->name('phoneverification.build'); 


Route::get('/call',function(){

	$code = random_int(100000, 999999);

	// Your Account SID and Auth Token from twilio.com/console
	$account_sid = 'AC6190055b23a084c6969f9ebb7f2ea3f1';
	$auth_token = 'dbe0938fed6aade59abe4b74174f56b0';
	// In production, these should be environment variables. E.g.:
	// $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]

	// A Twilio number you own with Voice capabilities
	$twilio_number = "+12058522814";

	// Where to make a voice call (your cell phone?)
	$to_number = "+12544012297";

	$client = new Client($account_sid, $auth_token);
	$client->account->calls->create(  
	    $to_number,
	    $twilio_number,
	    array(
	        "url" => "http://4a8d0944a6b4.ngrok.io/build-twiml/{$code}"
	    )
	);

	return ($client);

});

Route::get('/resend-sms', function(){

	$user = new User();
	return $user->resendSMS(auth()->id());
	
});


Route::get('/resend-ws', function(){

	$user = new User();
	return $user->resendWS(auth()->id());
	
});

Route::get('/site/denied/', function(Request $request){

	return '<p style="color:red">You are not allowed to the site because you are a minor, you are are ' . $request->age . ' years old</p>' ;

})->name('site.denied');


$hello = function(Request $request, $age) {
	return '<p style="color:green">You are allowed to the site becuase you are not a minor any more you are ' . $age . ' years old</p>';
};

Route::get('/site/{age}',$hello)->name('site.greet')->middleware('verifiedage');

Route::get('/user', function(Request $request){

	return $request->user();

});
