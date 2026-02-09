<?php
use App\Facades\ObzoraConfig;

header('Content-type: application/json');

if (! Auth::user()->hasGlobalAdmin()) {
    exit(json_encode([
        'status' => 'error',
        'message' => 'ERROR: You need to be admin',
    ]));
}

$template_id = $vars['template_id'];

if (is_numeric($template_id)) {
    $rules = get_rules_from_json();
    $rule = $rules[$template_id];
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
        'name' => $rule['name'],
        'notes' => $rule['notes'] ?? null,
        'builder' => $rule['builder'] ?? [],
        'extra' => array_replace($default_extra, (array) ($rule['extra'] ?? [])),
        'severity' => $rule['severity'] ?? ObzoraConfig::get('alert_rule.severity'),
        'invert_map' => ObzoraConfig::get('alert_rule.invert_map'),
    ];
} else {
    $output = [
        'status' => 'error',
        'message' => 'Invalid template',
    ];
}

exit(json_encode($output));
