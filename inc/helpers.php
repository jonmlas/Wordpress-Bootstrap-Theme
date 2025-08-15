<?php
// Helper functions for displaying any value or array.
function print_array($value) {
    echo '<pre style="
        background: #1e1e1e;
        color: #9cdcfe;
        padding: 15px 20px;
        border-radius: 6px;
        font-family: Consolas, Monaco, monospace;
        font-size: 14px;
        line-height: 1.4em;
        overflow-x: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
        box-shadow: 0 0 12px #0f85e7cc;
        border: 1px solid #0f85e7;
    ">';

    if (is_array($value) || is_object($value)) {
        $output = print_r($value, true);
        $output = htmlspecialchars($output, ENT_QUOTES);
        $output = preg_replace('/(\[[^\]]+\])/i', '<span style="color:#4ec9b0;">$1</span>', $output);
        $output = str_replace('=>', '<span style="color:#d4d4d4;">=></span>', $output);
        echo $output;
    } else {
        echo htmlspecialchars(var_export($value, true));
    }

    echo '</pre>';
}

// Function to pluralize a given word based on sophisticated rules
function sophisticated_pluralize($word) {
    // Sophisticated pluralization rules
    $plural_rules = array(
        '/(quiz)$/i' => '\1zes',      // quizzes
        '/^(ox)$/i' => '\1en',        // oxen
        '/([m|l])ouse$/i' => '\1ice', // mice, louse
        '/(matr|vert|ind)ix|ex$/i' => '\1ices', // matrices, vertices
        '/(x|ch|ss|sh)$/i' => '\1es', // boxes, watches, classes
        '/([^aeiouy]|qu)y$/i' => '\1ies', // parties
        '/(hive)$/i' => '\1s',       // hives
        '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves', // lives, wolves
        '/(shea|lea|loa|thie)f$/i' => '\1ves', // sheaves, thieves
        '/sis$/i' => 'ses',          // analyses
        '/([ti])um$/i' => '\1a',     // data, criteria
        '/(tomat|potat|ech|her)o$/i' => '\1oes', // tomatoes, potatoes
        '/(bu)s$/i' => '\1ses',      // buses
        '/(alias)$/i' => '\1es',     // aliases
        '/(octop)us$/i' => '\1i',    // octopi
        '/(ax|test)is$/i' => '\1es', // axes, tests
        '/us$/i' => 'i',             // fungi, nuclei
        '/s$/i' => 's',              // no change (compatibility)
        '/$/' => 's'                 // default case
    );

    // Apply pluralization rules
    foreach ($plural_rules as $rule => $replacement) {
        if (preg_match($rule, $word)) {
            return preg_replace($rule, $replacement, $word);
        }
    }

    return $word; // Default to returning the original word if no rules match
}

// Helper function to clean shortcode content
if (!function_exists('zg_clean_shortcode_content')) {
    function zg_clean_shortcode_content($content) {
        $content = shortcode_unautop($content);
        $content = preg_replace('/<br\s*\/?>/i', '', $content);
        return trim($content);
    }
}

/**
 * Get shortcodes from a file.
 *
 * This function reads a PHP file and extracts all shortcode tags defined within it.
 *
 * @param string $file The path to the PHP file.
 * @return array An array of shortcode tags found in the file.
 */
function theme_load_shortcodes() {
    $shortcode_dir = get_template_directory() . '/inc/shortcodes/';
    
    foreach (glob($shortcode_dir . '*.php') as $file) {
        include_once $file;
    }
}
add_action('init', 'theme_load_shortcodes');

function zg_get_all_shortcodes_config() {
    $shortcodes_config = [];
    foreach (glob(get_template_directory() . '/inc/shortcodes/*.php') as $file) {
        include_once $file;
        $config_fn = 'shortcode_config_' . basename($file, '.php');
        if (function_exists($config_fn)) {
            $shortcodes_config[] = $config_fn();
        }
    }
    return $shortcodes_config;
}

add_action('admin_enqueue_scripts', function() {
    wp_localize_script('insert-shortcode-js', 'shortcodes', zg_get_all_shortcodes_config());
});