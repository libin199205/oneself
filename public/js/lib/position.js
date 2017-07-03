// 弹出层 职位添加
function addPosition()
{
	layer.open({
		type: 2,
		title: "职位添加",
		skin: 'layui-layer-rim', //加上边框
		area: ['50%', '38%'], //宽高
		fixed: false, //不固定
		maxmin: true,
		content: positionAdd,
	});
}


// 弹出层 职位添加
function savePosition(id)
{
	layer.open({
		type: 2,
		title: "修改",
		skin: 'layui-layer-rim', //加上边框
		area: ['50%', '38%'], //宽高
		fixed: false, //不固定
		maxmin: true,
		content: positionSave+"?id="+id,
	});
}


// 添加操作 修改操作
layui.use('form', function(){
	var $ = layui.jquery;
	var form = layui.form();

	// 添加操作
	form.on('submit(formDemo)', function(data){

		var pname = $("#pyname option:selected").attr('data-pyname');
		$("#posname").val(pname);

		$.ajax({
			url:  PositionUrl,
			type: "POST",
			data: $('#myForm').serialize(),
			dataType: 'json',
			success: function(e){
				if(e.code == 0){
					layer.msg(e.msg, {icon: 5}, function(){

					});
				} else {
					layer.msg(e.msg, {icon: 1,time: 1500,shade: 0.1}, function(){
						var index = parent.layer.getFrameIndex(window.name);
						parent.layer.close(index);
						window.parent.location.reload();
					});
				}
			}
		});
		return false;
	});
});

// 品牌删除
function delPosition(sid,name)
{
	layer.open({
		type : 0,
		title : '是否提交？',
		btn: ['确定', '取消'],
		icon : 3,
		closeBtn : 2,
		area: true,
		content: "是否删除职位：【"+name+"】",
		scrollbar: true,
		yes: function(){

			$.ajax({
				url:  positionDel,
				type: "POST",
				data: {sid:sid},
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