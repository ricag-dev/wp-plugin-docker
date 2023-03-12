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

if ( defined( 'WP_CLI' ) && class_exists( 'Ai1wm_Backup_WP_CLI_Base' ) ) {
	class Ai1wmee_Mega_WP_CLI_Command extends Ai1wm_Backup_WP_CLI_Base {
		public function __construct() {
			parent::__construct();

			if ( ! get_option( 'ai1wmee_mega_user_email', false ) || ! get_option( 'ai1wmee_mega_user_password', false ) ) {
				WP_CLI::error_multi_line(
					array(
						__( 'In order to use All-in-One WP Migration Mega extension you need to configure it first.', AI1WMEE_PLUGIN_NAME ),
						__( 'Please navigate to WP Admin > All-in-One WP Migration > Mega Settings and Link your Mega account.', AI1WMEE_PLUGIN_NAME ),
					)
				);
				exit;
			}
		}

		/**
		 * Creates a new backup and uploads to Mega.
		 *
		 * ## OPTIONS
		 *
		 * [--sites]
		 * : Export sites by id (Multisite only). To list sites use: wp site list --fields=blog_id,url
		 *
		 * [--password[=<password>]]
		 * : Encrypt backup with password
		 *
		 * [--exclude-spam-comments]
		 * : Do not export spam comments
		 *
		 * [--exclude-post-revisions]
		 * : Do not export post revisions
		 *
		 * [--exclude-media]
		 * : Do not export media library (files)
		 *
		 * [--exclude-themes]
		 * : Do not export themes (files)
		 *
		 * [--exclude-inactive-themes]
		 * : Do not export inactive themes (files)
		 *
		 * [--exclude-muplugins]
		 * : Do not export must-use plugins (files)
		 *
		 * [--exclude-plugins]
		 * : Do not export plugins (files)
		 *
		 * [--exclude-inactive-plugins]
		 * : Do not export inactive plugins (files)
		 *
		 * [--exclude-cache]
		 * : Do not export cache (files)
		 *
		 * [--exclude-database]
		 * : Do not export database (sql)
		 *
		 * [--exclude-tables]
		 * : Do not export selected database tables (sql)
		 *
		 * [--exclude-email-replace]
		 * : Do not replace email domain (sql)
		 *
		 * [--replace]
		 * : Find and replace text in the database
		 *
		 * [<find>...]
		 * : A string to search for within the database
		 *
		 * [<replace>...]
		 * : Replace instances of the first string with this new string
		 *
		 * ## EXAMPLES
		 *
		 * $ wp ai1wm mega backup --replace "wp" "WordPress"
		 * Backup in progress...
		 * Uploading wordpress-20181109-092410-450.wpress (17 MB) [29% complete]
		 * Uploading wordpress-20181109-092410-450.wpress (17 MB) [59% complete]
		 * Uploading wordpress-20181109-092410-450.wpress (17 MB) [89% complete]
		 * Uploading wordpress-20181109-092410-450.wpress (17 MB) [100% complete]
		 * Backup complete.
		 * Backup file: wordpress-20181109-082635-610.wpress
		 * Backup location: wordpress/wordpress-20190509-143253-267.wpress
		 * @subcommand backup
		 */
		public function backup( $args = array(), $assoc_args = array() ) {
			$params = $this->run_backup(
				$this->build_export_params( $args, $assoc_args )
			);

			WP_CLI::log( sprintf( __( 'Backup location: %s', AI1WMEE_PLUGIN_NAME ), $this->get_backup_location( $params ) ) );
		}

		/**
		 * Get a list of Mega backup files.
		 *
		 * ## OPTIONS
		 *
		 * [--folder-path=<path>]
		 * : List backups in a specific Mega subfolder
		 *
		 * ## EXAMPLES
		 *
		 * $ wp ai1wm mega list-backups
		 * +------------------------------------------------+--------------+-----------+
		 * | Backup name                                    | Date created | Size      |
		 * +------------------------------------------------+--------------+-----------+
		 * | migration-wp-20170908-152313-435.wpress        | 4 days ago   | 536.77 MB |
		 * | migration-wp-20170908-152103-603.wpress        | 4 days ago   | 536.77 MB |
		 * | migration-wp-20170908-152036-162.wpress        | 4 days ago   | 536.77 MB |
		 * +------------------------------------------------+--------------+-----------+
		 *
		 * $ wp ai1wm mega list-backups --folder-path=/backups/daily
		 * +------------------------------------------------+--------------+-----------+
		 * | Backup name                                    | Date created | Size      |
		 * +------------------------------------------------+--------------+-----------+
		 * | migration-wp-20170908-152313-435.wpress        | 4 days ago   | 536.77 MB |
		 * | migration-wp-20170908-152103-603.wpress        | 4 days ago   | 536.77 MB |
		 * +------------------------------------------------+--------------+-----------+
		 *
		 * @subcommand list-backups
		 */
		public function list_backups( $args = array(), $assoc_args = array() ) {
			$backups = new cli\Table;

			$backups->setHeaders(
				array(
					'name' => __( 'Backup name', AI1WMEE_PLUGIN_NAME ),
					'date' => __( 'Date created', AI1WMEE_PLUGIN_NAME ),
					'size' => __( 'Size', AI1WMEE_PLUGIN_NAME ),
				)
			);

			$node_id = $this->get_node_id( $assoc_args );
			$items   = $this->list_items( $node_id );

			// Set folder structure
			$response = array( 'items' => array(), 'num_hidden_files' => 0 );

			foreach ( $items as $item ) {
				if ( pathinfo( $item['name'], PATHINFO_EXTENSION ) === 'wpress' ) {
					$backups->addRow(
						array(
							'name' => $item['name'],
							'date' => sprintf( __( '%s ago', AI1WMEE_PLUGIN_NAME ), human_time_diff( $item['date'] ) ),
							'size' => ai1wm_size_format( $item['bytes'], 2 ),
						)
					);
				}
			}

			$backups->display();
		}

		/**
		 * Restores a backup from Mega.
		 *
		 * ## OPTIONS
		 *
		 * <file>
		 * : Name of the backup file
		 *
		 * [--folder-path=<path>]
		 * : Download a backup from a specific Mega folder
		 *
		 * [--yes]
		 * : Automatically confirm the restore operation
		 *
		 * ## EXAMPLES
		 *
		 * $ wp ai1wm mega restore migration-wp-20170913-095743-931.wpress
		 * Restore in progress...
		 * Restore complete.
		 *
		 * $ wp ai1wm mega restore migration-wp-20170913-095743-931.wpress --folder-path=/backups/daily
		 * @subcommand restore
		 */
		public function restore( $args = array(), $assoc_args = array() ) {
			if ( ! isset( $args[0] ) ) {
				WP_CLI::error_multi_line(
					array(
						__( 'A backup name must be provided in order to proceed with the restore process.', AI1WMEE_PLUGIN_NAME ),
						__( 'Example: wp ai1wm mega restore migration-wp-20170913-095743-931.wpress', AI1WMEE_PLUGIN_NAME ),
					)
				);
				exit;
			}

			$node_id = $this->get_node_id( $assoc_args );
			$items   = $this->list_items( $node_id );

			$file = null;
			foreach ( $items as $item ) {
				if ( $item['name'] === $args[0] ) {
					$file = $item;
					break;
				}
			}

			if ( is_null( $file ) ) {
				WP_CLI::error_multi_line(
					array(
						__( 'The backup file could not be located.', AI1WMEE_PLUGIN_NAME ),
						__( 'To list available backups use: wp ai1wm mega list-backups', AI1WMEE_PLUGIN_NAME ),
					)
				);
				exit;
			}

			$params = array(
				'archive'    => $args[0],
				'storage'    => ai1wm_storage_folder(),
				'node_id'    => $file['id'],
				'node_key'   => $file['key'],
				'node_size'  => $file['bytes'],
				'cli_args'   => $assoc_args,
				'secret_key' => get_option( AI1WM_SECRET_KEY, false ),
			);

			$this->run_restore( $params );
		}

		/**
		 * Get backup items list
		 *
		 * @param  string $node_id Node ID of parent element where backups located
		 * @return array  $items   Backup items
		 */
		protected function list_items( $node_id ) {
			// Set Mega client
			$mega = new Ai1wmee_Mega_Client(
				get_option( 'ai1wmee_mega_user_email', false ),
				get_option( 'ai1wmee_mega_user_password', false )
			);

			$mega->load_user_session( get_option( 'ai1wmee_mega_user_session', false ) );

			try {
				// Get node list
				$nodes = $mega->list_nodes( $node_id );
				$items = array();
				foreach ( $nodes as $node ) {
					$items[] = array(
						'id'    => $node->get_node_id(),
						'key'   => $node->get_key(),
						'name'  => $node->get_file_name(),
						'date'  => $node->get_last_modified_date(),
						'size'  => ai1wm_size_format( $node->get_size() ),
						'bytes' => $node->get_size(),
						'type'  => $node->get_type(),
					);
				}
				usort( $items, array( $this, 'sort_by_date_desc' ) );
			} catch ( Exception $e ) {
				WP_CLI::error( $e->getMessage() );
				exit;
			}

			return $items;
		}

		/**
		 * Comparison function for sort by date descending
		 *
		 * @param  array  $a First item to compare
		 * @param  array  $b Second item to compare
		 * @return int    -1/0/1 for less/equal/greater
		 */
		protected function sort_by_date_desc( $a, $b ) {
			if ( $a['date'] === $b['date'] ) {
				return 0;
			}

			return ( $a['date'] > $b['date'] ) ? - 1 : 1;
		}

		/**
		 * Get container node ID from command-line or WP settings
		 *
		 * @param  array  $assoc_args CLI params
		 * @return string node ID
		 */
		protected function get_node_id( $assoc_args ) {
			if ( isset( $assoc_args['folder-path'] ) ) {
				// Set Mega client
				$mega = new Ai1wmee_Mega_Client(
					get_option( 'ai1wmee_mega_user_email', false ),
					get_option( 'ai1wmee_mega_user_password', false )
				);

				$mega->load_user_session( get_option( 'ai1wmee_mega_user_session', false ) );

				$path   = explode( '/', trim( $assoc_args['folder-path'], '/' ) );
				$parent = '';
				foreach ( $path as $folder_name ) {
					$node_id = $mega->get_node_id_by_name( $folder_name, $parent );
					if ( empty( $node_id ) ) {
						WP_CLI::error( sprintf( __( 'Folder \'%s\' not found', AI1WMEE_PLUGIN_NAME ), $folder_name ) );
						exit;
					}
					$parent = $node_id;
				}
				return $node_id;
			}
			return get_option( 'ai1wmee_mega_node_id', null );
		}

		/**
		 * Get backup location string
		 *
		 * @param  array  $params Params
		 * @return string Human-readable backup file location
		 */
		protected function get_backup_location( $params ) {
			// Set Mega client
			$mega = new Ai1wmee_Mega_Client(
				get_option( 'ai1wmee_mega_user_email', false ),
				get_option( 'ai1wmee_mega_user_password', false )
			);

			$mega->load_user_session( get_option( 'ai1wmee_mega_user_session', false ) );

			$map = $mega->get_file_map();

			$folder_node_id = $params['node_id'];

			while ( isset( $map[ $folder_node_id ] ) ) {
				$node           = $map[ $folder_node_id ];
				$path[]         = $node->get_file_name();
				$folder_node_id = $node->get_parent_node_id();
			}
			$path = join( '/', array_reverse( $path ) );
			$file = ai1wm_archive_name( $params );
			return sprintf( '%s/%s', $path, $file );
		}
	}
}
