<?php
/**
 * Copyright (C) 2014-2020 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

class Ai1wmee_Import_Settings {

	public static function execute( $params ) {

		// Set progress
		Ai1wm_Status::info( __( 'Getting Mega settings...', AI1WMEE_PLUGIN_NAME ) );

		$settings = array(
			'ai1wmee_mega_cron_timestamp'       => get_option( 'ai1wmee_mega_cron_timestamp', time() ),
			'ai1wmee_mega_cron'                 => get_option( 'ai1wmee_mega_cron', array() ),
			'ai1wmee_mega_user_email'           => get_option( 'ai1wmee_mega_user_email', false ),
			'ai1wmee_mega_user_password'        => get_option( 'ai1wmee_mega_user_password', false ),
			'ai1wmee_mega_user_session'         => get_option( 'ai1wmee_mega_user_session', false ),
			'ai1wmee_mega_node_id'              => get_option( 'ai1wmee_mega_node_id', false ),
			'ai1wmee_mega_backups'              => get_option( 'ai1wmee_mega_backups', false ),
			'ai1wmee_mega_total'                => get_option( 'ai1wmee_mega_total', false ),
			'ai1wmee_mega_days'                 => get_option( 'ai1wmee_mega_days', false ),
			'ai1wmee_mega_notify_toggle'        => get_option( 'ai1wmee_mega_notify_toggle', false ),
			'ai1wmee_mega_notify_error_toggle'  => get_option( 'ai1wmee_mega_notify_error_toggle', false ),
			'ai1wmee_mega_notify_error_subject' => get_option( 'ai1wmee_mega_notify_error_subject', false ),
			'ai1wmee_mega_notify_email'         => get_option( 'ai1wmee_mega_notify_email', false ),
		);

		// Save settings.json file
		$handle = ai1wm_open( ai1wm_settings_path( $params ), 'w' );
		ai1wm_write( $handle, json_encode( $settings ) );
		ai1wm_close( $handle );

		// Set progress
		Ai1wm_Status::info( __( 'Done getting Mega settings.', AI1WMEE_PLUGIN_NAME ) );

		return $params;
	}
}
