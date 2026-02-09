<?php
namespace ObzoraNMS\Tests\Unit\Data;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Data\Store\Kafka;
use ObzoraNMS\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('external-dependencies')]
class KafkaDBStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ObzoraConfig::set('kafka.enable', true);
        ObzoraConfig::set('kafka.broker.list', 'localhost:9092');
        ObzoraConfig::set('kafka.topic', 'obzora');
        ObzoraConfig::set('kafka.idempotence', false);
        ObzoraConfig::set('kafka.buffer.max.message', 10);
        ObzoraConfig::set('kafka.batch.max.message', 25);
        ObzoraConfig::set('kafka.linger.ms', 5000);
        ObzoraConfig::set('kafka.request.required.acks', 0);
    }

    public function testDataPushToKafka()
    {
        $producer = \Mockery::mock(Kafka::getClient());
        $producer->shouldReceive('newTopic')->once();

        /** @var \RdKafka\Producer $producer */
        $producer = $producer;
        $kafka = new Kafka($producer);

        $device = ['device_id' => 1, 'hostname' => 'testhost'];
        $measurement = 'excluded_measurement';
        $tags = ['ifName' => 'testifname', 'type' => 'testtype'];
        $fields = ['ifIn' => 234234, 'ifOut' => 53453];

        $metadata = [
            'device' => $device,
        ];
        $kafka->write($measurement, $fields, $tags, $metadata);
    }

    protected function tearDown(): void
    {
        ObzoraConfig::set('kafka.enable', false);
        parent::tearDown();
    }
}
