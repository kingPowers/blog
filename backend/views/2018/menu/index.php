<div style="margin: 10px 20px;">
<a type="button" class="btn btn-success" href="/menu/edit">新增菜单</a>
    <a type="button" class="btn btn-sort">保存排序</a>
</div>
<table class="table table-bordered table-hover definewidth m10">
    <thead>
        <tr>

          <?php foreach ($title as $value) { ?>

            <th><?= $value ?></th>

          <?php } ?>

        </tr>
    </thead>
    <tbody>
      <?php
        foreach ($list as $item) {
          echo "<tr>";
          foreach ($title as $tk => $tv) {
            echo "<td>".$item[$tk]."</td>";
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