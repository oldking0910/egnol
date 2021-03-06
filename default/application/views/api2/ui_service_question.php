<style>
.pic_input { color:#000; }
</style>
<div id="content-login">
	<div class="login-ins">
		<form id="question_form" enctype="multipart/form-data" method="post" action="<?=$api_url?>api2/service_question_ajax?site=<?=$site?>">
			<div class="login-form">
				<table class="member_info">
					<tr>
						<th>遊戲名稱</th>
						<td>
							<select name="game" class="required" style="width:90%;">
								<option value="">--請選擇--</option>
								<? foreach($games->result() as $row):?>
								<option value="<?=$row->game_id?>" <?=($site==$row->game_id ? 'selected="selected"' : '')?>><?=$row->name?></option>
								<? endforeach;?>
							</select>
						</td>
					</tr>
					<tr>
						<th>伺服器</th>
						<td>
							<select name="server" class="required" style="width:90%;">
								<option value="">--請先選擇遊戲--</option>
							</select>

							<select id="server_pool" style="display:none;">
								<? foreach($servers->result() as $row):?>
								<option value="<?=$row->server_id?>" <?=($this->input->get("server")==$row->server_id ? 'selected="selected"' : '')?> class="<?=$row->game_id?>"><?=$row->name?></option>
								<? endforeach;?>
							</select>
						</td>
					</tr>
					<tr>
						<th>角色名稱</th>
						<td>
							<select name="character_name" style="width:90%;">
								<option value="">--請選擇角色--</option>
							</select>

							<select id="character_pool" style="display:none;">
								<? foreach($characters->result() as $row): ?>
								<option value="<?=$row->name?>" class="<?=$row->server_id?>"><?=$row->name?></option>
								<? endforeach;?>
							</select>
						</td>
					</tr>
					<tr>
						<th>問題類型</th>
						<td>
							<select name="question_type" class="required" style="width:90%;">
								<option value="">--請選擇--</option>
								<? foreach($this->config->item("question_type") as $id => $type):?>
								<option value="<?=$id?>"><?=$type?></option>
								<? endforeach;?>
							</select>
						</td>
					<tr>
						<th>問題描述</th><td><textarea name="content" class="required" minlength="5" maxlength="500"></textarea></td>
					</tr>
					<? /*
					<tr>
						<th>圖片附件</th><td style="white-space:pre-wrap;"><img src="<?=$longe_url?>p/image/server/server-pic-btn1.png" class="pic_btn"> <input type="file" name="file01" class="pic_input" /></td>
					</tr>
					<tr>
						<th>&nbsp;</th><td style="white-space:pre-wrap;"><img src="<?=$longe_url?>p/image/server/server-pic-btn2.png" class="pic_btn"> <input type="file" name="file02" class="pic_input"></td>
					</tr>
					<tr>
						<th>&nbsp;</th><td style="white-space:pre-wrap;"><img src="<?=$longe_url?>p/image/server/server-pic-btn3.png" class="pic_btn"> <input type="file" name="file03" class="pic_input"></td>
					</tr>
					<tr>
						<th></th>
						<td style="white-space:pre-wrap;">圖檔可接受格式：jpg、png、gif、bmp<br/>最大尺寸 6144x6144 畫素，容量最大 6MB。</td>
					</tr>
					*/ ?>
				</table>
				<div class="login-button">
					<p>
						<input name="doSubmit" type="submit" id="doSubmit" value="" style="display:none;" />
                        <img style="cursor:pointer;" src="<?=$longe_url?>p/image/server/server-back-btn1.png" class="button_submit" onclick="javascript:$('#doSubmit').trigger('click')" />&nbsp;
						<img style="cursor:pointer;" src="<?=$longe_url?>p/image/server/server-back-btn2.png" class="button_submit" onclick="javascript:history.back();" />
					</p>
				</div>
			</div>
		</form>
	</div>
</div>
