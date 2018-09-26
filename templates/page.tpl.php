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

<?php if ($breadcrumb): ?>
    <nav id="breadcrumb"><?php print $breadcrumb; ?></nav>
<?php endif; ?>

<section id="main">
	<article id="content">
	<?php 
		print render($page['content']);
		
		if (isset($children) && count($children)>0): 
			foreach ($children as $child): 
				$description = $child['link']['options']['attributes']['title'] ? $child['link']['options']['attributes']['title'] : '';
			    ?>

			
			<article class="sibling blue">
			<h1><?php print $child['link']['title']; ?></h1>

                <?php if (isset($child['link']['options']['content']) && $child['link']['options']['content']['image']) {
                    $image = file_load($child['link']['options']['content']['image']);
                    print theme("image_style", array('path' => $image->uri, 'style_name' => 'menu', 'alt' => $child['link']['title'], 'title' => $child['link']['title']));
                } ?>
				<p><?php print $description; ?></p>
				<a href="<?php print url($child['link']['href']);?>" title="<?php print $child['link']['title']; ?>" class="cta cta-blue">Lees meer</a>
			</article>

			
			<?php
			endforeach;
		endif;
	?>
	</article>
	<?php if ($page['sidebar']): ?>
		<aside id="sidebar">
		  <?php print render($page['sidebar']); ?>
		</aside>
	<?php endif; ?>
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
