<?php
use App\Facades\ObzoraConfig;
use ObzoraNMS\Alerting\QueryBuilderParser;

header('Content-type: application/json');

if (! Auth::user()->hasGlobalAdmin()) {
    exit(json_encode([
        'status' => 'error',
        'message' => 'ERROR: You need to be admin',
    ]));
}

$rule_id = $vars['rule_id'];

if (is_numeric($rule_id)) {
    $rule = dbFetchRow('SELECT * FROM alert_rules where id=?', [$rule_id]);

    $default_extra = [
        'mute' => ObzoraConfig::get('alert_rule.mute_alerts'),
        'count' => ObzoraConfig::get('alert_rule.max_alerts'),
        'delay' => 60 * ObzoraConfig::get('alert_rule.delay'),
        'invert' => ObzoraConfig::get('alert_rule.invert_rule_match'),
        'interval' => 60 * ObzoraConfig::get('alert_rule.interval'),
        'recovery' => ObzoraConfig::get('alert_rule.recovery_alerts'),
        'acknowledgement' => ObzoraConfig::get('alert_rule.acknowledgement_alerts'),
    ];
    $output = [
        'status' => 'ok',
        'name' => $rule['name'] . ' - Copy',
        'builder' => QueryBuilderParser::fromJson($rule['builder']),
        'extra' => array_replace($default_extra, (array) json_decode($rule['extra'])),
        'severity' => $rule['severity'] ?: ObzoraConfig::get('alert_rule.severity'),
        'invert_map' => $rule['invert_map'],
    ];
} else {
    $output = [
        'status' => 'error',
        'message' => 'Invalid template',
    ];
}

exit(json_encode($output));
