<?php
$sourceMsg = [
	// 验证码的链接
	'url' => 'https://authserver.jxust.edu.cn/authserver/captcha.html?ts=89',
	'rootUrl' => 'https://authserver.jxust.edu.cn',
	// 最多识别次数
	'num' => 15,
	// 匹配度权值，如果一直匹配不上可以尝试降低一下权值，但是准确性会降低
	'limit' => 90 
];

// 这是数据库的配置，reback里也要配置一下
$database = [
	'host' => 'localhost',
	'user' => 'root',
	'password' => 'z123654',
	'database' => 'test',
];


// echo '<br><img src="http://jw.jxust.edu.cn/verifycode.servlet"><br><br>';
function getPic () {
	global $sourceMsg;
	global $database;
    $filename = "image.jpg";
    # 文件下载 BEGIN #
    // 打开临时文件，用于写入（w),b二进制文件
    // $resource = fopen($tmpFile, 'wb');
    // $resource = '';
    $fp = fopen($filename,'wb');
    $curl = curl_init();
    // 设置输出文件为刚打开的
    curl_setopt($curl,CURLOPT_URL, $sourceMsg['url']);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)
     AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36');
    curl_setopt($curl, CURLOPT_FILE, $fp);
    // 不需要头文件
    curl_setopt($curl, CURLOPT_REFERER, $sourceMsg['rootUrl']);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // curl_setopt($curl, CURLOPT_COOKIE, 'route=94a807440c1b0b087ab98739e4609bc3; org.springframework.web.servlet.i18n.CookieLocaleResolver.LOCALE=zh_CN; JSESSIONID=M6eYjgu6iyItf9-i6ndOZDrc5TcQuRC4taDDivdQIWfHGUKUKPtG!-1085678171');
    curl_setopt($curl,CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($curl,CURLOPT_TIMEOUT,60);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
    // 执行
    curl_exec($curl);
    // 关闭curl
    curl_close($curl);
    // return $res;
    // var_dump($fp);
    fclose($fp);
}
        
$pic_2 = [];
$link= mysqli_connect($database['host'],$database['user'],$database['password'],$database['database']);
$link->query('set names utf8');
$sql = 'SELECT * FROM `source1_1`';
$res = $link->query($sql);
$source = [];
while($s = mysqli_fetch_assoc($res)){
    $source[] = $s;
}
$link->close();
function getHec($imagePath, $num, $source)
{
	global $database;
	global $sourceMsg;
    getPic();
    $pic_1 = [];
    $res = imagecreatefromjpeg($imagePath);
    //echo $res[]
    //  $size = getimagesize($imagePath);
    //  var_dump($size);
    // $codes = [];
    $k = 0;
    $pic = [];
    $pic_1[0] = ''; 
    $pic_1[1] = ''; 
    $pic_1[2] = ''; 
    $pic_1[3] = '';
    for ($i = 1, $m = 0; $i < 33; $i++) {
        for ($j = 1; $j < 91; $j++) {
            $rgb = imagecolorat($res, $j, $i);
            $rgbarray = imagecolorsforindex($res, $rgb);
            if ($rgbarray['red'] < 100 || $rgbarray['green'] < 100 || $rgbarray['blue'] < 100) {
                // echo "0";
                $pic[$m][$k++] = '0';
            } else {
                // echo "1";
                $pic[$m][$k++] = '1';
            }
        }
        $m++;
        $k = 0;
        // echo "<br>";
    }
    // echo $pic[0][0];
    // echo count($pic[0]);
    // $coeds = [];
    for ($i = 0; $i < count($pic[0]); $i++) {
        for ($j = 0; $j < count($pic); $j++) {
            if(($pic[$j][$i] == '0')&&($i != '0')&&($j != 0)&&($i != (count($pic[0]) - 1))&&($j != (count($pic) - 1))){
                if(($pic[$j + 1][$i] == '0')||($pic[$j - 1][$i] == '0')||($pic[$j][$i + 1] == '0')||($pic[$j][$i - 1] == '0')){
                    $pic[$j][$i] = '0';
                }else{
                    $pic[$j][$i] = '1';
                }
            }
            $codes[$i][$j] = $pic[$j][$i];
        }
    }
    // var_dump($codes);
    for ($i = 1, $falg = 0, $row=0; $i < count($codes)-1; $i++) {
        if($row>3){
            break;
        }
        if($falg&&(!in_array('0' , $codes[$i]))){
            $row++;
            $falg = 0;
            continue;
        }
        for ($j = 0; $j < count($codes[$i]); $j++) {
            if ($j == count($codes[$i])-1) {
                if($falg) $pic_1[$row] = $pic_1[$row] . '1';
                continue;
            }
            if($codes[$i][$j] == '0' && ($i != 0) && ($j != 0)){
                if(($codes[$i+1][$j] == '0')||($codes[$i-1][$j] == '0')||($codes[$i][$j+1] == '0')||($codes[$i][$j-1] == '0')){
                    $pic_1[$row] = $pic_1[$row] . '0';
                    $falg = 1;
                }else {
                    if($falg)  $pic_1[$row] = $pic_1[$row] . '1';
                    // $pic_1[$row] = $pic_1[$row] . '1';
                }
            }else {
                if($falg)  $pic_1[$row] = $pic_1[$row] . '1';
            }
            // echo $pic[$j][$i];
        }
        // echo "<br>";
    }
    
    
    for($i = 0; $i < count($pic_1); $i++){
        $pic_1[$i] = cutString($pic_1[$i], '1'); 
        // echo 'size=' . strlen($pic_1[$i]) . '<br>';
        // echo $pic_1[$i] . '<br>';
    }

    // echo '<br><br><br>';
    $true_codes = [];
    $parcent = [];
    $sourceID = [];
    // echo '|' . date("i:s") . '&nbsp;&nbsp;';
    for ($i = 0; $i < count($pic_1); $i++) {
        $true_codes[$i] = '';
        if($pic_1[3] == ''){
            break;
        }
        for ($j = 0; $j < count($source); $j++) {
            if (similar_text($pic_1[$i], $source[$j]['code'], $parcent[$i])) {
                if($parcent[$i] >= $sourceMsg['limit']){
                    $link= mysqli_connect($database['host'],$database['user'],$database['password'],$database['database']);
                    $link->query('set names utf8');
                    $sql = "UPDATE `source1_1` SET `extra` = ". ($source[$j]['extra'] ? ($source[$j]['extra']+1) : 1) ." WHERE `source1_1`.`id` = " . $source[$j]['id'];
                    $res = $link->query($sql);
                    $link->close();
                    $true_codes[$i] = $source[$j]['value'];
                    $sourceID[$i] = $source[$j]['id'];
                    // echo '<br>';
                    // echo $parcent;
                    // echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $source[$j]['id'];
                    break;
                }
            }
        }
        if ($true_codes[$i] == '') {
            break;
        }
    }
    // echo date("i:s") . '&nbsp;&nbsp;';
    // echo '<br><img src="image.jpg"><br><br>';
    // var_dump($true_codes);
    // var_dump($parcent);
    // var_dump($sourceID);
    // return $pic_1;
    if ($true_codes[0] && $true_codes[1] && $true_codes[2] && $true_codes[3]){
        echo '<br><img src="image.jpg"><br><br>第' . $num . '次识别成功！结果是：' . implode('',$true_codes);
        // var_dump($pic_1);
        var_dump($parcent);
        var_dump($sourceID);
        // var_dump($true_codes);
        return $pic_1;
    }else{
        if($num <= $sourceMsg['num']){
            $num++;
            return getHec($imagePath, $num, $source);
        }else{
            echo "识别失败";
            exit;
        }
    }
}
$pic_2 = getHec("image.jpg", 1, $source);
function cutString($str, $s){
    if(substr($str, -1, 1) == $s){
        $str = substr($str, 0, -1);
        return cutString($str, $s);
    }else{
        return $str;
    }
}

// var_dump($pic);
// echo "<br>";
// var_dump($codes);
// echo $pic_1 . "<br>";
?>

    <form action="reback.php" method="POST" >
        <input type="text" value="<?php echo $pic_2[0]; ?>" name="code_0">
        <input type="text" value="<?php echo $pic_2[1]; ?>" name="code_1">
        <input type="text" value="<?php echo $pic_2[2]; ?>" name="code_2">
        <input type="text" value="<?php echo $pic_2[3]; ?>" name="code_3"><br>
        <input type="text" class="value" value="" name="value_0">
        <input type="text" class="value" value="" name="value_1">
        <input type="text" class="value" value="" name="value_2">
        <input type="text" class="value" value="" name="value_3">
        <input type="submit" id="submit" value="提交">
    </form>
    <!-- <script>window.location.href = 'demo - 4.php'</script> -->
    <script>
        
        var submit = document.getElementById('submit')
            value = document.getElementsByClassName('value')
        window.onload = function () {
            for(var i = 0; i<value.length; i++){
                value[i].value = '';
            }
        }
    </script>

