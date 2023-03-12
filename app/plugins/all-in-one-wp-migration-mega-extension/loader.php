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

// Include all the files that you want to load in here
if ( defined( 'WP_CLI' ) ) {
	require_once AI1WMEE_VENDOR_PATH .
				DIRECTORY_SEPARATOR .
				'servmask' .
				DIRECTORY_SEPARATOR .
				'command' .
				DIRECTORY_SEPARATOR .
				'ai1wm-wp-cli.php';

	require_once AI1WMEE_VENDOR_PATH .
				DIRECTORY_SEPARATOR .
				'servmask' .
				DIRECTORY_SEPARATOR .
				'command' .
				DIRECTORY_SEPARATOR .
				'class-ai1wmee-mega-wp-cli-command.php';
}

require_once AI1WMEE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-main-controller.php';

require_once AI1WMEE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-export-controller.php';

require_once AI1WMEE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-import-controller.php';

require_once AI1WMEE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-settings-controller.php';

require_once AI1WMEE_CONTROLLER_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-stats-controller.php';

require_once AI1WMEE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-export-mega.php';

require_once AI1WMEE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-export-upload.php';

require_once AI1WMEE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-export-retention.php';

require_once AI1WMEE_EXPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-export-done.php';

require_once AI1WMEE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-import-mega.php';

require_once AI1WMEE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-import-download.php';

require_once AI1WMEE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-import-settings.php';

require_once AI1WMEE_IMPORT_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-import-database.php';

require_once AI1WMEE_MODEL_PATH .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-settings.php';

require_once AI1WMEE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'mega-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-mega-client.php';

require_once AI1WMEE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'mega-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-mega-curl.php';

require_once AI1WMEE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'mega-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-mega-rsa.php';

require_once AI1WMEE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'mega-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-mega-crypto.php';

require_once AI1WMEE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'mega-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-mega-utils.php';

require_once AI1WMEE_VENDOR_PATH .
			DIRECTORY_SEPARATOR .
			'mega-client' .
			DIRECTORY_SEPARATOR .
			'class-ai1wmee-mega-file-info.php';
