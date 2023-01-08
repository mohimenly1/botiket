<?php

namespace App\Helpers;

use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Intervention\Image\ImageManagerStatic as Image;

class FileHelper
{
    public static function upload_file($folder_path, $image)
    {
        if ($image) {
            $file_name = time();
            $file_name .= rand();
            $file_name = sha1($file_name);
            $ext = $image->getClientOriginalExtension();
            $actual_image = Image::make($image->getRealPath());
            $full_path=storage_path() . '/app/public' . $folder_path;
            File::ensureDirectoryExists($full_path);
            $actual_image->save(storage_path() . '/app/public' . $folder_path.  $file_name . "." . $ext);
            
            $local_url = $file_name . "." . $ext;
            $s3_url = '/storage'. $folder_path . $local_url;
            return $s3_url;
        }
    }
  
    public static function delete_file($picture)
    {
        File::delete(storage_path() . '/app/public'. $picture);
        return true;
    }

    public static function move_file($old_path, $new_path)
    {
        $old_path = storage_path() . '/app/public' . $old_path;
        $new_path = storage_path() . '/app/public' . $new_path;

        if (!File::isDirectory($new_path)) {
            File::makeDirectory($new_path, 0777, true, true);
        }
        $move = File::move($old_path, $new_path . '/' . basename($old_path));
        //Remove Files Older Than a day
        collect(Storage::disk('public')->listContents('orders/temp', true))->each(function ($file) {
            if ($file['type'] == 'file' && $file['timestamp'] < now()->subDays(1)->getTimestamp()) {
                Storage::disk('public')->delete($file['path']);
            }
        });
        return $move;
    }
}
