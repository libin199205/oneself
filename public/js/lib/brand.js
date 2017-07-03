// 弹出层 品牌添加
function addBrand()
{
	layer.open({
		type: 2,
		title: "品牌添加",
		skin: 'layui-layer-rim', //加上边框
		area: ['50%', '35%'], //宽高
		fixed: false, //不固定
		maxmin: true,
		content: brandAdd,
	});
}

// 品牌修改
function saveBrand(e)
{
	layer.open({
		type: 2,
		title: "修改",
		skin: 'layui-layer-rim', //加上边框
		area: ['50%', '35%'], //宽高
		fixed: false, //不固定
		maxmin: true,
		content: brandSave+"?id="+e,
	});
}


// 添加操作 修改操作
layui.use('form', function(){
	var $ = layui.jquery;
	var form = layui.form();

	// 添加操作
	form.on('submit(formDemo)', function(data){

		$.ajax({
			url:  brandUrl,
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
function delBrand(sid,name)
{
	layer.open({
		type : 0,
		title : '是否提交？',
		btn: ['确定', '取消'],
		icon : 3,
		closeBtn : 2,
		area: true,
		content: "是否删除品牌：【"+name+"】",
		scrollbar: true,
		yes: function(){

			$.ajax({
				url:  brandDel,
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