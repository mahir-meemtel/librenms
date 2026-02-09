<?php
namespace ObzoraNMS\Enum;

enum IntegerType
{
    case int8;
    case int16;
    case int32;
    case int64;
    case uint8;
    case uint16;
    case uint32;
    case uint64;

    public function maxValue(): int
    {
        return match ($this) {
            self::int8 => 127,
            self::int16 => 32767,
            self::int32 => 2147483647,
            self::int64 => 4611686018427387903,
            self::uint8 => 255,
            self::uint16 => 65535,
            self::uint32 => 4294967295,
            self::uint64 => 9223372036854775807,
        };
    }

    public function isSigned(): bool
    {
        return match ($this) {
            self::int8,self::int16,self::int32,self::int64 => true,
            self::uint8,self::uint16,self::uint32,self::uint64 => false,
        };
    }
}
