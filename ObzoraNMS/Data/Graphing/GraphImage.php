<?php
namespace ObzoraNMS\Data\Graphing;

use ObzoraNMS\Enum\ImageFormat;

class GraphImage
{
    public function __construct(public readonly ImageFormat $format, public readonly string $title, public readonly string $data)
    {
    }

    public function base64(): string
    {
        return base64_encode($this->data);
    }

    public function inline(): string
    {
        return 'data:' . $this->contentType() . ';base64,' . $this->base64();
    }

    public function fileExtension(): string
    {
        return $this->format->name;
    }

    public function contentType(): string
    {
        return $this->format->contentType();
    }

    public function __toString()
    {
        return $this->data;
    }
}
