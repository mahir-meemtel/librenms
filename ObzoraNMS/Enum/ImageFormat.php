<?php
namespace ObzoraNMS\Enum;

use App\Facades\ObzoraConfig;

enum ImageFormat: string
{
    case png = 'png';
    case svg = 'svg';

    public static function forGraph(?string $type = null): ImageFormat
    {
        return ImageFormat::tryFrom($type ?? ObzoraConfig::get('webui.graph_type')) ?? ImageFormat::png;
    }

    public function contentType(): string
    {
        return $this->value == 'svg' ? 'image/svg+xml' : 'image/png';
    }

    public function getImageEnd(): string
    {
        $image_suffixes = [
            'png' => hex2bin('0000000049454e44ae426082'),
            'svg' => '</svg>',
        ];

        return $image_suffixes[$this->value] ?? '';
    }
}
