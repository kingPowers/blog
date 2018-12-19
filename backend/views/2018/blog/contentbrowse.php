<?php $viewParams = $this->params; ?>
<style type="text/css">
.code-key {color:#F92672 }
.code-annotations{color: #75715E}
.code-function{color: #66D9EF}
.code-this{color: #FD9720}
.code-function-name{color: #A6E22E}
.code-string{color: #E6DB74}
.code-constant-name{color: #AE81FF}
</style>
<div style="text-align:center;font-weight: bolder;"><h3><?= $viewParams['articleInfo']['title'] ?></h3></div>
<div id="content" style="height: 700px;width: 900px;background: #fff;margin: 0;padding: 10px;max-height: 800px;overflow: scroll;">
	<?= $viewParams['articleInfo']['content'] ?>
</div>