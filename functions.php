<?php

function theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function favicon_link() {
	echo '<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />' . "\n";
}
add_action( 'wp_head', 'favicon_link' );

/**
 * Antispam Bee filter for custom RegExp patterns
 */
add_action(
	'init',
	'antispam_bee_patterns'
);
function antispam_bee_patterns() {
	add_filter(
		'antispam_bee_patterns',
		'antispam_bee_add_custom_patterns'
	);
}
function antispam_bee_add_custom_patterns($patterns) {
	// Pattern for phony author names.
	// Fun Fact: The last one is 'Prada' in Japanese.
	$patterns[] = array(
		'author' => 'moncler|north face|vuitton|handbag|burberry|outlet|dress|maillot|oakley|ralph lauren|ray ban|iphone|プラダ'
	);

	// Pattern for phony web pages.
	$patterns[] = array(
		'host' => '^(www\.)?fkbook\.co\.uk$|^(www\.)?nsru\.net$|^(www\.)?goo\.gl$|^(www\.)?bit\.ly$'
	);

	// Pattern for text containing strings like 'targetted visitors'.
	$patterns[] = array(
		'body' => 'target[t]?ed (visitors|traffic)'
	);

	return $patterns;
}

/**
 *  Log login failures with something else than 200 OK
 *  Copied from https://kovshenin.com/2014/fail2ban-wordpress-nginx/
 */
function my_login_failed_403() {
    status_header( 403 );
}
add_action( 'wp_login_failed', 'my_login_failed_403' );
