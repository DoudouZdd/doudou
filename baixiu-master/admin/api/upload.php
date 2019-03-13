<?php

/**
 * @Author: Deng Qiong
 * @Date:   2018-06-06 14:51:24
 * @Last Modified by:   Deng Qiong
 * @Last Modified time: 2018-06-06 15:03:31
 */
//var_dump($_FILES['avatar']);

if(empty($_FILES['avatar'])){
	exit("必须上传文件");
}

$avatar = $_FILES['avatar'];
if($avatar['error']!='UPLOAD_ERR_OK'){
	exit('上传失败');
}

//校验类型 大小

//移动文件到网站范围内
$ext = pathinfo($avatar['name'],PATHINFO_EXTENSION);
$target = '../../static/uploads/img-'.uniqid().'.'.$ext;

if(!move_uploaded_file($avatar['tmp_name'], $target)){
	exit('上传失败');
}

//上传成功
echo substr($target, 5);