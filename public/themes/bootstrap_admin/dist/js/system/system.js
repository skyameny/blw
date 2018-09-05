$(function () {

  'use strict';
  
  //编辑系统配置
  $(".bl_edit_modal").on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget); // Button that triggered the modal
      var modal = $(this);
      var form = modal.find("form");
      var info_params = {};
      info_params["s_key"] = button.data("bl_key");
      var info_url = "/admin/system/getSetting";
      var info = $.post(info_url,info_params,function(data){
    	  if(data.status !=0){
    		  alert(data.info);
    		  modal.modal("hiden");
    		  return false;
    	  }
    	  data = data.data;
    	  modal.find("input[name='s_key']").val(data["key"]);
    	  modal.find("textarea[name='s_value']").html(data["value"]);
    	  modal.find("textarea[name='s_describe']").val(data["describe"]);
    	  modal.find("input[name='s_status']").val(data["status"]);
      });
      //保存
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
  		    clearForm: false,  //成功提交后，是否清除所有表单元素的值
  		    resetForm: false,  //成功提交后，是否重置所有表单元素的值
  		    timeout: 3000     //限制请求的时间，当请求大于3秒后，跳出请求
  		};
     modal.find(".save-data").off("click");
     modal.find(".save-data").on("click",function(){
  	  form.ajaxSubmit(options);
  	  return false;
    });
      
  });
  
});