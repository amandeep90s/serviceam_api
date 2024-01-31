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
    public $company_id;

    public function __construct()
    {
        // Get settings from Helper
        $this->settings = Helper::setting();

        // Get the authenticated user
        $this->user = $this->getAuthenticatedUser();

        // Only set $this->company_id if $this->user is not null
        $this->company_id = $this->user ? $this->user->company_id : null;
    }

    // Method to get the authenticated user
    private function getAuthenticatedUser(): ?Authenticatable
    {
        // Check if the user is authenticated before accessing properties
        return Auth::guard(strtolower(Helper::getGuard()))->user();
    }

    // Method to remove a model
    public function removeModel($id): JsonResponse
    {
        try {
            // Find the model by id and delete it
            $model = $this->model->find($id);
            $model->delete();
            // Return a response with a success message
            return Helper::getResponse(['message' => trans('admin.user_msgs.user_delete')]);
        } catch (\Throwable $e) {
            // Return a response with an error message if something goes wrong
            return Helper::getResponse(['status' => 404, 'message' => trans('admin.user_msgs.user_not_found'), 'error' => $e->getMessage()]);
        }
    }

    // Method to remove multiple models
    public function removeMultiple(): JsonResponse
    {
        try {
            // Get the ids from the request and explode them into an array
            $request = $this->request;
            $items = explode(',', $request->id);
            // Destroy the models by ids
            $this->model->destroy($items);
            // Return a response with a success message
            return Helper::getResponse(['message' => trans('admin.user_msgs.user_delete')]);
        } catch (\Throwable $e) {
            // Return a response with an error message if something goes wrong
            return Helper::getResponse(['status' => 404, 'message' => trans('admin.user_msgs.user_not_found'), 'error' => $e->getMessage()]);
        }
    }

    // Method to change the status of a model
    public function changeStatus(): JsonResponse
    {
        $request = $this->request;
        try {
            // Update the status of the model by id
            $this->model->where('id', $request->id)->update(['status' => $request->status]);
            // Return a response with a success message
            return Helper::getResponse(['message' => trans('admin.user_msgs.user_delete')]);
        } catch (\Throwable $e) {
            // Return a response with an error message if something goes wrong
            return Helper::getResponse(['status' => 404, 'message' => trans('admin.user_msgs.user_not_found'), 'error' => $e->getMessage()]);
        }
    }

    // Method to change the status of multiple models
    public function changeStatusAll(): JsonResponse
    {
        try {
            // Get the ids from the request and explode them into an array
            $request = $this->request;
            $items = explode(',', $request->id);
            // Update the status of the models by ids
            $this->model->whereIn('id', $items)->update(['status' => $request->status]);
            // Return a response with a success message
            return Helper::getResponse(['message' => trans('admin.user_msgs.user_delete')]);
        } catch (\Throwable $e) {
            // Return a response with an error message if something goes wrong
            return Helper::getResponse(['status' => 404, 'message' => trans('admin.user_msgs.user_not_found'), 'error' => $e->getMessage()]);
        }
    }

    // Method to send user data
    public function sendUserData($mailData)
    {
        try {
            // Get the settings for the authenticated user's company
            $settings = Setting::where('company_id', Auth::user()->company_id)->first()->settings_data->site;
            // Check if the settings allow sending emails
            if (!empty($settings->send_email) && $settings->send_email == 1) {
                // Prepare the email data
                $toEmail = $mailData['email'] ?? '';
                $name = $mailData['first_name'] ?? $mailData['name'];
                $subject = "Notification";
                $data = ['body' => $mailData['body'], 'username' => $name, 'contact_mail' => $settings->contact_email, 'contact_number' => $settings->contact_number[0]->number];
                $templateFile = 'mails/notification_mail';
                // Send the email
                Helper::sendEmails($templateFile, $toEmail, $subject, $data);
            }
            return true;
        } catch (\Throwable $e) {
            // Throw an exception if something goes wrong
            return Helper::getResponse(['status' => 404, 'error' => $e->getMessage()]);
        }
    }
}
