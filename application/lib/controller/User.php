<?php
namespace app\lib\controller;
use think\Db;
class User extends Base
{

	/**
	 * 管理员添加
	 * @return [type] [description]
	 */
    public function add()
    {
    	if ( request()->isAjax() )
    	{
    		if ( input('param.username')=='' )
    		{
    			return $this->error('管理员名称不能为空');
    		}

    		if ( input('param.email')=='' || !filter_var(input('param.email'), FILTER_VALIDATE_EMAIL) )
    		{
    			return $this->error('管理员邮箱为空 或 格式错误');
    		}

    		if ( input('param.phone')=='' || !preg_match("/1\d{10}$/",input('param.phone')) )
    		{
    			return $this->error('手机号码为空 或 格式不对');
    		}

    		if ( input('param.password')=='' )
    		{
    			return $this->error('管理员密码不能为空');
    		}

    		if ( input('param.password') != input('param.repassword') )
    		{
    			return $this->error('确认密码必须和管理员密码一致');
    		}

    		if ( input('param.position_id')==0 )
    		{
    			return $this->error('请选择权限');
    		}

    		$name = Db::name('admin')->where('username',input('param.username'))->count();
    		if ($name)
    		{
    			return $this->error('管理员名称已经存在');
    		}

    		$email = Db::name('admin')->where('email',input('param.email'))->count();
    		if ($email)
    		{
    			return $this->error('管理员邮箱已经存在');
    		}

    		$phone = Db::name('admin')->where('phone',input('param.phone'))->count();
    		if ($phone)
    		{
    			return $this->error('手机号码已经存在');
    		}

    		$data['username']	   = trim(input('param.username'));
    		$data['email']		   = trim(input('param.email'));
    		$data['phone']	  	   = trim(input('param.phone'));
    		$data['password'] 	   = trim(md5(md5(input('param.password')).'l!b!n%^&'));
    		$data['position_id']   = trim(input('param.position_id'));
    		$data['position_name'] = trim(input('param.posname'));
    		$data['add_time']	   = trim(time());

    		$re = Db::name('admin')->insert($data);
    		if($re)
    		{
				return $this->success('操作成功',url('user/lists'));
			}
			else
			{
				return $this->error('操作失败');
			}
            exit;
    	}
        $position = Db::name('position')->select();
        $this->assign('position',$position);
        return $this->laview();
    }


    /**
     * 管理员列表
     * @return [type] [description]
     */
    public function lists()
    {
    	// 正常管理员
    	$rest = Db::name('admin')->field(true)->where('status','1')->count();
    	// 异常管理员
    	$norest = Db::name('admin')->field(true)->where('status','neq','1')->count();

    	$where = array();
    	// 管理员名称
    	if (!empty(input('param.username')))
    	{
    		$name = trim(input('param.username'));
    		$where['username'] = array('like',"%{$name}%");
    	}
    	// 管理员邮箱
    	if (!empty(input('param.email')))
    	{
    		$ema = trim(input('param.email'));
    		$where['email'] = array('like',"%{$ema}%");
    	}
    	// 手机号码
    	if (!empty(input('param.phone')))
    	{
    		$pho = trim(input('param.phone'));
    		$where['phone'] = array('like',"%{$pho}%");
    	}
    	// 职位
    	if (!empty(input('param.position')))
    	{
    		$where['position_id'] = trim(input('param.position'));
    	}
    	// 状态
    	if (!empty(input('param.status')))
    	{
    		$where['status'] = trim(input('param.status'));
    	}

    	$lists = Db::name('admin')->field(true)->where($where)->paginate(20,false,array('query'=>input('param.')));
		$page = $lists->render();

		// 职位
		$potion = Db::name('position')->field(true)->select();

		$this->assign(array(
			'lists' => $lists,
			'page'	=> $page,
			'count'	=> $rest+$norest,
			'rest'  => $rest,
			'norest'=> $norest,
			'potion'=> $potion,
		));

    	return $this->laview();
    }

    /**
     * 修改管理员状态
     * @return [type] [description]
     */
    public function savestatus()
    {
    	if (request()->isAjax())
    	{
    		$sid = trim(input('param.sid'));
    		if (!$sid)
    		{
    			return $this->error('参数错误');
    			exit;
    		}

    		if (input('param.sval')=='1')
    		{
    			$re = Db::name('admin')->where('id',$sid)->update(['status'=>2]);
    		}
    		else
    		{
    			$re = Db::name('admin')->where('id',$sid)->update(['status'=>1]);
    		}

    		if ($re)
    		{
    			return $this->success('操作成功');
    		}
    		else
    		{
    			return $this->error('操作失败');
    		}
    		exit;
    	}
    }



    /**
     * 修改管理员
     * @return [type] [description]
     */
    public function save()
    {
    	if (request()->isAjax())
    	{
    		if ( input('param.username')=='' )
    		{
    			return $this->error('管理员名称不能为空');
    		}

    		if ( input('param.email')=='' || !filter_var(input('param.email'), FILTER_VALIDATE_EMAIL) )
    		{
    			return $this->error('管理员邮箱为空 或 格式错误');
    		}

    		if ( input('param.phone')=='' || !preg_match("/1\d{10}$/",input('param.phone')) )
    		{
    			return $this->error('手机号码为空 或 格式不对');
    		}

    		if ( input('param.password') != input('param.repassword') )
    		{
    			return $this->error('确认密码必须和管理员密码一致');
    		}

    		if ( input('param.position_id')==0 )
    		{
    			return $this->error('请选择权限');
    		}

    		$name = Db::name('admin')->where('username',input('param.username'))->where('id','neq',input('param.sid'))->count();
    		if ($name)
    		{
    			return $this->error('管理员名称已经存在');
    		}

    		$email = Db::name('admin')->where('email',input('param.email'))->where('id','neq',input('param.sid'))->count();
    		if ($email)
    		{
    			return $this->error('管理员邮箱已经存在');
    		}

    		$phone = Db::name('admin')->where('phone',input('param.phone'))->where('id','neq',input('param.sid'))->count();
    		if ($phone)
    		{
    			return $this->error('手机号码已经存在');
    		}
    		
    		$data['username']	   = trim(input('param.username'));
    		$data['email']		   = trim(input('param.email'));
    		$data['phone']	  	   = trim(input('param.phone'));
    		if (!empty(input('param.password')))
    		{
    			$data['password']  = trim(md5(md5(input('param.password')).'l!b!n%^&'));
    		}
    		$data['position_id']   = trim(input('param.position_id'));
    		$data['position_name'] = trim(input('param.posname'));
    		$data['add_time']	   = trim(time());

    		$re = Db::name('admin')->where('id',input('param.sid'))->update($data);
    		if($re !== false)
    		{
				return $this->success('操作成功',url('user/lists'));
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
    		exit;
    	}

    	// 职位
		$position = Db::name('position')->field(true)->select();
        $this->assign('position',$position);


    	$re = Db::name('admin')->field(true)->where('id',$id)->find();
    	$this->assign('re',$re);

    	return $this->fetch();
    }

}