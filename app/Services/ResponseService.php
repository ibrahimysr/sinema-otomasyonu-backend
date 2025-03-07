<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ResponseService
{
    /**
     * Başarılı yanıt oluştur
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function success($data, string $message = 'İşlem başarılı.', int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Hata yanıtı oluştur
     *
     * @param string $message
     * @param mixed $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public function error(string $message = 'Bir hata oluştu.', $data = null, int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Bulunamadı yanıtı oluştur
     *
     * @param string $message
     * @return JsonResponse
     */
    public function notFound(string $message = 'Kayıt bulunamadı.'): JsonResponse
    {
        return $this->error($message, null, 404);
    }
} 