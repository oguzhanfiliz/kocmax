<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Slider", description="Ana sayfa slider API uç noktaları")
 */
class SliderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/sliders",
     *     summary="Aktif slider'ları listele",
     *     description="Ana sayfa için aktif slider'ları sıralı şekilde getirir",
     *     operationId="getSliders", 
     *     tags={"Slider"},
     *     @OA\Response(
     *         response=200,
     *         description="Slider'lar başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Yeni Koleksiyon"),
     *                     @OA\Property(property="image_url", type="string", example="/storage/sliders/slider-1.jpg"),
     *                     @OA\Property(property="button_text", type="string", example="Şimdi Keşfet"),
     *                     @OA\Property(property="button_link", type="string", example="/products"),
     *                     @OA\Property(property="text_fields", type="object",
     *                         @OA\Property(property="text_1", type="string", example="İş Güvenliği"),
     *                         @OA\Property(property="text_2", type="string", example="En İyi Fiyatlar")
     *                     ),
     *                     @OA\Property(property="sort_order", type="integer", example=1)
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Slider'lar başarıyla getirildi")
     *         )
     *     ),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function index()
    {
        try {
            $sliders = Slider::active()
                ->ordered()
                ->get()
                ->map(function (Slider $slider) {
                    return [
                        'id' => $slider->id,
                        'title' => $slider->title,
                        'image_url' => $slider->image_url,
                        'button_text' => $slider->button_text,
                        'button_link' => $slider->button_link,
                        'text_fields' => $slider->text_fields ? $this->transformTextFields($slider->text_fields) : null,
                        'sort_order' => $slider->sort_order,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $sliders,
                'message' => 'Slider\'lar başarıyla getirildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Slider\'lar getirilirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/sliders/{id}",
     *     summary="Belirli bir slider'ı getir",
     *     description="ID'ye göre belirli bir slider'ın detaylarını getirir",
     *     operationId="getSlider",
     *     tags={"Slider"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Slider ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Slider başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Yeni Koleksiyon"),
     *                 @OA\Property(property="image_url", type="string", example="/storage/sliders/slider-1.jpg"),
     *                 @OA\Property(property="button_text", type="string", example="Şimdi Keşfet"),
     *                 @OA\Property(property="button_link", type="string", example="/products"),
     *                 @OA\Property(property="text_fields", type="object",
     *                     @OA\Property(property="text_1", type="string", example="İş Güvenliği"),
     *                     @OA\Property(property="text_2", type="string", example="En İyi Fiyatlar")
     *                 ),
     *                 @OA\Property(property="sort_order", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-19T15:03:32.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-19T15:03:32.000000Z")
     *             ),
     *             @OA\Property(property="message", type="string", example="Slider başarıyla getirildi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Slider bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Slider bulunamadı")
     *         )
     *     ),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function show(int $id)
    {
        try {
            $slider = Slider::active()->find($id);

            if (!$slider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Slider bulunamadı'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $slider->id,
                    'title' => $slider->title,
                    'image_url' => $slider->image_url,
                    'button_text' => $slider->button_text,
                    'button_link' => $slider->button_link,
                    'text_fields' => $slider->text_fields ? $this->transformTextFields($slider->text_fields) : null,
                    'sort_order' => $slider->sort_order,
                    'created_at' => $slider->created_at,
                    'updated_at' => $slider->updated_at,
                ],
                'message' => 'Slider başarıyla getirildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Slider getirilirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform text fields from repeater format to key-value pairs
     */
    private function transformTextFields(?array $textFields): ?array
    {
        if (!$textFields || !is_array($textFields)) {
            return null;
        }

        $transformed = [];
        foreach ($textFields as $field) {
            if (is_array($field) && isset($field['key']) && isset($field['value'])) {
                $transformed[$field['key']] = $field['value'];
            }
        }

        return $transformed ?: null;
    }
}
