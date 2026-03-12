<?php

if (!function_exists('uploadFileToPublic')) {
    function uploadFileToPublic($file, $folder = 'uploads')
    {
        if (!$file) {
            return null;
        }

        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . uniqid() . '.' . $extension;

        $destinationPath = public_path($folder);

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $fileName);

        return $fileName;
    }
}

if (!function_exists('deleteFileFromPublic')) {
    function deleteFileFromPublic($fileName, $folder = 'uploads')
    {
        if (!$fileName) {
            return;
        }

        $filePath = public_path($folder . '/' . $fileName);

        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }
}
