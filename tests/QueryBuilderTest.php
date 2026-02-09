<?php
namespace ObzoraNMS\Tests;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Alerting\QueryBuilderFluentParser;
use PHPUnit\Framework\Attributes\DataProvider;

class QueryBuilderTest extends TestCase
{
    private static string $data_file = 'tests/data/misc/querybuilder.json';

    public function testHasQueryData(): void
    {
        $this->assertNotEmpty(
            $this->loadQueryData(),
            'Could not load query builder test data from ' . self::$data_file
        );
    }

    /**
     * @param  string  $legacy
     * @param  array  $builder
     * @param  string  $display
     * @param  string  $sql
     */
    #[DataProvider('loadQueryData')]
    public function testQueryConversion($legacy, $builder, $display, $sql, $query): void
    {
        $qb = QueryBuilderFluentParser::fromJson($builder);
        $this->assertEquals($display, $qb->toSql(false));
        $this->assertEquals($sql, $qb->toSql());

        $qbq = $qb->toQuery();
        $this->assertEquals($query[0], $qbq->toSql(), 'Fluent SQL does not match');
        $this->assertEquals($query[1], $qbq->getBindings(), 'Fluent bindings do not match');
    }

    public static function loadQueryData(): array
    {
        $base = ObzoraConfig::get('install_dir');
        $data = file_get_contents("$base/" . self::$data_file);

        return json_decode($data, true);
    }
}
