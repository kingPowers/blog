<?php $viewParams = $this->params; ?>
<form id='menuForm' class="definewidth m20" >
<input type="hidden" name="_csrf-backend" value="<?= Yii::$app->request->csrfToken?>">
<input type="hidden" name="is_sub" value="1">
<input type="hidden" name="menuid" value="<?= $viewParams['menuInfo']['id'] ?>">
  <table class="table table-bordered table-hover m10">
    <tr>
      <td width="10%" class="tableleft">上级</td>
      <td><select name="pid">
      <option value="0">一级导航</option>
      <?php
        foreach ($viewParams['pList'] as $value) {
            $selected = ($viewParams['menuInfo']['pid'] == $value['id'])?"selected='selected'":"";
            if ($selected) $pMenu = $value;
            echo "<option value='".$value['id']."' ".$selected.">".$value['title']."</option>";
        }
      ?>
      </select></td>
    </tr>
    <tr>
      <td class="tableleft">名称</td>
      <td><input type="text" name="title" jschecktitle="名称" jscheckrule="null=0" value="<?= $viewParams['menuInfo']['title'] ?>"/></td>
    </tr>
    <tr>
      <td class="tableleft">分组</td>
      <td><input type="text" name="group" disabled="disabled" value="<?= $pMenu['title'] ?>"/></td>
    </tr>
    <tr>
      <td class="tableleft">Model</td>
      <td><input type="text" name="module" jschecktitle="Model" jscheckrule="null=0" value="<?= $viewParams['menuInfo']['module'] ?>"/></td>
    </tr>
    <tr>
      <td class="tableleft">Action</td>
      <td><input type="text" name="action" <?php if($viewParams['menuInfo']['pid'] == 0) echo "disabled='disabled'"; ?> value="<?= $viewParams['menuInfo']['action'] ?>"/></td>
    </tr>
    <tr>
      <td class="tableleft">类型</td>
      <td>
        <?php
          foreach ($viewParams['type'] as $key => $value) {
            $checked = ($viewParams['menuInfo']['type'] == $key)?"checked='checked'":"";
            echo "<input type='radio' name='type' value='".$key."' ".$checked.">".$value."&nbsp;&nbsp;";
          }
        ?>
    </tr>
    <tr>
      <td class="tableleft">状态</td>
      <td>
        <?php
          foreach ($viewParams['status'] as $key => $value) {
            $checked = ($viewParams['menuInfo']['status'] == $key)?"checked='checked'":"";
            echo "<input type='radio' name='status' value='".$key."' ".$checked.">".$value."&nbsp;&nbsp;";
          }
        ?>
    </tr>
    <tr>
      <td class="tableleft"></td>
      <td><button class="btn btn-primary" type="button"> 保存 </button>
        <button type="button" class="btn btn-success" name="backid" id="backid">返回列表</button></td>
    </tr>
  </table>
</form>
<script language="javascript">

$(function(){
  $('button.btn-primary').click(function(){
    var subMenu = new formOperate("menuForm",{url:'/menu/edit'});
    subMenu.success = function (F,a) {console.log(F);
      top.jdbox.alert(F.status,F.info);
      if (F.status) {
        window.location.href = "/menu/index";
      }
    }
    top.jdbox.alert(2);
    subMenu.submitForm();
  });
	$("select[name='pid']").change(function(){
		var self = $(this), parent = false;
		$(this).find('option').each(function(){
			if( self.val() == $(this).val()){
				$("input[name='group']").val( $(this).html() );
				if(self.val()!=0){
					parent = true;
				}
			}
		});
		if(parent){
			$("input[name='action']").attr({disabled:false,jscheckrule:'null=0'});
		}else{
			$("input[name='action']").attr({disabled:true,jscheckrule:''});
		}
	});
	$('button#backid').click(function(){
		window.location.href= "{:url('Menu/index')}";
	})
})
</script>    
