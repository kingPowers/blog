<div class="ibox">
	<div class="ibox-title">
	<h5>提示</h5>
</div>
<div class="ibox-content fadeInUp animated">
	<div class="alert alert-success alert-dismissible" role="alert" style="border-radius:0">
		<p style="font-size:18px;padding-bottom:10px">》<?php echo $message; ?></p>
		<?php if (empty($url)): ?>
		<script type="text/javascript">
		if(history.length > 0) document.write('<a href="javascript:history.go(-1);" class="lightlink">点击这里返回上一页</a>');
		</script>
		<?php else: ?>
		<p style="font-size:18px;padding-bottom:10px">
			<a href="<?php echo $url; ?>" class="lightlink">如果您的浏览器没有自动跳转，请点击这里</a>
		</p>
		<script type="text/JavaScript">
			<?php if ($isParent): ?>
			setTimeout("parentRedirect('<?php echo $url; ?>');", 3000);
			<?php else: ?>
			setTimeout("redirect('<?php echo $url; ?>');", 3000);
			<?php endif;?>
		</script>
		<?php endif; ?>        
	</div>
</div>
</div>