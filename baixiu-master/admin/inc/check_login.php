<?php

/**
 * @Author: Deng Qiong
 * @Date:   2018-06-04 16:09:08
 * @Last Modified by:   Deng Qiong
 * @Last Modified time: 2018-06-04 16:22:08
 */
// 校验数据当前访问用户的 箱子（session）有没有登录的登录标识
session_start();

if (empty($_SESSION['current_login_user'])) {
  // 没有当前登录用户信息，意味着没有登录
  header('Location: /admin/login.php?reurl='.urlencode($_SERVER['PHP_SELF']));
}

?>