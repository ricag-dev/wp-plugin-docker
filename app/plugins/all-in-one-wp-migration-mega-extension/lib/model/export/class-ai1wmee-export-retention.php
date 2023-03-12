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

class Ai1wmee_Export_Retention {

	/**
	 * Mega client
	 *
	 * @var Ai1wmee_Mega_Client
	 */
	private static $mega = null;

	/**
	 * Node ID
	 *
	 * @var string
	 */
	private static $node_id = null;

	public static function execute( $params, Ai1wmee_Mega_Client $mega = null ) {

		// Set Mega client
		if ( is_null( $mega ) ) {
			$mega = new Ai1wmee_Mega_Client(
				get_option( 'ai1wmee_mega_user_email', false ),
				get_option( 'ai1wmee_mega_user_password', false )
			);
		}

		$mega->load_user_session( get_option( 'ai1wmee_mega_user_session', false ) );

		self::$mega    = $mega;
		self::$node_id = $params['node_id'];

		// No backups, no need to apply backup retention
		$backups = self::get_files();
		if ( count( $backups ) === 0 ) {
			return $params;
		}

		// The order is very important - we delete files by date, by size, and finally by total count
		self::delete_backups_older_than();
		self::delete_backups_when_total_size_over();
		self::delete_backups_when_total_count_over();

		return $params;
	}

	private static function delete_backups_older_than() {
		$backups = self::get_files();
		$days    = intval( get_option( 'ai1wmee_mega_days', 0 ) );
		if ( $days > 0 ) {
			foreach ( $backups as $backup ) {
				if ( $backup->get_last_modified_date() <= time() - $days * 86400 ) {
					self::delete_file( $backup );
				}
			}
		}
	}

	private static function delete_backups_when_total_size_over() {
		$backups        = self::get_files();
		$retention_size = ai1wm_parse_size( get_option( 'ai1wmee_mega_total', 0 ) );

		// Get the size of the latest backup before we remove it
		$size_of_backups = $backups[0]->get_size();

		// Remove the latest backup, the user should have at least one backup
		array_shift( $backups );

		if ( $retention_size > 0 ) {
			foreach ( $backups as $backup ) {
				if ( $size_of_backups + $backup->get_size() > $retention_size ) {
					self::delete_file( $backup );
				} else {
					$size_of_backups += $backup->get_size();
				}
			}
		}
	}

	private static function delete_backups_when_total_count_over() {
		$backups = self::get_files();
		$limit   = intval( get_option( 'ai1wmee_mega_backups', 0 ) );

		if ( $limit > 0 ) {
			if ( count( $backups ) > $limit ) {
				for ( $i = $limit; $i < count( $backups ); $i++ ) {
					self::delete_file( $backups[ $i ] );
				}
			}
		}
	}

	private static function get_files() {
		$items = self::$mega->list_nodes( self::$node_id );

		$backups = array();
		foreach ( $items as $item ) {
			if ( $item->is_file() && pathinfo( $item->get_file_name(), PATHINFO_EXTENSION ) === 'wpress' ) {
				$backups[] = $item;
			}
		}

		usort( $backups, 'Ai1wmee_Export_Retention::sort_by_date_desc' );
		return $backups;
	}

	public static function sort_by_date_desc( $first_backup, $second_backup ) {
		return intval( $second_backup->get_last_modified_date() ) - intval( $first_backup->get_last_modified_date() );
	}

	private static function delete_file( $backup ) {
		return self::$mega->delete( $backup->get_node_id() );
	}
}
