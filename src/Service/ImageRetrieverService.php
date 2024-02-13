<?php

namespace PrestaShop\Module\ImageRetriever\Service;

class ImageRetrieverService
{
    /**
     * @param string $imageFolderPath   The absolute path to the image are stored
     * @param int|string $idImage       Identifier of the image
     *
     * @return array
     *
     * @throws PrestaShopDatabaseException
     */
    public function getImage(string $imageFolderPath, int|string $idImage): array
    {
        $urls = [];

        $path_parts = pathinfo($idImage);

        $extension = $path_parts['extension'];
        if ($extension) {
            $extension = '.' . $extension;
        }

        $idImage = $path_parts['filename'];

        $originalImagePath = implode(
            DIRECTORY_SEPARATOR, [
                $imageFolderPath,
                $idImage . $extension,
            ]
        );

        $imageFolderUrl = str_replace(_PS_ROOT_DIR, '', $imageFolderPath);

        $configuredImageFormats = ServiceLocator::get(ImageFormatConfiguration::class)->getGenerationFormats();
        $rewrite = $idImage;

        // Check and generate each thumbnail size
        $imageTypes = ImageType::getImagesTypes(null, true);
        foreach ($imageTypes as $imageType) {
            $sources = [];

            $originalFileName = $idImage . $extension;

            // Get path of original uploaded image we will use to get thumbnails
            $originalImagePath = implode(DIRECTORY_SEPARATOR, [
                $imageFolderPath,
                $originalFileName,
            ]);

            foreach ($configuredImageFormats as $imageFormat) {
                // Generate the thumbnail
                $this->checkOrGenerateImageType($originalImagePath, $imageFolderPath, $idImage, $imageType, $imageFormat);

                // Get the URL of the thumb and add it to sources
                $path = __PS_BASE_URI__ . $imageFolderUrl . $rewrite . '-' . $imageType['name'] . '.' . $imageFormat;
                $sources[$imageFormat] = Context::getContext()->link->protocol_content . Tools::getMediaServer($path) . $path;
            }

            // Let's resolve the base image URL we will use
            if (isset($sources['jpg'])) {
                $baseUrl = $sources['jpg'];
            } elseif (isset($sources['png'])) {
                $baseUrl = $sources['png'];
            } else {
                $baseUrl = reset($sources);
            }

            // And add this size to our list
            $urls[$imageType['name']] = [
                'url' => $baseUrl,
                'width' => (int) $imageType['width'],
                'height' => (int) $imageType['height'],
                'sources' => $sources,
            ];
        }

        // Sort thumbnails by size
        uasort($urls, function (array $a, array $b) {
            return $a['width'] * $a['height'] > $b['width'] * $b['height'] ? 1 : -1;
        });

        // Resolve some basic sizes - the smallest, middle and largest
        $keys = array_keys($urls);
        $small = $urls[$keys[0]];
        $large = end($urls);
        $medium = $urls[$keys[ceil((count($keys) - 1) / 2)]];

        return [
            'bySize' => $urls,
            'small' => $small,
            'medium' => $medium,
            'large' => $large,
            'legend' => '',
            'idImage' => $idImage,
        ];
    }

    /**
     * @param string     $originalImagePath
     * @param string     $imageFolderPath
     * @param int|string $idImage
     * @param array      $imageTypeData
     * @param string     $imageFormat
     *
     * @return void
     */
    private function checkOrGenerateImageType(string $originalImagePath, string $imageFolderPath, int|string $idImage, array $imageTypeData, string $imageFormat): void
    {
        $fileName = sprintf('%s-%s.%s', $idImage, $imageTypeData['name'], $imageFormat);
        $resizedImagePath = implode(
            DIRECTORY_SEPARATOR, [
                $imageFolderPath,
                $fileName,
            ]
        );

        // Check if the thumbnail exists and generate it if needed
        if (!file_exists($resizedImagePath)) {
            ImageManager::resize(
                $originalImagePath,
                $resizedImagePath,
                (int) $imageTypeData['width'],
                (int) $imageTypeData['height'],
                $imageFormat
            );
        }
    }
}