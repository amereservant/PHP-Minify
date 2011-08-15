<?php
require 'yui-compress.php';
require 'functions.php';

// Process Submitted Form ...
if( isset($_POST['code']) ) $out = minify_code();
?><!doctype html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>YUI Compressor - Online CSS and Javascript Minify</title>
	<meta name="description" content="PHP Minify is an online web-based YUI Compressor interface for minifying css and javascript." />
	<meta name="author" content="Amereservant" />
	<link rel="stylesheet" href="css/style.min.css" />
	<script src="js/libs/modernizr-2.0.6.min.js"></script>
	<link href="http://fonts.googleapis.com/css?family=Droid+Sans:400,700" rel="stylesheet" type="text/css" />
	<link href="http://fonts.googleapis.com/css?family=Jura:600" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="wrapper" class="clearfix">
	<header id="title-header" class="clearfix">
        <h1>YUI Compressor</h1><h2>Online CSS and Javascript Minify</h2>
    </header>
    <form method="post">
        <aside>
            <h3>Select Compression Options</h3>
            <ul>
                <li>
                    <h3 class="first">Input Type</h3>
                    <input type="radio" name="type" value="js"<?php value_exists( 'type', 'js', 'checked' ); ?> /> <label>JS</label>
                    <input type="radio" name="type" value="css"<?php value_exists( 'type', 'css', 'checked' ); ?> /> <label>CSS</label>
                </li>
                <li>
                    <h3>Javascript Options</h3>
                    <input type="checkbox" name="nomunge" value="1"<?php value_exists( 'nomunge', '1', 'checked' ); ?> /> <label>No symbol obfuscation</label>
                    <br />
                    <input type="checkbox" name="preserve-semi" value="1"<?php value_exists( 'preserve-semi', '1', 'checked' ); ?> /> <label>Preserve extra semicolons</label>
                    <br />
                    <input type="checkbox" name="disable-optimizations" value="1"<?php value_exists( 'disable-optimizations', '1', 'checked' ); ?> /> <label>Disable micro optimizations</label>
                </li>
            </ul>
        </aside>
	    <article id="main" role="main">
	        <header>
	            <h3>Paste your code to compress.</h3>
	        </header>
	        <section>
                <p>
                    <textarea name="code" rows="10" cols="57"><?php value_exists('code'); ?></textarea>
                </p>
                <p>
                    <input id="submit" type="submit" value="Compress it!" />
                </p>
            </section>
            <section id="results" class="hide">
                <header>
	                <h3>Your compressed code.</h3>
	            </header>
                <textarea id="compressed_code" rows="10" cols="57"></textarea>
                <p id="restext">
                    <span>Original Size:</span> <strong></strong><br />
                    <span>Compressed Size:</span> <strong></strong><br />
                    <span>Compressed Diff:</span> <strong></strong>
                </p>
            </section>
            <?php if(isset($out)) { ?>
            <!-- If javascript is disabled, the compressor still works and PHP will render the results here instead. -->
            <header>
	            <h3>Your compressed code.</h3>
	        </header>
            <textarea name="compressed_code" rows="10" cols="57"><?php echo $out['compressed']; ?></textarea>
            <p id="restext">
                <span>Original Size:</span> <strong><?php echo file_size($out['original_size']); ?></strong><br />
                <span>Compressed Size:</span> <strong><?php echo file_size($out['compressed_size']); ?></strong><br />
                <span>Compressed Diff:</span> <strong><?php echo $out['difference']; ?>%</strong>
            </p>
            <?php } ?>
	    </article>
	</form>
	<div style="clear:both"></div>
</div><!-- #wrapper -->
<div id="loader"></div>
<footer>
    <p>
        Licensed under the <a href="http://www.opensource.org/licenses/mit-license.php" rel="license">MIT license</a>.
        Hosted and managed at <a href="https://github.com/amereservant/PHP-Minify">GitHub</a>.
    </p>
</footer>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>');</script>
<!-- AJAX Compression Handling Script -->
<script src="js/script.min.js"></script>
<!--[if lt IE 7 ]>
	<script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
	<script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
<![endif]-->
</body>
</html>
