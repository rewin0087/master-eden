<!DOCTYPE html>
<html>
	 
<head>
	<title><?php print $title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<?php if(isset($meta) && is_array($meta)): ?>
    <?php foreach($meta as $name => $content): ?>
    <meta name="<?php print $name; ?>" content="<?php print $content; ?>" />
    <?php endforeach; ?>
    <?php endif; ?>
	<link rel="shortcut icon" href="/assets/images/favicon.ico" type="image/x-icon">    
    <link rel="stylesheet" type="text/css" media="screen" href="/assets/style/bootstrap.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="/assets/style/responsive.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="/assets/style/master.css" />
    <script type="text/javascript" src="/assets/script/jquery.js"></script>
</head>

<body class="<?php print $class; ?>">
	<div class="page">
		<div class="head"><?php print $head; ?></div>
		<div class="body container"><?php print $body; ?></div>
		<div class="foot"><?php print $foot; ?></div>
    </div>
    <script type="text/javascript" src="/assets/script/bootstrap.js"></script>
    <script type="text/javascript" src="/assets/script/base.js"></script>
	<script type="text/javascript" src="/assets/script/master.js"></script>
</body>

</html>
