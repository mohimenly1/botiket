<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\Hash;

trait MainTrait
{

    public function traitupdate($model, $id, $arr)
    {

        foreach ($arr as $index => $value) {

            $model::where('id', $id)->update([$index => $value]);
        }

    }
    public function ChoseProviderType($service_type)
    {
        $provider_type = [];
        $service_types = ['sale', 'buy', 'rent', 'investment', 'building', 'renovation', 'finishing'];
        if (in_array($service_type, $service_types)) {
            if ($service_type == 'sale' || $service_type == 'buy') {
                $provider_type = ['buildings_appraiser', 'buildings_expert'];
            } elseif ($service_type == 'rent' || $service_type == 'investment') {
                $provider_type = ['contracts_consultant', 'buildings_expert'];

            } elseif ($service_type == 'building' || $service_type == 'renovation' || $service_type == 'finishing') {
                $provider_type = ['consultant_engineer'];

            }

        }
        return $provider_type;
    }
    public function GetProviderTypeServices($provider_type)
    {
        $services = [];
        $provider_types = ['buildings_appraiser', 'contracts_consultant', 'buildings_expert', 'consultant_engineer'];
        if (in_array($provider_type, $provider_types)) {
            if ($provider_type == 'buildings_appraiser') {
                $services = ['sale', 'buy'];
            } elseif ($provider_type == 'contracts_consultant') {
                $services = ['rent', 'investment'];
            } elseif ($provider_type == 'buildings_expert') {
                $services = ['sale', 'buy', 'rent', 'investment'];
            } elseif ($provider_type == 'consultant_engineer') {
                $services = ['building', 'renovation', 'finishing'];
            }

        }
        return $services;
    }
    public function ChangePasswordTrait($model, $id, $old_password, $new_password)
    {
        $item = $this->model->where('id', $id)->first();
        if (Hash::check($old_password, $item->password)) {

            $model::where('id', $id)->update(['password' => bcrypt($new_password)]);

        } else {
            return 'error';
        }
    }

}
