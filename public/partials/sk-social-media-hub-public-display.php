<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 */
?>
<?php //util::debug( $feeds ); ?>

<?php if(!empty( $feeds )) : ?>

  <?php //util::debug( $feeds ); ?>
  <div class="social-media-hub">
    <?php foreach( $feeds as $feed ) : ?>
      <a href="<?php echo $feed->service_link; ?>" class="feed-item">
      
      
        <div class="feed-content">
        <?php if( ! empty( $feed->service_image_url ) ) : ?>
        <div class="feed-image"><img src="<?php echo $feed->service_image_url; ?>"></div>
        <?php endif; ?>
        <p><?php echo $this->string_cut( $feed->post_content, 200 ); ?></p>
        </div><!-- .feed-content -->
        <div class="feed-footer">
          <span class="feed-sender-meta"><?php echo mb_strtolower( date_i18n( 'd M, Y', strtotime( $feed->post_date ) ) ); ?> - <?php echo $feed->service_user; ?></span>
          <?php SK_Social_Media_Hub_Public::get_feed_icon( $feed ); ?>
        </div><!-- .feed-footer -->
      </a>
    <?php endforeach; ?>
  </div><!-- .social-media-hub -->
<?php endif; ?>