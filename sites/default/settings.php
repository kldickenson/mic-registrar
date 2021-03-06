<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all envrionments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to insure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

/**
 * If there is a stage settings file, then include it
 */
$stage_settings = __DIR__ . '/settings.stage.php';
if (file_exists($stage_settings)) {
  include $stage_settings;
}

/**
 * If there is a production settings file, then include it
 */
$production_settings = __DIR__ . '/settings.prod.php';
if (file_exists($production_settings)) {
  include $production_settings;
}

$settings['install_profile'] = 'standard';
