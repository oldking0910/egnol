<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class G_Wallet
{
    var $CI;
    var $error_message = '';

    function __construct()
    {    
    	$this->CI =& get_instance();  
    }
    
    function get_order($order_id)
    {
    	return $this->CI->db->select("ub.*, u.account")
    				->from("user_billing ub")
    				->join("users u", "ub.uid=u.uid")
    				->where("id", $order_id)->get()->row();
    }
    
    function get_balance($uid)
    {
    	$query = $this->CI->db->select("balance")->from("users")
    		->where("uid", $uid)->limit(1)->get();
    	if ($query->num_rows() > 0) {
    		return $query->row()->balance;
    	} 
    	else return 0;
    }
    
    function get_success_balance($uid)
    {
    	$this->CI->db->where("result", "1");
    	return $this->get_balance($uid);
    }    
    
    function chk_money_enough($uid, $chk_money)
    {
    	$balance = $this->get_balance($uid);
    	return $chk_money > $balance ? false : true; 
    }
    
    function chk_balance($uid)
    {    	
    	$sql = "
    	SELECT 
			u.uid,
			COALESCE((SELECT SUM(amount) FROM user_billing WHERE billing_type=1 AND result=1 AND uid=u.uid GROUP BY uid), 0) aq,
		    COALESCE((SELECT SUM(amount) FROM user_billing WHERE billing_type=2 AND result=1 AND uid=u.uid AND transaction_type not in ('rc_billing') GROUP BY uid), 0) amount,
		    COALESCE((SELECT SUM(amount) FROM user_billing WHERE billing_type=3 AND result=1 AND uid=u.uid GROUP BY uid), 0) rq,
		    COALESCE((SELECT SUM(amount) FROM user_billing WHERE billing_type=4 AND result=1 AND uid=u.uid GROUP BY uid), 0) gq,
		    u.balance
		FROM users u
		WHERE u.uid={$uid}";
    	$query = $this->CI->db->query($sql);
    	if ($query->num_rows() > 0) {
    		$row = $query->row();
    		$total = $row->aq + $row->rq + $row->gq;
    		if ( $total == ($row->amount + $row->balance)) return true; 
    		else return $this->_return_error("餘額不平衡");
    	} else return $this->_return_error('角色不存在');
    }
            
    //billing_type: 1購買,2轉點,3回補,4贈送
    function produce_order($uid, $transaction_type, $billing_type, $amount, $pay_server_id='', $order='')
    {	
    	if ($order) {
	    	$cnt = $this->CI->db->from("user_billing")->where("order", $order)->where_in("result", array("1","3"))->count_all_results();
			if ($cnt > 0)  return $this->_return_error("第三方訂單號已被使用");
    	}	
    	
    	$balance = $this->get_balance($uid);

    	switch ($billing_type)
    	{
    		/*case 1:
    			$calc_balance = $balance + $amount;
    			break;*/
    		
    		case 2:    	    	
		    	if (in_array($transaction_type, array('rc_billing', 'omg_billing', 'kimi_billing', 'beanfun_billing', 'bahamut_billing', 'artsy_billing', 'dtalent_billing', '179game_billing', 'smmo_billing', 'muxplay_billing', 'egame101_billing', '58play_billing', 'nicegame_billing', 'skyler_billing'))) {
		    		//omg交易從omg方扣款
		    		$calc_balance = $balance; 
		    	}
		    	else {
    				if ($amount > $balance) return $this->_return_error("餘額不足");
    				$calc_balance = $balance - $amount;
		    	}
    			break;
    			
    		case 3:
    			$calc_balance = $balance + $amount;
    			break;
    		
    		default:
				return $this->_return_error("billing_type錯誤");
    	}
    	
    	$users_data = array(
    		'balance' 		=> $calc_balance
    	);    	
			
    	$this->CI->db
    		->set("update_time", "now()", false)
			->where("uid", $uid)
    		->update("users", $users_data);
    	
		$country_code = geoip_country_code3_by_name($_SERVER['REMOTE_ADDR']);
		$country_code = ($country_code) ? $country_code : null;
		
    	$user_billing_data = array(
    		'uid' 			=> $uid,
    		'transaction_type' => $transaction_type,
    		'billing_type'	=> $billing_type,
    		'amount' 		=> $amount,
    		'pay_server_id' 		=> $pay_server_id,
    		'ip'		 	=> $_SERVER['REMOTE_ADDR'],
    		'balance' 		=> $calc_balance,
    		'result'		=> '0',
    		'note'			=> '',
			'country_code'  => $country_code,
    	);    	
    	$order && $user_billing_data["order"] = $order;
    	
    	$this->CI->db
    		->set("create_time", "now()", false)
    		->set("update_time", "now()", false)
    		->insert("user_billing", $user_billing_data);
			
    	return $this->CI->db->insert_id();
    }
    
    function complete_order($order)
    {
    	$this->CI->db->where("id", $order->id)->update("user_billing", array("result" => "1"));
    }
    
    function confirm_order($order)
    {
    	$this->CI->db->where("id", $order->id)->update("user_billing", array("is_confirmed" => "1"));
    }
    
    function cancel_order($order, $note='')
    {
    	$balance = $this->get_success_balance($order->uid);
    	$this->CI->db->where("id", $order->id)->update("user_billing", array("result" => "2", "balance" => $balance, "note" => $note));
	}
	
    function cancel_timeout_order($order)
    {
    	$balance = $this->get_balance($order->uid);
    	$this->CI->db->where("id", $order->id)->update("user_billing", array("result" => "3", "balance" => $balance));
	}	
	
    function cancel_other_order($order, $note='')
    {
    	$balance = $this->get_balance($order->uid);
    	$this->CI->db->where("id", $order->id)->update("user_billing", array("result" => "4", "balance" => $balance, "note" => $note));
	}		
	
    function update_order_note($order, $note='')
    {
    	$this->CI->db->where("id", $order->id)->update("user_billing", array("note" => $note));
	}		
	
    function update_order($order, $data) 
    {
		$this->CI->db
    		->set("update_time", "now()", false)
    		->where("id", $order->id)
    		->update("user_billing", $data);
    }
    
    function produce_mycard_order($uid, $mycard_billing_id, $transaction_type, $amount)
	{	
		if ( ! in_array($transaction_type, array("mycard_ingame", "mycard_billing"))) return $this->_return_error("transaction_type 錯誤");
		$cnt = $this->CI->db->from("user_billing")->where("mycard_billing_id", $mycard_billing_id)->where("result", "1")->count_all_results();
		if ($cnt > 0)  return $this->_return_error("ID已被使用");		
		
    	$balance = $this->get_balance($uid);
		$calc_balance = $balance + $amount;
    	
    	$users_data = array(
    			'balance' 		=> $calc_balance
    		);
    	
    	$this->CI->db
		    ->set("update_time", "now()", false)
			->where("uid", $uid)
			->update("users", $users_data);
    	
		$country_code = geoip_country_code3_by_name($_SERVER['REMOTE_ADDR']);
		$country_code = ($country_code) ? $country_code : null;
		
    	$user_billing_data = array(
    			'uid' 			=> $uid,
    			'transaction_type' => $transaction_type,
    			'billing_type'	=> '1',
    			'amount' 		=> $amount,
    			'ip'		 	=> $_SERVER['REMOTE_ADDR'],
    			'result'		=> '1',
    			'mycard_billing_id' => $mycard_billing_id,
				'country_code'  => $country_code,
    		);
    	
    	$this->CI->db->set("create_time", "now()", false)->insert("user_billing", $user_billing_data);
    	return $this->CI->db->insert_id();
    }    
    
    function produce_gash_order($uid, $gash_billing_id, $amount)
	{	
		$cnt = $this->CI->db->from("user_billing")->where("gash_billing_id", $gash_billing_id)->where("result", "1")->count_all_results();
		if ($cnt > 0)  return $this->_return_error("ID已被使用");			
		
    	$balance = $this->get_balance($uid);
		$calc_balance = $balance + $amount;
    	
    	$users_data = array(
    			'balance' 		=> $calc_balance
    		);
    	
    	$this->CI->db
		    ->set("update_time", "now()", false)
			->where("uid", $uid)
			->update("users", $users_data);
			
		$country_code = geoip_country_code3_by_name($_SERVER['REMOTE_ADDR']);
		$country_code = ($country_code) ? $country_code : null;
    	
    	$user_billing_data = array(
    			'uid' 			=> $uid,
    			'transaction_type' => 'gash_billing',
    			'billing_type'	=> '1',
    			'amount' 		=> $amount,
    			'ip'		 	=> $_SERVER['REMOTE_ADDR'],
    			'result'		=> '1',
    			'gash_billing_id' => $gash_billing_id,
				'country_code'  => $country_code,
    		);
    	
    	$this->CI->db->set("create_time", "now()", false)->insert("user_billing", $user_billing_data);
    	return $this->CI->db->insert_id();
    }      
    
    function produce_income_order($uid, $tran_type, $tran_id, $amount, $order='')
	{	
		if ($order) {
			$cnt = $this->CI->db->from("user_billing")
					->where("transaction_type", $tran_type)->where("order", $order)->where_in("result", "1")->count_all_results();
			if ($cnt > 0)  return $this->_return_error("第三方訂單號已被使用");
		}
		else {
			$cnt = $this->CI->db->from("user_billing")
					->where("transaction_type", $tran_type)->where("transaction_id", $tran_id)
					->where("result", "1")->count_all_results();
			if ($cnt > 0) return $this->_return_error("交易ID已被使用");
		}			
		
    	$balance = $this->get_balance($uid);
    	$calc_balance = $balance + $amount;
    	
    	$users_data = array(
    			'balance' 		=> $calc_balance
    		);
    	
    	$this->CI->db
    		->set("update_time", "now()", false)
    		->insert("users", $users_data);
    	
		$country_code = geoip_country_code3_by_name($_SERVER['REMOTE_ADDR']);
		$country_code = ($country_code) ? $country_code : null;
		
    	$user_billing_data = array(
    			'uid' 			=> $uid,
    			'transaction_type' => $tran_type,
    			'transaction_id'=> $tran_id,    			
    			'billing_type'	=> '1',
    			'amount' 		=> $amount,
    			'ip'		 	=> $_SERVER['REMOTE_ADDR'],
    			'result'		=> '1',
				'country_code'  => $country_code,
    		);
    	$order && $data["order"] = $order;
    	
    	$this->CI->db
    		->set("create_time", "now()", false)
    		->set("update_time", "now()", false)
    		->insert("user_billing", $user_billing_data);
    	
    	return $this->CI->db->insert_id();
    }      
    
    function _return_error($msg) {
    	$this->error_message = $msg;
    	return false;
    }
}

/* End of file Template.php */
/* Location: ./system/application/libraries/Template.php */