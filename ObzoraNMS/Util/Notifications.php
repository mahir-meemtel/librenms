<?php
namespace ObzoraNMS\Util;

use App\Facades\ObzoraConfig;
use App\Models\Notification;
use Illuminate\Support\Arr;

class Notifications
{
    /**
     * Post notifications to users
     */
    public static function post(): void
    {
        $notifications = self::fetch();
        echo '[ ' . date('r') . ' ] Updating DB ';
        foreach ($notifications as $notif) {
            if (! Notification::where('checksum', $notif['checksum'])->exists()) {
                Notification::create($notif);
                echo '.';
            }
        }
        echo ' Done' . PHP_EOL;
    }

    /**
     * Create a new custom notification. Duplicate title+message notifications will not be created.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  string  $source  A string describing what created this notification
     * @param  int  $severity  0=ok, 1=warning, 2=critical
     * @param  string|null  $date
     * @return bool
     */
    public static function create(string $title, string $message, string $source, int $severity = 0, ?string $date = null): bool
    {
        $checksum = hash('sha512', $title . $message);

        return Notification::firstOrCreate([
            'checksum' => $checksum,
        ], [
            'title' => $title,
            'body' => $message,
            'severity' => $severity,
            'source' => $source,
            'checksum' => $checksum,
            'datetime' => date('Y-m-d', is_null($date) ? time() : strtotime($date)),
        ])->wasRecentlyCreated;
    }

    /**
     * Removes all notifications with the given title.
     * This should be used with care.
     */
    public static function remove(string $title): void
    {
        Notification::where('title', $title)->get()->each->delete();
    }

    /**
     * Pull notifications from remotes
     *
     * @return array Notifications
     */
    protected static function fetch(): array
    {
        $notifications = [];
        foreach (ObzoraConfig::get('notifications') as $name => $url) {
            echo '[ ' . date('r') . " ] $name $url ";

            $feed = json_decode(json_encode(simplexml_load_string(file_get_contents($url))), true);
            $feed = isset($feed['channel']) ? self::parseRss($feed) : self::parseAtom($feed);

            array_walk($feed, function (&$items, $key, $url) {
                $items['source'] = $url;
            }, $url);
            $notifications = array_merge($notifications, $feed);

            echo '(' . count($notifications) . ')' . PHP_EOL;
        }

        return Arr::sort($notifications, 'datetime');
    }

    protected static function parseRss(array $feed): array
    {
        $obj = [];
        if (! array_key_exists('0', $feed['channel']['item'])) {
            $feed['channel']['item'] = [$feed['channel']['item']];
        }
        foreach ($feed['channel']['item'] as $item) {
            $obj[] = [
                'title' => $item['title'],
                'body' => $item['description'],
                'checksum' => hash('sha512', $item['title'] . $item['description']),
                'datetime' => date('Y-m-d', strtotime($item['pubDate']) ?: time()),
            ];
        }

        return $obj;
    }

    /**
     * Parse Atom
     *
     * @param  array  $feed  Atom Object
     * @return array Parsed Object
     */
    protected static function parseAtom(array $feed): array
    {
        $obj = [];
        if (! array_key_exists('0', $feed['entry'])) {
            $feed['entry'] = [$feed['entry']];
        }
        foreach ($feed['entry'] as $item) {
            $obj[] = [
                'title' => $item['title'],
                'body' => $item['content'],
                'checksum' => hash('sha512', $item['title'] . $item['content']),
                'datetime' => date('Y-m-d', strtotime($item['updated']) ?: time()),
            ];
        }

        return $obj;
    }
}
