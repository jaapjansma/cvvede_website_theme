<?php

/**
 * @file
 * Default simple view template to display a list of rows.
 *
 * @ingroup views_templates
 */
?>
<style type="text/css">
	article.node .views-field-event-start-date{
		display: block;
		color: #AEAEAE;
		margin-bottom: 16px;
	}
	article.node h1 {
		color: #2DAAE1;
	}
	article.node h1:hover {
		color: #82358C;
	}
</style>
<?php if (!empty($title)): ?>
  <h3><?php print $title; ?></h3>
<?php endif; ?>
<?php foreach ($rows as $id => $row): ?>
  <div <?php if ($classes_array[$id]) { print ' class="' . $classes_array[$id] .'"';  } ?>>
  	<article class="node">
  		<div style="display: block; padding: 16px; height: 100%;">
    	<?php print $row; ?>
    	</div>
    </article>
  </div>
<?php endforeach; ?>