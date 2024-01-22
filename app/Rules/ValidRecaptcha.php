<?php

namespace App\Rules;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidRecaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): PotentiallyTranslatedString $fail
     * @throws GuzzleException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $client = new Client([
            'base_uri' => 'https://google.com/recaptcha/api/'
        ]);

        $response = $client->post('siteverify', [
            'query' => [
                'secret' => env('GOOGLE_RECAPTCHA_SECRET'),
                'response' => $value
            ]
        ]);

        $success = json_decode($response->getBody())->success;

        if (!$success) {
            $fail('ReCaptcha verification failed.');
        }
    }
}
