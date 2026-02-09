<?php
namespace ObzoraNMS\Tests\Unit\Alert\Transports;

use App\Models\AlertTransport;
use App\Models\Device;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use ObzoraNMS\Alert\AlertData;
use ObzoraNMS\Alert\Transport;
use ObzoraNMS\Tests\TestCase;

class SlackTest extends TestCase
{
    public function testSlackNoConfigDelivery(): void
    {
        Http::fake();

        $slack = new Transport\Slack(new AlertTransport);

        /** @var Device $mock_device */
        $mock_device = Device::factory()->make();
        $slack->deliverAlert(AlertData::testData($mock_device));

        Http::assertSent(function (Request $request) {
            return
                $request->url() == '' &&
                $request->method() == 'POST' &&
                $request->hasHeader('Content-Type', 'application/json') &&
                $request->data() == [
                    'attachments' => [
                        [
                            'fallback' => 'This is a test alert',
                            'color' => '#ff0000',
                            'title' => 'Testing transport from ObzoraNMS',
                            'text' => 'This is a test alert',
                            'mrkdwn_in' => [
                                'text',
                                'fallback',
                            ],
                            'author_name' => null,
                        ],
                    ],
                    'channel' => null,
                    'icon_emoji' => null,
                ];
        });
    }

    public function testSlackLegacyDelivery(): void
    {
        Http::fake();

        $slack = new Transport\Slack(new AlertTransport([
            'transport_config' => [
                'slack-url' => 'https://slack.com/some/webhook',
                'slack-options' => "icon_emoji=smile\nauthor=Me\nchannel=Alerts",
            ],
        ]));

        /** @var Device $mock_device */
        $mock_device = Device::factory()->make();
        $slack->deliverAlert(AlertData::testData($mock_device));

        Http::assertSent(function (Request $request) {
            return
                $request->url() == 'https://slack.com/some/webhook' &&
                $request->method() == 'POST' &&
                $request->hasHeader('Content-Type', 'application/json') &&
                $request->data() == [
                    'attachments' => [
                        [
                            'fallback' => 'This is a test alert',
                            'color' => '#ff0000',
                            'title' => 'Testing transport from ObzoraNMS',
                            'text' => 'This is a test alert',
                            'mrkdwn_in' => [
                                'text',
                                'fallback',
                            ],
                            'author_name' => 'Me',
                        ],
                    ],
                    'channel' => 'Alerts',
                    'icon_emoji' => ':smile:',
                ];
        });
    }

    public function testSlackDelivery(): void
    {
        Http::fake();

        $slack = new Transport\Slack(new AlertTransport([
            'transport_config' => [
                'slack-url' => 'https://slack.com/some/webhook',
                'slack-options' => "icon_emoji=smile\nauthor=Me\nchannel=Alerts",
                'slack-icon_emoji' => ':slight_smile:',
                'slack-author' => 'Other',
                'slack-channel' => 'Critical',
            ],
        ]));

        /** @var Device $mock_device */
        $mock_device = Device::factory()->make();
        $slack->deliverAlert(AlertData::testData($mock_device));

        Http::assertSent(function (Request $request) {
            return
                $request->url() == 'https://slack.com/some/webhook' &&
                $request->method() == 'POST' &&
                $request->hasHeader('Content-Type', 'application/json') &&
                $request->data() == [
                    'attachments' => [
                        [
                            'fallback' => 'This is a test alert',
                            'color' => '#ff0000',
                            'title' => 'Testing transport from ObzoraNMS',
                            'text' => 'This is a test alert',
                            'mrkdwn_in' => [
                                'text',
                                'fallback',
                            ],
                            'author_name' => 'Other',
                        ],
                    ],
                    'channel' => 'Critical',
                    'icon_emoji' => ':slight_smile:',
                ];
        });
    }
}
