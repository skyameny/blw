$(function () {

  'use strict';
  var $image = $("#admin_avatar");
  var options = {
	        aspectRatio: 1 / 1,
	        preview: '.img-preview',
	        crop: function (e) {
	            console.log(event.detail.x);
	            console.log(event.detail.y);
	            console.log(event.detail.width);
	            console.log(event.detail.height);
	            console.log(event.detail.rotate);
	            console.log(event.detail.scaleX);
	            console.log(event.detail.scaleY);
	        }
	      };
  var originalImageURL = $image.attr('src');
  var uploadedImageName = 'cropped.jpg';
  var uploadedImageType = 'image/jpeg';
  var uploadedImageURL;
  
  // Cropper
  $image.on({
    ready: function (e) {
      console.log(e.type);
    },
    cropstart: function (e) {
      console.log(e.type, e.detail.action);
    },
    cropmove: function (e) {
      console.log(e.type, e.detail.action);
    },
    cropend: function (e) {
      console.log(e.type, e.detail.action);
    },
    crop: function (e) {
      console.log(e.type);
    },
    zoom: function (e) {
      console.log(e.type, e.detail.ratio);
    }
  }).cropper(options);
  

//新增用户
//
//
//
//添加 用户
  $('#userEdit').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var modal = $(this);
      var form = modal.find("form");
      $("#saveuser").on("click",function(){
        var postData = {};
        postData["username"] = modal.find("input[name='username']").val();
        postData["nickname"] = modal.find("input[name='nickname']").val();
        postData["user_role"] = modal.find("select[name='user_role']").val();
        postData["user_garden"] = modal.find("select[name='user_garden']").val();
        postData["passwd"] = modal.find("input[name='passwd']").val();
        postData["repasswd"] = modal.find("input[name='repasswd']").val();
        postData["mobile"] = modal.find("input[name='mobile']").val();
        var cn = modal.find("input[name='status']:checked");
        postData["status"] = (cn.length <= 0)?0:1;
        if(!postData["username"] || postData["username"].length >120){
          alert("用户名称不合法");
          return;
        }
        if(!postData["mobile"] || !/^1[3578]\d{9}$/.test(postData["mobile"])){
          alert("手机号码不合法");
          return;
        }
        $.ajax({
          url:"/admin/rbac/adduser",
          data:postData,
          type:'post',
          success:function(data){
            if(data.status!==0){
              alert(data.info);
              return ;
            }
            modal.modal("hide");
            refreshView();
          }
        });
      });
    });

//清空
    $('#userEdit').on('hidden.bs.modal', function(event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var modal = $(this);
      modal.find("input[name='user_name']").val("");
      modal.find("textarea[name='user_remark']").val("");
      modal.find("input[name='status']").val("");
    });
//删除
    $(".deluser").on("click",function(event){
      event.preventDefault();
      var urlink = $(this).attr("href");
      var uid = $(this).data("uid");
      var pData = {};
      pData["uid"] = uid;
      //先这么凑合着吧
      if(!window.confirm("您确认要删除该用户吗？")){
        return false;
      }
      $.ajax({
        url : urlink,
        data  :pData,
        context : document.body,
        success : function(data) {
          if(!data.status == 0){
            alert("删除失败："+data.info);
            return ;
          }
          refreshView();
        }
      });
    });
//拉黑
$(".infolink").on("click", function() {
      var urlink = $(this).data("link");
      $.ajax({
        url : urlink,
        context : document.body,
        success : function(data) {
          $("#cinfo_area").html(data);
        }
      });
    });

});