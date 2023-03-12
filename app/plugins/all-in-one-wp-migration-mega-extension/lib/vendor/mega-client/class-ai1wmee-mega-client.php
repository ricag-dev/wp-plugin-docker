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

class Ai1wmee_Mega_Client {

	const MEGA_GLOBAL_SERVER_URL = 'https://g.api.mega.co.nz';
	const MEGA_EUROPE_SERVER_URL = 'https://eu.api.mega.co.nz';

	/**
	 * User email
	 *
	 * @var string
	 */
	protected $user_email = null;

	/**
	 * User password
	 *
	 * @var string
	 */
	protected $user_password = null;

	/**
	 * User key
	 *
	 * @var string
	 */
	protected $user_key = null;

	/**
	 * User private key
	 *
	 * @var string
	 */
	protected $user_private_key = null;

	/**
	 * User session ID
	 *
	 * @var string
	 */
	protected $user_session_id = null;

	/**
	 * API sequence number
	 *
	 * @var integer
	 */
	protected $seq_number = null;

	/**
	 * Upload URL
	 *
	 * @var string
	 */
	protected $upload_url = null;

	/**
	 * Download URL
	 *
	 * @var string
	 */
	protected $download_url = null;

	/**
	 * SSL mode
	 *
	 * @var boolean
	 */
	protected $ssl = null;

	public function __construct( $user_email, $user_password ) {
		$this->user_email    = strtolower( $user_email );
		$this->user_password = $user_password;
		$this->seq_number    = rand( 0, PHP_INT_MAX );
	}

	/**
	 * Load upload URL
	 *
	 * @param  string $url Upload URL
	 * @return void
	 */
	public function load_upload_url( $url ) {
		$this->upload_url = $url;
	}

	/**
	 * Load download URL
	 *
	 * @param  string $url Download URL
	 * @return void
	 */
	public function load_download_url( $url ) {
		$this->download_url = $url;
	}

	/**
	 * Pre-login to Mega API
	 *
	 * @return string
	 */
	public function pre_login() {
		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a'    => 'us0',
						'user' => $this->user_email,
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		if ( isset( $response[0]['s'] ) ) {
			if ( isset( $response[0]['v'] ) && $response[0]['v'] === 2 ) {
				return $response[0]['s'];
			}
		}
	}

	/**
	 * Login to Mega API
	 *
	 * @return array
	 */
	public function login() {
		if ( ( $password_salt = $this->pre_login() ) ) {
			$password_salt = Ai1wmee_Mega_Utils::base64_url_decode( $password_salt );
			$derived_key   = Ai1wmee_Mega_Crypto::hash_pbkdf2( 'sha512', $this->user_password, $password_salt, 100000, 32, true );

			$password_key  = substr( $derived_key, 0, 16 );
			$password_hash = substr( $derived_key, 16, 32 );
			$password_hash = Ai1wmee_Mega_Utils::base64_url_encode( $password_hash );
		} else {
			$password_key  = Ai1wmee_Mega_Crypto::prepare_key_pwd( $this->user_password );
			$password_hash = Ai1wmee_Mega_Crypto::string_hash( $this->user_email, $password_key );
		}

		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a'    => 'us',
						'user' => $this->user_email,
						'uh'   => $password_hash,
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Bad_Arguments_Exception $e ) {
			throw new Ai1wmee_Bad_Arguments_Exception( __( 'Invalid e-mail address or password. <a href="https://help.servmask.com/knowledgebase/mega-error-codes/#bad-arguments" target="_blank">Technical details</a>', AI1WMEE_PLUGIN_NAME ) );
		} catch ( Ai1wmee_Resource_Does_Not_Exist_Exception $e ) {
			throw new Ai1wmee_Resource_Does_Not_Exist_Exception( __( 'Invalid e-mail address or password. <a href="https://help.servmask.com/knowledgebase/mega-error-codes/#resource-does-not-exist" target="_blank">Technical details</a>', AI1WMEE_PLUGIN_NAME ) );
		} catch ( Ai1wmee_Cryptographic_Error_Exception $e ) {
			throw new Ai1wmee_Cryptographic_Error_Exception( __( 'Private key could not be decrypted. <a href="https://help.servmask.com/knowledgebase/mega-error-codes/#cryptographic-error" target="_blank">Technical details</a>', AI1WMEE_PLUGIN_NAME ) );
		}

		// Decrypt master key
		if ( isset( $response[0]['k'] ) ) {
			$master_key = Ai1wmee_Mega_Utils::base64_to_a32( $response[0]['k'] );

			// Session ID
			if ( count( $master_key ) === 4 ) {
				$password_key = Ai1wmee_Mega_Utils::str_to_a32( $password_key );
				$master_key   = Ai1wmee_Mega_Crypto::decrypt_key( $password_key, $master_key );

				if ( isset( $response[0]['tsid'] ) ) {
					// Temporary Session ID
				} elseif ( isset( $response[0]['csid'] ) ) {
					$private_key = Ai1wmee_Mega_Crypto::decrypt_key( $master_key, Ai1wmee_Mega_Utils::base64_to_a32( $response[0]['privk'] ) );
					$private_key = Ai1wmee_Mega_Utils::a32_to_str( $private_key );

					$rsa_private_key = array();

					// Decompose private key
					for ( $i = 0; $i < 4; $i++ ) {
						$l                     = ( ( ord( $private_key[0] ) * 256 + ord( $private_key[1] ) + 7 ) >> 3 ) + 2;
						$rsa_private_key[ $i ] = Ai1wmee_Mega_Utils::mpi2b( substr( $private_key, 0, $l ) );
						$private_key           = substr( $private_key, $l );
					}

					$csid = Ai1wmee_Mega_Utils::base64_url_decode( $response[0]['csid'] );
					$csid = Ai1wmee_Mega_Utils::mpi2b( $csid );

					$sid = Ai1wmee_Mega_Rsa::rsa_decrypt( $csid, $rsa_private_key[0], $rsa_private_key[1], $rsa_private_key[2] );
					$sid = Ai1wmee_Mega_Utils::base64_url_encode( substr( strrev( $sid ), 0, 43 ) );

					// Check format
					if ( $i === 4 && strlen( $private_key ) < 16 ) {
						// Check remaining padding for added early wrong password detection likelihood
						$this->user_key         = $master_key;
						$this->user_private_key = $rsa_private_key;
						$this->user_session_id  = $sid;

						return array(
							'user_key'         => $master_key,
							'user_private_key' => $rsa_private_key,
							'user_session_id'  => $sid,
						);
					}
				}
			}
		}
	}

	/**
	 * Logout from Mega API
	 *
	 * @return array
	 */
	public function logout() {
		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a' => 'sml',
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		return $response;
	}

	/**
	 * Load user session
	 *
	 * @param  string $session User session
	 * @return void
	 */
	public function load_user_session( $session ) {
		if ( ! $session || ! is_array( $session ) ) {
			return false;
		}

		$this->user_key         = $session['user_key'];
		$this->user_private_key = $session['user_private_key'];
		$this->user_session_id  = $session['user_session_id'];
	}

	/**
	 * Get node ID by name
	 *
	 * @param  string $node_name      Node name
	 * @param  string $parent_node_id Parent node ID
	 * @return string
	 */
	public function get_node_id_by_name( $node_name, $parent_node_id = null ) {
		foreach ( $this->list_nodes( $parent_node_id ) as $node ) {
			if ( $node->get_file_name() === $node_name ) {
				return $node->get_node_id();
			}
		}
	}

	/**
	 * Get node ID by ID
	 *
	 * @param  string $node_id Node ID
	 * @return string
	 */
	public function get_node_id_by_id( $node_id ) {
		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a' => 'f',
						'c' => 1,
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		$files = array();
		if ( isset( $response[0]['f'] ) ) {
			foreach ( $response[0]['f'] as $index => $node ) {
				// phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison
				if ( $node['t'] == 0 || $node['t'] == 1 ) {
					if ( isset( $node['h'] ) && $node['h'] == $node_id ) {
						return $node['h'];
					}
				}
				// phpcs:enable WordPress.PHP.StrictComparisons.LooseComparison
			}
		}
	}

	/**
	 * Get node name by ID
	 *
	 * @param  string $node_id Node ID
	 * @return string
	 */
	public function get_node_name_by_id( $node_id ) {
		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a' => 'f',
						'c' => 1,
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		$files = array();
		if ( isset( $response[0]['f'] ) ) {
			foreach ( $response[0]['f'] as $index => $node ) {
				// phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison
				if ( $node['t'] == 0 || $node['t'] == 1 ) {
					if ( isset( $node['k'] ) ) {
						if ( ( $key = $this->decrypt_node_key( $node['k'] ) ) ) {
							if ( ( $attr = Ai1wmee_Mega_Utils::base64_to_string( $node['a'] ) ) ) {
								if ( isset( $node['h'] ) && $node['h'] == $node_id ) {
									$attr = Ai1wmee_Mega_Crypto::decrypt_node_attribute( $attr, $key );
									if ( isset( $attr['n'] ) ) {
										return $attr['n'];
									}
								}
							}
						}
					}
				}
				// phpcs:enable WordPress.PHP.StrictComparisons.LooseComparison
			}
		}
	}

	/**
	 * Get file map
	 *
	 * @return array Ai1wmee_Mega_File_Info
	 */
	public function get_file_map() {
		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a' => 'f',
						'c' => 1,
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		$nodes = array();
		if ( isset( $response[0]['f'] ) ) {
			foreach ( $response[0]['f'] as $index => $node ) {
				if ( isset( $node['k'] ) ) {
					if ( ( $key = $this->decrypt_node_key( $node['k'] ) ) ) {
						if ( ( $attr = Ai1wmee_Mega_Utils::base64_to_string( $node['a'] ) ) ) {
							$nodes[ $node['h'] ] = new Ai1wmee_Mega_File_Info(
								array(
									'h'  => isset( $node['h'] ) ? $node['h'] : null,
									'p'  => isset( $node['p'] ) ? $node['p'] : null,
									'u'  => isset( $node['u'] ) ? $node['u'] : null,
									'a'  => Ai1wmee_Mega_Crypto::decrypt_node_attribute( $attr, $key ),
									't'  => isset( $node['t'] ) ? $node['t'] : null,
									'k'  => isset( $node['k'] ) ? $node['k'] : null,
									's'  => isset( $node['s'] ) ? $node['s'] : null,
									'ts' => isset( $node['ts'] ) ? $node['ts'] : null,
								)
							);
						}
					}
				}
			}
		}

		return $nodes;
	}

	/**
	 * List nodes
	 *
	 * @param  string $parent_node_id Parent node ID
	 * @return array
	 */
	public function list_nodes( $parent_node_id = null ) {
		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a' => 'f',
						'c' => 1,
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		$files = array();
		if ( isset( $response[0]['f'] ) ) {
			foreach ( $response[0]['f'] as $index => $node ) {
				// phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison
				if ( $node['t'] == 0 || $node['t'] == 1 ) {
					if ( isset( $node['k'] ) ) {
						if ( ( $key = $this->decrypt_node_key( $node['k'] ) ) ) {
							if ( ( $attr = Ai1wmee_Mega_Utils::base64_to_string( $node['a'] ) ) ) {
								$files[] = new Ai1wmee_Mega_File_Info(
									array(
										'h'  => isset( $node['h'] ) ? $node['h'] : null,
										'p'  => isset( $node['p'] ) ? $node['p'] : null,
										'u'  => isset( $node['u'] ) ? $node['u'] : null,
										'a'  => Ai1wmee_Mega_Crypto::decrypt_node_attribute( $attr, $key ),
										't'  => isset( $node['t'] ) ? $node['t'] : null,
										'k'  => isset( $node['k'] ) ? $node['k'] : null,
										's'  => isset( $node['s'] ) ? $node['s'] : null,
										'ts' => isset( $node['ts'] ) ? $node['ts'] : null,
									)
								);
							}
						}
					}
				} elseif ( $node['t'] == 2 ) {
					if ( empty( $parent_node_id ) ) {
						$parent_node_id = isset( $node['h'] ) ? $node['h'] : null;
					}
				}
				// phpcs:enable WordPress.PHP.StrictComparisons.LooseComparison
			}
		}

		$nodes = array();
		foreach ( $files as $file ) {
			if ( $file->get_parent_node_id() === $parent_node_id ) {
				$nodes[] = $file;
			}
		}

		return  $nodes;
	}

	/**
	 * Get download URL
	 *
	 * @param  string $node_id Node ID
	 * @return string
	 */
	public function get_download_url( $node_id ) {
		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a' => 'g',
						'g' => 1,
						'n' => $node_id,
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		if ( isset( $response[0]['g'] ) ) {
			return $response[0]['g'];
		}
	}

	/**
	 * Download file chunk
	 *
	 * @param  resource $file_stream      File stream
	 * @param  string   $node_key         Node key
	 * @param  integer  $file_range_start File range start
	 * @param  integer  $file_range_end   File range end
	 * @return boolean
	 */
	public function download_file_chunk( $file_stream, $node_key, $file_range_start = 0, $file_range_end = 0 ) {
		$node_key = $this->decrypt_node_key( $node_key );
		if ( empty( $node_key ) ) {
			return false;
		}

		// Create key
		$aes_key = array(
			$node_key[0] ^ $node_key[4],
			$node_key[1] ^ $node_key[5],
			$node_key[2] ^ $node_key[6],
			$node_key[3] ^ $node_key[7],
		);

		// Create IV
		$iv = array(
			$node_key[4],
			$node_key[5],
			$file_range_start / 0x1000000000,
			$file_range_start / 0x10,
		);

		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( $this->download_url );
		$api->set_option( CURLOPT_RANGE, sprintf( '%d-%d', $file_range_start, $file_range_end ) );

		try {
			$file_chunk_data = $api->make_request();
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		// Decrypt file chunk data
		$file_chunk_data = Ai1wmee_Mega_Crypto::aes_ctr_decrypt( $file_chunk_data, Ai1wmee_Mega_Utils::a32_to_str( $aes_key ), Ai1wmee_Mega_Utils::a32_to_str( $iv ) );

		// Copy file chunk data into file stream
		if ( fwrite( $file_stream, $file_chunk_data ) === false ) {
			throw new Ai1wmee_Error_Exception( __( 'Unable to save the file from Mega', AI1WMEE_PLUGIN_NAME ) );
		}

		return true;
	}

	/**
	 * Get upload URL
	 *
	 * @param  integer $file_size File size
	 * @return string
	 */
	public function get_upload_url( $file_size ) {
		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a' => 'u',
						's' => $file_size,
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		if ( isset( $response[0]['p'] ) ) {
			return $response[0]['p'];
		}
	}

	/**
	 * Upload file chunk
	 *
	 * @param  string  $file_chunk_data  File chunk data
	 * @param  array   $file_key         File key
	 * @param  array   $file_mac         File mac
	 * @param  integer $file_range_start File range start
	 * @return string
	 */
	public function upload_file_chunk( $file_chunk_data, &$file_key = array(), &$file_mac = array(), $file_range_start = 0 ) {
		// File key
		if ( empty( $file_key ) ) {
			$file_key = array( 0, 1, 2, 3, 4, 5 );
			for ( $i = 0; $i < 6; $i++ ) {
				$file_key[ $i ] = rand( 0, 0xFFFFFFFF );
			}
		} else {
			for ( $i = 0; $i < count( $file_key ); $i++ ) {
				$file_key[ $i ] = intval( $file_key[ $i ] );
			}
		}

		// File mac
		if ( empty( $file_mac ) ) {
			$file_mac = array( 0, 0, 0, 0 );
		} else {
			for ( $i = 0; $i < count( $file_mac ); $i++ ) {
				$file_mac[ $i ] = intval( $file_mac[ $i ] );
			}
		}

		// Create key
		$aes_key = array(
			$file_key[0],
			$file_key[1],
			$file_key[2],
			$file_key[3],
		);

		// Create IV
		$iv = array(
			$file_key[4],
			$file_key[5],
			$file_range_start / 0x1000000000,
			$file_range_start / 0x10,
		);

		// Create MAC
		$file_chunk_mac = array(
			$file_key[4],
			$file_key[5],
			$file_key[4],
			$file_key[5],
		);

		for ( $i = 0; $i < strlen( $file_chunk_data ); $i += 16 ) {
			$block = substr( $file_chunk_data, $i, 16 );
			if ( strlen( $block ) % 16 ) {
				$block .= str_repeat( "\0", ( 16 - strlen( $block ) % 16 ) );
			}

			$block = Ai1wmee_Mega_Utils::str_to_a32( $block );

			$file_chunk_mac = array(
				$file_chunk_mac[0] ^ $block[0],
				$file_chunk_mac[1] ^ $block[1],
				$file_chunk_mac[2] ^ $block[2],
				$file_chunk_mac[3] ^ $block[3],
			);

			$file_chunk_mac = Ai1wmee_Mega_Crypto::encrypt_aes_cbc_a32( $aes_key, $file_chunk_mac );
		}

		$file_mac = array(
			$file_mac[0] ^ $file_chunk_mac[0],
			$file_mac[1] ^ $file_chunk_mac[1],
			$file_mac[2] ^ $file_chunk_mac[2],
			$file_mac[3] ^ $file_chunk_mac[3],
		);

		$file_mac = Ai1wmee_Mega_Crypto::encrypt_aes_cbc_a32( $aes_key, $file_mac );

		// Encrypt file chunk data
		$file_chunk_data = Ai1wmee_Mega_Crypto::aes_ctr_encrypt( $file_chunk_data, Ai1wmee_Mega_Utils::a32_to_str( $aes_key ), Ai1wmee_Mega_Utils::a32_to_str( $iv ) );

		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( $this->upload_url );
		$api->set_path( "/{$file_range_start}" );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, $file_chunk_data );

		try {
			$response = $api->make_request();
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		return $response;
	}

	/**
	 * Upload complete file on Mega API
	 *
	 * @param  string $file_name File name
	 * @param  string $upload_id Upload ID
	 * @param  string $node_id   Node ID
	 * @param  array  $file_key  File key
	 * @param  array  $file_mac  File mac
	 * @return object
	 */
	public function upload_complete( $file_name, $upload_id, $node_id, &$file_key = array(), &$file_mac = array() ) {
		// File key
		for ( $i = 0; $i < count( $file_key ); $i++ ) {
			$file_key[ $i ] = intval( $file_key[ $i ] );
		}

		// File mac
		for ( $i = 0; $i < count( $file_mac ); $i++ ) {
			$file_mac[ $i ] = intval( $file_mac[ $i ] );
		}

		// Create key
		$aes_key = array(
			$file_key[0],
			$file_key[1],
			$file_key[2],
			$file_key[3],
		);

		// Create MAC
		$meta_mac = array(
			$file_mac[0] ^ $file_mac[1],
			$file_mac[2] ^ $file_mac[3],
		);

		$attributes = array( 'n' => $file_name );

		$enc_attributes = Ai1wmee_Mega_Crypto::encrypt_node_attribute( $attributes, $aes_key );

		$key = array(
			$file_key[0] ^ $file_key[4],
			$file_key[1] ^ $file_key[5],
			$file_key[2] ^ $meta_mac[0],
			$file_key[3] ^ $meta_mac[1],
			$file_key[4],
			$file_key[5],
			$meta_mac[0],
			$meta_mac[1],
		);

		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a' => 'p',
						't' => $node_id,
						'n' => array(
							array(
								'h' => $upload_id,
								't' => 0,
								'a' => Ai1wmee_Mega_Utils::base64_url_encode( $enc_attributes ),
								'k' => Ai1wmee_Mega_Utils::a32_to_base64( Ai1wmee_Mega_Crypto::encrypt_key( $this->user_key, $key ) ),
							),
						),
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		if ( isset( $response[0]['f'] ) ) {
			if ( ( $node = $response[0]['f'][0] ) ) {
				$file = new Ai1wmee_Mega_File_Info(
					array(
						'h'  => isset( $node['h'] ) ? $node['h'] : null,
						'p'  => isset( $node['p'] ) ? $node['p'] : null,
						'u'  => isset( $node['u'] ) ? $node['u'] : null,
						'a'  => Ai1wmee_Mega_Crypto::decrypt_node_attribute( Ai1wmee_Mega_Utils::base64_to_string( $node['a'] ), $this->decrypt_node_key( $node['k'] ) ),
						't'  => isset( $node['t'] ) ? $node['t'] : null,
						'k'  => isset( $node['k'] ) ? $node['k'] : null,
						's'  => isset( $node['s'] ) ? $node['s'] : null,
						'ts' => isset( $node['ts'] ) ? $node['ts'] : null,
					)
				);

				return $file;
			}
		}
	}

	/**
	 * Create node
	 *
	 * @param  string $node_name      Node name
	 * @param  string $parent_node_id Parent node ID
	 * @return string
	 */
	public function create( $node_name, $parent_node_id = null ) {
		// File key
		$file_key = array( 0, 0, 0, 0, 0, 0 );
		for ( $i = 0; $i < 6; $i++ ) {
			$file_key[ $i ] = rand( 0, 0xFFFFFFFF );
		}

		// Create key
		$aes_key = array(
			$file_key[0],
			$file_key[1],
			$file_key[2],
			$file_key[3],
		);

		$attributes = array( 'n' => $node_name );

		$enc_attributes = Ai1wmee_Mega_Crypto::encrypt_node_attribute( $attributes, $aes_key );

		$key = array(
			$file_key[0],
			$file_key[1],
			$file_key[2],
			$file_key[3],
		);

		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a' => 'p',
						't' => $parent_node_id,
						'n' => array(
							array(
								'h' => 'xxxxxxxx',
								't' => 1,
								'a' => Ai1wmee_Mega_Utils::base64_url_encode( $enc_attributes ),
								'k' => Ai1wmee_Mega_Utils::a32_to_base64( Ai1wmee_Mega_Crypto::encrypt_key( $this->user_key, $key ) ),
							),
						),
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		if ( isset( $response[0]['f'][0]['h'] ) ) {
			return $response[0]['f'][0]['h'];
		}
	}

	/**
	 * Delete node
	 *
	 * @param  string  $node_id Node ID
	 * @return boolean
	 */
	public function delete( $node_id ) {
		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a' => 'd',
						'n' => $node_id,
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			return false;
		}

		return true;
	}

	/**
	 * Get account info
	 *
	 * @return array
	 */
	public function get_account_info() {
		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a' => 'ug',
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		if ( isset( $response[0] ) ) {
			return $response[0];
		}
	}

	/**
	 * Get storage info
	 *
	 * @return array
	 */
	public function get_storage_info() {
		// HTTP request
		$api = new Ai1wmee_Mega_Curl;
		$api->set_base_url( self::MEGA_EUROPE_SERVER_URL );
		$api->set_path( '/cs' );
		$api->set_query( $this->rawurlencode_query( array( 'id' => ++$this->seq_number, 'sid' => $this->user_session_id ) ) );
		$api->set_header( 'Content-Type', 'application/json' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option(
			CURLOPT_POSTFIELDS,
			json_encode(
				array(
					array(
						'a'    => 'uq',
						'xfer' => 1,
						'strg' => 1,
					),
				)
			)
		);

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmee_Error_Exception $e ) {
			throw $e;
		}

		if ( isset( $response[0] ) ) {
			return $response[0];
		}
	}

	/**
	 * Decrypt node key
	 *
	 * @param  string $k Node key
	 * @return string
	 */
	public function decrypt_node_key( $k ) {
		static $cache = array();
		if ( ! isset( $cache[ $k ] ) ) {
			$cache[ $k ] = false;

			if ( ( $keys = explode( '/', $k ) ) ) {
				list( $value, $key ) = explode( ':', $keys[0] );
			}

			if ( ! empty( $key ) ) {
				$key = Ai1wmee_Mega_Utils::base64_to_a32( $key );
				$key = Ai1wmee_Mega_Crypto::decrypt_key( $this->user_key, $key );

				$cache[ $k ] = $key;
			}
		}

		return $cache[ $k ];
	}

	/**
	 * Encode URL query
	 *
	 * @param  array  $query Base query
	 * @return string
	 */
	public function rawurlencode_query( $query ) {
		return str_replace( '%7E', '~', array_map( 'rawurlencode', array_filter( $query, 'is_scalar' ) ) );
	}
}
