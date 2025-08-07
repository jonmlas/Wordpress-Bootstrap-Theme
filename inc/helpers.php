<?php

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