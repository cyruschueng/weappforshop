//奖池设置，当类型为优惠券时显示编辑优惠券的信息

$(function(){
	
	$("#prizeType").change(function(){
		if($(this).val() ==4){
			//优惠券
			$(".ticketinfo").css("display","block");
			
		}else{
			$(".ticketinfo").css("display","none");
		}
		
	});
	
});