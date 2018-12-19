<?php $viewParams = $this->params; ?>
<div style="margin: 10px 20px;">
<a type="button" class="btn btn-success" href="/menu/edit">新增菜单</a>
    <a type="button" class="btn btn-sort">保存排序</a>
</div>
<table class="table table-bordered table-hover definewidth m10">
    <thead>
        <tr>

          <?php foreach ($viewParams['title'] as $value) { ?>

            <th><?= $value ?></th>

          <?php } ?>

        </tr>
    </thead>
    <tbody>
      <?php
        foreach ($viewParams['list'] as $list) {
          echo "<tr>";
          foreach ($viewParams['title'] as $tk => $tv) {
            echo "<td>".$list[$tk]."</td>";
          }
          echo "</tr>";
        }
      ?>
    </tbody>
</table>
<script type="text/javascript">
var edit = function (id)
{
  window.location.href = "/menu/edit?menuid=" + id;
}
</script>