<?php

/**
 * @file
 */
?>


<?php 
	$field_date = $content['body']['#object']->field_date['und'][0]['value'];
	if ($page): ?>

<article class="<?php print $classes; ?>" data-nid="<?php print $node->nid; ?>" >

  <?php if ($title_prefix || $title_suffix || $display_submitted || $unpublished || $title): ?>
    <header>
	  <time datetime="<?php print format_date(strtotime($field_date), 'custom', 'Y-m-d');?>"><?php print format_date(strtotime($field_date), 'custom', 'j F Y');?></time>	    
      <?php print render($title_prefix); ?>
      <?php if ($title): ?>
        <h1<?php print $title_attributes; ?>><?php print $title; ?></h1>
      <?php endif; ?>
      <?php print render($title_suffix); ?>

      <?php if ($unpublished): ?>
        <p class="unpublished"><?php print t('Unpublished'); ?></p>
      <?php endif; ?>
    </header>
  <?php endif; ?>

  <div class="content">
    <?php
    // We hide the comments to render below.
    hide($content['comments']);
    hide($content['links']);
    hide($content['field_date']);    
    print render($content);
    ?>
  </div><!-- /content -->

</article><!-- /node -->

<?php else: ?>

<article class="<?php print $classes; ?>" data-nid="<?php print $node->nid; ?>"><a href="<?php print $node_url;  ?>" title="Ga naar hulpvraag">
	<time datetime="<?php print format_date(strtotime($field_date), 'custom', 'Y-m-d');?>"><?php print format_date(strtotime($field_date), 'custom', 'j F Y');?></time>
	<h1><?php print $title; ?></h1>
</a></article>

<?php endif; ?>