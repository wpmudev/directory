<?php
/* sample functins page - rename it to function.php to make it active */

function thisischildtheme() { ?>
	<p>I'm a child theme, Hello there.</p>
<?php
}
add_action('wp_footer', 'thisischildtheme');
?>