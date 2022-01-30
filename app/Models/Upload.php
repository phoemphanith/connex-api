<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class Upload extends Model
{
    public static function saveFile($destination, $image, $temperature)
    {
        if ($image != null) {
            $logoPath = public_path('uploads') . $destination;
            $logoName = $image->getClientOriginalName();
            $unique_logoName = time() . "-" . $logoName;
            if ($image->move($logoPath, $unique_logoName)) {
                $logo_url = Request::root() . '/public/uploads' . $destination . '/' . $unique_logoName;
            }

            if ($temperature != '') {
                $filename = public_path() . '/uploads' . $destination . '/' . $temperature;
                File::delete($filename);
            }
        } else {
            $unique_logoName = $temperature;
        }

        return $unique_logoName;
    }

    public static function deleteFile($destination, $temperature)
    {
        if ($temperature) {
            $filename = public_path() . '/uploads' . $destination . '/' . $temperature;
            File::delete($filename);
        }
    }
}
