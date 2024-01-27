<?php

namespace App\Traits;

use App\Helpers\Helper;
use App\Models\Common\Setting;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

trait Actions
{

    public mixed $settings;
    public $user;

    public function __construct()
    {
        $this->settings = Helper::setting();

        // Check if the user is authenticated before accessing properties
        $this->user = Auth::guard(strtolower(Helper::getGuard()))->user();

        // Only set $this->company_id if $this->user is not null
        $this->company_id = $this->user ? $this->user->company_id : null;
    }

    public function removeModel($id): JsonResponse
    {
        try {
            $model = $this->model->find($id);
            $model->delete();
            return Helper::getResponse(['message' => trans('admin.user_msgs.user_delete')]);
        } catch (\Throwable $e) {
            return Helper::getResponse(['status' => 404, 'message' => trans('admin.user_msgs.user_not_found'), 'error' => $e->getMessage()]);
        }
    }

    public function removeMultiple(): JsonResponse
    {

        try {
            $request = $this->request;
            $items = explode(',', $request->id);
            $this->model->destroy($items);
            return Helper::getResponse(['message' => trans('admin.user_msgs.user_delete')]);
        } catch (\Throwable $e) {
            return Helper::getResponse(['status' => 404, 'message' => trans('admin.user_msgs.user_not_found'), 'error' => $e->getMessage()]);
        }
    }

    public function changeStatus(): JsonResponse
    {
        $request = $this->request;
        try {
            $this->model->where('id', $request->id)->update(['status' => $request->status]);
            return Helper::getResponse(['message' => trans('admin.user_msgs.user_delete')]);
        } catch (\Throwable $e) {
            return Helper::getResponse(['status' => 404, 'message' => trans('admin.user_msgs.user_not_found'), 'error' => $e->getMessage()]);
        }
    }

    public function changeStatusAll(): JsonResponse
    {
        try {
            $request = $this->request;
            $items = explode(',', $request->id);
            $this->model->whereIn('id', $items)->update(['status' => $request->status]);
            return Helper::getResponse(['message' => trans('admin.user_msgs.user_delete')]);
        } catch (\Throwable $e) {
            return Helper::getResponse(['status' => 404, 'message' => trans('admin.user_msgs.user_not_found'), 'error' => $e->getMessage()]);
        }
    }

    /**
     * @throws \Exception
     */
    public function sendUserData($mailData): bool
    {
        try {
            $settings = Setting::where('company_id', Auth::user()->company_id)->first()->settings_data->site;
            if (!empty($settings->send_email) && $settings->send_email == 1) {
                $toEmail = $mailData['email'] ?? '';
                $name = $mailData['first_name'] ?? $mailData['name'];
                //  SEND MAIL TO USER, PROVIDER, FLEET
                $subject = "Notification";
                $data = ['body' => $mailData['body'], 'username' => $name, 'contact_mail' => $settings->contact_email, 'contact_number' => $settings->contact_number[0]->number];
                $templateFile = 'mails/notification_mail';
                Helper::send_emails($templateFile, $toEmail, $subject, $data);
            }
            return true;
        } catch (\Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }
}
