<?php
namespace ObzoraNMS\Exceptions;

use ObzoraNMS\Util\Graph;

class RrdGraphException extends RrdException
{
    /** @var string */
    protected $image_output;
    /** @var string|null */
    private $short_text;
    /** @var int|string|null */
    private $width;
    /** @var int|string|null */
    private $height;

    /**
     * @param  string  $error
     * @param  string|null  $short_text
     * @param  int|string|null  $width
     * @param  int|string|null  $height
     * @param  int  $exit_code
     * @param  string  $image_output
     */
    public function __construct($error, $short_text = null, $width = null, $height = null, $exit_code = 0, $image_output = '')
    {
        parent::__construct($error, $exit_code);
        $this->short_text = $short_text;
        $this->image_output = $image_output;
        $this->width = $width;
        $this->height = $height;
    }

    public function getImage(): string
    {
        return $this->image_output;
    }

    public function generateErrorImage(): string
    {
        return Graph::error(
            $this->getMessage(),
            $this->short_text,
            empty($this->width) ? 300 : (int) $this->width,
            empty($this->height) ? null : (int) $this->height,
        );
    }
}
