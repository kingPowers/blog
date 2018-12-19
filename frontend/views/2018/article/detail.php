<?php $viewParams = $this->params;?>
<link rel="stylesheet" type="text/css" href="_STATIC_/manager/plugins/bootstrap/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="_STATIC_/public/css/article.css">
<div class="article-all">
	<?php echo $this->render('left.php'); ?>
	<div class="article-content">
		<div class="article-content-top">
			<div class="article-title"><h2><?= $viewParams['article']['title'] ?></h2></div>
			<div class="article-des">
				<div class="article-des-time"><?= $viewParams['article']['timeadd'] ?></div>
				<div class="article-des-views">阅读数：<?= $viewParams['article']['views'] ?></div>
			</div>
			<hr style="margin-top: 10px;" />
		</div>
		<div class="article-content-body"><?= $viewParams['article']['content'] ?></div>
		<div class="article-content-bottom">
			<div class="statement">版权声明：本文为博主原创文章，未经博主允许不得转载。	<?= \Yii::$app->request->hostInfo . \Yii::$app->request->getUrl(); ?></div>
			<div class="next-pre">
				<?php if ($viewParams['article']['pre_article']) { ?>
				<div class="pre">
					上一篇：
					<a href="/article/detail/?id=<?= $viewParams['article']['pre_article']['id'] ?>"><?= $viewParams['article']['pre_article']['title'] ?></a>
				</div>
				<?php } ?>
				<?php if ($viewParams['article']['next_article']) { ?>
				<div class="next">
					下一篇：
					<a href="/article/detail/?id=<?= $viewParams['article']['next_article']['id'] ?>"><?= $viewParams['article']['next_article']['title'] ?></a>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php echo $this->render('right.php'); ?>
</div>