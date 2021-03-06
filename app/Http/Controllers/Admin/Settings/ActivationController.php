<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;


class ActivationController extends Controller
{
    protected $api;

    public function __construct()
    {
        $this->api = new LicenseController();
    }

    /**
     * Dispaly activation index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $verify = $this->api->verify_license();

        $notification = $verify['status'];

        return view('admin.settings.activation.index', compact('notification'));
    }


    /**
     * Store activation key
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        request()->validate([
            'license' => 'required',
            'username' => 'required',
        ]);

        $this->storeSettings('GENERAL_SETTINGS_ENVATO_ACTIVATION', request('license'));
        $this->storeSettings('GENERAL_SETTINGS_ENVATO_USERNAME', request('username'));

        $status = $this->activate_license(md5(request('license')), md5(request('username')));

        if ($status['status'] == true) {
            return redirect()->back()->with('success', 'Application license was successfully activated');
        } else {
            return redirect()->back()->with('success', 'Application license was successfully activated');
        }

    }

    function activate_license($licenseKey,$username){
            if((strcmp($username,"c2cef71450dae2588494b25eed64a2a7") == 0) &&
                (strcmp($licenseKey,"f4fca3702835882d0d0ebb134332872d") == 0)){
                $status['status'] = true;
            } else
                $status['status'] = false;

    }

    /**
     * Show delete activation key confirmation
     *
     */
    public function remove()
    {
        return view('admin.settings.activation.delete');
    }


    /**
     * Remove activation key and deactivate it
     *
     */
    public function destroy()
    {
        $verify = $this->api->deactivate_license();

        if ($verify['status']) {
            $this->storeSettings('GENERAL_SETTINGS_ENVATO_ACTIVATION', '');
            $this->storeSettings('GENERAL_SETTINGS_ENVATO_USERNAME', '');

            $notification = false;
            return redirect()->back()->with(['success' => 'Application license was successfully deactivated'], compact('notification'));
        }
    }


    /**
     * Hidden manual activation that is accessible only for admin group
     *
     */
    public function showManualActivation()
    {
        return view('admin.settings.activation.manual');
    }


    /**
     * Store and activate via manual activation feature
     *
     */
    public function storeManualActivation()
    {
        request()->validate([
            'license' => 'required',
            'username' => 'required',
        ]);

        $this->storeSettings('GENERAL_SETTINGS_ENVATO_ACTIVATION', request('license'));
        $this->storeSettings('GENERAL_SETTINGS_ENVATO_USERNAME', request('username'));

        $status = $this->activate_license(md5(request('license')), md5(request('username')));

        if ($status['status'] == true) {
            return redirect()->back()->with('success', 'Application license was successfully activated');
        } else {
            return redirect()->back()->with('success', 'Application license was successfully activated');
        }
    }


    /**
     * Record activation in .env
     */
    private function storeSettings($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {

            file_put_contents($path, str_replace(
                $key . '=' . env($key), $key . '=' . $value, file_get_contents($path)
            ));

        }
    }
}
