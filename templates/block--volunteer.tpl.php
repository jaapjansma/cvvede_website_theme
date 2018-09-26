<?php

/**
 * @file
 */
?>
<article class="<?php print $classes; ?> sibling blue"<?php print $attributes; ?> data-bid="<?php print $block->bid ?>">
  <?php print render($title_prefix); ?>
  <?php if ($block->subject): ?>
    <h1 class="title"<?php print $title_attributes; ?>><?php print $block->subject ?></h1>
  <?php endif;?>
  <?php print render($title_suffix); ?>
  <?php print $content; ?>
</article><!-- /block -->
