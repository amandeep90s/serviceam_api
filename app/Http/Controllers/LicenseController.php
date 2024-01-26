<?php

namespace App\Http\Controllers;

use App\Models\Common\AdminService;
use App\Models\Common\CmsPage;
use App\Models\Common\Company;
use App\Models\Common\CompanyCountry;
use App\Models\Common\Setting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LicenseController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws ValidationException
     */
    public function verify(Request $request): JsonResponse
    {
        Log::info(implode(', ', $request->all()));

        $this->validate($request, [
            'access_key' => 'required',
            'domain' => 'required',
        ]);

        $license = Company::where('access_key', $request->access_key)->where('domain', 'like', '%' . $request->domain . '%')->first();
        if ($license != null) {
            if (Carbon::parse($license->expiry_date)->lt(Carbon::now())) {
                return response()->json(['message' => 'License Expired', 'error' => '503']);
            }

            $admin_service = AdminService::where('company_id', $license->id)->where('status', 1)->get();
            $company_country = CompanyCountry::with('country')->where('company_id', $license->id)->where('status', 1)->get();

            $settings = Setting::where('company_id', $license->id)->first();
            $cmsPage = CmsPage::where('company_id', $license->id)->get();

            $base_url = $license->base_url;
            $socket_url = $license->socket_url;

            return response()->json([
                'country' => $company_country,
                'services' => $admin_service,
                'base_url' => $base_url,
                'socket_url' => $socket_url,
                'settings' => json_decode($settings),
                'cmspage' => $cmsPage
            ]);
        } else {
            return response()->json(['message' => 'Domain is not authorised', 'error' => '503']);
        }
    }
}
