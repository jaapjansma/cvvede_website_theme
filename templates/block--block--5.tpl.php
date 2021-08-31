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
