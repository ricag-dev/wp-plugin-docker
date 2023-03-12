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

class Ai1wmee_Settings_Controller {

	public static function index() {
		$model = new Ai1wmee_Settings;

		$mega_backup_schedules = get_option( 'ai1wmee_mega_cron', array() );
		$mega_cron_timestamp   = get_option( 'ai1wmee_mega_cron_timestamp', time() );
		$last_backup_timestamp = get_option( 'ai1wmee_mega_timestamp', false );

		$last_backup_date = $model->get_last_backup_date( $last_backup_timestamp );
		$next_backup_date = $model->get_next_backup_date( $mega_backup_schedules );

		Ai1wm_Template::render(
			'settings/index',
			array(
				'mega_backup_schedules' => $mega_backup_schedules,
				'mega_cron_timestamp'   => $mega_cron_timestamp,
				'notify_ok_toggle'      => get_option( 'ai1wmee_mega_notify_toggle', false ),
				'notify_error_toggle'   => get_option( 'ai1wmee_mega_notify_error_toggle', false ),
				'notify_email'          => get_option( 'ai1wmee_mega_notify_email', get_option( 'admin_email', false ) ),
				'last_backup_date'      => $last_backup_date,
				'next_backup_date'      => $next_backup_date,
				'node_id'               => get_option( 'ai1wmee_mega_node_id', false ),
				'timestamp'             => get_option( 'ai1wmee_mega_timestamp', false ),
				'user_email'            => get_option( 'ai1wmee_mega_user_email', false ),
				'user_password'         => get_option( 'ai1wmee_mega_user_password', false ),
				'user_session'          => get_option( 'ai1wmee_mega_user_session', false ),
				'backups'               => get_option( 'ai1wmee_mega_backups', false ),
				'total'                 => get_option( 'ai1wmee_mega_total', false ),
				'days'                  => get_option( 'ai1wmee_mega_days', false ),
			),
			AI1WMEE_TEMPLATES_PATH
		);
	}

	public static function connection( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Mega update
		if ( isset( $params['ai1wmee_mega_link'] ) ) {
			$model = new Ai1wmee_Settings;

			// Set user email
			if ( isset( $params['ai1wmee_mega_user_email'] ) ) {
				$model->set_user_email( trim( $params['ai1wmee_mega_user_email'] ) );
			}

			// Set user password
			if ( isset( $params['ai1wmee_mega_user_password'] ) ) {
				$model->set_user_password( trim( $params['ai1wmee_mega_user_password'] ) );
			}

			try {
				// Set user session
				$model->set_user_session( $model->do_login() );

				// Set message
				Ai1wm_Message::flash( 'success', __( 'Mega connection is successfully established.', AI1WMEE_PLUGIN_NAME ) );
			} catch ( Ai1wmee_Error_Exception $e ) {
				Ai1wm_Message::flash( 'error', $e->getMessage() );
			}
		}

		// Redirect to settings page
		wp_redirect( network_admin_url( 'admin.php?page=ai1wmee_settings' ) );
		exit;
	}

	public static function picker() {
		Ai1wm_Template::render(
			'settings/picker',
			array(),
			AI1WMEE_TEMPLATES_PATH
		);
	}

	public static function settings( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Mega update
		if ( isset( $params['ai1wmee_mega_update'] ) ) {
			$model = new Ai1wmee_Settings;

			// Cron timestamp update
			if ( ! empty( $params['ai1wmee_mega_cron_timestamp'] ) && ( $cron_timestamp = strtotime( $params['ai1wmee_mega_cron_timestamp'], current_time( 'timestamp' ) ) ) ) {
				$model->set_cron_timestamp( strtotime( get_gmt_from_date( date( 'Y-m-d H:i:s', $cron_timestamp ) ) ) );
			} else {
				$model->set_cron_timestamp( time() );
			}

			// Cron update
			if ( ! empty( $params['ai1wmee_mega_cron'] ) ) {
				$model->set_cron( (array) $params['ai1wmee_mega_cron'] );
			} else {
				$model->set_cron( array() );
			}

			// Set number of backups
			if ( ! empty( $params['ai1wmee_mega_backups'] ) ) {
				$model->set_backups( (int) $params['ai1wmee_mega_backups'] );
			} else {
				$model->set_backups( 0 );
			}

			// Set size of backups
			if ( ! empty( $params['ai1wmee_mega_total'] ) && ! empty( $params['ai1wmee_mega_total_unit'] ) ) {
				$model->set_total( (int) $params['ai1wmee_mega_total'] . trim( $params['ai1wmee_mega_total_unit'] ) );
			} else {
				$model->set_total( 0 );
			}

			// Set age of backups
			if ( ! empty( $params['ai1wmee_mega_days'] ) ) {
				$model->set_days( (int) $params['ai1wmee_mega_days'] );
			} else {
				$model->set_days( 0 );
			}

			// Set Node ID
			$model->set_node_id( trim( $params['ai1wmee_mega_node_id'] ) );

			// Set notify ok toggle
			$model->set_notify_ok_toggle( isset( $params['ai1wmee_mega_notify_toggle'] ) );

			// Set notify error toggle
			$model->set_notify_error_toggle( isset( $params['ai1wmee_mega_notify_error_toggle'] ) );

			// Set notify email
			$model->set_notify_email( trim( $params['ai1wmee_mega_notify_email'] ) );

			// Set message
			Ai1wm_Message::flash( 'settings', __( 'Your changes have been saved.', AI1WMEE_PLUGIN_NAME ) );
		}

		// Redirect to settings page
		wp_redirect( network_admin_url( 'admin.php?page=ai1wmee_settings' ) );
		exit;
	}

	public static function revoke( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Mega logout
		if ( isset( $params['ai1wmee_mega_logout'] ) ) {
			$model = new Ai1wmee_Settings;
			$model->revoke();
		}

		// Redirect to settings page
		wp_redirect( network_admin_url( 'admin.php?page=ai1wmee_settings' ) );
		exit;
	}

	public static function account() {
		ai1wm_setup_environment();

		try {
			$model = new Ai1wmee_Settings;
			if ( ( $account = $model->get_account() ) ) {
				echo json_encode( $account );
				exit;
			}
		} catch ( Ai1wmee_Error_Exception $e ) {
			status_header( 400 );
			echo json_encode( array( 'message' => $e->getMessage() ) );
			exit;
		}
	}

	public static function selector( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_GET );
		}

		// Set Node ID
		$node_id = null;
		if ( isset( $params['node_id'] ) ) {
			$node_id = $params['node_id'];
		}

		// Set Mega client
		$mega = new Ai1wmee_Mega_Client(
			get_option( 'ai1wmee_mega_user_email', false ),
			get_option( 'ai1wmee_mega_user_password', false )
		);

		$mega->load_user_session( get_option( 'ai1wmee_mega_user_session', false ) );

		// Get node list
		$items = $mega->list_nodes( $node_id );

		// Set folder structure
		$response = array( 'items' => array(), 'num_hidden_files' => 0 );

		// Set folder items
		foreach ( $items as $item ) {
			if ( $item->is_dir() ) {
				$response['items'][] = array(
					'id'    => $item->get_node_id(),
					'key'   => $item->get_key(),
					'name'  => $item->get_file_name(),
					'date'  => human_time_diff( $item->get_last_modified_date() ),
					'size'  => ai1wm_size_format( $item->get_size() ),
					'bytes' => $item->get_size(),
					'type'  => $item->get_type(),
				);
			} else {
				$response['num_hidden_files']++;
			}
		}

		echo json_encode( $response );
		exit;
	}

	public static function folder() {
		ai1wm_setup_environment();

		// Set Mega client
		$mega = new Ai1wmee_Mega_Client(
			get_option( 'ai1wmee_mega_user_email', false ),
			get_option( 'ai1wmee_mega_user_password', false )
		);

		$mega->load_user_session( get_option( 'ai1wmee_mega_user_session', false ) );

		// Get folder ID
		$node_id = get_option( 'ai1wmee_mega_node_id', false );

		try {
			// Create folder
			if ( ! ( $node_id = $mega->get_node_id_by_id( $node_id ) ) ) {
				if ( ! ( $node_id = $mega->get_node_id_by_name( ai1wm_archive_folder() ) ) ) {
					$node_id = $mega->create( ai1wm_archive_folder() );
				}
			}

			// Set folder ID
			update_option( 'ai1wmee_mega_node_id', $node_id );

			// Get folder name
			if ( ! ( $folder_name = $mega->get_node_name_by_id( $node_id ) ) ) {
				status_header( 400 );
				echo json_encode(
					array(
						'message' => __(
							'We were unable to retrieve your backup folder details. ' .
							'Mega servers are overloaded at the moment. ' .
							'Please wait for a few minutes and try again by refreshing the page.',
							AI1WMEE_PLUGIN_NAME
						),
					)
				);
				exit;
			}
		} catch ( Ai1wmee_Error_Exception $e ) {
			status_header( 400 );
			echo json_encode( array( 'message' => $e->getMessage() ) );
			exit;
		}

		echo json_encode( array( 'id' => $node_id, 'name' => $folder_name ) );
		exit;
	}

	public static function init_cron() {
		$model = new Ai1wmee_Settings;
		return $model->init_cron();
	}

	public static function notify_ok_toggle() {
		$model = new Ai1wmee_Settings;
		return $model->get_notify_ok_toggle();
	}

	public static function notify_error_toggle() {
		$model = new Ai1wmee_Settings;
		return $model->get_notify_error_toggle();
	}

	public static function notify_error_subject() {
		$model = new Ai1wmee_Settings;
		return $model->get_notify_error_subject();
	}

	public static function notify_email() {
		$model = new Ai1wmee_Settings;
		return $model->get_notify_email();
	}
}
