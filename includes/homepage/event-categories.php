<?php
// Declare order of boxes and type
$boxes = array(
  17 => 'event-categories', // Ongoing Classes
  140 => 'product_cat', // Certifications
  22 => 'event-categories', // Private Sessions
  23 => 'event-categories', // Workshops
  37 => 'category', // Webinars
  38 => 'category' // Gallery
);

// Create the boxes
echo "<div id='home-event-cats'>";

  foreach ($boxes as $id => $type):

    // Get the category
    $category = get_term($id, $type);

    if ( ! $category || is_wp_error( $category ) ) {
      continue;
    }

    // Shorten up the description
    $excerpt = explode(' ', $category->description);
    $excerpt = implode(' ', array_slice($excerpt, 0, 21));

    // Get link and allow modifications through filters
    $link = get_term_link($id, $type);
    $filter_name = 'home_box_'.strtolower(str_replace(' ', '_', $category->name)).'_link';
    $link = apply_filters($filter_name, $link);

    // Class name
    $class = str_replace(' ', '-', $category->name);
    $class = strtolower($class);

    // Get the image
    // Output html
    echo '<div class="cats '.$class.'">';
    echo '<h3><a href="'.$link.'">'.$category->name.'</a></h3>';
    echo $excerpt;
    echo ' ...<a class="read-more" href="'.$link.'">Read more</a>';
    echo '</div>';
  endforeach;

echo "</div>"; // #home-event-cats
?>