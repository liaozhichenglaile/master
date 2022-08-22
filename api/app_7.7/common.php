<?php
/**
 * _ooOoo_
 * o8888888o
 * 88" . "88
 * (| -_- |)
 *  O\ = /O
 * ___/`---'\____
 * .   ' \\| |// `.
 * / \\||| : |||// \
 * / _||||| -:- |||||- \
 * | | \\\ - /// | |
 * | \_| ''\---/'' | |
 * \ .-\__ `-` ___/-. /
 * ___`. .' /--.--\ `. . __
 * ."" '< `.___\_<|>_/___.' >'"".
 * | | : `- \`.;`\ _ /`;.`/ - ` : | |
 * \ \ `-. \_ __\ /__ _/ .-` / /
 * ======`-.____`-.___\_____/___.-`____.-'======
 * `=---='
 *          .............................................
 *           佛曰：bug泛滥，我已瘫痪！
 */

if (!function_exists('set_password')) {
	/**
	 * 设置密码
	 *
	 * @param string $password 密码
	 * @return string
	 */
	function set_password($password)
	{
		return md5(env('database.prefix') . $password);
	}
}

if (!function_exists('sms_code')) {
	/**
	 * 生产短信验证码
	 *
	 * @return int
	 */
	function sms_code()
	{
		return rand(111111, 999999);
	}
}

if (!function_exists('set_uid')) {
	/**
	 * Uid
	 *
	 * @param string $param 参数
	 * @return string
	 */
	function set_uid($param)
	{
		return md5(time() . $param);
	}
}

if (!function_exists('set_order_no')) {
	/**
	 * 生产订单号
	 * @param $num
	 * @return string
	 */
	function set_order_no($num = 4)
	{
		mt_srand((double)microtime() * 1000000);//用 seed 来给随机数发生器播种。
		$strand = str_pad(mt_rand(1, 999), $num, "0", STR_PAD_LEFT);
		return date('ymdHis') . $strand;
	}
}

if (!function_exists('array_sort')) {
	/**
	 * 数组排序
	 *
	 * @param array $array
	 * @param string $keys
	 * @param string $sort
	 * @return array
	 */
	function array_sort($array, $keys, $sort = 'asc')
	{
		$newArr = $valArr = [];
		foreach ($array as $key => $value) {
			$valArr[$key] = $value[$keys];
		}

		// 先利用keys对数组排序，目的是把目标数组的key排好序
		($sort == 'asc') ? asort($valArr) : arsort($valArr);
		// 指针指向数组第一个值
		reset($valArr);
		foreach ($valArr as $key => $value) {
			$newArr[$key] = $array[$key];
		}
		return array_values($newArr);
	}
}

if (!function_exists('group_by')) {
	/**
	 * 根据keys分组,其余列合并
	 *
	 * @param array $array
	 * @param array $keys
	 * @return array
	 */
	function group_by($array, $keys = [])
	{
		$i = 0;
		while ($i < sizeof($array)) {
			$x = sizeof($array) - 1;
			while ($x > $i) {
				//取数组交集并返回交集，保留键名
				$temp = array_intersect_assoc($array[$i], $array[$x]);
				if (!empty($temp)) {
					// 取键名交集，并做比较，如果相交等于分组依据的键名，则说明两个子数组可以合并
					if (array_intersect_assoc($keys, array_keys($temp)) == $keys) {
						foreach ($array[$i] as $k => $v) {
							if (!in_array($k, $keys)) {
								$array[$i][$k] += $array[$x][$k];
							}
						}
						//将合并部分移出数组
						array_splice($array, $x, 1);
					}
				}
				$x--;
			}
			$i++;
		}
		return $array;
	}
}

if (!function_exists('curl_get')) {
	/**
	 * get 请求
	 * @param string $url
	 * @param array $options
	 * @param null $header
	 * @return bool|string
	 */
	function curl_get($url = '', $header = null, $options = [])
	{
		$ch = curl_init($url);
		if (!empty($header)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_HEADER, 0);//返回response头部信息
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		if (!empty($options)) {
			curl_setopt_array($ch, $options);
		}
		// https请求 不验证证书和host
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}

if (!function_exists('curl_post')) {
	/**
	 * post 请求
	 * @param string $url
	 * @param string $postData
	 * @param array $options
	 * @param null $header
	 * @return bool|string
	 */
	function curl_post($url = '', $postData = '', $header = null, $options = [])
	{
		if (is_array($postData)) {
			$postData = http_build_query($postData);
		}
		$ch = curl_init();
		if (!empty($header)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_HEADER, 0);//返回response头部信息
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
		if (!empty($options)) {
			curl_setopt_array($ch, $options);
		}
		//https请求 不验证证书和host
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}

if (!function_exists('num_to_cn_money')) {
	/**
	 * 数字转金额大写
	 *
	 * @param string $num
	 * @param boolean $mode
	 * @param boolean $sim
	 * @return string
	 */
	function num_to_cn_money($num, $mode = true, $sim = false)
	{
		if (!is_numeric($num)) return '含有非数字非小数点字符！';
		$char = $sim ? ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九']
			: ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
		$unit = $sim ? ['', '十', '百', '千', '', '万', '亿', '兆']
			: ['', '拾', '佰', '仟', '', '萬', '億', '兆'];
		$retval = $mode ? '元' : '点';
		//小数部分
		if (strpos($num, '.')) {
			[$num, $dec] = explode('.', $num);
			$dec = strval(round($dec, 2));
			if ($mode) {
				$retval .= "{$char[$dec['0']]}角{$char[$dec['1']]}分";
			} else {
				for ($i = 0, $c = strlen($dec); $i < $c; $i++) {
					$retval .= $char[$dec[$i]];
				}
			}
		}
		//整数部分
		$str = $mode ? strrev(intval($num)) : strrev($num);
		for ($i = 0, $c = strlen($str); $i < $c; $i++) {
			$out[$i] = $char[$str[$i]];
			if ($mode) {
				$out[$i] .= $str[$i] != '0' ? $unit[$i % 4] : '';
				if ($i > 1 and $str[$i] + $str[$i - 1] == 0) {
					$out[$i] = '';
				}
				if ($i % 4 == 0) {
					$out[$i] .= $unit[4 + floor($i / 4)];
				}
			}
		}
		$retval = join('', array_reverse($out)) . $retval;
		return $retval;
	}
}

if (!function_exists('convert_amount_to_cn')) {
	/**
	 * 将数值金额转换为中文大写金额
	 * @param $amount float 金额(支持到分)
	 * @param $type   int   补整类型,0:到角补整;1:到元补整
	 * @return mixed 中文大写金额
	 */
	function convert_amount_to_cn($amount, $type = 1)
	{
		// 判断输出的金额是否为数字或数字字符串
		if (!is_numeric($amount)) {
			return "要转换的金额只能为数字!";
		}

		// 金额为0,则直接输出"零元整"
		if ($amount == 0) {
			return "人民币零元整";
		}

		// 金额不能为负数
		if ($amount < 0) {
			return "要转换的金额不能为负数!";
		}

		// 金额不能超过万亿,即12位
		if (strlen($amount) > 12) {
			return "要转换的金额不能为万亿及更高金额!";
		}

		// 预定义中文转换的数组
		$digital = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
		// 预定义单位转换的数组
		$position = ['仟', '佰', '拾', '亿', '仟', '佰', '拾', '万', '仟', '佰', '拾', '元'];

		// 将金额的数值字符串拆分成数组
		$amountArr = explode('.', $amount);

		// 将整数位的数值字符串拆分成数组
		$integerArr = str_split($amountArr[0], 1);

		// 将整数部分替换成大写汉字
		$result = '人民币';
		$integerArrLength = count($integerArr);     // 整数位数组的长度
		$positionLength = count($position);         // 单位数组的长度
		$zeroCount = 0;                             // 连续为0数量
		for ($i = 0; $i < $integerArrLength; $i++) {
			// 如果数值不为0,则正常转换
			if ($integerArr[$i] != 0) {
				// 如果前面数字为0需要增加一个零
				if ($zeroCount >= 1) {
					$result .= $digital[0];
				}
				$result .= $digital[$integerArr[$i]] . $position[$positionLength - $integerArrLength + $i];
				$zeroCount = 0;
			} else {
				$zeroCount += 1;
				// 如果数值为0, 且单位是亿,万,元这三个的时候,则直接显示单位
				if (($positionLength - $integerArrLength + $i + 1) % 4 == 0) {
					$result = $result . $position[$positionLength - $integerArrLength + $i];
				}
			}
		}

		// 如果小数位也要转换
		if ($type == 0) {
			// 将小数位的数值字符串拆分成数组
			$decimalArr = str_split($amountArr[1], 1);
			// 将角替换成大写汉字. 如果为0,则不替换
			if ($decimalArr[0] != 0) {
				$result = $result . $digital[$decimalArr[0]] . '角';
			}
			// 将分替换成大写汉字. 如果为0,则不替换
			if ($decimalArr[1] != 0) {
				$result = $result . $digital[$decimalArr[1]] . '分';
			}
		} else {
			$result = $result . '整';
		}
		return $result;
	}
}

if (!function_exists('base64_image_content')) {
	/**
	 * 将Base64图片转换为本地图片并保存
	 *
	 * @param string $base64_image_content [要保存的Base64]
	 * @param string $path [要保存的路径]
	 * @return string|bool
	 */
	function base64_image_content($base64_image_content, $path)
	{
		//匹配出图片的格式
		if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
			$type = $result[2];
			$newFile = "/" . date('Ymd', time()) . "/";
			if (!file_exists($path . $newFile)) {
				//检查是否有该文件夹，如果没有就创建，并给予最高权限
				mkdir($path . $newFile, 0700, true);
			}
			$newFile = $newFile . uniqid() . ".{$type}";
			if (file_put_contents($path . $newFile, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
				return $newFile;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

if (!function_exists('get_random_string')) {
	/**
	 * 随机字符串
	 *
	 * @param int $len 长度
	 * @param null $chars 基数
	 * @return string
	 */
	function get_random_string($len, $chars = null)
	{
		if (is_null($chars)) {
			$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		}
		mt_srand(10000000 * (double)microtime());
		for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
			$str .= $chars[mt_rand(0, $lc)];
		}
		return $str;
	}

	if (!function_exists('get_express')) {
		/**
		 * 快递查询
		 * @param $express_num 快递号
		 * @return bool|mixed
		 */
		function get_express($express_num)
		{
			$host = 'https://wdexpress.market.alicloudapi.com';
			$path = '/gxali';
			$appcode = env('EXPRESS_ALIYUN_APP_CODE');//开通服务后 买家中心-查看AppCode
			$headers = [];
			array_push($headers, 'Authorization:APPCODE ' . $appcode);
			$querys = "n=$express_num";

			$url = $host . $path . "?" . $querys;

			$out_put = curl_get($url, $headers);

			if (!$out_put) {
				return false;
			}

			return json_decode($out_put);
		}
	}
}

if (!function_exists('is_url')) {
	/**
	 * 是否是url
	 * @param $url
	 * @return false|int
	 */
	function is_url($url)
	{
		$r = "/http[s]?:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is";
		return preg_match($r, $url);
	}
}

if (!function_exists('save_img')) {
	function save_img($imgUrl)
	{
		$ext = strrchr($imgUrl, '.');
		if (!in_array($ext, ['.jpg', '.png', '.jpeg', '.gif']))
			return $imgUrl;
		$baseName = basename($imgUrl);

		//文件保存绝对路径
		$path = public_path('storage/upload') . $baseName;
		$img = file_get_contents($imgUrl);
		file_put_contents($path, $img);
		return $path;
	}
}

if (!function_exists('filter_emoji')) {
	/**
	 * 过滤表情
	 * @param $str
	 * @return string|string[]|null
	 */
	function filter_emoji($str)
	{
		if (is_string($str) && $str) {
			$str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
				'/./u',
				function (array $match) {
					return strlen($match[0]) >= 4 ? '?' : $match[0];
				},
				$str);
		}
		return $str;
	}
}

if (!function_exists('constellation')) {
	/**
	 * 身份证获取星座
	 * @param $idCard
	 * @return string
	 */
	function get_constellation($idCard)
	{
		$bir = substr($idCard, 10, 4);
		$month = (int)substr($bir, 0, 2);
		$day = (int)substr($bir, 2);
		$strValue = '';
		if (($month == 1 && $day >= 20) || ($month == 2 && $day <= 18)) {
			$strValue = "水瓶座";
		} else if (($month == 2 && $day >= 19) || ($month == 3 && $day <= 20)) {
			$strValue = "双鱼座";
		} else if (($month == 3 && $day > 20) || ($month == 4 && $day <= 19)) {
			$strValue = "白羊座";
		} else if (($month == 4 && $day >= 20) || ($month == 5 && $day <= 20)) {
			$strValue = "金牛座";
		} else if (($month == 5 && $day >= 21) || ($month == 6 && $day <= 21)) {
			$strValue = "双子座";
		} else if (($month == 6 && $day > 21) || ($month == 7 && $day <= 22)) {
			$strValue = "巨蟹座";
		} else if (($month == 7 && $day > 22) || ($month == 8 && $day <= 22)) {
			$strValue = "狮子座";
		} else if (($month == 8 && $day >= 23) || ($month == 9 && $day <= 22)) {
			$strValue = "处女座";
		} else if (($month == 9 && $day >= 23) || ($month == 10 && $day <= 23)) {
			$strValue = "天秤座";
		} else if (($month == 10 && $day > 23) || ($month == 11 && $day <= 22)) {
			$strValue = "天蝎座";
		} else if (($month == 11 && $day > 22) || ($month == 12 && $day <= 21)) {
			$strValue = "射手座";
		} else if (($month == 12 && $day > 21) || ($month == 1 && $day <= 19)) {
			$strValue = "魔羯座";
		}
		return $strValue;
	}

	if (!function_exists('get_sex')) {
		/**
		 * 身份证获取性别
		 * @param $idCard
		 * @return int
		 */
		function get_sex($idCard)
		{
			$sexInt = (int)substr($idCard, 16, 1);

			return $sexInt % 2 === 0 ? 0 : 1;
		}
	}

	if (!function_exists('get_age')) {
		/**
		 * 身份证获取年龄
		 * @param $idCard
		 * @return false|string
		 */
		function get_age($idCard)
		{
			$year = substr($idCard, 6, 4);
			$monthDay = substr($idCard, 10, 4);

			$age = date('Y') - $year;
			if ($monthDay > date('md')) {
				$age--;
			}

			return $age;
		}
	}
}
