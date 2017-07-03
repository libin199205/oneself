<?php
namespace app\lib\controller;
use think\Db;
class Sys extends Base
{
	/**
	 * 品牌列表
	 * @return [type] [description]
	 */
	public function brand_list()
	{
		$brand = Db::name('brand')->field(true)->where('show','1')->paginate(20,false,array('query'=>input('param.')));
		$page = $brand->render();		
		$this->assign('page',$page);
		$this->assign('brand',$brand);
		return $this->laview();
	}

	/**
	 * 品牌添加
	 * @return [type] [description]
	 */
	public function brand_add()
	{
		if ( request()->isAjax() )
		{
			if (strpos(input('param.brand_name')," "))
			{
				return $this->error('品牌名称存在空格');
			}

			if( preg_match('/^\d+$/i', input('param.brand_name')) )
			{
				return $this->error('品牌名称不能为纯数字');
			}

			$re = Db::name('brand')->where('brand_name',input('brand_name'))->count();
			if ($re)
			{
				return $this->error('品牌名称已经存在');
			}

			$data['brand_name'] = trim(input('param.brand_name'));
			$add = Db::name('brand')->insert($data);

			if ($add)
			{
				return $this->success('操作成功');
			}
			else
			{
				return $this->error('操作失败');
			}
			exit;
		}
		return $this->fetch();
	}

	/**
	 * 品牌修改
	 * @return [type] [description]
	 */
	public function brand_edit()
	{
		if ( request()->isAjax() )
		{
			if (strpos(input('param.brand_name')," "))
			{
				return $this->error('品牌名称存在空格');
			}

			if( preg_match('/^\d+$/i', input('param.brand_name')) )
			{
				return $this->error('品牌名称不能为纯数字');
			}

			$re = Db::name('brand')->where('brand_name',input('brand_name'))->where('brand_id','neq',input('param.sid'))->where('show','1')->count();
			if ($re)
			{
				return $this->error('品牌名称已经存在');
			}
			
			Db::startTrans();
			$data['brand_name'] = trim(input('param.brand_name'));
			$save = Db::name('brand')->where('brand_id',input('param.sid'))->update($data);

			$temp['possion_type_name'] = trim(input('param.brand_name'));
			$posave = Db::name('position')->where('possion_type_id',input('param.sid'))->update($temp);

			if ($save !== false && $posave !== false)
			{
				Db::commit();	// 提交事务
				return $this->success('操作成功');
			}
			else
			{
				Db::rollback();	// 回滚事务
				return $this->error('操作失败');
			}
			exit;
		}

		$id = trim(input('param.id'));
		if (!$id)
		{
			return $this->error('参数错误');
		}
		$brand = Db::name('brand')->where('brand_id',$id)->find();
		$this->assign('brand',$brand);
		return $this->fetch();
	}

	/**
	 * 品牌删除
	 * @return [type] [description]
	 */
	public function brand_del()
	{
		if (request()->isAjax())
		{
		$sid = trim(input('param.sid'));
		if (!$sid)
		{
			return $this->error('参数错误');
			exit;
		}
		Db::startTrans();

		$re = Db::name('brand')->where('brand_id',$sid)->update(['show'=>2]);

		$is_pe = Db::name('position')->where('possion_type_id',$sid)->count();
		if ($is_pe)
		{
			$pe = Db::name('position')->where('possion_type_id',$sid)->update(['po_show'=>'2']);
		}
		else
		{
			$pe = 1;
		}


		if ($re && $pe)
		{
			Db::commit();	// 回滚事务
			return $this->success('操作成功');
		}
		else
		{
			Db::rollback();	// 回滚事务
			return $this->error('操作失败');
		}
		exit;
		}
	}



	/**
	 * 职位列表
	 * @return [type] [description]
	 */
	public function position_list()
	{
		$position = Db::name('position')->field(true)->where('po_show','1')->paginate(20,false,array('query'=>input('param.')));
		$page = $position->render();	
		$this->assign('page',$page);
		$this->assign('position',$position);
		return $this->laview();
	}


	/**
	 * 职位添加
	 * @return [type] [description]
	 */
	public function position_add()
	{
		if ( request()->isAjax() )
		{
			if (strpos(input('param.possion_name')," "))
			{
				return $this->error('职位名称存在空格');
			}

			if( preg_match('/^\d+$/i', input('param.possion_name')) )
			{
				return $this->error('职位名称不能为纯数字');
			}

			if ( input('param.possion_type_id')=='' || input('param.possion_type_id')=='0')
			{
				return $this->error('请选择品牌');
			}


			$re = Db::name('position')->where('possion_name',input('param.possion_name'))->where('possion_type_id',input('param.possion_type_id'))->count();
			if ($re)
			{
				return $this->error('职位名称已经存在');
			}
			$data['possion_name'] = trim(input('param.possion_name'));
			$data['possion_type_id'] = trim(input('param.possion_type_id'));
			$data['possion_type_name'] = trim(input('param.pyname'));
			$add = Db::name('position')->insert($data);

			if ($add)
			{
				return $this->success('操作成功');
			}
			else
			{
				return $this->error('操作失败');
			}
			exit;
		}
		// 获取品牌列表
		$brand = Db::name('brand')->where('show','1')->select();
		$this->assign('brand',$brand);
		return $this->fetch();
	}

	/**
	 * 职位修改
	 * @return [type] [description]
	 */
	public function position_edit()
	{
		if ( request()->isAjax() )
		{
			if (strpos(input('param.possion_name')," "))
			{
				return $this->error('职位名称存在空格');
			}

			if( preg_match('/^\d+$/i', input('param.possion_name')) )
			{
				return $this->error('职位名称不能为纯数字');
			}

			if ( input('param.possion_type_id')=='' || input('param.possion_type_id')=='0')
			{
				return $this->error('请选择品牌');
			}


			$re = Db::name('position')->where('possion_name',input('param.possion_name'))->where('possion_type_id',input('param.possion_type_id'))->where('possion_id','neq',input('param.pid'))->count();
			if ($re)
			{
				return $this->error('职位名称已经存在');
			}
			$data['possion_name'] = trim(input('param.possion_name'));
			$data['possion_type_id'] = trim(input('param.possion_type_id'));
			$data['possion_type_name'] = trim(input('param.pyname'));
			$re = Db::name('position')->where('possion_id',input('param.pid'))->update($data);

			if ($re !== false)
			{
				return $this->success('操作成功');
			}
			else
			{
				return $this->error('操作失败');
			}
			exit;
		}


		$id = trim(input('param.id'));
		if (!$id)
		{
			return $this->error('参数错误');
		}
		$ption = Db::name('position')->where('possion_id',$id)->find();
		$this->assign('ption',$ption);
		// 获取品牌列表
		$brand = Db::name('brand')->where('show','1')->select();
		$this->assign('brand',$brand);
		return $this->fetch();
	}


	/**
	 * 职位删除
	 * @return [type] [description]
	 */
	public function position_del()
	{
		if (request()->isAjax())
		{
			$sid = trim(input('param.sid'));
			if (!$sid)
			{
				return $this->error('参数错误');
				exit;
			}

			Db::startTrans();

			$re = Db::name('position')->where('possion_id',$sid)->update(['po_show'=>2]);

			$is_pe = Db::name('group')->where('possion_id',$sid)->count();
			if ($is_pe)
			{
				$pe = Db::name('group')->where('possion_id',$sid)->update(['show'=>2]);
			}
			else
			{
				$pe = 1;
			}

			if ($re && $pe)
			{
			    Db::commit();	// 回滚事务
				return $this->success('操作成功');
			}
			else
			{
				Db::rollback();	// 回滚事务
				return $this->error('操作失败');
			}
			exit;
		}
	}



	/**
	 * 权限列表
	 * @return [type] [description]
	 */
	public function power()
	{
		// 品牌列表
		$brand = Db::name('brand')->where('show','1')->select();
		$this->assign('brand',$brand);

		if (request()->isAjax())
		{
			if (input('post.possion_type_id'))
				$ajsxBrand = Db::name('position')->where(array('possion_type_id'=>input('post.possion_type_id')))->order("possion_id ASC")->select();
			else
				$ajsxBrand = Db::name('position')->order("possion_id ASC")->select();

			return $ajsxBrand;
			exit;
		}

		// 职位条件
		$where = array();
		if(input('param.possion_type_id')){
			$where=" possion_type_id =".input('param.possion_type_id');
		}

		// 职位列表
		$position = Db::name('position')->where($where)->where('po_show','1')->select();
		$this->assign('position',$position);


		//权限
		$power=$this->getMenu('all');

		$current = array();
		$currentPower = array();

		//当前职位授权状态
		if (input('param.possion_id'))
		{
			$current = Db::name('position')->where(array('possion_id'=>input('param.possion_id')))->find();
			//获取表中的值
			$rules = Db::name('group')->where(array('possion_id'=>input('param.possion_id')))->find();
			//过滤空值
			$currentPower=array_filter(explode(',', $rules['power']));
		}

		$this->assign(array(
			'power'=>$power,
			'currentPower'=>$currentPower,
			'current'=>$current,
		));

		return $this->laview();
	}

	/**
	 * 职位授权设置
	 * @return [type] [description]
	 */
	public function setpower()
	{
		if (request()->isAjax())
		{
			if (input('param.possion_id')=='0')
			{
				return $this->error('请选择职位授权');
				exit;
			}
			$grlist = Db::name('group')->where(array('possion_id'=>input('param.possion_id')))->find();
			if (!empty($grlist))
			{
				$status = Db::name('group')->where(array('possion_id'=>input('param.possion_id')))->update(array('power'=>input('post.limit')));
			}
			else
			{
				$status = Db::name('group')->insert(array('possion_id'=>input('param.possion_id'),'power'=>input('post.limit')));
			}

			if($status === false)
			{
				return $this->error("授权失败");
			}
			else
			{
				return $this->success('操作成功');
			}
			exit;
		}
	}


}