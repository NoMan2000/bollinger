<?php
$hasregionpre = $PAGE->blocks->is_known_region('side-pre');
$hasregionpost = $PAGE->blocks->is_known_region('side-post');

$bc = new block_contents;
$bc->title = 'Login Info';
$bc->content = $OUTPUT->login_info();
if($hasregionpost) {
    $PAGE->blocks->add_fake_block($bc, 'side-post');
} elseif($hasregionpre) {
    $PAGE->blocks->add_fake_block($bc, 'side-pre');
}
if (!empty($PAGE->layout_options['langmenu'])) {
    $bc = new block_contents;
    $bc->title = 'Language Menu';
    $bc->content = $OUTPUT->lang_menu();
    if($hasregionpost) {
        $PAGE->blocks->add_fake_block($bc, 'side-post');
    } elseif($hasregionpre) {
        $PAGE->blocks->add_fake_block($bc, 'side-pre');
    }
}

$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
$showsidepre = $hassidepre && !$PAGE->blocks->region_completely_docked('side-pre', $OUTPUT);
$showsidepost = $hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT);
$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$bodyclasses = array();
if ($showsidepre && !$showsidepost) {
    $bodyclasses[] = 'side-pre-only';
} else if ($showsidepost && !$showsidepre) {
    $bodyclasses[] = 'side-post-only';
} else if (!$showsidepost && !$showsidepre) {
    $bodyclasses[] = 'content-only';
}
if ($hascustommenu) {
    $bodyclasses[] = 'has_custom_menu';
}

$OUTPUT->doctype();
?>
<!DOCTYPE html>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
	<!--[if lte IE 7]><link rel="stylesheet" href="<?php echo $CFG->wwwroot . '/theme/' . $PAGE->theme->name . '/style/style.ie7.css';?>" media="screen" /><![endif]-->

<meta name="viewport" content="initial-scale = 1.0, maximum-scale = 1.0, user-scalable = no, width = device-width"/>
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Serif&amp;subset=latin"/>

</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>
<div id="moodle-main">










<div class="moodle-sheet clearfix" id="page-content">




<!-- BEGIN OF HEADER -->
<?php if ($hasheading || $hasnavbar) { ?>
 
<header class="moodle-header clearfix">
        <?php if ($hasheading) { ?>
		
		
		<div class="moodle-shapes">


            </div>
		
		
		<?php } ?>
		
		
</header>
<?php } ?>
<!-- END OF HEADER -->


	<?php if ($hascustommenu) { ?>
		<nav class="moodle-nav clearfix">				
			
				<?php echo $custommenu; ?>
			
		</nav>
	<?php } ?>
<!-- END Menu and HEADER position inside Sheet-->

<div class="moodle-layout-wrapper clearfix">
		<div class="moodle-content-layout">
			<div class="moodle-content-layout-row">
			
				

				<div class="moodle-layout-cell moodle-content clearfix" id="region-main">
					<article class="moodle-post moodle-article">
						<div class="moodle-postcontent moodle-postcontent-0 clearfix region-content">
							<?php if ($hasnavbar) { ?>
								<div class='navbar'>
									<div class='breadcrumb'><?php echo $OUTPUT->navbar(); ?></div>
									<div class='navbutton'><?php echo $PAGE->button; ?></div>
								</div>
							<?php } ?>
							<?php echo core_renderer::MAIN_CONTENT_TOKEN ?>
						</div>
					</article>
				</div>
				
				
					
					<?php if ($hassidepre) { ?>
						<div class="moodle-layout-cell moodle-sidebar1 clearfix">
							<?php echo $OUTPUT->blocks_for_region('side-pre') ?>		
						</div>
					<?php } ?>
					
				
				
				
				<?php if ($hassidepost) { ?>
					<div class="moodle-layout-cell moodle-sidebar2 clearfix">
						<?php echo $OUTPUT->blocks_for_region('side-post') ?>
					</div>
				<?php } ?>
				
			</div>
		</div>
	</div>
	
		<?php if ($hasfooter) { ?>
		
		<footer class="moodle-footer clearfix">
<div class="moodle-content-layout">
    <div class="moodle-content-layout-row">
    <div class="moodle-layout-cell layout-item-0" style="width: 50%">
        <p style="float: left; padding-left: 20px; text-align: left;"><a href="#">Home</a>&nbsp;&nbsp; | &nbsp;<a href="#">Contact</a> &nbsp; | &nbsp; <a href="#">Support</a> &nbsp; | &nbsp;&nbsp;<a href="#">TOS</a></p>
    </div><div class="moodle-layout-cell layout-item-0" style="width: 50%">
        <p style="padding-right: 20px; text-align: left;">LED FastStart ©2013. All rights reserved.<br>
        <br></p>
        <br>
        <br>
        <p><br></p>
    </div>
    </div>
</div>

</footer>
		<?php } ?>
	
	
	</div>
<!-- START OF FOOTER -->
	
	
</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
<script type="text/javascript">
	setTimeout(initRadioButtons, 4000);
</script>
</body>
</html>