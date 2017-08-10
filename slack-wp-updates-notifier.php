<?php
/**
 * Plugin Name: Slack WP Updates Notifier
 * Plugin URI: https://github.com/garymarkfuller
 * Description: Extends the Slack plugin to send notifications when an update is available.
 * Author: garymarkfuller
 * Version: 1.1.3
 * Author URI: https://github.com/garymarkfuller
 * Text Domain: slack-wp-updates-notifier
 * License: GPL v2 or later
 * Requires at least: 4.8.1
 * Tested up to: 4.8.1
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @package slack-wp-updates-notifier
 */

/**
 * Adds new events that send notifications to a Slack channel
 * when there is a WordPress update.
 *
 * @param  array $events List of events.
 * @return array $events List of events.
 *
 * @filter slack_get_events
 */

add_filter( 'slack_get_events', function( $events ) {
    $events['plugin_update'] = array(
        'action'      => 'wp_update_plugins',
        'description' => __( 'Whether there are plugin updates available', 'slack' ),
        'message'     => function() {
            $update_plugins = get_site_transient( 'update_plugins' );
            if (!empty( $update_plugins->response )) {
                $plugins_needing_updates = $update_plugins->response;
                $plugin_names_array = array_keys($plugins_needing_updates);
                $plugin_names = implode(", ", $plugin_names_array);
                return sprintf('Please update %s.', $plugin_names);
            } else {
                return;
            }
        }
    );
    $events['core_update'] = array(
        'action'      => 'wp_version_check',
        'description' => __( 'Whether there are core updates available', 'slack' ),
        'message'     => function() {
            $update_core = get_site_transient( 'update_core' );
            if ('upgrade' == $update_core->updates[0]->response) {
                $new_core_version = $update_core->updates[0]->current;
                return sprintf('Please update WordPress to version %s.', $new_core_version);
            } else {
                return;
            }
        }
    );
    $events['theme_update'] = array(
        'action'      => 'wp_update_themes',
        'description' => __( 'Whether there are theme updates available', 'slack' ),
        'message'     => function() {
            $update_themes = get_site_transient( 'update_themes' );
            if (!empty( $update_themes->response )) {
                $themes_needing_updates = $update_themes->response;
                $themes_names_array = array_keys($themes_needing_updates);
                $themes_names = implode(", ", $themes_names_array);
                return sprintf('Please update %s.', $themes_names);
            } else {
                return;
            }
        }
    );
    return $events;
} );
