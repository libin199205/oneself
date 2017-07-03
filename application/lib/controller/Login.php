<?php
namespace app\lib\controller;
use think\Controller;
use think\Db;

class Login extends Controller
{

	/**
	 * 登录
	 * @return [type] [description]
	 */
    public function index()
    {
    	if (request()->isAjax())
    	{
    		$name = input('post.name');
    		if(trim($name)==''){
    			$this->error('用户名不能为空');
    		}
    		$password = input('post.password');
    		if(trim($password)==''){
    			$this->error('密码不能为空');
    		}
    		$verify = input('post.verify');
    		if(!captcha_check($verify)){
			 	$this->error('验证码错误');
			};
			$result = Db::name('admin')->where('username|email|phone',$name)->where('status',1)->find();
			if (!$result) $this->error('用户尚未注册');

			if($result['password'] == md5(md5($password).'l!b!n%^&') )
			{
				//登录时间和ip更新
				Db::name('admin')->where(array('id'=>$result['id']))->update(array('last_login_time'=>time(), 'last_login_ip'=>get_client_ip()));

				//登录次数+1
				Db::name('admin')->where(array('id'=>$result['id']))->setInc('login_num');

				session('admin',$result);

				$this->success('登录成功',url('index/index'));
			} else {

				$this->error('密码错误');
				
			}
			exit;
    	}
        return $this->fetch('public/login');
    }



    /**
     * 退出登录
     * @return [type] [description]
     */
    public function logout()
    {
        session("admin", NULL);
        return $this->redirect(url('login/index'));
    }

}