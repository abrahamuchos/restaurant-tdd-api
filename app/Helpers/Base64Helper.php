<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Base64Helper
{
    /**
     * Get data image and decode base64
     *
     * @param string $data - base64
     *
     * @return array[string, string]
     * @throws \Exception
     */
    public static function getDataImage(string $data): array
    {
        try {
            $image = explode(',', $data);
            $image = end($image);
            $dataDecoded = base64_decode($image);

            //Validate image
            if (imagecreatefromstring($dataDecoded)) {
                $size = getimagesizefromstring($dataDecoded);
                $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if(!in_array($size['mime'], $allowed)){
                    $allowedString = implode(', ', $allowed);
                    throw new \Exception("Invalid extension image allowed: $allowedString");
                }
                $parts = explode("/", $size['mime']);
                $extension = end($parts);

                return [$dataDecoded, $extension];
            } else {
                throw new \Exception('Invalid image!!');
            }

        } catch (\Exception $e) {
            Log::error(
                'Message: {message}  | File: {file} | Line: {line} | Base64 Helper Error',
                [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
            throw new \Exception('Error Image to base 64, please check logs to more details');
        }
    }
}
