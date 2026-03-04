<?php
/**
 * Blank page template for Tabel Ucheta plugin.
 * Removes all WordPress theme markup (header, footer, sidebar, nav).
 * The page content (shortcode) is rendered directly.
 */
if (!defined('ABSPATH')) exit;
?>
<!DOCTYPE html>
<html lang="<?php echo get_locale(); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php wp_title('—', true, 'right'); ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* Reset: remove all default browser + WP styles */
html, body { margin: 0; padding: 0; background: #fafbfc; }
</style>
</head>
<body class="tabel-ucheta-page">
<?php
// Render the page content (which contains our shortcode)
while (have_posts()) {
    the_post();
    the_content();
}
?>
</body>
</html>
