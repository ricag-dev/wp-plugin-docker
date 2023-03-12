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

class Ai1wmee_Mega_Utils {

	/**
	 * Unsubstitute standard base64 special characters, restore padding.
	 *
	 * @param  string $data Input data
	 * @return string
	 */
	public static function base64_url_decode( $data ) {
		$data .= substr( '==', ( 2 - strlen( $data ) * 3 ) & 3 );
		$data  = str_replace( array( '-', '_', ',' ), array( '+', '/', '' ), $data );

		return base64_decode( $data );
	}

	/**
	 * Substitute standard base64 special characters to prevent json escaping
	 *
	 * @param  string $data Input data
	 * @return string
	 */
	public static function base64_url_encode( $data ) {
		$data = base64_encode( $data );
		$data = str_replace( array( '+', '/', '=' ), array( '-', '_', '' ), $data );

		return $data;
	}

	/**
	 * Array of 32 bit words to string (bin endian)
	 *
	 * @param  array $a Input data
	 * @return mixed
	 */
	public static function a32_to_str( $a ) {
		return call_user_func_array( 'pack', array_merge( array( 'N*' ), $a ) );
	}

	/**
	 * Array of 32 bit words to base 64 (bin endian)
	 *
	 * @param  array $a Input data
	 * @return mixed
	 */
	public static function a32_to_base64( $a ) {
		return self::base64_url_encode( self::a32_to_str( $a ) );
	}

	/**
	 * String to array of 32bit words (big endian)
	 *
	 * @param  string $b Input data
	 * @return array
	 */
	public static function str_to_a32( $b ) {
		$padding = ( ( ( strlen( $b ) + 3 ) >> 2 ) * 4 ) - strlen( $b );

		if ( $padding > 0 ) {
			$b .= str_repeat( "\0", $padding );
		}

		return array_values( unpack( 'N*', $b ) );
	}

	/**
	 * Base 64 to array of 32 bit words (bin endian)
	 *
	 * @param  array $a Input data
	 * @return mixed
	 */
	public static function base64_to_a32( $s ) {
		return self::str_to_a32( self::base64_url_decode( $s ) );
	}

	/**
	 * String to binary string (ab_to_base64)
	 *
	 * @param  string $str Input data
	 * @return string
	 */
	public static function str_to_base64( $str ) {
		return self::base64_url_encode( $str );
	}

	/**
	 * Binary string to string (base64_to_ab)
	 *
	 * @param  string $base_str Input data
	 * @return string
	 */
	public static function base64_to_string( $base_str ) {
		return self::str_pad( self::base64_url_decode( $base_str ) );
	}

	/**
	 * Binary string depadding (ab_to_str_depad)
	 *
	 * @param  string $bin_str Input data
	 * @return string
	 */
	public static function str_depad( $bin_str ) {
		for ( $i = strlen( $bin_str ); $i-- && ! self::uniord( $bin_str[ $i ] ); ) {
		}

		$bin_str = substr( $bin_str, 0, $i + 1 );

		return $bin_str;
	}

	/**
	 * Binary string 0-padded to AES block size (str_to_ab)
	 *
	 * @param  string $b Input data
	 * @return string
	 */
	public static function str_pad( $b ) {
		$padding = 16 - ( ( strlen( $b ) - 1 ) & 15 );
		return $b . str_repeat( "\0", $padding - 1 );
	}

	public static function mpi2b( $s ) {
		$s   = bin2hex( substr( $s, 2 ) );
		$len = strlen( $s );

		$n = 0;

		for ( $i = 0; $i < $len; $i++ ) {
			$n = bcadd( $n, bcmul( hexdec( $s[ $i ] ), bcpow( 16, $len - $i - 1 ) ) );
		}

		return $n;
	}

	public static function to8( $unicode ) {
		return $unicode;
	}

	public static function from8( $utf8 ) {
		return $utf8;
	}

	public static function uniord( $u ) {
		return hexdec( bin2hex( $u ) );
	}

	public static function get_chunks( $size ) {
		$chunks = array();

		$p  = 0;
		$pp = 0;
		$i  = 1;

		while ( $i <= 8 && $p < ( $size - $i * 0x20000 ) ) {
			$chunks[ $p ] = $i * 0x20000;

			$pp = $p;
			$p += $chunks[ $p ];
			$i++;
		}

		while ( $p < $size ) {
			$chunks[ $p ] = 0x100000;

			$pp = $p;
			$p += $chunks[ $p ];
		}

		$chunks[ $pp ] = $size - $pp;
		if ( empty( $chunks[ $pp ] ) ) {
			unset( $chunks[ $pp ] );
		}

		return $chunks;
	}
}
