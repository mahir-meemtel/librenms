#!/usr/bin/env php
<?php
$init_modules = [];
require __DIR__ . '/includes/init.php';
error_reporting(E_ERROR);

$irc = new ObzoraNMS\IRCBot();
