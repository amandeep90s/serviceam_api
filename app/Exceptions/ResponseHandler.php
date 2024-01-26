<?php

namespace App\Exceptions;

use App\Helpers\Helper;
use App\Jobs\SendEmailJob;
use App\Models\Common\Company;
use App\Models\Common\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ResponseHandler extends Exception
{
    public function handle(): JsonResponse
    {
        $response = [
            'statusCode' => 500,
            'title' => 'Oops Something went wrong!',
            'message' => 'Oops Something went wrong!',
            'responseData' => [],
            'error' => $this->formatExceptionAsArray(),
        ];

        return $this->jsonResponse($response);
    }

    private function formatExceptionAsArray(): array
    {
        return [
            'message' => 'Internal Server Error',
            // Add any other details you want to expose
        ];
    }

    private function jsonResponse($data): JsonResponse
    {
        $jsonOptions = defined('JSON_PARTIAL_OUTPUT_ON_ERROR') ? JSON_PARTIAL_OUTPUT_ON_ERROR : 0;

        $response = Response::json($data, $data['statusCode'], [], $jsonOptions);

        $this->handleErrorEmail($data['error']);

        return $response;
    }

    private function handleErrorEmail($error): void
    {
        try {
            $setting = Setting::where('company_id', $this->getCompanyId())->first();

            if ($setting !== null && $setting->error_mode == 1) {
                $company = Company::find($this->getCompanyId());
                $emails = explode(',', $setting->error_mail);

                $error['time'] = Carbon::now()->format('d/m/Y h:m:s');
                dispatch(new SendEmailJob($error, $company->company_name, $emails));
            }
        } catch (Exception $exception) {
            // Handle the exception or log it as needed
        }
    }

    private function getCompanyId()
    {
        return Auth::guard(strtolower(Helper::getGuard()))->user()->company_id;
    }
}
