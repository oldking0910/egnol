<?php
	$channels = $this->config->item('channels'); 
	$c_game_query = $this->db->from("games")->order_by("rank")->get();

	$c_game = array();
	$c_game_menu["獨代"] = array();
	$c_game_menu["聯運"] = array();
	$c_game_menu["關閉"] = array();
	
	$exchange_rate = 1;
	
	foreach($c_game_query->result() as $row) {
		$c_game[$row->game_id] = $row;		
		if (!$row->is_active) {$c_game_menu["關閉"][] = $row; continue;}
		if (strpos($row->tags.",", "聯運,") !== false) {$c_game_menu["聯運"][] = $row; continue;}
		$c_game_menu["獨代"][] = $row;
		
		if ($row->game_id == $game_id) $exchange_rate = $row->exchange_rate;
	}
    
    $current_hour = date('G', time());
    $is_today = (date("Y-m-d",time())==$start_date)?true:false;
?>
<div id="func_bar">
	
</div>

<form method="get" action="<?=site_url("statistics/user_online")?>" class="form-search">
	<!--input type="hidden" name="game_id" value="<?=$this->input->get("span")?>"-->
	<input type="hidden" name="span" value="<?=$this->input->get("span")?>">
	<div class="control-group">
		
		<select name="game_id">
		    <option value="">--請選擇遊戲--</option>
			<?
			foreach($c_game_menu as $category => $c_menu):?>
				<option value=""> -------- <?=$category?> --------</option>
				<? foreach($c_menu as $key => $row):?>
				<option value="<?=$row->game_id?>" <?=($game_id==$row->game_id ? 'selected="selected"' : '')?>><?=$row->name?>	</option>
				<? endforeach;?>
			<? $i++;  
			endforeach;?>	
	    </select>
		
		時間
		<input type="text" name="start_date" class="date required" value="<?=($this->input->get("start_date"))?$this->input->get("start_date"):date("Y-m-d",time())?>" style="width:120px">
		<a href="javascript:;" class="clear_date"><i class="icon-remove-circle" title="清除"></i></a>
		
		<input type="submit" class="btn btn-small btn-inverse" name="action" value="即時在線">	
	
	</div>
		
</form>

<?
    if ($sharp):?>
	<? if (!isset($sharp)): echo '<div class="none">查無資料</div>'; else: ?>
	<table class="table table-striped table-bordered" style="width:auto;">
		<thead>
			<tr>
				<th>目前在線</th>
				<th></th>
				<th>00:00</th>		 
				<th>02:00</th>		
				<th>04:00</th>		
				<th>06:00</th>		
				<th>08:00</th>		
				<th>10:00</th>		
				<th>12:00</th>		
				<th>14:00</th>		
				<th>16:00</th>		
				<th>18:00</th>		
				<th>20:00</th>	
				<th>22:00</th>				
			</tr>
		</thead>
		<tbody>
			<tr>
				<th><?=$user_count;?></th>
				<th>今日</th>
				<th><?=(isset($sharp[$start_date][0]))?$sharp[$start_date][0]:0?></th>	
				<th><?=($is_today && $current_hour<2)?"":((isset($sharp[$start_date][2]))?$sharp[$start_date][2]:0)?></th>	
				<th><?=($is_today && $current_hour<4)?"":((isset($sharp[$start_date][4]))?$sharp[$start_date][4]:0)?></th>	
				<th><?=($is_today && $current_hour<6)?"":((isset($sharp[$start_date][6]))?$sharp[$start_date][6]:0)?></th>	
				<th><?=($is_today && $current_hour<8)?"":((isset($sharp[$start_date][8]))?$sharp[$start_date][8]:0)?></th>	
				<th><?=($is_today && $current_hour<10)?"":((isset($sharp[$start_date][10]))?$sharp[$start_date][10]:0)?></th>	
				<th><?=($is_today && $current_hour<12)?"":((isset($sharp[$start_date][12]))?$sharp[$start_date][12]:0)?></th>	
				<th><?=($is_today && $current_hour<14)?"":((isset($sharp[$start_date][14]))?$sharp[$start_date][14]:0)?></th>	
				<th><?=($is_today && $current_hour<16)?"":((isset($sharp[$start_date][16]))?$sharp[$start_date][16]:0)?></th>	
				<th><?=($is_today && $current_hour<18)?"":((isset($sharp[$start_date][18]))?$sharp[$start_date][18]:0)?></th>	
				<th><?=($is_today && $current_hour<20)?"":((isset($sharp[$start_date][20]))?$sharp[$start_date][20]:0)?></th>	
				<th><?=($is_today && $current_hour<22)?"":((isset($sharp[$start_date][22]))?$sharp[$start_date][22]:0)?></th>	
			</tr>
			<tr>
				<th>今日峰值</th>
				<th>昨日</th>
				<th><?=(isset($sharp[$prev_date][0]))?$sharp[$prev_date][0]:0?></th>	
				<th><?=(isset($sharp[$prev_date][2]))?$sharp[$prev_date][2]:0?></th>	
				<th><?=(isset($sharp[$prev_date][4]))?$sharp[$prev_date][4]:0?></th>	
				<th><?=(isset($sharp[$prev_date][6]))?$sharp[$prev_date][6]:0?></th>	
				<th><?=(isset($sharp[$prev_date][8]))?$sharp[$prev_date][8]:0?></th>	
				<th><?=(isset($sharp[$prev_date][10]))?$sharp[$prev_date][10]:0?></th>	
				<th><?=(isset($sharp[$prev_date][12]))?$sharp[$prev_date][12]:0?></th>	
				<th><?=(isset($sharp[$prev_date][14]))?$sharp[$prev_date][14]:0?></th>	
				<th><?=(isset($sharp[$prev_date][16]))?$sharp[$prev_date][16]:0?></th>	
				<th><?=(isset($sharp[$prev_date][18]))?$sharp[$prev_date][18]:0?></th>	
				<th><?=(isset($sharp[$prev_date][20]))?$sharp[$prev_date][20]:0?></th>	
				<th><?=(isset($sharp[$prev_date][22]))?$sharp[$prev_date][22]:0?></th>
			</tr>
			<tr>
				<th><?=$high_peak;?></th>
				<th>時段峰值(今)</th>
				<th><?=$bi_peak[0]?></th>	
				<th><?=($is_today && $current_hour<2)?"":($bi_peak[2])?></th>	
				<th><?=($is_today && $current_hour<4)?"":($bi_peak[4])?></th>	
				<th><?=($is_today && $current_hour<6)?"":($bi_peak[6])?></th>	
				<th><?=($is_today && $current_hour<8)?"":($bi_peak[8])?></th>	
				<th><?=($is_today && $current_hour<10)?"":($bi_peak[10])?></th>	
				<th><?=($is_today && $current_hour<12)?"":($bi_peak[12])?></th>	
				<th><?=($is_today && $current_hour<14)?"":($bi_peak[14])?></th>	
				<th><?=($is_today && $current_hour<16)?"":($bi_peak[16])?></th>	
				<th><?=($is_today && $current_hour<18)?"":($bi_peak[18])?></th>	
				<th><?=($is_today && $current_hour<20)?"":($bi_peak[20])?></th>	
				<th><?=($is_today && $current_hour<22)?"":($bi_peak[22])?></th>	
			</tr>
		</tbody>
	</table>
	<? endif;?>
<? endif;?>