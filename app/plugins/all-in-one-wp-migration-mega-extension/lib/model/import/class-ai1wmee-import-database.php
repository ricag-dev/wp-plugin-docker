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

class Ai1wmee_Import_Database {

	public static function execute( $params ) {

		$model = new Ai1wmee_Settings;

		// Set progress
		Ai1wm_Status::info( __( 'Updating Mega settings...', AI1WMEE_PLUGIN_NAME ) );

		// Read settings.json file
		$handle = ai1wm_open( ai1wm_settings_path( $params ), 'r' );

		// Parse settings.json file
		$settings = ai1wm_read( $handle, filesize( ai1wm_settings_path( $params ) ) );
		$settings = json_decode( $settings, true );

		// Close handle
		ai1wm_close( $handle );

		// Update Mega settings
		$model->set_cron_timestamp( $settings['ai1wmee_mega_cron_timestamp'] );
		$model->set_cron( $settings['ai1wmee_mega_cron'] );
		$model->set_user_email( $settings['ai1wmee_mega_user_email'] );
		$model->set_user_password( $settings['ai1wmee_mega_user_password'] );
		$model->set_user_session( $settings['ai1wmee_mega_user_session'] );
		$model->set_node_id( $settings['ai1wmee_mega_node_id'] );
		$model->set_backups( $settings['ai1wmee_mega_backups'] );
		$model->set_total( $settings['ai1wmee_mega_total'] );
		$model->set_days( $settings['ai1wmee_mega_days'] );
		$model->set_notify_ok_toggle( $settings['ai1wmee_mega_notify_toggle'] );
		$model->set_notify_error_toggle( $settings['ai1wmee_mega_notify_error_toggle'] );
		$model->set_notify_error_subject( $settings['ai1wmee_mega_notify_error_subject'] );
		$model->set_notify_email( $settings['ai1wmee_mega_notify_email'] );

		// Set progress
		Ai1wm_Status::info( __( 'Done updating Mega settings.', AI1WMEE_PLUGIN_NAME ) );

		return $params;
	}
}
