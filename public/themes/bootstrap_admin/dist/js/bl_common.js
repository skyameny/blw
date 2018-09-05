$(function () {
  'use strict';
  
  
//  全局loading
  $(document).bind("ajaxSend", function () {
      $("#bl-loading").show();
  }).bind("ajaxComplete", function () {
       $("#bl-loading").hide();
  });
  
  
  //搜索高亮
  if($("input[name='search']").val()){
  var keywords = $("input[name='search']").val().replace(/<\/?.+?>/g,"").replace(/^\s+|\s+$/gm,'');
  if(keywords){
	  $(".content_area").find("td").each(function(){
		    var text = $(this).html();
		    text = text.replace(keywords,"<b class='text-warning'>"+keywords+"</b>");
		    $(this).html(text);
	  });
	  
  }
	  
  }
  
  
  
  
  //编辑模态框
  $(".bl_add_modal").on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var modal = $(this);
      var form = modal.find("form");
      form.resetForm();//清理
      //绑定事件
      var options = {
    		    url: form.attr("action"), //提交地址：默认是form的action,如果申明,则会覆盖
    		    type: "post",   //默认是form的method（get or post），如果申明，则会覆盖
    		    beforeSubmit: function(arr, $form, options){
    		    	
    		    	//alert("Ded");
    		    }, //提交前的回调函数
    		    success: function(data){
    		    	if(data.status !=0){
      		    		alert(data.info);
      		    		return ;
      		    	}else{
      		    		modal.modal('hide');
      		    	}
    		    	refreshView();
    		    },  //提交成功后的回调函数
    		    dataType: "json", //html(默认), xml, script, json...接受服务端返回的类型
    		    clearForm: true,  //成功提交后，是否清除所有表单元素的值
    		    resetForm: true,  //成功提交后，是否重置所有表单元素的值
    		    timeout: 3000     //限制请求的时间，当请求大于3秒后，跳出请求
    		};
      modal.find(".save-data").off("click");
      modal.find(".save-data").on("click",function(){
    	  form.ajaxSubmit(options);
    	  return false;
      });
  });
  
  //删除
  $(".bl_ajax_del").on('click',function(event){
	  event.preventDefault();
	  var urlink = $(this).attr("href");
	  var pData = {};
	  pData["bl_id"] = $(this).data("bl_id");
	  if(!window.confirm("您确认要删除该记录吗？")){
	        return false;
	  }
	  //远程请求
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
  
  /**
   * 刷新
   */
  $(".bl_refresh").on("click",function(){
	  refreshView();
  });
});