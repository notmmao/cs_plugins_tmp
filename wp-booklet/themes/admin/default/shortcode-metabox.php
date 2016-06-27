<?php if ( $post->post_status == 'publish' ) : ?>
<p>Copy and paste the following shortcode to the page/post the booklet must appear on.</p>
<input class="widefat wp-booklet-shortcode-display" type="text" readonly value="[wp-booklet id=<?php echo $booklet->get_shortcode_id() ?>]"/>
<?php else : ?>
<p>Publish this post to use the shortcode.</code>
<?php endif ?>