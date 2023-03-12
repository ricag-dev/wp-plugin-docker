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

class Ai1wmee_Import_Controller {

	public static function button() {
		return Ai1wm_Template::get_content(
			'import/button',
			array( 'user_session' => get_option( 'ai1wmee_mega_user_session', false ) ),
			AI1WMEE_TEMPLATES_PATH
		);
	}

	public static function picker() {
		Ai1wm_Template::render(
			'import/picker',
			array(),
			AI1WMEE_TEMPLATES_PATH
		);
	}

	public static function browser( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_GET );
		}

		// Set node ID
		$node_id = null;
		if ( isset( $params['node_id'] ) ) {
			$node_id = trim( $params['node_id'] );
		}

		// Set Mega client
		$mega = new Ai1wmee_Mega_Client(
			get_option( 'ai1wmee_mega_user_email', false ),
			get_option( 'ai1wmee_mega_user_password', false )
		);

		$mega->load_user_session( get_option( 'ai1wmee_mega_user_session', false ) );

		// Get node list
		$nodes = $mega->list_nodes( $node_id );

		// Set folder structure
		$response = array( 'items' => array(), 'num_hidden_files' => 0 );

		// Loop over node list
		foreach ( $nodes as $node ) {
			if ( $node->is_dir() || pathinfo( $node->get_file_name(), PATHINFO_EXTENSION ) === 'wpress' ) {
				$response['items'][] = array(
					'id'    => $node->get_node_id(),
					'key'   => $node->get_key(),
					'name'  => $node->get_file_name(),
					'date'  => human_time_diff( $node->get_last_modified_date() ),
					'size'  => ai1wm_size_format( $node->get_size() ),
					'bytes' => $node->get_size(),
					'type'  => $node->get_type(),
				);
			} else {
				$response['num_hidden_files']++;
			}
		}

		// Sort nodes by type desc and name asc
		usort( $response['items'], 'Ai1wmee_Import_Controller::sort_by_type_desc_name_asc' );

		echo json_encode( $response );
		exit;
	}

	public static function sort_by_type_desc_name_asc( $first_item, $second_item ) {
		$sorted_items = strcasecmp( $second_item['type'], $first_item['type'] );
		if ( $sorted_items !== 0 ) {
			return $sorted_items;
		}

		return strcasecmp( $first_item['name'], $second_item['name'] );
	}

	public static function pro() {
		return Ai1wm_Template::get_content( 'import/pro', array(), AI1WMEE_TEMPLATES_PATH );
	}
}
