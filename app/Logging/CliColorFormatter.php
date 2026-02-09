<?php
namespace App\Logging;

class CliColorFormatter extends \Monolog\Formatter\LineFormatter
{
    /**
     * @var \Console_Color2
     */
    private $console_color;

    protected bool $console;

    public function __construct($format = "%message% %context% %extra%\n", $dateFormat = null, $allowInlineLineBreaks = true, $ignoreEmptyContextAndExtra = true)
    {
        parent::__construct(
            $format,
            $dateFormat,
            $allowInlineLineBreaks,
            $ignoreEmptyContextAndExtra
        );

        $this->console_color = new \Console_Color2();
        $this->console = $this->console ?? \App::runningInConsole();
    }

    public function format(\Monolog\LogRecord $record): string
    {
        // if no line break is specified, just output the raw message (maybe colored)
        if (isset($record->context['nlb']) && $record->context['nlb'] === true) {
            if (isset($record->context['color']) && $record->context['color']) {
                return $this->console_color->convert($record->message, $this->console);
            }

            return $record->message;
        }

        // only format messages where color is enabled
        if (isset($record->context['color']) && $record->context['color']) {
            $context = $record->context;
            unset($context['color']);

            $record = new \Monolog\LogRecord(
                $record->datetime,
                $record->channel,
                $record->level,
                $this->console_color->convert($record->message, $this->console),
                $context,
                $record->extra,
                $record->formatted,
            );
        }

        return parent::format($record);
    }
}
