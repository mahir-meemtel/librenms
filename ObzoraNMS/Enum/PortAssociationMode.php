<?php
namespace ObzoraNMS\Enum;

class PortAssociationMode
{
    const ifIndex = 1;
    const ifName = 2;
    const ifDescr = 3;
    const ifAlias = 4;

    /**
     * Get mode names keyed by id
     *
     * @return string[]
     */
    public static function getModes(): array
    {
        return [
            self::ifIndex => 'ifIndex',
            self::ifName => 'ifName',
            self::ifDescr => 'ifDescr',
            self::ifAlias => 'ifAlias',
        ];
    }

    /**
     * Translate a named port association mode to an integer for storage
     *
     * @param  string  $name
     * @return int|null
     */
    public static function getId(string $name): ?int
    {
        $names = array_flip(self::getModes());

        return $names[$name] ?? null;
    }

    /**
     * Get name of given port association mode id
     *
     * @param  int  $id
     * @return string|null
     */
    public static function getName(int $id): ?string
    {
        $modes = self::getModes();

        return $modes[$id] ?? null;
    }
}
