<?php
namespace App\View;

use ObzoraNMS\Util\StringHelpers;

class SimpleTemplate
{
    private string $regex = '/{{ \$?([a-zA-Z0-9\-_.:]+)(\|[^}]+)? }}/';
    private bool $keepEmpty = false;
    /** @var ?callable */
    private $callback = null;

    public function __construct(
        private readonly string $template,
        private array $variables = []
    ) {
    }

    /**
     * By default, unmatched templates will be removed from the output, set this to keep them
     */
    public function keepEmptyTemplates(): SimpleTemplate
    {
        $this->keepEmpty = true;

        return $this;
    }

    /**
     * Add a variable to the set of possible substitutions
     */
    public function setVariable(string $key, string $value): SimpleTemplate
    {
        $this->variables[$key] = $value;

        return $this;
    }

    /**
     * Instead of using the given variables to replace {{ var }}
     * send the matched variable to this callback, which will return a string to replace it
     */
    public function replaceWith(callable $callback): SimpleTemplate
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Create and parse a simple template
     */
    public static function parse(string $template, array $variables): string
    {
        return (string) new static($template, $variables);
    }

    /**
     * Parse and apply filters to a variable value
     */
    private function applyFilters(string $value, string $filterChain): string
    {
        $filterPattern = '/([a-zA-Z_][a-zA-Z0-9_]*)(?:\(([^)]*)\))?/';

        if (preg_match_all($filterPattern, trim($filterChain, '|'), $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $filterName = $match[1];
                $argsString = $match[2] ?? '';
                $args = ! empty($argsString) ? $this->parseArguments($argsString) : [];
                $value = $this->executeFilter($value, $filterName, $args);
            }
        }

        return $value;
    }

    /**
     * Parse function arguments from string
     */
    private function parseArguments(string $argsString): array
    {
        $args = [];
        $current = '';
        $inQuotes = false;
        $quoteChar = null;
        $depth = 0;

        for ($i = 0, $len = strlen($argsString); $i < $len; $i++) {
            $char = $argsString[$i];

            if ($inQuotes) {
                if ($char === $quoteChar) {
                    $inQuotes = false;
                    $quoteChar = null;
                }
                $current .= $char;
                continue;
            }

            if ($char === '"' || $char === "'") {
                $inQuotes = true;
                $quoteChar = $char;
                $current .= $char;
                continue;
            }

            if ($char === '(') {
                $depth++;
            } elseif ($char === ')') {
                $depth--;
            }

            if ($char === ',' && $depth === 0) {
                $args[] = $this->parseArgumentValue($current);
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if (trim($current) !== '') {
            $args[] = $this->parseArgumentValue($current);
        }

        return $args;
    }

    /**
     * Parse individual argument value (string, number, boolean)
     */
    private function parseArgumentValue(string $value): mixed
    {
        $value = trim($value);

        // Handle single-quoted strings by removing quotes directly since json decode doesn't work there
        if (strlen($value) >= 2 && $value[0] === "'" && $value[strlen($value) - 1] === "'") {
            return substr($value, 1, -1);
        }

        $decoded = json_decode($value);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Handle unquoted strings (not valid JSON but common in templates)
        return $value;
    }

    /**
     * Execute a specific filter on a value - Twig-compatible filters only
     */
    private function executeFilter(string $value, string $filterName, array $args): string
    {
        return match ($filterName) {
            // Basic string filters
            'trim' => trim($value, ...($args ?: [" \t\n\r\0\x0B"])),
            'upper' => strtoupper($value),
            'lower' => strtolower($value),
            'title' => ucwords(strtolower($value)),
            'capitalize' => ucfirst(strtolower($value)),
            'length' => (string) strlen($value),

            // String manipulation
            'replace' => count($args) >= 2 ? str_replace($args[0], $args[1], $value) : $value,
            'slice' => $this->sliceFilter($value, $args),

            // Encoding/escaping
            'escape' => $this->escapeFilter($value, $args),
            'url_encode' => urlencode($value),

            // HTML
            'striptags' => strip_tags($value, ...($args ?: [])),
            'nl2br' => nl2br($value),

            // Formatting
            'number_format' => $this->numberFormatFilter($value, $args),
            'date' => $this->dateFilter($value, $args),
            'json_encode' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: $value,

            // Utility
            'default' => ($value === '' && isset($args[0])) ? (string) $args[0] : $value,
            'abs' => (string) abs((float) $value),
            'round' => (string) round((float) $value, $args[0] ?? 0),

            default => $value // raw
        };
    }

    private function sliceFilter(string $value, array $args): string
    {
        $start = $args[0] ?? 0;
        $length = $args[1] ?? null;

        if ($length === null) {
            return substr($value, $start) ?: $value;
        }

        return substr($value, $start, $length) ?: $value;
    }

    private function escapeFilter(string $value, array $args): string
    {
        $strategy = $args[0] ?? 'html';

        return match ($strategy) {
            'js' => json_encode($value, JSON_UNESCAPED_UNICODE),
            'css' => $this->escapeCss($value),
            'url' => urlencode($value),
            default => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), // html
        };
    }

    private function escapeCss(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9\-_]/', '\\\\$0', $value);
    }

    private function numberFormatFilter(string $value, array $args): string
    {
        $decimals = $args[0] ?? 0;
        $decimalPoint = $args[1] ?? '.';
        $thousandsSep = $args[2] ?? ',';

        return number_format((float) $value, $decimals, $decimalPoint, $thousandsSep);
    }

    private function dateFilter(string $value, array $args): string
    {
        $format = $args[0] ?? 'F j, Y H:i'; // Twig's default format

        if (is_numeric($value)) {
            $timestamp = (int) $value;
        } else {
            $timestamp = strtotime($value);
            if ($timestamp === false) {
                $timestamp = time();
            }
        }

        return date($format, $timestamp);
    }

    public function __toString(): string
    {
        return preg_replace_callback($this->regex, $this->callback ?? function ($matches) {
            $variableName = $matches[1];
            $value = $this->variables[$variableName] ?? ($this->keepEmpty ? $matches[0] : '');

            if (! StringHelpers::isStringable($value)) {
                return '';
            }

            $stringValue = (string) $value;

            // Apply filters if present
            if (! empty($matches[2])) {
                $stringValue = $this->applyFilters($stringValue, $matches[2]);
            }

            return $stringValue;
        }, $this->template);
    }
}
