<?php

// 数据库的配置index里面也要去配置一下
$database = [
	'host' => 'localhost',
	'user' => 'root',
	'password' => 'z123654',
	'database' => 'test',
];


$code[0] = @$_POST['code_0'];
$code[1] = @$_POST['code_1'];
$code[2] = @$_POST['code_2'];
$code[3] = @$_POST['code_3'];
$value[0] = @$_POST['value_0'];
$value[1] = @$_POST['value_1'];
$value[2] = @$_POST['value_2'];
$value[3] = @$_POST['value_3'];


$link = mysqli_connect($database['host'],$database['user'],$database['password'],$database['database']);
$link->query('set names utf8');
for ($i = 0; $i < count($code); $i++) {
    if ($value[$i]) {
        $sql = "INSERT INTO `source1_1` (`code`, `value`, `extra`, `time`) VALUES ('" . $code[$i] . "', '" . $value[$i] . "', NULL, CURRENT_TIMESTAMP)";
        $res = $link->query($sql);
        if (!$res) {
            echo 'error';
            exit;
        }
    }
}
echo "<script>window.history.back();</script>";
$link->close();
