<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;

class WhatsAppController extends Controller
{

    public function sendLink($phone, $link, $message)
    {
        $client = new Client();

        $url = 'https://api.z-api.io/instances/3C71DE8B199F70020C478ECF03C1E469/token/DC7D43456F83CCBA2701B78B/send-link';

        $response = $client->post($url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'Client-Token'  => 'Fabe25dbd69e54f34931e1c5f0dda8c5bS',
            ],
            'json' => [
                'phone'     => '55' . $phone,
                'message'   => $message,
                'image'     => 'https://grupo7assessoria.com.br/wp-content/uploads/2023/06/ASSESSORIA-700-x-140-px.png',
                'linkUrl'   => $link,
            ],
            'verify'        => false
        ]);

        $responseData = json_decode($response->getBody(), true);

        if (isset($responseData['id'])) {
            return true;
        } else {
            return false;
        }
    }
}
