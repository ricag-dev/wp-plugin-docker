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
?>

<div id="ai1wmee-import-modal" class="ai1wmee-modal-container" role="dialog" tabindex="-1">
	<div class="ai1wmee-modal-content" v-if="items !== false">
		<div class="ai1wmee-file-browser">
			<div class="ai1wmee-path-list">
				<template v-for="(item, index) in path">
					<span v-if="index !== path.length - 1">
						<span class="ai1wmee-path-item" v-on:click="browse(item, index)" v-html="item.name"></span>
						<i class="ai1wm-icon-chevron-right"></i>
					</span>
					<span v-else>
						<span class="ai1wmee-path-item" style="cursor: default" v-html="item.name"></span>
					</span>
				</template>
			</div>
			<div class="ai1wmee-file-list" v-if="items !== false && items.length > 0">
				<div class="ai1wmee-file-item">
					<span class="ai1wmee-file-name-header">
						<?php _e( 'Name', AI1WMEE_PLUGIN_NAME ); ?>
					</span>
					<span class="ai1wmee-file-date-header">
						<?php _e( 'Date', AI1WMEE_PLUGIN_NAME ); ?>
					</span>
					<span class="ai1wmee-file-size-header">
						<?php _e( 'Size', AI1WMEE_PLUGIN_NAME ); ?>
					</span>
				</div>
			</div>
			<ul class="ai1wmee-file-list">
				<li v-for="item in items" v-on:click="browse(item)" class="ai1wmee-file-item">
					<span class="ai1wmee-file-name">
						<i v-bind:class="item.type | icon"></i>
						{{ item.name }}
					</span>
					<span class="ai1wmee-file-date">{{ item.date }}</span>
					<span class="ai1wmee-file-size">{{ item.size }}</span>
				</li>
				<li
					v-if="items !== false && items.length === 0"
					style="text-align: center; cursor: default;"
					class="ai1wmee-file-item">
					<strong><?php _e( 'No folders or files to list. Click on the navbar to go back.', AI1WMEE_PLUGIN_NAME ); ?></strong>
				</li>
				<li class="ai1wmee-file-info" v-if="num_hidden_files === 1">
					{{ num_hidden_files }}
					<?php _e( 'file is hidden', AI1WMEE_PLUGIN_NAME ); ?>
					<i class="ai1wm-icon-help" title="<?php _e( 'Only wpress backups are listed', AI1WMEE_PLUGIN_NAME ); ?>"></i>
				</li>
				<li class="ai1wmee-file-info" v-if="num_hidden_files > 1">
					{{ num_hidden_files }}
					<?php _e( 'files are hidden', AI1WMEE_PLUGIN_NAME ); ?>
					<i class="ai1wm-icon-help" title="<?php _e( 'Only wpress backups are listed', AI1WMEE_PLUGIN_NAME ); ?>"></i>
				</li>
			</ul>
		</div>
	</div>

	<div class="ai1wmee-modal-loader" v-if="items === false">
		<p>
			<span style="float: none; visibility: visible;" class="spinner"></span>
		</p>
		<p>
			<span class="ai1wmee-contact-mega">
				<?php _e( 'Connecting to Mega ...', AI1WMEE_PLUGIN_NAME ); ?>
			</span>
		</p>
	</div>

	<div class="ai1wmee-modal-legend">
		<p style="box-shadow: 0px -1px 1px 0px rgb(221, 221, 221);" class="ai1wmee-file-info" v-if="items !== false">
			<?php _e( 'Open with a click', AI1WMEE_PLUGIN_NAME ); ?>
		</p>
	</div>

	<div class="ai1wmee-modal-action">
		<transition>
			<p class="ai1wmee-selected-file" v-if="file">
				<i class="ai1wm-icon-file-zip"></i>
				{{ file.name }}
			</p>
		</transition>

		<p class="ai1wmee-justified-container">
			<button type="button" class="ai1wm-button-red" v-on:click="cancel">
				<?php _e( 'Close', AI1WMEE_PLUGIN_NAME ); ?>
			</button>
			<button type="button" class="ai1wm-button-green" v-if="file" v-on:click="restore(file)">
				<i class="ai1wm-icon-publish"></i>
				<?php _e( 'Import', AI1WMEE_PLUGIN_NAME ); ?>
			</button>
		</p>
	</div>
</div>

<div id="ai1wmee-import-overlay" class="ai1wmee-overlay"></div>
