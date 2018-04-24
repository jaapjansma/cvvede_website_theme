<?php

/**
* @file
*/
?>

<header>
	<a href="/" title="Home" id="logo"><span>CVVE</span></a>
	<nav id="topbar">	
	<?php 
/*
		if (!empty($secondary_menu)): 
        print theme('links', array(
			'links' => $secondary_menu,
			'attributes' => array(
				'id' => 'secondary',
				'class' => array('sub-menu'),
			),
        ));
		endif;
*/
		?>
		<ul class="some-list">
			<li class="mobile-hidden"><a href="mailto:info@cvvede.nl" title="Mail CVVE" class="email">info@cvvede.nl</a></li>
			<li class="mobile-hidden"><a href="https://www.facebook.com/coordinatievve" target="_blank" title="Facebook" class="some facebook"><span>Facebook</span></a></li>
			<li class="mobile-hidden"><a href="https://www.instagram.com/cvvede/" target="_blank" title="Instagram" class="some instagram"><span>Instagram</span></a></li>
			<li class="mobile-hidden"><a href="https://twitter.com/edecvv" target="_blank" title="Twitter" class="some twitter"><span>Twitter</span></a></li>
			<li><a href="#" id="mobilemenu_trigger"><span class="hamburger-box"><span class="hamburger-inner"></span></span></a></li>
		</ul>
	</nav>	
	<?php if (!empty($main_menu)): ?>		
		<nav id="mainnav">
		<?php
        print theme('links', array(
			'links' => $main_menu,
			'attributes' => array(
				'id' => 'primary',
				'class' => array('main-menu'),
			),
        ));
        ?>
		</nav>
	<?php endif;?>
</header>

<section id="teaser">
	<div class="intro"><p>CVVE verbindt mensen vanuit een vluchtsituatie en Edenaren met elkaar.</p></div>
</section>
	
<section id="main">

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

<section class="row buddy">
	<article class="sibling purple">
		<h1>Are you looking for a buddy?</h1>
		<p>Are you a refugee and do you live in Ede? Would you like to get in touch with Dutch people? To do fun things, drink a cup of tea, eat together, cook, play a game, go to the market or something else?</p>
		<a href="https://crm.cvvede.nl/vwn/maatje-statushouders" class="cta cta-purple">Apply for a dutch buddy</a>			
	</article>
	
	<?php print render($page['buddy']); ?>
	
	<article class="dummy"></article>
</section>

<section class="row news">
	<header>
		<h1>Actueel</h1><a href="/over-cvve/actueel/" title="Toon alle berichten" class="cta cta-blue">Alle berichten</a>
	</header>
	<?php foreach ($variables['cvve_nodes']['article']['nodes'] as $article): ?>
		<article><a href="<?php print url('node/'.$article->nid);  ?>" title="Ga naar hulpvraag">
			<time datetime="<?php print format_date($article->created, 'custom', 'Y-m-d');?>"><?php print format_date($article->created, 'custom', 'j F Y');?></time>
			<h1><?php print $article->title; ?></h1>
			<p><?php print $article->body['und'][0]['summary']; ?></p>
		</a></article>		
	<?php endforeach; ?>
</section>

<section class="row agenda">
	<header>
		<h1>Agenda</h1><a href="/agenda/" title="Toon alle gebeurtenissen" class="cta cta-blue">Alle gebeurtenissen</a>
	</header>
	<?php foreach($variables['events'] as $event) { ?>
		<article class="<?php print $event['classes'];?>">
			<?php if (!empty($event['link'])) { ?><a style="display: inline; padding: 0px;" href="<?php echo $event['link']; ?>"> <?php } ?>
			<div style="display: block; padding: 16px; height: 100%;">
				<time datetime="<?php print $event['date_time']->format('d-m-Y H:i');?>"><?php print $event['date_time']->format('d-m-Y H:i');?></time>
				<h1><?php print $event['title']; ?></h1>
				<p></p>
			</div>
			<?php if (!empty($event['link'])) { ?></a> <?php } ?>
		</article>
	<?php } ?>
</section>
	
</section>

<nav id="cta"><span>Wil je vluchtelingen helpen inburgeren in Ede?</span><a href="/ik-wil-helpen/" class="cta cta-white">Ik wil helpen</a></nav>
	
<footer>
	<nav class="footer-col extra-nav">
		<?php print render($page['footer1']); ?>
	</nav><nav class="footer-col extra-nav">
		<?php print render($page['footer2']); ?>
	</nav><nav class="footer-col extra-nav">
		<?php print render($page['footer3']); ?>
	</nav><nav class="footer-col">
		<?php print render($page['footer4']); ?>	
	</nav>
</footer>
