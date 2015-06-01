<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Statistics extends MY_Controller {
	
	function __construct() 
	{
		parent::__construct();					
		
		error_reporting(E_ALL);
		ini_set('display_errors','On');		
	}		
	
	function _init_statistics_layout()
	{
		$this->zacl->check_login(true);
		if ($this->zacl->check_acl("all_game", "all") == false) {
			if ($this->game_id) $this->zacl->check($this->game_id, "read");
		}
		
		return $this->_init_layout()
			->add_breadcrumb("統計", "statistics");
	}
	
	function statistics()
	{			
		$this->_init_statistics_layout();			
		$this->load->helper("output_table");
		
		$this->zacl->check("game_statistics", "read");
		
		if ($this->input->get("action")) 
		{											
			$date_field = "";
			
			switch ($this->input->get("action"))
			{
				case "流失率統計":
					
					$start_date = $this->input->get("start_date") ? $this->input->get("start_date") : date("Y-m-d");
					$end_date = $this->input->get("end_date") ? $this->input->get("end_date") : date("Y-m-d");
					
					$where = " where 1=1";
					
					if ($server = $this->input->get("server")) {
						$where .= " and gi.server_id={$server} ";
					} else $where .= " and gi.game_id='{$this->game_id}' ";					
					
					if ($channel = $this->input->get("channel")) {
						if ($channel == 'long_e') $where .= " and lgl.account not like '%@%' ";
						else {
							$where .= " and lgl.account like '%@{$channel}' ";
						}
					}
					
					if ($ad_channel = $this->input->get("ad_channel")) {
						$where .= " and lgl.ad like '{$ad_channel}%' ";
					}
			
					$query = $this->db->query("
							
select d, count(*) 'login_cnt', sum(role) 'role_cnt', 
	round(1-sum(role)/count(*), 3) 'role_p',
	sum(role)-sum(c1) 'c1', 
	round((sum(role)-sum(c1)) / sum(role), 3) 'c1_p',
	sum(role)-sum(c7) 'c7', 
	round((sum(role)-sum(c7)) / sum(role), 3) 'c7_p',
	sum(role)-sum(c15) 'c15', 
	round((sum(role)-sum(c15)) / sum(role), 3) 'c15_p',
	sum(role)-sum(c30) 'c30', 
	round((sum(role)-sum(c30)) / sum(role), 3) 'c30_p'
from 
(
	SELECT 
		date(lgl.create_time) 'd', lgl.uid, lgl.server_id, character_name,
		if (gsr.id is null, '0', '1') 'role',
		if (gsr.id is null, '0', (select if(count(*)>0, 1, 0) from log_game_logins
			where uid=gsr.uid and server_id=gsr.server_id 
				and create_time >= date_add(date(gsr.create_time), interval 1 day))
		) 'c1', 
		if (gsr.id is null, '0', (select if(count(*)>0, 1, 0) from log_game_logins
			where uid=gsr.uid and server_id=gsr.server_id 
				and create_time >= date_add(date(gsr.create_time), interval 6 day))
		) 'c7', 
		if (gsr.id is null, '0', (select if(count(*)>0, 1, 0) from log_game_logins
			where uid=gsr.uid and server_id=gsr.server_id 
				and create_time >= date_add(date(gsr.create_time), interval 14 day)) 
		) 'c15', 
		if (gsr.id is null, '0', (select if(count(*)>0, 1, 0) from log_game_logins
			where uid=gsr.uid and server_id=gsr.server_id 
				and create_time >= date_add(date(gsr.create_time), interval 29 day)) 
		) 'c30'
	FROM log_game_logins lgl
	join servers gi on lgl.server_id=gi.server_id
	left join characters gsr on date(gsr.create_time)=date(lgl.create_time) and gsr.uid=lgl.uid and gsr.server_id=lgl.server_id
	{$where}
	and lgl.create_time between '{$start_date}' and '{$end_date} 23:59:59'
	and not exists(select * from characters where 
		create_time < lgl.create_time and uid=lgl.uid and server_id=lgl.server_id
	)	
	group by date(lgl.create_time), lgl.uid, lgl.server_id 
) tmp
group by d														
					");
					break;
					
				case "廣告時段統計":	
					$query = $this->db->select("ga.ad, LEFT(ga.create_time, {$len}) time, COUNT(*) cnt", false)
						->from('characters ga')
						->join("servers gi", "ga.server_id=gi.server_id")
						->group_by("time, ga.ad")
						->order_by("time desc, ga.ad")->get();
					//die($this->db->last_query());
					break;					
			}			
			
			if (empty($query)) {
				if ($this->input->get("start_date")) {
					$start_date = $this->db->escape($this->input->get("start_date"));
					if ($this->input->get("end_date")) {
						$end_date = $this->db->escape($this->input->get("end_date").":59");
						$this->db->where("{$date_field} between {$start_date} and {$end_date}", null, false);	
					}	
					else $this->db->where("{$date_field} >= {$start_date}", null, false);
				}			
				
				$query = $this->db->get();
			}
		}
		else {
			$default_value = array(
				'use_default' => true,
				'time_unit' => 'day',
				'start_date' => date("Y-m-d"),
				'single_date' => date("Y-m-d"),
			);
			$_GET = $default_value;	
		}		
		
		$this->g_layout
			->set("query", isset($query) ? $query : false)
			->set("servers", $this->db->where("game_id", $this->game_id)->from("servers")->order_by("id")->get())
			->add_js_include("game/statistics")
			->add_js_include("jquery-ui-timepicker-addon")
			->render();
	}
	
	function retention()
	{			
		$this->_init_statistics_layout();			
		$this->load->helper("output_table");
		
		$this->zacl->check("game_statistics", "read");
		
		$start_date = $this->input->get("start_date") ? $this->input->get("start_date") : date("Y-m-d");
		$end_date = $this->input->get("end_date") ? $this->input->get("end_date") : date("Y-m-d");
		$game_id = $this->input->get("game_id_1") ? $this->input->get("game_id_1") : ($this->input->get("game_id_2") ? $this->input->get("game_id_2") : ($this->input->get("game_id_3") ? $this->input->get("game_id_3") : ""));
					
		$query = $this->db->where("game_id", $game_id)->where("date >=", $start_date)->where("date <=", $end_date)->get("statistics");
        
		$this->g_layout
			->set("query", isset($query) ? $query : false)
			->set("game_id", $game_id)
			->set("game_id_1", $this->input->get("game_id_1"))
			->set("game_id_2", $this->input->get("game_id_2"))
			->set("game_id_3", $this->input->get("game_id_3"))
		
		->set("servers", $this->db->where("game_id", $this->game_id)->from("servers")->order_by("id")->get())
			->add_js_include("game/statistics")
			->add_js_include("jquery-ui-timepicker-addon")
			->render();
	}
	
	function game_length()
	{			
		$this->_init_statistics_layout();			
		$this->load->helper("output_table");
		
		$this->zacl->check("game_statistics", "read");
		
		$span = $this->input->get("span");
		$start_date = $this->input->get("start_date") ? $this->input->get("start_date") : date("Y-m-d");
		$end_date = $this->input->get("end_date") ? $this->input->get("end_date") : date("Y-m-d");
		$game_id = $this->input->get("game_id_1") ? $this->input->get("game_id_1") : ($this->input->get("game_id_2") ? $this->input->get("game_id_2") : ($this->input->get("game_id_3") ? $this->input->get("game_id_3") : ""));

        switch($span) {
			case "weekly":
			    $date_group = 'WEEK';
				break;
			
			case "monthly":
			    $date_group = 'MONTH';
				break;
				
			default:
			    $date_group = 'DATE';
				break;
		}		
		
		$query = $this->db->query("
		SELECT new_gt.game_id, new_gt.date,
		    new_login_count,
			new_t15,
			new_t30,
			new_t60,
			new_t90,
			new_t120,
			new_tmore,
		    all_login_count,
			all_t15,
			all_t30,
			all_t60,
			all_t90,
			all_t120,
			all_tmore,
		    deposit_login_count,
			deposit_t15,
			deposit_t30,
			deposit_t60,
			deposit_t90,
			deposit_t120,
			deposit_tmore,
		    new_deposit_login_count,
			new_deposit_t15,
			new_deposit_t30,
			new_deposit_t60,
			new_deposit_t90,
			new_deposit_t120,
			new_deposit_tmore
		FROM 
		(SELECT game_id, {$date_group}(create_time) 'date',
		    COUNT(uid) 'all_login_count',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) < 15 THEN 1 ELSE NULL END) 'all_t15',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) >= 15 AND TIMESTAMPDIFF(MINUTE, create_time, logout_time) < 30 THEN 1 ELSE NULL END) 'all_t30',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) >= 30 AND TIMESTAMPDIFF(MINUTE, create_time, logout_time) < 60 THEN 1 ELSE NULL END) 'all_t60',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) >= 60 AND TIMESTAMPDIFF(MINUTE, create_time, logout_time) < 90 THEN 1 ELSE NULL END) 'all_t90',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) >= 90 AND TIMESTAMPDIFF(MINUTE, create_time, logout_time) < 120 THEN 1 ELSE NULL END) 'all_t120',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) >= 120 THEN 1 ELSE NULL END) 'all_tmore'
		FROM log_game_logins
		WHERE game_id = '{$game_id}'
		AND create_time >= DATE('{$start_date}')
		AND create_time <= DATE('{$end_date}')
		AND is_first = 1
		GROUP BY {$date_group}(create_time)) AS all_gt
		LEFT JOIN
		(SELECT game_id, {$date_group}(create_time) 'date',
		    COUNT(uid) 'new_login_count',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) < 15 THEN 1 ELSE NULL END) 'new_t15',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) >= 15 AND TIMESTAMPDIFF(MINUTE, create_time, logout_time) < 30 THEN 1 ELSE NULL END) 'new_t30',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) >= 30 AND TIMESTAMPDIFF(MINUTE, create_time, logout_time) < 60 THEN 1 ELSE NULL END) 'new_t60',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) >= 60 AND TIMESTAMPDIFF(MINUTE, create_time, logout_time) < 90 THEN 1 ELSE NULL END) 'new_t90',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) >= 90 AND TIMESTAMPDIFF(MINUTE, create_time, logout_time) < 120 THEN 1 ELSE NULL END) 'new_t120',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, create_time, logout_time) >= 120 THEN 1 ELSE NULL END) 'new_tmore'
		FROM log_game_logins
		WHERE game_id = '{$game_id}'
		AND create_time >= DATE('{$start_date}')
		AND create_time <= DATE('{$end_date}')
		GROUP BY {$date_group}(create_time)) AS new_gt ON all_gt.game_id=new_gt.game_id AND all_gt.date=new_gt.date
		LEFT JOIN
		(SELECT lgl.game_id, {$date_group}(lgl.create_time) 'date',
		    COUNT(lgl.uid) 'deposit_login_count',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) < 15 THEN 1 ELSE NULL END) 'deposit_t15',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) >= 15 AND TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) < 30 THEN 1 ELSE NULL END) 'deposit_t30',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) >= 30 AND TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) < 60 THEN 1 ELSE NULL END) 'deposit_t60',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) >= 60 AND TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) < 90 THEN 1 ELSE NULL END) 'deposit_t90',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) >= 90 AND TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) < 120 THEN 1 ELSE NULL END) 'deposit_t120',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) >= 120 THEN 1 ELSE NULL END) 'deposit_tmore'
		FROM log_game_logins lgl
		JOIN user_billing ub ON lgl.uid=ub.uid AND lgl.server_id=ub.server_id AND lgl.create_time >= ub.create_time
		WHERE lgl.game_id = '{$game_id}'
		AND lgl.create_time >= DATE('{$start_date}')
		AND lgl.create_time <= DATE('{$end_date}')
        AND ub.billing_type = 2 
        AND ub.result = 1
		GROUP BY {$date_group}(lgl.create_time)) AS deposit_gt ON all_gt.game_id=deposit_gt.game_id AND all_gt.date=deposit_gt.date
		LEFT JOIN
		(SELECT lgl.game_id, {$date_group}(lgl.create_time) 'date',
		    COUNT(lgl.uid) 'new_deposit_login_count',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) < 15 THEN 1 ELSE NULL END) 'new_deposit_t15',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) >= 15 AND TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) < 30 THEN 1 ELSE NULL END) 'new_deposit_t30',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) >= 30 AND TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) < 60 THEN 1 ELSE NULL END) 'new_deposit_t60',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) >= 60 AND TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) < 90 THEN 1 ELSE NULL END) 'new_deposit_t90',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) >= 90 AND TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) < 120 THEN 1 ELSE NULL END) 'new_deposit_t120',
			COUNT(CASE WHEN TIMESTAMPDIFF(MINUTE, lgl.create_time, lgl.logout_time) >= 120 THEN 1 ELSE NULL END) 'new_deposit_tmore'
		FROM log_game_logins lgl
		JOIN user_billing ub ON lgl.uid=ub.uid AND lgl.server_id=ub.server_id AND DATE(lgl.create_time) = DATE(ub.create_time)
		WHERE lgl.game_id = '{$game_id}'
		AND lgl.create_time >= DATE('{$start_date}')
		AND lgl.create_time <= DATE('{$end_date}')
        AND ub.billing_type = 2 
        AND ub.result = 1
		GROUP BY {$date_group}(lgl.create_time)) AS new_deposit_gt ON all_gt.game_id=new_deposit_gt.game_id AND all_gt.date=new_deposit_gt.date
		");
        
		$this->g_layout
			->set("query", isset($query) ? $query : false)
			->set("game_id", $game_id)
			->set("span", $span)
			->set("game_id_1", $this->input->get("game_id_1"))
			->set("game_id_2", $this->input->get("game_id_2"))
			->set("game_id_3", $this->input->get("game_id_3"))
		
		->set("servers", $this->db->where("game_id", $this->game_id)->from("servers")->order_by("id")->get())
			->add_js_include("game/statistics")
			->add_js_include("jquery-ui-timepicker-addon")
			->render();
	}
	
	function operation()
	{			
		$this->_init_statistics_layout();			
		$this->load->helper("output_table");
		
		$this->zacl->check("game_statistics", "read");
		
		$span = $this->input->get("span");
		$start_date = $this->input->get("start_date") ? $this->input->get("start_date") : date("Y-m-d");
		$end_date = $this->input->get("end_date") ? $this->input->get("end_date") : date("Y-m-d");
		if (empty($this->input->get("start_date")) && empty($this->input->get("end_date"))) {
			$start_date = date("Y-m-d",strtotime("-1 days"));
			$end_date = date("Y-m-d",strtotime("-8 days"));
		} 
		$game_id = $this->input->get("game_id_1") ? $this->input->get("game_id_1") : ($this->input->get("game_id_2") ? $this->input->get("game_id_2") : ($this->input->get("game_id_3") ? $this->input->get("game_id_3") : ""));
		
        switch($span) {
			case "weekly":
			    $date_group = 'WEEK';
				break;
			
			case "monthly":
			    $date_group = 'MONTH';
				break;
				
			default:
			    $date_group = 'DATE';
				break;
		}
		
		if ('DATE'==$date_group) {
		    $query = $this->db->query("
		    (SELECT 
		        stc.date 'date',
			    stc.game_id 'game_id',
			    stc.login_count 'login_count',
			    stc.new_login_count 'new_login_count',
			    stc.new_character_count 'new_character_count',
			    stc.device_count 'device_count',
			    stc2.login_count 'y_login_count',
			    stc2.new_login_count 'y_new_login_count',
			    stc2.one_retention_all_count 'y_one_retention_all_count',
                stc2.one_retention_count 'y_one_retention_count',
                stc.deposit_user_count 'deposit_user_count',
                stc.new_deposit_user_count 'new_deposit_user_count',
                stc.consume_user_count 'consume_user_count',
                stc.new_consume_user_count 'new_consume_user_count',
                stc.currency_total 'currency_total',
                stc.paid_currency_total 'paid_currency_total',
                stc.deposit_total 'deposit_total',
                stc.consume_total 'consume_total',
                stc.peak_user_count 'peak_user_count',
                stc.total_time 'total_time',
                stc.paid_total_time 'paid_total_time'
		    FROM statistics stc
		    LEFT JOIN statistics stc2 ON stc.game_id=stc2.game_id AND stc.date=DATE_ADD(stc2.date, interval 1 day)
		    WHERE stc.game_id = '{$game_id}'
		    AND stc.date >= '{$start_date}'
		    AND stc.date <= '{$end_date}')
		
		    UNION
		
		    (SELECT 
		        stc.date 'date',
			    stc.game_id 'game_id',
			    stc.login_count 'login_count',
			    stc.new_login_count 'new_login_count',
			    stc.new_character_count 'new_character_count',
			    stc.device_count 'device_count',
			    stc2.login_count 'y_login_count',
			    stc2.new_login_count 'y_new_login_count',
			    stc2.one_retention_all_count 'y_one_retention_all_count',
                stc2.one_retention_count 'y_one_retention_count',
                stc.deposit_user_count 'deposit_user_count',
                stc.new_deposit_user_count 'new_deposit_user_count',
                stc.consume_user_count 'consume_user_count',
                stc.new_consume_user_count 'new_consume_user_count',
                stc.currency_total 'currency_total',
                stc.paid_currency_total 'paid_currency_total',
                stc.deposit_total 'deposit_total',
                stc.consume_total 'consume_total',
                stc.peak_user_count 'peak_user_count',
                stc.total_time 'total_time',
                stc.paid_total_time 'paid_total_time'
		    FROM statistics stc
		    RIGHT JOIN statistics stc2 ON stc.game_id=stc2.game_id AND stc.date=DATE_ADD(stc2.date, interval 1 day)
		    WHERE stc.game_id = '{$game_id}'
		    AND stc.date >= '{$start_date}'
		    AND stc.date <= '{$end_date}')
		    ORDER BY date DESC
		    ");
		}
		else
		{
		    $query = $this->db->query("
		    (SELECT 
		        {$date_group}(stc.date) 'date',
			    stc.game_id 'game_id',
			    SUM(stc.login_count) 'login_count',
			    SUM(stc.new_login_count) 'new_login_count',
			    SUM(stc.new_character_count) 'new_character_count',
			    SUM(stc.device_count) 'device_count',
			    SUM(stc2.login_count) 'y_login_count',
			    SUM(stc2.new_login_count) 'y_new_login_count',
			    SUM(stc2.one_retention_all_count) 'y_one_retention_all_count',
                SUM(stc2.one_retention_count) 'y_one_retention_count',
                SUM(stc.deposit_user_count) 'deposit_user_count',
                SUM(stc.new_deposit_user_count) 'new_deposit_user_count',
                SUM(stc.consume_user_count) 'consume_user_count',
                SUM(stc.new_consume_user_count) 'new_consume_user_count',
                SUM(stc.currency_total) 'currency_total',
                SUM(stc.paid_currency_total) 'paid_currency_total',
                SUM(stc.deposit_total) 'deposit_total',
                SUM(stc.consume_total) 'consume_total',
                SUM(stc.peak_user_count) 'peak_user_count',
                SUM(stc.total_time) 'total_time',
                SUM(stc.paid_total_time) 'paid_total_time'
		    FROM statistics stc
		    LEFT JOIN statistics stc2 ON stc.game_id=stc2.game_id AND stc.date=DATE_ADD(stc2.date, interval 1 {$date_group})
		    WHERE stc.game_id = '{$game_id}'
		    AND stc.date >= '{$start_date}'
		    AND stc.date <= '{$end_date}'
		    GROUP BY {$date_group}(stc.date))
		
		    UNION
		
		    (SELECT 
		        {$date_group}(stc.date) 'date',
			    stc.game_id 'game_id',
		    	SUM(stc.login_count) 'login_count',
			    SUM(stc.new_login_count) 'new_login_count',
			    SUM(stc.new_character_count) 'new_character_count',
			    SUM(stc.device_count) 'device_count',
			    SUM(stc2.login_count) 'y_login_count',
			    SUM(stc2.new_login_count) 'y_new_login_count',
			    SUM(stc2.one_retention_all_count) 'y_one_retention_all_count',
                SUM(stc2.one_retention_count) 'y_one_retention_count',
                SUM(stc.deposit_user_count) 'deposit_user_count',
                SUM(stc.new_deposit_user_count) 'new_deposit_user_count',
                SUM(stc.consume_user_count) 'consume_user_count',
                SUM(stc.new_consume_user_count) 'new_consume_user_count',
                SUM(stc.currency_total) 'currency_total',
                SUM(stc.paid_currency_total) 'paid_currency_total',
                SUM(stc.deposit_total) 'deposit_total',
                SUM(stc.consume_total) 'consume_total',
                SUM(stc.peak_user_count) 'peak_user_count',
                SUM(stc.total_time) 'total_time',
                SUM(stc.paid_total_time) 'paid_total_time'
		    FROM statistics stc
		    RIGHT JOIN statistics stc2 ON stc.game_id=stc2.game_id AND stc.date=DATE_ADD(stc2.date, interval 1 {$date_group})
		    WHERE stc.game_id = '{$game_id}'
		    AND stc.date >= '{$start_date}'
		    AND stc.date <= '{$end_date}'
		    GROUP BY {$date_group}(stc.date))
		    ORDER BY date DESC
		    ");
		}
		
		$this->g_layout
			->set("query", isset($query) ? $query : false)
			->set("game_id", $game_id)
			->set("span", $span)
			->set("game_id_1", $this->input->get("game_id_1"))
			->set("game_id_2", $this->input->get("game_id_2"))
			->set("game_id_3", $this->input->get("game_id_3"))
			->set("servers", $this->db->where("game_id", $this->game_id)->from("servers")->order_by("id")->get())
			->add_js_include("game/statistics")
			->add_js_include("jquery-ui-timepicker-addon")
			->render();
	}
	
}

/* End of file search.php */
/* Location: ./application/controllers/search.php */