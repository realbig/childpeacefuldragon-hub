<?php
// Query some classes
$pd_classes = new WP_Query( array(
  'post_type' => 'event',
  'posts_per_page' => 5
) );
if ( $pd_classes->have_posts() ):
?>
<section id="our-team" class="home-section">

	<div class="wrapper">

		<header class="section-title">
			<h1>Our Classes</h1>
		</header>

	</div><!-- /.wrapper -->
<div class="widget widget_woothemes_our_team">
	<div class="team-members component effect-fade">
<?php // Start the classes loop
while ( $pd_classes->have_posts() ) : $pd_classes->the_post();
$pd_class_id = get_the_id();
$pd_class_thumb = get_the_post_thumbnail($pd_class_id, array(300,300), array ('class' => 'alignleft'));
$pd_class_title = get_the_title();
$pd_class_link = get_permalink();
$pd_class_date = get_post_meta( $pd_class_id, '_event_start_date', true );
$pd_class_ex = get_the_excerpt();
?>
	<div class="team-member">
		<figure>
			<?php echo $pd_class_thumb; ?>
		</figure>
		<div class="team-member-content">
			<h3>
				<a href="<?php echo $pd_class_link; ?>"><?php echo $pd_class_title; ?></a>
			</h3>
			<p class="role"><?php echo $pd_class_date; ?></p>
			<div class="team-member-text">
				<p><?php echo $pd_class_ex; ?></p>
			</div>
		</div>
	</div>
<?php endwhile; ?>
	</div>
</div>
</section><!-- /#our-team -->
<?php
wp_reset_postdata();
endif;
?>