<section class="row wanted">
    <header>
        <h1>Gezocht</h1><a href="/ik-wil-helpen/hulpvragen/" title="Toon alle hulpvragen" class="cta cta-blue">Alle hulpvragen</a>
    </header>

  <?php foreach ($variables['cvve_nodes']['cvve_wanted']['nodes'] as $wanted): ?>
      <article><a href="<?php print url('node/'.$wanted->nid);  ?>" title="Ga naar hulpvraag">
              <time datetime="<?php print format_date($wanted->created, 'custom', 'Y-m-d');?>"><?php print format_date($wanted->created, 'custom', 'j F Y');?></time>
              <h1><?php print $wanted->title; ?></h1>
          </a></article>
  <?php endforeach; ?>
</section>

<div class="<?php print $classes; ?>"<?php print $attributes; ?> data-bid="<?php print $block->bid ?>">
  <?php print render($title_prefix); ?>
  <?php if ($block->subject): ?>
    <h3 class="title"<?php print $title_attributes; ?>><?php print $block->subject ?></h3>
  <?php endif;?>
  <?php print render($title_suffix); ?>
  <?php print $content; ?>
</div><!-- /block -->
