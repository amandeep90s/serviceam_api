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
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class LicenseController extends Controller
{
    /**
     * Display a listing of the resource.
     * @throws ValidationException
     */
    public function verify(Request $request): JsonResponse|Response
    {
        Log::info(implode(', ', $request->all()));

        $this->validate($request, [
            'access_key' => 'required',
            'domain' => 'required',
        ]);

        $company = Company::where('access_key', $request->access_key)
            ->where('domain', 'like', '%' . $request->domain . '%')
            ->first();

        if ($company != null) {
            if (Carbon::parse($company->expiry_date)->lt(Carbon::now())) {
                return response()->json(['message' => 'License Expired', 'error' => '503']);
            }

            $admin_service = AdminService::where('company_id', $company->id)->where('status', 1)->get();
            $company_country = CompanyCountry::with('country')
                ->where('company_id', $company->id)
                ->where('status', 1)
                ->get();

            $settings = Setting::where('company_id', $company->id)->first();
            $cmsPage = CmsPage::where('company_id', $company->id)->get();

            return response()->json([
                'country' => $company_country,
                'services' => $admin_service,
                'base_url' => $company->base_url,
                'socket_url' => $company->socket_url,
                'settings' => json_decode($settings),
                'cmspage' => $cmsPage
            ]);
        } else {
            return response()->json(['message' => 'Domain is not authorised', 'error' => '503']);
        }
    }
}
