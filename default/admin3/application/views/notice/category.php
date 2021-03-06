<div id="func_bar">
	<a href="<?=site_url("bulletin/add_category?game_id={$this->game_id}")?>" class="btn btn-primary">+ 新增</a>
</div>

<? if ($query->num_rows() == 0):?>

<div class="none">尚無資料，<a href="<?=site_url("bulletin/add_category?game_id={$this->game_id}")?>">立即新增</a>。</div>

<? else:?>

<table class="table table-striped">
	<thead>
		<tr><td style="text-align:center; width:80px">編號</td><td style="width:160px">分類名稱</td><td style="text-align:center; width:50px">文章數</td><td style="text-align:center; width:60px">是否顯示</td><td></td></tr>
	</thead>
	<tbody>
	<? foreach($query->result() as $row):?>
		<tr>
			<td style="text-align:center;">
				<?=$row->id?>
			</td>
			<td style="text-align:left;">
				<?=$row->category?>
			</td>
			<td style="text-align:center;"><?=$row->cnt?></td>
			<td style="text-align:center;"><?=$row->display=="1" ? "是" : "否"?></td>
			<td>
				<a href="<?=site_url("bulletin/edit_category/{$row->id}?game_id={$this->game_id}")?>">修改</a> |
				<a href="javascript:;" class="del" cnt="<?=$row->cnt?>" url="<?=site_url("bulletin/delete_category/{$row->id}")?>">刪除</a>
			</td>
		</tr>
	<? endforeach;?>
	</tbody>
</table>

<? endif;?>