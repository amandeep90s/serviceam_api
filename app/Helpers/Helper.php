<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class Helper
{
    private static mixed $cache = null;

    /**
     * Get favicon icon
     * @return null
     */
    public static function getFavIcon()
    {
        return self::getSettingField('site_icon');
    }

    /**
     * Get setting field
     * @param $fieldName
     * @return null
     */
    private static function getSettingField($fieldName)
    {
        $settings = self::getCache()->settings;

        if ($settings != null) {
            return $settings->settings_data->site->$fieldName ?? null;
        }
        return null;
    }

    /**
     * Get cache
     * @return mixed|null
     */
    public static function getCache()
    {
        if (self::$cache === null) {
            $domain = $_SERVER['SERVER_NAME'];
            self::$cache = json_decode(Redis::get($domain));
        }

        return self::$cache;
    }

    /**
     * Get site logo
     * @return null
     */
    public static function getSiteLogo()
    {
        return self::getSettingField('site_logo');
    }

    /**
     * Get base url
     * @return null
     */
    public static function getBaseUrl()
    {
        return self::getCache()->base_url ?? null;
    }

    /**
     * Get socket url
     * @return null
     */
    public static function getSocketUrl()
    {
        return self::getCache()->socket_url ?? null;
    }

    /**
     * Get service base url
     * @return bool|string
     */
    public static function getServiceBaseUrl(): bool | string
    {
        $services = self::getCache()->services ?? [];

        $servicesBaseUrl = [];
        foreach ($services as $service) {
            $servicesBaseUrl[$service->admin_service] = $service->base_url;
        }

        return json_encode($servicesBaseUrl);
    }

    /**
     * Is destination
     * @return bool
     */
    public static function isDestination(): bool
    {
        return self::getSettingValue('transport->destination', 1) == 1;
    }

    /**
     * Get setting value
     * @param $settingName
     * @param $default
     * @return mixed|null
     */
    private static function getSettingValue($settingName, $default = null)
    {
        $settings = self::getCache()->settings;
        return $settings->$settingName ?? $default;
    }

    /**
     * Get demo mode
     * @return mixed|null
     */
    public static function getDemomode()
    {
        return self::getSettingValue('demo_mode', 0);
    }

    /**
     * Get chat mode
     * @return mixed|null
     */
    public static function getChatmode()
    {
        return self::getSettingValue('chat', 0);
    }

    /**
     * Get encrypt
     * @return mixed|null
     */
    public static function getEncrypt()
    {
        return self::getSettingValue('encrypt', 0);
    }

    /**
     * Get banner
     * @return mixed|null
     */
    public static function getBanner()
    {
        return self::getSettingValue('banner', 0);
    }

    /**
     * Get salt key
     * @return string
     */
    public static function getSaltKey()
    {
        return base64_encode(self::getSettingValue('company_id'));
    }

    /**
     * Check service
     * @param $type
     * @return bool
     */
    public static function checkService($type)
    {
        return in_array($type, self::getServiceList());
    }

    /**
     * Get service list
     * @return array
     */
    public static function getServiceList(): array
    {
        $services = self::getCache()->services;

        $data = [];
        foreach ($services as $service) {
            $data[$service->id] = $service->admin_service;
        }

        return $data;
    }

    /**
     * Get cms page
     * @return mixed
     */
    public static function getcmspage()
    {
        return self::getCache()->cmspage;
    }

    /**
     * Check payment setting
     * @param $type
     * @return bool
     */
    public static function checkPayment($type): bool
    {
        $paymentConfig = json_decode(json_encode(self::getSettings()->payment), true);
        $payment = array_values(array_filter($paymentConfig, fn($e) => $e['name'] == $type));
        return empty($payment) ? $payment[0]["status"] == 1 : false;
    }

    /**
     * Get settings
     * @return null
     */
    public static function getSettings()
    {
        $settings = self::getCache()->settings;

        return $settings->settings_data ?? null;
    }

    /**
     * Get country list
     * @return array
     */
    public static function getCountryList(): array
    {
        $countryList = self::getCache()->country;

        $data = [];
        foreach ($countryList as $country) {
            $data[$country->country->id] = $country->country->country_name;
        }

        return $data;
    }

    /**
     * Permission list
     * @return array|mixed
     */
    public static function permissionList()
    {
        $user = Session::get('user_id');
        $permissions = Redis::get($user);

        return $permissions ? json_decode($permissions) : [];
    }

    /**
     * Get country currency
     * @param $id
     * @return mixed|null
     */
    public static function getCountryCurrency($id)
    {
        $countryList = self::getCache()->country;

        $data = [];
        foreach ($countryList as $country) {
            $data[$country->country->id] = $country->currency;
        }

        return $data[$id] ?? null;
    }

    /**
     * Get access key
     * @return mixed|string
     */
    public static function getAccessKey()
    {
        $domain = $_SERVER['SERVER_NAME'];
        $path = storage_path('license') . '/' . $domain . '.json';
        $configFileExists = file_exists($path);

        if ($configFileExists) {
            $config = file_get_contents($path);
            return json_decode($config, true)['accessKey'];
        }

        return '123456';
    }
}
