<?php
namespace app\lib\controller;
use think\Controller;
use think\Db;
use think\Request;
class Base extends Controller
{
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		parent::__construct();
		$request = request::instance();
		// 判断session，没有退出到登录页
		if (!session('?admin')){
			return $this->redirect(url('login/index'));
			exit;
		}

		// 菜单
		// $powerId = implode(',',session('user.powerArr'));
		// $menu=$this->getMenu($powerId);
		// 开发使用这个
		$menu=$this->getMenu('ol');

		// 当前方法URL地址
		$tSign=strtolower($request->controller().'/'.$request->action());

		//当前方法URL地址id
		$tSignId=Db::name('menu')->where(array('link'=>$tSign))->value('id');
		
		//面包屑导航
		$crmNav=$this->crumbTrail($tSignId);

		$this->assign(array(
			'troller'	=>  $request->controller(),
			'menu'		=>	$menu,		//菜单
			'tSign'		=>	$tSign,		//当前地址
			'crmNav'	=>	$crmNav,	//面包屑导航
		));
	}


	/**
	 * 加载视图
	 * @param  string $filename [description]
	 * @return [type]           [description]
	 */
	public function laview($filename="")
	{
 		echo $this->fetch('public/header');
 		echo $this->fetch($filename);
 		return $this->fetch('public/footer');
 	}


 	/**
	 * 递归获菜单
	 * @param string $powerId 权限id
	 * @param string $pid 父级分类id
	 * @param bool $all  查询模式，false查询显示的，true查询全部
	 * @param array $datas 临时存储
	 * @return \Think\mixed
	 */
	protected function getMenu($powerId,$pid='0',$all=false,$datas=array())
	{
		if(!$all){
			if($powerId=='ol'){//开发使用这个，完成去掉；
				$where=array('pid'=>$pid,'show'=>1);
			}elseif($powerId=='all'){
				$where=array('pid'=>$pid);
			}else{
				$where=array('pid'=>$pid,'show'=>1,'id'=>array('in',$powerId));
			}
		}else{
			if($powerId=='all'){
				$where=array('pid'=>$pid);
			}else{
				$where=array('pid'=>$pid,'id'=>array('in',$powerId));
			}
		}
		$datas=Db::name('menu')->where($where)->order('sort asc')->select();
		if (count($datas)>0){
			foreach ($datas as $key=>$row){
				$datas[$key]['sub']=$this->getMenu($powerId,$row['id'],$all);
			}
		}
		return $datas;
	}

	/**
	 * 根据分类id向上回溯构造面包屑
	 * @param  $id 要进行向上回溯用的分类id
	 * @param  $categories 由所有分类组成的数组
	 * @param  $data 用于保存结果的数组，传入一个空数组就好
	 */
	protected function crumbTrail($id,$categories=array(), &$data=array())
	{
		$category = Db::name('menu')->where(array('id'=>$id))->find();
		if( $category['pid'] != 0 ) 
		{  // 这里的 0 是根节点id（root节点id）
			$this->crumbTrail($category['pid'],$categories, $data);
		}
		$data[$category['id']] = $category;
		return $data;
	}

}