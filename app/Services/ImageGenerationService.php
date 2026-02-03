<?php

namespace App\Services;

use App\Clients\BothubClient;
use App\Repositories\Contracts\MarsImageRepositoryInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

readonly class ImageGenerationService
{
    public function __construct(
        private BothubClient $bothubClient,
        private MarsImageRepositoryInterface $marsImageRepository,
    ) {}

    /**
     * @throws ConnectionException
     */
    public function generateImage(array $data): string
    {
        $prompt = $data[1];

        $imageData = $this->bothubClient->generateImage($prompt)['base64_image'];

        return $imageData;
    }

    /**
     * Generate Mars image with weather context
     *
     * @throws ConnectionException
     */
    public function generateMarsImage(int $marsMonth, array $weatherData): array
    {
        $prompt = $this->buildPrompt($marsMonth, $weatherData);
        $imageData = $this->bothubClient->generateImage($prompt);

        // Сохранить изображение на диск
        $imagePath = $this->saveImageToDisk(
            $imageData['base64_image'],
            $marsMonth
        );

        // Сохранить в БД
        $savedImage = $this->marsImageRepository->create([
            'mars_month' => $marsMonth,
            'image_path' => $imagePath,
            'interpretation' => $imageData['content'],
            'weather_context' => $weatherData,
        ]);

        return [
            'image' => Storage::url($imagePath),
            'interpretation' => $imageData['content'],
            'month' => $marsMonth,
            'weather_context' => $weatherData,
            'saved_image_id' => $savedImage->id,
        ];
    }

    /**
     * Save base64 image to disk
     */
    private function saveImageToDisk(string $base64Image, int $marsMonth): string
    {
        // Извлечь base64 данные (убрать data:image/png;base64, если есть)
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $decoded = base64_decode($imageData);

        // Создать путь
        $directory = "mars-images/month-{$marsMonth}";
        $filename = now()->format('Y-m-d_H-i-s').'_'.Str::random(8).'.png';
        $path = "{$directory}/{$filename}";

        // Сохранить файл
        Storage::disk('public')->put($path, $decoded);

        return $path;
    }

    /**
     * Build prompt for Mars image generation
     */
    private function buildPrompt(int $marsMonth, array $weatherData): string
    {
        $season = $weatherData['season'];
        $seasonDescription = $weatherData['season_description'];
        $lsRange = $weatherData['ls_range'];
        $avgTemp = $weatherData['statistics']['average_temp'];
        $minTemp = $weatherData['statistics']['absolute_min'];
        $maxTemp = $weatherData['statistics']['absolute_max'];
        $outfit = $this->getDoomguyOutfit($season);

        $prompt = <<<'PROMPT'
Создай УЛЬТРАРЕАЛИСТИЧНОЕ фотографическое изображение поверхности Марса с максимальной детализацией.

ТЕХНИЧЕСКИЕ ТРЕБОВАНИЯ:
- Стиль: Ultra-realistic, photorealistic, 8K resolution, cinematic photography
- Качество: Hyper-detailed, professional NASA-grade imagery, RAW photo quality
- Освещение: Natural volumetric lighting, realistic atmospheric scattering, physically accurate shadows
- Рендеринг: Ray-traced lighting, HDR, depth of field, film grain for realism

МАРСИАНСКАЯ СРЕДА:
- Марсианский месяц: %d (%s)
- Сезон: %s
- Время суток: Золотой час - сразу после восхода Солнца на Марсе
  * Рассветное освещение с теплыми красно-оранжевыми тонами
  * Длинные тени от неровностей рельефа
  * Мягкий боковой свет подчеркивает текстуру камней и пыли
- Температура: средняя %.1f°C (диапазон от %d°C до %d°C)
- Атмосфера: Тонкая пыльная дымка соответствующая температурному режиму и сезону

ДЕТАЛИ ЛАНДШАФТА:
- Реалистичная марсианская поверхность с красноватой охристой почвой
- Разнообразные камни и валуны с выветренной текстурой
- Мелкие кратеры и неровности рельефа
- Тонкий слой марсианской пыли с реалистичными следами ветра
- Видимые слои осадочных пород на склонах
- Далекие марсианские горы и холмы на горизонте

ПЕРСОНАЖ:
Doomguy (легендарный герой из игры Doom) в центре композиции:
- Экипировка: %s
- Поза: Стоит уверенно, слегка развернут к камере, лицом к восходящему солнцу
- Детализация: Реалистичные текстуры ткани и брони, потертости, пыль на костюме
- Освещение: Контровой свет от солнца создает эффектный силуэт и rim lighting
- Интеграция: Органично вписан в марсианский пейзаж, отбрасывает реалистичную тень

КОМПОЗИЦИЯ:
- Ракурс: Wide-angle cinematic shot, слегка снизу для героического эффекта
- Правило третей: Doomguy смещен от центра для динамики
- Передний план: Марсианские камни и почва с максимальной детализацией
- Средний план: Doomguy на фоне ландшафта
- Задний план: Марсианский горизонт с восходящим солнцем
- Глубина: Выраженный depth of field с фокусом на персонаже

АТМОСФЕРНЫЕ ЭФФЕКТЫ:
- Марсианская пыль в воздухе создает атмосферную дымку
- God rays (лучи света) пробивающиеся сквозь пыль
- Particle effects: пылевые частицы подсвеченные солнцем
- Реалистичное color grading с красно-оранжевой цветовой палитрой

ФИНАЛЬНАЯ ОБРАБОТКА:
- Цветокоррекция в стиле научной фотографии NASA
- Легкая кинематографическая обработка (как в фильмах про космос)
- Сохранение естественности - никакой мультяшности или фантастики
- Фотореалистичность на уровне кадра из документального фильма

После генерации изображения предоставь ДЕТАЛЬНЫЙ АНАЛИЗ в формате Markdown:
1. Как температурный режим (%d°C - %d°C) отражен в атмосфере, освещении и цветовой гамме
2. Какие визуальные элементы соответствуют сезону "%s"
3. Почему выбрана именно такая экипировка для Doomguy в этих условиях
4. Технические детали фотографии (композиция, освещение, атмосфера)
5. Какие марсианские погодные явления видны на изображении
PROMPT;

        return sprintf(
            $prompt,
            $marsMonth,
            $lsRange,
            $seasonDescription,
            $avgTemp,
            $minTemp,
            $maxTemp,
            $outfit,
            $minTemp,
            $maxTemp,
            $seasonDescription
        );
    }

    /**
     * Get Doomguy outfit based on season
     */
    private function getDoomguyOutfit(string $season): string
    {
        return match ($season) {
            'Весна' => 'легкую тактическую куртку и ветровку поверх классического зеленого костюма, с открытым шлемом',
            'Лето' => 'облегченный вариант классической брони с открытыми руками, легкой защитой корпуса',
            'Осень' => 'тактическую куртку средней плотности с защитными накладками и теплыми перчатками',
            'Зима' => 'тяжелую утепленную версию своей брони с зимним шлемом, шарфом и толстыми перчатками',
            default => 'свой классический костюм',
        };
    }
}
