<?php
namespace ObzoraNMS\Tests\Unit;

use ObzoraNMS\Tests\TestCase;
use ObzoraNMS\Validations\Rrd\CheckRrdVersion;
use Storage;

class ValidationFixTest extends TestCase
{
    public function testRrdVersionFix(): void
    {
        Storage::fake('base');
        Storage::disk('base')->put('config.php', <<<'EOF'
<?php
$config['test'] = 'rrdtool_version';
$config['rrdtool_version'] = '1.0';
$config["rrdtool_version"] = '1.1';
# comment

EOF
        );

        (new CheckRrdVersion())->fix();

        $actual = Storage::disk('base')->get('config.php');
        $this->assertSame(<<<'EOF'
<?php
$config['test'] = 'rrdtool_version';
# comment

EOF, $actual);
        Storage::disk('base')->delete('config.php');
    }
}
