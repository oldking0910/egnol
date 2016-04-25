<?php

$config["gash"] = array(
	"amount" => array("100", "300", "500", "1000", "3000", "5000", "10000", "20000", "30000"),
	"m_amount" => array("60", "150", "300", "660", "790", "1490", "3590", "5990", "9990", "14900", "29900"),		
	"global" => array(
		"MID" => "M1000491",
		"CID" => "C004910000938",
		"key" => "PR7yTf%SJUhwJ",
		"secret1" => "9AgMquZpqYQsWSaEjaTusI6RqjznXAvE",
		"secret2" => "tpt2EaoWcK4=",
	),
	"tw" => array(
		"MID" => "M1000490",
		"CID" => "C004900000937",
		"key" => "KDTW8#PSq",
		"secret1" => "qmt6aVg3mO1lm0ZHkX01RKJl3uMXU1ym",
		"secret2" => "EuTzhXbemAU=",
	),
	"long_e2" => array(
		"MID" => "M1000309",
		"CID" => "C003090000490",
		"key" => "SPODFJS'PIHSJP'",
		"secret1" => "xi1agWVjPvqg1MWUzo2Og2F5psDyum06",
		"secret2" => "/XryKjqPjus=",
	),		
	"url" => array(
		"order" => "https://api.eg.gashplus.com/CP_Module/order.aspx",
		"settle" => "https://api.eg.gashplus.com/CP_Module/settle.asmx?WSDL",
		"checkorder" => "https://api.eg.gashplus.com/CP_Module/checkorder.asmx?WSDL",
	),
	"PAID" => array(
		"COPGAM02" 	=> "GASH+點數卡",
		"COPGAM05" 	=> "GASH+點數卡(Moblie)",
		"COPGAM08"	=> "GASH+ 會員帳戶支付",			
		"TELCHT05"	=> "中華電信839三合一",
		"TELCHT06"	=> "中華電信數據三合一(Hinet)",
		"TELCHT07"	=> "中華電信市話三合一",
		"BNK80801"	=> "玉山銀行WEB ATM",
		"BNK80802"	=> "玉山銀行消費性扣款",
		"BNK80803"	=> "玉山銀行(大額交易)",
		"BNK80804"	=> "玉山支付通(支付寶)",
		"BNKRBS01" 	=> "全球信用卡",
		"BNK82201" 	=> "中國信託一般信用卡",	
		"COPPAL01"	=> "PayPal",
		"COPPEZ01"	=> "PAYEASY",
		"COPGV01" 	=> "Indonesia untuk membayar",
		"COPMOZ01" 	=> "Pilipinas upang bayaran",
		"COPPSB01" 	=> "ธนาคารไทยการชำระเงิน",
		"COPPST01" 	=> "Thanh Toán Việt Nam",
		"COPWBC02" 	=> "Malaysia Bank untuk Bayar",
		"TELSON04"	=> "亞太電信",
		"TELCHT05"	=> "中華電信 手機 839(三合一)",
		"TELCHT06"	=> "中華電信 Hinet(三合一)",
		"TELCHT07"	=> "中華電信 市話(三合一)",
		"TELFET01"	=> "遠傳電信一般型",
		"TELTCC01"	=> "台灣大哥大一般型",
		"TELDANAL01" 	=> "한국 통신 결제",
	),
	"CUID" => array(
		"PIN"	=> "GASH 點數卡儲值",
		"TWD"	=> "新台幣",
		"PHP" 	=> "菲國比索",
		"MYR" 	=> "馬來西亞令吉",
		"USD" 	=> "美金",
		"EUR" 	=> "歐元",
		"IDR" 	=> "印尼盾",
		"THB" 	=> "泰銖",	
		"VND" 	=> "越南幣",
		"KRW" 	=> "韓元",
		"HKD" 	=> "港幣",				
	),		
	"converter" => array(
		"TWD" 	=> 1,
		"PIN" 	=> 1,
		"PHP" 	=> 1.7,
		"MYR" 	=> 0.14,
		"USD" 	=> 0.036,
		"EUR" 	=> 0.028,
		"IDR" 	=> 460,
		"THB" 	=> 1.5,	
		"VND" 	=> 880,
		"KRW" 	=> 44,
		"HKD" 	=> 2.5, // 1港幣 = 10港點, 4台幣 = 1港幣 => 1台幣 = 2.5港點
	),			
	"RCODE" => array(
		'1001' => '驗證碼錯誤',
		'1002' => '未傳入BASE64編碼參數',
		'1101' => '錯誤的訊息格式',
		'1102' => '錯誤的訊息格式(PA幣別錯誤) ',
		'1103' => '錯誤的訊息格式(XML無法解析) ',
		'1104' => '不合法的交易',
		'1105' => '不合法的金額',
		'1106' => '不合法的ERP商品代碼',
		'1107' => '不合法的交易訊息代碼或交易處理代碼',
		'1108' => '不合法的月租交易參數',
		'1109' => '錯誤的訊息格式(CONTENT幣別錯誤) ',
		'1110' => '此訂單的PA不支援小數金額',
		'1201' => '不合法的商家代碼、服務代碼',
		'1202' => '不合法的商家代碼',
		'1203' => '不合法的平台代碼',
		'1204' => '不合法的服務代碼',
		'1205' => '不合法的網路位址',
		'1301' => '不合法或不存在的付款機構',
		'1401' => '無法找到原始交易(例如退訂找不到原始訂單編號)',
		'1402' => '交易內容與原始交易不一致',
		'1501' => '額度不足 ',
		'1502' => '超過金額上限',
		'1503' => '無效的交易時間',
		'1504' => '不允許使用的付款機構',
		'1601' => '未啟用的商家代碼',
		'1602' => '未啟用的平台代碼',
		'1603' => '未啟用的服務代碼',
		'1604' => '停用的商家代碼',
		'1605' => '停用的平台代碼',
		'1606' => '停用的服務代碼',
		'1607' => '停用的付款機構',
		'1999' => '交易參數驗證失敗',
		'2001' => '交易重複 ',
		'2002' => '重複請款 ',
		'2003' => '月租僅第一期需要請款',
		'2004' => '無法解開交易參數',
		'3001' => '無法完成付款',
		'3002' => '付款機構不提供月租服務',
		'3003' => '付款機構只提供月租服務',
		'3004' => '消費者付款待確認',
		'3010' => '超過使用者額度',
		'3011' => '號碼資格不符',
		'3012' => '使用者已申請此服務',
		'3098' => '付款機構系統繁忙，請稍後再試',
		'3099' => '付款機構系統錯誤',
		'3801' => 'GASHPCP帳號剩餘點數不足',
		'3802' => 'GASHPCP帳號未開啟異業轉點服務',
		'3803' => 'GASHPCP帳號交易金額累計已達上限',
		'3901' => '儲值點數不一致',
		'3902' => '儲值密碼已鎖定',
		'3903' => '儲值密碼已使用',
		'3904' => '儲值密碼錯誤',
		'3905' => '儲值密碼無法使用',
		'8101' => 'GPS系統維護中',
		'8201' => '付款機構系統維護中',
		'9001' => '交易參數無法轉換',
		'9002' => '無法初始化記錄檔',
		'9003' => '無法初始化交易',
		'9404' => 'REQUEST頁面不存在',
		'9500' => '系統發生錯誤',
		'9700' => '資料查詢失敗',
		'9701' => '資料查詢失敗(參數不允許為NULL) ',
		'9702' => '資料新增失敗',
		'9703' => '資料新增失敗(超過迴圈限制) ',
		'9704' => '資料修改失敗',
		'9705' => '資料修改失敗(參數不允許為NULL) ',
		'9880' => 'KEY值重複',
		'9990' => '商家系統繁忙',
		'9998' => 'GPS系統繁忙',
		'9999' => 'GPS系統異常',					
	),
	"items" => array(
		"Gash+儲值卡(港幣)" => array("PAID"=>"COPGAM02", "CUID"=>"HKD", "ERP_ID"=>"PINHALL"),
		"Gash+儲值卡(菲國比索)" => array("PAID"=>"COPGAM02", "CUID"=>"PHP", "ERP_ID"=>"PINHALL"),
		"Gash+儲值卡(馬來西亞令吉)" => array("PAID"=>"COPGAM02", "CUID"=>"MYR", "ERP_ID"=>"PINHALL"),
		"全球信用卡(美金)" => array("PAID"=>"BNKRBS01", "CUID"=>"USD", "ERP_ID"=>"J990001"),
		"全球信用卡(歐元)" => array("PAID"=>"BNKRBS01", "CUID"=>"EUR", "ERP_ID"=>"J990001"),
		"Indonesia untuk membayar(印尼盾)" => array("PAID"=>"COPGV01", "CUID"=>"IDR", "ERP_ID"=>"J990001"),
		"Pilipinas upang bayaran(菲國比索)" => array("PAID"=>"COPMOZ01", "CUID"=>"PHP", "ERP_ID"=>"J990001"),
		"ธนาคารไทยการชำระเงิน(泰銖)" => array("PAID"=>"COPPSB01", "CUID"=>"THB", "ERP_ID"=>"J990001"),
		"Thanh Toán Việt Nam(越南幣)" => array("PAID"=>"COPPST01", "CUID"=>"VND", "ERP_ID"=>"J990001"),
		"Malaysia Bank untuk Bayar(馬來西亞令吉)" => array("PAID"=>"COPWBC02", "CUID"=>"MYR", "ERP_ID"=>"J990001"),
		"한국 통신 결제(韓元)" => array("PAID"=>"TELDANAL01", "CUID"=>"KRW", "ERP_ID"=>"J990001"),												
	)		
);

if (ENVIRONMENT == 'development') {
	$config["gash"]["url"] = array(
			"order" => "https://stage-api.eg.gashplus.com/CP_Module/order.aspx",
			"settle" => "https://stage-api.eg.gashplus.com/CP_Module/settle.asmx?WSDL",
			"checkorder" => "https://stage-api.eg.gashplus.com/CP_Module/checkorder.asmx?WSDL",
	);
	$config["gash"]["global"]["MID"] = "M1000525";
	$config["gash"]["global"]["CID"] = "C005250000918";
	$config["gash"]["global"]["key"] = "fhnxdrrQ3RWRVZFD";
	$config["gash"]["global"]["secret1"] = "jC8/z7Rk6JWGcr3K1LN3YxxEyFphfygV";
	$config["gash"]["global"]["secret2"] = "tTVvofLpTMo=";	
	$config["gash"]["tw"]["MID"] = "M1000524";
	$config["gash"]["tw"]["CID"] = "C005240000917";
	$config["gash"]["tw"]["key"] = "45Y75RIJMCGH";
	$config["gash"]["tw"]["secret1"] = "0/NKR+XpXmQVXreqh5p/ZRoAQ7i5oMp1";
	$config["gash"]["tw"]["secret2"] = "rQDFxzAKGF8=";
}
