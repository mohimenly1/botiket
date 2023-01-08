<?php
namespace App\Http\Traits;

trait ResponseTraits
{
       
    public function prepare_response( $errors = null, $message = '', $data = null,  $server_status = null)
    {


       $array = array(
            'errors'  => $errors,
            'message' => $message,
            'data'    => $data
        );
        return response()->json($array, $server_status);
    }

    public function getFullObjects($objects, $unsetObject = null)
    {
        $data = [];
        if (!is_null($objects)) {
            foreach ($objects as $object) {
                $result = $object->getFullObj();
                if (!is_null($unsetObject)) {
                    unset($result[$unsetObject]);
                }
                $data[] = $result;
            }
        }
        return $data;
    }
}
