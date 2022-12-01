<?php

# composer require google/cloud
# composer require google/apiclient

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Throwable;

class PostController extends Controller
{
    private string $sitekey;
    private string $secretKey;

    public function __construct()
    {
        # Login to your Cloudflare Dashboard and open Turnstile at the left, some url like https://dash.cloudflare.com/.../turnstile
        # Add your website then create site key and secret key

        $this->secretKey = 'your_cloudflare_turnstile_captcha_secretkey';
    }

    # This function is to process your form post data through the protection of Google ReCaptcha Enterprise
    public function process(Request $request)
    {
        # Validate if has required input data
        $request->validate([
            'cf-turnstile-response' => 'required'
        ]);
        $params = [
            [
                'name'     => 'secret',
                'contents' => 'your_cloudflare_turnstile_captcha_secretkey'
            ],
            [
                'name'     => 'response',
                'contents' => $request->input('cf-turnstile-response')
            ]
        ];
        $client = new Client();
        try {
            $cf = $client->request('POST', 'https://challenges.cloudflare.com/turnstile/v0/siteverify', ['multipart' => $params]);
        } catch (GuzzleException $e) {
            # something like return $e->getMessage() if you want to debug
            # Read more at https://developers.cloudflare.com/turnstile/get-started/server-side-validation/
        }
        $cf_response = json_decode($cf->getBody()->getContents(), true);
        if (!($cf_response['success'])) {
            # something like return 'Captcha verification failed';
        }

        # something like return 'Success passed captcha'
    }
}
