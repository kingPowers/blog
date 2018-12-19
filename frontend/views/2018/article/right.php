<?php $viewParams = $this->params;?>
<div class="article-right">
	<div class="article-re-labels">
		<div class="article-labels-top">相关标签</div>
		<div class="article-labels-body">
			<?php foreach ($viewParams['article']['label'] as $key => $value) { ?>
			<a href="/article/list/?label=<?= $value['id'] ?>" class='label-span'><span><?= $value['name'] ?></span></a>
			<?php } ?>
		</div>	
	</div>
</div>