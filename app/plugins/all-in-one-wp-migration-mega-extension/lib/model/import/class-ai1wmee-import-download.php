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

class Ai1wmee_Import_Download {

	public static function execute( $params, Ai1wmee_Mega_Client $mega = null ) {

		$params['completed'] = false;

		// Validate node key
		if ( ! isset( $params['node_key'] ) ) {
			throw new Ai1wm_Import_Exception( __( 'Mega Node Key is not specified.', AI1WMEE_PLUGIN_NAME ) );
		}

		// Validate node size
		if ( ! isset( $params['node_size'] ) ) {
			throw new Ai1wm_Import_Exception( __( 'Mega Node Size is not specified.', AI1WMEE_PLUGIN_NAME ) );
		}

		// Validate download URL
		if ( ! isset( $params['download_url'] ) ) {
			throw new Ai1wm_Import_Exception( __( 'Mega Download URL is not specified.', AI1WMEE_PLUGIN_NAME ) );
		}

		// Set archive offset
		if ( ! isset( $params['archive_offset'] ) ) {
			$params['archive_offset'] = 0;
		}

		// Set file range start
		if ( ! isset( $params['file_range_start'] ) ) {
			$params['file_range_start'] = 0;
		}

		// Get file chunks
		$file_chunks = Ai1wmee_Mega_Utils::get_chunks( $params['node_size'] );

		// Set file chunk size
		if ( ! isset( $params['file_chunk_size'] ) ) {
			if ( ! empty( $file_chunks[ $params['file_range_start'] ] ) ) {
				$params['file_chunk_size'] = $file_chunks[ $params['file_range_start'] ];
			}
		}

		// Set file range end
		if ( ! isset( $params['file_range_end'] ) ) {
			$params['file_range_end'] = $params['file_chunk_size'] - 1;
		}

		// Set download retries
		if ( ! isset( $params['download_retries'] ) ) {
			$params['download_retries'] = 0;
		}

		// Set Mega client
		if ( is_null( $mega ) ) {
			$mega = new Ai1wmee_Mega_Client(
				get_option( 'ai1wmee_mega_user_email', false ),
				get_option( 'ai1wmee_mega_user_password', false )
			);
		}

		$mega->load_download_url( $params['download_url'] );
		$mega->load_user_session( get_option( 'ai1wmee_mega_user_session', false ) );

		// Open the archive file for writing
		$archive = fopen( ai1wm_archive_path( $params ), 'cb' );

		// Write file chunk data
		if ( ( fseek( $archive, $params['archive_offset'] ) !== -1 ) ) {
			try {

				$params['download_retries'] += 1;

				// Download file chunk data
				$mega->download_file_chunk( $archive, $params['node_key'], $params['file_range_start'], $params['file_range_end'] );

				// Unset download retries
				unset( $params['download_retries'] );

			} catch ( Ai1wmee_Connect_Exception $e ) {
				if ( $params['download_retries'] <= 3 ) {
					return $params;
				}

				throw $e;
			}
		}

		// Set archive offset
		$params['archive_offset'] = ftell( $archive );

		// Set file chunk size
		if ( isset( $file_chunks[ $params['file_range_start'] ] ) ) {
			$params['file_chunk_size'] = $file_chunks[ $params['file_range_start'] ];
		} else {
			$params['file_chunk_size'] = AI1WMEE_FILE_CHUNK_SIZE;
		}

		// Set file range start
		if ( $params['node_size'] <= ( $params['file_range_start'] + $params['file_chunk_size'] ) ) {
			$params['file_range_start'] = $params['node_size'] - 1;
		} else {
			$params['file_range_start'] = $params['file_range_start'] + $params['file_chunk_size'];
		}

		// Set file chunk size
		if ( isset( $file_chunks[ $params['file_range_end'] + 1 ] ) ) {
			$params['file_chunk_size'] = $file_chunks[ $params['file_range_end'] + 1 ];
		} else {
			$params['file_chunk_size'] = AI1WMEE_FILE_CHUNK_SIZE;
		}

		// Set file range end
		if ( $params['node_size'] <= ( $params['file_range_end'] + $params['file_chunk_size'] ) ) {
			$params['file_range_end'] = $params['node_size'] - 1;
		} else {
			$params['file_range_end'] = $params['file_range_end'] + $params['file_chunk_size'];
		}

		// Get progress
		$progress = (int) ( ( $params['file_range_start'] / $params['node_size'] ) * 100 );

		// Set progress
		if ( defined( 'WP_CLI' ) ) {
			WP_CLI::log( sprintf( __( 'Downloading [%d%% complete]', AI1WMEE_PLUGIN_NAME ), $progress ) );
		} else {
			Ai1wm_Status::progress( $progress );
		}

		// Completed?
		if ( $params['file_range_start'] === ( $params['node_size'] - 1 ) ) {

			// Unset node key
			unset( $params['node_key'] );

			// Unset node size
			unset( $params['node_size'] );

			// Unset download URL
			unset( $params['download_url'] );

			// Unset archive offset
			unset( $params['archive_offset'] );

			// Unset file range start
			unset( $params['file_range_start'] );

			// Unset file chunk size
			unset( $params['file_chunk_size'] );

			// Unset file range end
			unset( $params['file_range_end'] );

			// Unset completed
			unset( $params['completed'] );
		}

		// Close the archive file
		fclose( $archive );

		return $params;
	}
}
