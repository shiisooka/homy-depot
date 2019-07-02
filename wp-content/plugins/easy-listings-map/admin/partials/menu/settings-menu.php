<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var array $view_args[
 *      @type array tabs
 * ]
 */

$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $view_args['tabs'] ) ? $_GET['tab'] : 'general';

$ignore = get_option( 'ignore_asana_plugins_messages', 0 );
if ( 1 === absint( $ignore ) ) {
	?>
	<div class="update-nag asn-advertise">
		<div class="asn-adv-logo"></div>
		<p class="asn-adv-title">Do you want easy to use and advanced plugins for Easy Property Listings with less price and better support?</p>
		<p class="asn-adv-body">We are working on easy to use and advanced plugins for <strong>Easy Property Listings</strong> that you can find them <a href="http://www.asanaplugins.com/?utm_source=asanaplugins_messages&utm_medium=link"><strong>here</strong></a>.</p>
		<p class="asn-adv-body"><strong>Please support us</strong> by purchasing our plugins and we <strong>promise</strong> to creating easy to use and advanced plugins for <strong>Easy Property Listings</strong> with less price and better support.</p>
		<p class="asn-adv-body">Do you want custom works on <strong>Easy Property Listings</strong> <a href="http://www.asanaplugins.com/contact/?utm_source=asanaplugins_messages&utm_medium=link" target="_blank">Contact US</a>.</p>
		<p class="asn-adv-body">Please note that without your supports we can't <strong>live</strong>.</p>
		<ul class="asn-adv-body">
			<li>
				<span class="dashicons dashicons-media-text"></span>
				<a href="https://asanaplugins.freshdesk.com/support/solutions" target="_blank">Documentation</a>
			</li>
			<li>
				<span class="dashicons dashicons-sos"></span>
				<a href="https://asanaplugins.freshdesk.com/support/tickets/new" target="_blank">Premium Support</a>
			</li>
			<li>
				<span class="dashicons dashicons-admin-plugins"></span>
				<a href="http://www.asanaplugins.com/products/?utm_source=asanaplugins_messages&utm_medium=link" target="_blank">Plugins</a>
			</li>
		</ul>
	</div>
	<?php
}
?>
<div class="wrap">
	<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $view_args['tabs'] as $tab_id => $tab_name ) {
			$tab_url = esc_url_raw( add_query_arg( array(
				'settings-updated' => false,
				'tab'              => $tab_id,
			) ) );

			$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

			echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">' .
					esc_html( $tab_name ) . '</a>';
		}
		?>
	</h2>
	<div id="tab-container">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'elm_settings' );
			do_settings_sections( 'elm_settings_' . $active_tab );

			submit_button();
			?>
		</form>
	</div>
</div>
