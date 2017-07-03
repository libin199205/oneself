// 添加操作 修改操作
layui.use('form', function(){
	var $ = layui.jquery;
	var form = layui.form();

	// 添加操作 修改操作
	form.on('submit(formDemo)', function(data){
		var pname = $("#psname option:selected").attr('data-poname');
		$("#poname").val(pname);
		$.ajax({
			url:  userUrl,
			type: "POST",
			data: $('#myForm').serialize(),
			dataType: 'json',
			success: function(e){
				if(e.code == 0){
					layer.msg(e.msg, {icon: 5}, function(){

					});
				} else {
					layer.msg(e.msg, {icon: 1,time: 1500,shade: 0.1}, function(){
						if (staForm == 1) {
							window.location.href=e.url;
						} else {
							var index = parent.layer.getFrameIndex(window.name);
							parent.layer.close(index);
							window.parent.location.reload();
						}
					});
				}
			}
		});
		return false;
	});
});


// 删除，修改状态
function savestatus(sid,name,sval)
{
	layer.open({
		type : 0,
		title : '是否提交？',
		btn: ['确定', '取消'],
		icon : 3,
		closeBtn : 2,
		area: true,
		content: "是否修改【"+name+"】管理员的状态",
		scrollbar: true,
		yes: function(){

			$.ajax({
				url:  saveSta,
				type: "POST",
				data: {sid:sid,sval:sval},
				dataType: 'json',
				success: function(e){
					if(e.code == 0){
						layer.msg(e.msg, {icon: 5}, function(){

						});
					} else {
						layer.msg(e.msg, {icon: 1,time: 1500,shade: 0.1}, function(){
							window.location.href=e.url;
						});
					}
				}
			});

		},
	});
	return false;
}


// 弹出层 修改管理员
function saveInfo(id)
{
	layer.open({
		type: 2,
		title: "修改",
		skin: 'layui-layer-rim', //加上边框
		area: ['50%', '68%'], //宽高
		fixed: false, //不固定
		maxmin: true,
		content: saveInf+"?id="+id,
	});
}