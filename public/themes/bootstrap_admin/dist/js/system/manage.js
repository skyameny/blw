$(function () {

  'use strict';

  //load app
  $(".app-link").on("click",function(event){
	  event.preventDefault();
	  //load app
	  var url = $(this).attr("href");
	  var $frame = $("#app-content");
	  var _this = $(this);
	  var _loading = $("#bl-loading").find("img");
	  
	  $.ajax({
		  async:true,
		  url:url,
		  beforeSend:function(){
			  _this.append(_loading.clone());
			  _this.find(".bl_loading").show();
		  },
		  complete:function(XHR, TS){
			  _this.parent().find(".bl_loading").remove();
		  },
		  success:function(rhtml){
			 // _this.parent().find(".fa-spinner").remove();
			  $frame.html(rhtml);
			  loadAction($frame);
		  },
	  });
	  
  });
  
  //初始化动作
  function loadAction($frame)
  {	
	    $('.mailbox-messages input[type="checkbox"]').iCheck({
	      checkboxClass: 'icheckbox_flat-blue',
	      radioClass   : 'iradio_flat-blue'
	    })

	    //Enable check and uncheck all functionality
	    $('.checkbox-toggle').click(function () {
	      var clicks = $(this).data('clicks')
	      if (clicks) {
	        //Uncheck all checkboxes
	        $('.mailbox-messages input[type=\'checkbox\']').iCheck('uncheck')
	        $('.fa', this).removeClass('fa-check-square-o').addClass('fa-square-o')
	      } else {
	        //Check all checkboxes
	        $('.mailbox-messages input[type=\'checkbox\']').iCheck('check')
	        $('.fa', this).removeClass('fa-square-o').addClass('fa-check-square-o')
	      }
	      $(this).data('clicks', !clicks)
	    })

	    //Handle starring for glyphicon and font awesome
	    $('.mailbox-star').click(function (e) {
	      e.preventDefault()
	      //detect type
	      var $this = $(this).find('a > i')
	      var glyph = $this.hasClass('glyphicon')
	      var fa    = $this.hasClass('fa')

	      //Switch states
	      if (glyph) {
	        $this.toggleClass('glyphicon-star')
	        $this.toggleClass('glyphicon-star-empty')
	      }

	      if (fa) {
	        $this.toggleClass('fa-star')
	        $this.toggleClass('fa-star-o')
	      }
	    })
	  
	  
  }
  
  
  
});