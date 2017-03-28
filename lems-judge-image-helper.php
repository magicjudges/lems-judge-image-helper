<?php
/*
Plugin Name: Lems Judge Image Helper
Plugin URI:  http://www.jasonlemahieu.com
Description: Easily create judge tooltips and judge images.
Version: 0.2
Author: Jason Lemahieu, with lots of CSS help from Steffen Baumgart
Author URI: http://www.jasonlemahieu.com
*/

if ( ! defined( 'WP_CONTENT_DIR' ) )
	define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if ( ! defined( 'WP_CONTENT_FOLDER' ) )
	define('WP_CONTENT_FOLDER', str_replace( ABSPATH, '/', WP_CONTENT_DIR ));

// plugin directory
define('LEMS_JUDGE_DIR', WP_CONTENT_FOLDER . '/plugins/lems-judge-image-helper');

define('JUDGEAPPS_BASEURL', 'https://apps.magicjudges.org');

add_action( 'init', 'lems_judge_image_helper_init' );

function lems_judge_image_helper_init() {
	if ( ! is_admin() ) {
		wp_enqueue_style( 'lems_judge_hovers', get_bloginfo( 'wpurl' ) . LEMS_JUDGE_DIR . '/css/mouseover_avatar.css' );
	}
}

function get_source_from_dci( $dci, $size = 200 ) {
	return JUDGEAPPS_BASEURL . "/dci/avatar?dci=" . $dci . "&size=$size";
}

function lems_judge_hover_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'dci' => ''
	), $atts ) );

	$dci = (int) $dci;
	$src = get_source_from_dci( $dci );
	$judgeapps_url = JUDGEAPPS_BASEURL . '/judges/dci/' . $dci;

	$html = "<span class='judge-tooltip'><a href='$judgeapps_url' >" . $content . "</a><span class='avatar'><img width='200' height='200' src='" . $src . "'></span></span>";

	return $html;
}

add_shortcode( 'judge', 'lems_judge_hover_shortcode' );

function lems_judge_quicktags( $hook ) {
	if (wp_script_is('quicktags')) {

		?>
		<script type="text/javascript">
			QTags.addButton('judge', 'Judge Hover', '[judge dci=]', '[/judge]');
			QTags.addButton('judge-img', 'Judge Image', '[judgeimg align=none dci=]', '[/judgeimg]');
		</script>
		<?php
	} // if quicktags
}

add_action( 'admin_print_footer_scripts', 'lems_judge_quicktags', 100 );

function lems_judge_image_shortcode( $atts, $content = null ) {
	extract( shortcode_atts( array(
		'dci' => '',
		'align' => 'none'
	), $atts ) );

	$dci = (float) $dci;
	$judgeapps_img = get_source_from_dci( $dci );
	$judgeapps_url = JUDGEAPPS_BASEURL . '/judges/dci/' . $dci;

	switch ( $align ) {
		case 'left':
			$align = 'alignleft';
			break;
		case 'right':
			$align = 'alignright';
			break;
		case 'center':
			$align = 'aligncenter';
			break;
		default:
			$align = 'alignnone';
	}

	$html = '<div class="wp-caption ' . $align . ' judgeimg"><a href="' . $judgeapps_url . '"><img src=' . $judgeapps_img .
		' alt="' . wp_kses( $content, array() ) . '"></a>';

	if ( null !== $content ) {
		$allowed_html = array(
			'a' => array(
				'href' => array(),
				'title' => array(),
				'target' => array()
			),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
			'p' => array()
		);
		$html .= '<p class="wp-caption-text">' . wp_kses( $content, $allowed_html ) . '</p>';
	}
	$html .= '</div>';

	return $html;
}

add_shortcode( 'judge-img', 'lems_judge_image_shortcode' );
add_shortcode( 'judgeimg', 'lems_judge_image_shortcode' );
