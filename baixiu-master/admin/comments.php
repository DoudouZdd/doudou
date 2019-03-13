<?php 
require_once '../functions.php';

xiu_get_current_user();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <?php include 'inc/navbar.php'; ?>

    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <!-- <ul class="pagination pagination-sm pull-right">
          <li><a href="#">上一页</a></li>
          <li><a href="#">1</a></li>
          <li><a href="#">2</a></li>
          <li><a href="#">3</a></li>
          <li><a href="#">下一页</a></li>
        </ul> -->
        <ul class="pagination pagination-sm pull-right"></ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>
          <!-- <tr class="danger">
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>未批准</td>
            <td class="text-center">
              <a href="post-add.html" class="btn btn-info btn-xs">批准</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>已批准</td>
            <td class="text-center">
              <a href="post-add.html" class="btn btn-warning btn-xs">驳回</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td>大大</td>
            <td>楼主好人，顶一个</td>
            <td>《Hello world》</td>
            <td>2016/10/07</td>
            <td>已批准</td>
            <td class="text-center">
              <a href="post-add.html" class="btn btn-warning btn-xs">驳回</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr> -->
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'comments'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script id="comment_tmpl" type="text/x-jsrender">
    {{if success}}
    {{for data}}
    <tr class="{{: status === 'held' ? 'warning' : status === 'rejected' ? 'danger' : '' }}" data-id="{{: id }}">
      <td class="text-center"><input type="checkbox"></td>
      <td>{{: author }}</td>
      <td>{{: content }}</td>
      <td>《{{: post_title }}》</td>
      <td>{{: created}}</td>
      <td>{{: status === 'held' ? '待审' : status === 'rejected' ? '拒绝' : '准许' }}</td>
      <td class="text-center">
        {{if status === 'held'}}
        <a class="btn btn-info btn-xs btn-edit" href="javascript:;" data-status="approved">批准</a>
        <a class="btn btn-warning btn-xs btn-edit" href="javascript:;" data-status="rejected">拒绝</a>
        {{/if}}
        <a class="btn btn-danger btn-xs btn-delete" href="javascript:;">删除</a>
      </td>
    </tr>
    {{/for}}
    {{else}}
    <tr>
      <td colspan="7">{{: message }}</td>
    </tr>
    {{/if}}
  </script>
  <script>NProgress.done()</script>

  <script>
    $(function($){
      // var $alert = $('.alert');
      var $tbody = $('tbody');
      var $tmpl = $('#comment_tmpl');
      var $pagination = $('.pagination');

      // 页大小
      var size = 10;
      // 当前页码
      var currentPage = window.localStorage? localStorage.getItem("last_comments_page"): Cookie.read("last_comments_page") || 1;

       /**
       * 加载指定页数据
       * page:当前要加载的页数   
       * is_first:是否是初始化加载页(1初始化加载页0不是)
       */
      function loadData (page,is_first=0) {
        $.get('/admin/api/comment-list.php', { p: page, s: size }, function (res) {
          if(Math.ceil(res.total_count / size)<page){
            //当删除数据时  会有此情况出现
            //window.localStorage.setItem('last_comments_page',Math.ceil(res.total_count / size));
            currentPage = Math.ceil(res.total_count / size);
            loadData (Math.ceil(res.total_count / size));
            return false;
          }

          // 通过模板引擎渲染数据
          var html = $tmpl.render(res);
          // 设置到页面中
          $tbody.fadeOut(function(){
            $(this).html(html).fadeIn()
          });
            

          //解决分页组件不能重新渲染问题
          //注意：必须在有分页组件的情况下，才能销毁分页组件;否则报错
          if(!is_first)  $pagination.twbsPagination('destroy');
          $pagination.twbsPagination('destroy');
           
          // 分页组件
          var data = {
            first:'第一页',
            prev:'前一页',
            next:'后一页',
            last:'最后一页',
            startPage: parseInt(page),   
            //注意：必须为number类型
            totalPages: Math.ceil(res.total_count / size),
            //注意：必须为number类型

            initiateStartPageClick: false, 
            // false 第一次不会触发onPageClick；否则 onPageClick 第一次就会触发
            
            onPageClick: function (e, page) {   //页面点击时触发事件  
              currentPage = page;

              //点击时 存储数据-当前页码
              //存储，IE6~7 cookie ; 其他浏览器HTML5本地存储
              window.localStorage?localStorage.setItem("last_comments_page", currentPage):Cookie.write("last_comments_page", currentPage);

              loadData(page)
            }
          };
          $pagination.twbsPagination(data);

        })
      }
      loadData(currentPage,1);

      

      //删除评论
      //动态添加，为了能获取事件执行对象 使用委托事件
      $tbody.on('click','.btn-delete',function(){
        var $tr = $(this).parent().parent()
        var id = parseInt($tr.data('id'))
        $.get('/admin/api/comment-delete.php', { id: id }, function (res) {
          //alert(currentPage);
          res.success && loadData(currentPage)
        })
      });

      // 修改评论状态
      $tbody.on('click', '.btn-edit', function () {
        var id = parseInt($(this).parent().parent().data('id'))
        var status = $(this).data('status')
        $.post('/admin/api/comment-status.php?id=' + id, { status: status }, function (res) {
          res.success && loadData(currentPage)
        })
      })

      var $btnBatch = $('.btn-batch');

      // 批量操作按钮
      $tbody.on('change', 'td > input[type=checkbox]', function () {
        var id = parseInt($(this).parent().parent().data('id'))
        if ($(this).prop('checked')) {
          checkedItems.push(id)
        } else {
          checkedItems.splice(checkedItems.indexOf(id), 1)
        }
        checkedItems.length ? $btnBatch.fadeIn() : $btnBatch.fadeOut()
      })

      // 全选 / 全不选
      $('th > input[type=checkbox]').on('change', function () {
        var checked = $(this).prop('checked')
        $('td > input[type=checkbox]').prop('checked', checked).trigger('change')
      })

      // 批量操作
      $btnBatch
        // 批准
        .on('click', '.btn-info', function () {
          $.post('/admin/api/comment-status.php?id=' + checkedItems.join(','), { status: 'approved' }, function (res) {
            res.success && loadData(currentPage)
          })
        })
        // 拒绝
        .on('click', '.btn-warning', function () {
          $.post('/admin/api/comment-status.php?id=' + checkedItems.join(','), { status: 'rejected' }, function (res) {
            res.success && loadData(currentPage)
          })
        })
        // 删除
        .on('click', '.btn-danger', function () {
          $.get('/admin/api/comment-delete.php', { id: checkedItems.join(',') }, function (res) {
            res.success && loadData(currentPage)
          })
        })

    });
  </script>
</body>
</html>
