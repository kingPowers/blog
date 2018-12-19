<?php $viewParams = $this->params;?>
<link rel="stylesheet" type="text/css" href="_STATIC_/public/css/index.css">
<script type="text/javascript" src="_STATIC_/public/js/index.js"></script>
<div class="index-left">
	<div class="index-block index-left-block index-banner">
		<div class="banner-images">
			<div id="banner">
				<div class="loading"><img src="_STATIC_/public/plugins/myfocus/loading.gif"></div>
				<div class="pic">
					<ul>
						<?php foreach ($viewParams['banner'] as $value) {?>
							<li><a href="<?= $value['url'] ?>"><img alt="<?= $value['title'] ?>" text="<?= $value['title']?>" src="<?= $value['image'] ?>"></a></li>
						<?php }?>
					</ul>
				</div>
			</div>
		</div>		
	</div>
	<div class="index-block index-left-block top-essay">
		<span class="top-essay-mark">博主置顶</span>
		<?php foreach ($viewParams['topEssay'] as $key => $value) {?>
		<div class="essay-content">
			<span class="essay-title"><?= $value['title'] ?></span>
			<div class="content"><?= $value['introduction'] ?></div>
		</div>
		<?php } ?>	
	</div>
	<div class="index-block essay-list">
		<div class="list-top">最新发布</div>
		<?php foreach ($viewParams['essayList'] as $value) { ?>
			<div class="essay" onclick="window.location.href='/article/detail/?id=<?= $value['id'] ?>'">
				<div class="essay-text">
					<div class="title"><?= $value['title'] ?></div>
					<div class="label">
						<?php foreach ($value['label'] as $label) { ?>
							<a href="<?= $label['url'] ?>"><span><?= $label['name'] ?></span></a>
						<?php } ?>
					</div>
					<div class="content essay-content"><a href=""><?= $value['introduction'] ?></a></div>
					<div class="footer">
						<span><?= $value['author'] ?></span>
						<span>阅读：<?= $value['views'] ?></span>
						<span>评论：<?= $value['comments'] ?></span>
						<span><?= $value['timeadd'] ?></span>
					</div>
				</div>
				<div class="essay-img">
					<img src="<?= $value['image'] ?>">
				</div>
			</div>
		<?php } ?>
	</div>
</div>
<div class="index-right">
	<div class="index-block notice">
		<div class="notice-top">
			<span>站点公告</span>
		</div>
		<a href="<?= $viewParams['notice']['url'] ?>">
			<div class="notice-content">
				<span><?= $viewParams['notice']['title'] ?></span>
				<div class="content"><?= $viewParams['notice']['content'] ?></div>
			</div>
		</a>
	</div>
	<div class="index-block label-list">
		<div class="list-top">热门标签</div>
		<div class="label">
			<?php foreach ($viewParams['labelList'] as $label) { ?>
				<a href="<?= $label['url'] ?>"><span><?= $label['name'] ?></span></a>
			<?php } ?>
		</div>
	</div>
	<div class="index-block hot-essaies">
		<div class="list-top">最热文章</div>
		<div class="hot-list">
			<ul>
				<?php foreach ($viewParams['hotList'] as $hot) { ?>
					<li><a href="<?= $hot['url'] ?>"><?= $hot['title'] ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="index-block">
		<div class="list-top">友情连接</div>
		<div class="link-list">
			<ul>
				<?php foreach ($viewParams['linkList'] as $hot) { ?>
					<li><a href="<?= $hot['url'] ?>" target="_blank"><?= $hot['name'] ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>