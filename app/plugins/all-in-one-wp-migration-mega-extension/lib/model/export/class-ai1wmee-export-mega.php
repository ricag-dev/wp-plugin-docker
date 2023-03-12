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

class Ai1wmee_Export_Mega {

	public static function execute( $params, Ai1wmee_Mega_Client $mega = null ) {

		// Set progress
		Ai1wm_Status::info( __( 'Connecting to Mega...', AI1WMEE_PLUGIN_NAME ) );

		// Open the archive file for writing
		$archive = new Ai1wm_Compressor( ai1wm_archive_path( $params ) );

		// Append EOF block
		$archive->close( true );

		// Set Mega client
		if ( is_null( $mega ) ) {
			$mega = new Ai1wmee_Mega_Client(
				get_option( 'ai1wmee_mega_user_email', false ),
				get_option( 'ai1wmee_mega_user_password', false )
			);
		}

		$mega->load_user_session( get_option( 'ai1wmee_mega_user_session', false ) );

		// Get node ID
		$params['node_id'] = get_option( 'ai1wmee_mega_node_id', false );

		// Create folder if does not exist
		if ( ! ( $params['node_id'] = $mega->get_node_id_by_id( $params['node_id'] ) ) ) {
			if ( ! ( $params['node_id'] = $mega->get_node_id_by_name( ai1wm_archive_folder() ) ) ) {
				$params['node_id'] = $mega->create( ai1wm_archive_folder() );
			}
		}

		// Get upload URL
		$params['upload_url'] = $mega->get_upload_url( ai1wm_archive_bytes( $params ) );

		// Set progress
		Ai1wm_Status::info( __( 'Done connecting to Mega.', AI1WMEE_PLUGIN_NAME ) );

		return $params;
	}
}
