<p>
	<strong><?php echo $type; ?> <?php echo $level; ?></strong> from 
	<strong><?php echo $class; ?></strong> in 
	<strong><?php echo $file; ?></strong> on line 
	<strong><?php echo $line; ?></strong>
</p>
<p><strong>Eden Says:</strong> <?php echo $message; ?></p>
<table width="100%%" border="1" cellspacing="0" cellpadding="5">
<?php foreach($history as $line): ?>
<tr><td><?php echo $line[0]; ?></td><td><?php echo $line[1]; ?>(<?php echo $line[2]; ?>)</td></tr>
<?php endforeach; ?>
</table>
<br />
<p><b>URL :</b><?php echo $info['url']; ?></p>
<p><b>POST Request </b>: </p>
<pre style="font-size: 14px">
<?php print_r($info['post']); ?>
</pre>
<p><b>GET Request </b>: </p>
<pre style="font-size: 14px">
<?php print_r($info['get']); ?>
</pre>
<p><B>SESSION </B>: </p>
<pre style="font-size: 14px">
<?php print_r($info['session']); ?>
</pre>