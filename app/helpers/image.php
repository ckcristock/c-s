<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

if (!function_exists('saveBase64')) {
    function saveBase64($image, $path, $public = true)
    {
        $image = base64_decode(
            preg_replace(
                "#^data:image/\w+;base64,#i",
                "",
                $image
            )
        );

        $file_path = $path . Str::random(30) . time() . ".png";
        if ($public) {
            Storage::disk('public')->put($file_path, $image, "public");
            return $file_path;
        }
        Storage::put($file_path, $image, "public");

        return $file_path;
    }
}
