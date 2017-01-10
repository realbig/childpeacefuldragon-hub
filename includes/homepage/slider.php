<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Slider Component
 */

if (function_exists('soliloquy_slider')) {
    echo "<section id='home-slider' class='home-section'><div class='wrapper'>";
    soliloquy_slider('126');
    echo "</div></section>";
}
?>