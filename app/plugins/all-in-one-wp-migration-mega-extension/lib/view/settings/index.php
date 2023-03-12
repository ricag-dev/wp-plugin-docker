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

<div class="ai1wm-container">
	<div class="ai1wm-row">
		<div class="ai1wm-left">
			<div class="ai1wm-holder">
				<h1><i class="ai1wm-icon-gear"></i> <?php _e( 'Mega Settings', AI1WMEE_PLUGIN_NAME ); ?></h1>
				<br />
				<br />

				<?php if ( Ai1wm_Message::has( 'success' ) ) : ?>
					<div class="ai1wm-message ai1wm-success-message">
						<p><?php echo Ai1wm_Message::get( 'success' ); ?></p>
					</div>
				<?php elseif ( Ai1wm_Message::has( 'error' ) ) : ?>
					<div class="ai1wm-message ai1wm-error-message">
						<p><?php echo Ai1wm_Message::get( 'error' ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( $user_session ) : ?>
					<p id="ai1wmee-mega-details">
						<?php _e( 'Retrieving Mega account details..', AI1WMEE_PLUGIN_NAME ); ?>
					</p>

					<div id="ai1wmee-mega-progress">
						<div id="ai1wmee-mega-progress-bar"></div>
					</div>

					<p id="ai1wmee-mega-space"></p>

					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php?action=ai1wmee_mega_revoke' ) ); ?>">
						<button type="submit" class="ai1wm-button-red" style="float: left;" name="ai1wmee_mega_logout" id="ai1wmee-mega-logout">
							<i class="ai1wm-icon-exit"></i>
							<?php _e( 'Sign Out from your mega account', AI1WMEE_PLUGIN_NAME ); ?>
						</button>
						<span class="spinner" style="float: left;"></span>
						<div class="ai1wm-clear"></div>
					</form>
				<?php else : ?>
					<div id="ai1wmee-credentials">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php?action=ai1wmee_mega_connection' ) ); ?>">
							<div class="ai1wm-field">
								<label for="ai1wmee-mega-user-email">
									<?php _e( 'E-mail', AI1WMEE_PLUGIN_NAME ); ?>
									<br />
									<input type="text" placeholder="<?php _e( 'Enter user email', AI1WMEE_PLUGIN_NAME ); ?>" id="ai1wmee-mega-user-email" class="ai1wmee-settings-key" name="ai1wmee_mega_user_email" value="<?php echo esc_attr( $user_email ); ?>" />
								</label>
							</div>

							<div class="ai1wm-field">
								<label for="ai1wmee-mega-user-password">
									<?php _e( 'Password', AI1WMEE_PLUGIN_NAME ); ?>
									<br />
									<input type="password" placeholder="<?php _e( 'Enter user password', AI1WMEE_PLUGIN_NAME ); ?>" id="ai1wmee-mega-user-password" class="ai1wmee-settings-key" name="ai1wmee_mega_user_password" value="<?php echo esc_attr( $user_password ); ?>" autocomplete="off" />
								</label>
							</div>

							<p>
								<button type="submit" class="ai1wm-button-blue" style="float: left;" name="ai1wmee_mega_link" id="ai1wmee-mega-link">
									<i class="ai1wm-icon-enter"></i>
									<?php _e( 'Sign in', AI1WMEE_PLUGIN_NAME ); ?>
								</button>
								<span class="spinner" style="float: left;"></span>
								<div class="ai1wm-clear"></div>
							</p>
						</form>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $user_session ) : ?>
				<div id="ai1wmee-backups" class="ai1wm-holder">
					<h1><i class="ai1wm-icon-gear"></i> <?php _e( 'Mega Backups', AI1WMEE_PLUGIN_NAME ); ?></h1>
					<br />
					<br />

					<?php if ( Ai1wm_Message::has( 'settings' ) ) : ?>
						<div class="ai1wm-message ai1wm-success-message">
							<p><?php echo Ai1wm_Message::get( 'settings' ); ?></p>
						</div>
					<?php endif; ?>

					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php?action=ai1wmee_mega_settings' ) ); ?>">
						<article class="ai1wmee-article">
							<h3><?php _e( 'Configure your backup plan', AI1WMEE_PLUGIN_NAME ); ?></h3>

							<p>
								<label for="ai1wmee-mega-cron-timestamp">
									<?php _e( 'Backup time:', AI1WMEE_PLUGIN_NAME ); ?>
									<input type="text" name="ai1wmee_mega_cron_timestamp" id="ai1wmee-mega-cron-timestamp" value="<?php echo esc_attr( get_date_from_gmt( date( 'Y-m-d H:i:s', $mega_cron_timestamp ), 'g:i a' ) ); ?>" autocomplete="off" />
									<code><?php echo ai1wm_get_timezone_string(); ?></code>
								</label>
							</p>

							<ul id="ai1wmee-mega-cron">
								<li>
									<label for="ai1wmee-mega-cron-hourly">
										<input type="checkbox" name="ai1wmee_mega_cron[]" id="ai1wmee-mega-cron-hourly" value="hourly" <?php echo in_array( 'hourly', $mega_backup_schedules ) ? 'checked' : null; ?> />
										<?php _e( 'Every hour', AI1WMEE_PLUGIN_NAME ); ?>
									</label>
								</li>
								<li>
									<label for="ai1wmee-mega-cron-daily">
										<input type="checkbox" name="ai1wmee_mega_cron[]" id="ai1wmee-mega-cron-daily" value="daily" <?php echo in_array( 'daily', $mega_backup_schedules ) ? 'checked' : null; ?> />
										<?php _e( 'Every day', AI1WMEE_PLUGIN_NAME ); ?>
									</label>
								</li>
								<li>
									<label for="ai1wmee-mega-cron-weekly">
										<input type="checkbox" name="ai1wmee_mega_cron[]" id="ai1wmee-mega-cron-weekly" value="weekly" <?php echo in_array( 'weekly', $mega_backup_schedules ) ? 'checked' : null; ?> />
										<?php _e( 'Every week', AI1WMEE_PLUGIN_NAME ); ?>
									</label>
								</li>
								<li>
									<label for="ai1wmee-mega-cron-monthly">
										<input type="checkbox" name="ai1wmee_mega_cron[]" id="ai1wmee-mega-cron-monthly" value="monthly" <?php echo in_array( 'monthly', $mega_backup_schedules ) ? 'checked' : null; ?> />
										<?php _e( 'Every month', AI1WMEE_PLUGIN_NAME ); ?>
									</label>
								</li>
							</ul>

							<p>
								<?php _e( 'Last backup date:', AI1WMEE_PLUGIN_NAME ); ?>
								<strong>
									<?php echo $last_backup_date; ?>
								</strong>
							</p>

							<p>
								<?php _e( 'Next backup date:', AI1WMEE_PLUGIN_NAME ); ?>
								<strong>
									<?php echo $next_backup_date; ?>
								</strong>
							</p>
						</article>

						<article class="ai1wmee-article">
							<h3><?php _e( 'Destination folder', AI1WMEE_PLUGIN_NAME ); ?></h3>
							<p id="ai1wmee-mega-folder-details">
								<span class="spinner" style="visibility: visible;"></span>
								<?php _e( 'Retrieving Mega folder details..', AI1WMEE_PLUGIN_NAME ); ?>
							</p>
							<p>
								<input type="hidden" name="ai1wmee_mega_node_id" id="ai1wmee-mega-node-id" />
								<button type="button" class="ai1wm-button-gray" name="ai1wmee_mega_change" id="ai1wmee-mega-change">
									<i class="ai1wm-icon-folder"></i>
									<?php _e( 'Change', AI1WMEE_PLUGIN_NAME ); ?>
								</button>
							</p>
						</article>

						<article class="ai1wmee-article">
							<h3><?php _e( 'Notification settings', AI1WMEE_PLUGIN_NAME ); ?></h3>
							<p>
								<label for="ai1wmee-mega-notify-toggle">
									<input type="checkbox" id="ai1wmee-mega-notify-toggle" name="ai1wmee_mega_notify_toggle" <?php echo empty( $notify_ok_toggle ) ? null : 'checked'; ?> />
									<?php _e( 'Send an email when a backup is complete', AI1WMEE_PLUGIN_NAME ); ?>
								</label>
							</p>

							<p>
								<label for="ai1wmee-mega-notify-error-toggle">
									<input type="checkbox" id="ai1wmee-mega-notify-error-toggle" name="ai1wmee_mega_notify_error_toggle" <?php echo empty( $notify_error_toggle ) ? null : 'checked'; ?> />
									<?php _e( 'Send an email if a backup fails', AI1WMEE_PLUGIN_NAME ); ?>
								</label>
							</p>

							<p>
								<label for="ai1wmee-mega-notify-email">
									<?php _e( 'Email address', AI1WMEE_PLUGIN_NAME ); ?>
									<br />
									<input class="ai1wmee-email" style="width: 15rem;" type="email" id="ai1wmee-mega-notify-email" name="ai1wmee_mega_notify_email" value="<?php echo esc_attr( $notify_email ); ?>" />
								</label>
							</p>
						</article>

						<article class="ai1wmee-article">
							<h3><?php _e( 'Retention settings', AI1WMEE_PLUGIN_NAME ); ?></h3>
							<p>
								<div class="ai1wm-field">
									<label for="ai1wmee-mega-backups">
										<?php _e( 'Keep the most recent', AI1WMEE_PLUGIN_NAME ); ?>
										<input style="width: 4.5em;" type="number" min="0" name="ai1wmee_mega_backups" id="ai1wmee-mega-backups" value="<?php echo intval( $backups ); ?>" />
									</label>
									<?php _e( 'backups. <small>Default: <strong>0</strong> unlimited</small>', AI1WMEE_PLUGIN_NAME ); ?>
								</div>

								<div class="ai1wm-field">
									<label for="ai1wmee-mega-total">
										<?php _e( 'Limit the total size of backups to', AI1WMEE_PLUGIN_NAME ); ?>
										<input style="width: 4.5em;" type="number" min="0" name="ai1wmee_mega_total" id="ai1wmee-mega-total" value="<?php echo intval( $total ); ?>" />
									</label>
									<select style="margin-top: -2px;" name="ai1wmee_mega_total_unit" id="ai1wmee-mega-total-unit">
										<option value="MB" <?php echo strpos( $total, 'MB' ) !== false ? 'selected="selected"' : null; ?>><?php _e( 'MB', AI1WMEE_PLUGIN_NAME ); ?></option>
										<option value="GB" <?php echo strpos( $total, 'GB' ) !== false ? 'selected="selected"' : null; ?>><?php _e( 'GB', AI1WMEE_PLUGIN_NAME ); ?></option>
									</select>
									<?php _e( '<small>Default: <strong>0</strong> unlimited</small>', AI1WMEE_PLUGIN_NAME ); ?>
								</div>

								<div class="ai1wm-field">
									<label for="ai1wmee-mega-days">
										<?php _e( 'Remove backups older than ', AI1WMEE_PLUGIN_NAME ); ?>
										<input style="width: 4.5em;" type="number" min="0" name="ai1wmee_mega_days" id="ai1wmee-mega-days" value="<?php echo intval( $days ); ?>" />
									</label>
									<?php _e( 'days. <small>Default: <strong>0</strong> off</small>', AI1WMEE_PLUGIN_NAME ); ?>
								</div>
							</p>
						</article>

						<p>
							<button type="submit" class="ai1wm-button-blue" name="ai1wmee_mega_update" id="ai1wmee-mega-update">
								<i class="ai1wm-icon-database"></i>
								<?php _e( 'Update', AI1WMEE_PLUGIN_NAME ); ?>
							</button>
						</p>
					</form>
				</div>
			<?php endif; ?>

			<?php do_action( 'ai1wmee_settings_left_end' ); ?>

		</div>
		<div class="ai1wm-right">
			<div class="ai1wm-sidebar">
				<div class="ai1wm-segment">
					<?php if ( ! AI1WM_DEBUG ) : ?>
						<?php include AI1WM_TEMPLATES_PATH . '/common/share-buttons.php'; ?>
					<?php endif; ?>

					<h2><?php _e( 'Leave Feedback', AI1WMEE_PLUGIN_NAME ); ?></h2>

					<?php include AI1WM_TEMPLATES_PATH . '/common/leave-feedback.php'; ?>

					<?php include AI1WMEE_TEMPLATES_PATH . '/common/trust-pilot.php'; ?>
				</div>
			</div>
		</div>
	</div>
</div>
