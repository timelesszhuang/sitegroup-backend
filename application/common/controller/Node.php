<?php
namespace app\common\controller;


use think\Request;
use think\Validate;

class Node extends CommonLogin
{
    /**
     * 显示资源列表
     * @author jingzheng
     * @return \think\Response
     */
    public function index()
    {
        $request=$this->getLimit();
         return $this->resultArray('','',(new \app\common\model\Node())->getNode($request["limit"],$request["rows"]));
    }

    /**
     * 保存新建的资源
     * @author jingzheng
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        $rule=[
            ["name","require|unique:Node","请输入节点名称|节点名称重复"],
            ["com_name","require","请选择公司"],
            ["com_id","require","请选择公司"],
            ["user_id","require","请选择管理员"],
        ];
        $validate=new Validate($rule);
        $data=$this->request->post();
        if(!$validate->check($data)){
            return $this->resultArray($validate->getError(),"failed");
        }
        $node=new \app\common\model\Node();
        $node->startTrans();
            if(!\app\common\model\Node::create($data)){
            return $this->resultArray("failed","添加失败");
        }
        $where['user_id']= $data['user_id'];
        $nodeTemp = $node->where($where)->find();
        $user=\app\common\model\User::get($nodeTemp->user_id);
        $user->node_id=$nodeTemp["id"];
        $user->node_name = $data['name'];
        if(!$user->save()){
            $node->rollback();
        }
        $node->commit();
        return $this->resultArray("添加成功");
    }

    public function create(){
        $request=$this->getLimit();
        return $this->resultArray('','',(new \app\common\model\Node())->getUser($request));

    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        return $this->resultArray('','',\app\common\model\Node::get($id));
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        $rule=[
            ["name","require|unique:Node","请输入节点名称|节点名称重复"],
            ["com_name","require","请选择公司"],
            ["com_id","require","请选择公司"],
        ];
        $data = $this->request->put();
        $validate = new Validate($rule);
        if (!$validate->check($data)) {
            return $this->resultArray('failed',$validate->getError());
        }
        if (!\app\common\model\Node::update($data)) {
            return $this->resultArray( 'failed','修改失败');
        }
        return $this->resultArray('修改成功');
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $Industry = \app\common\model\Node::get($id);
        if (!$Industry->delete()) {
            return $this->resultArray( 'failed','删除失败');
        }
        return $this->resultArray('删除成功');
    }
    public function status()
    {
        $status = $this->request->get('status');
        $id = $this->request->get('id');
        $Node = new \app\common\model\Node();
        if($Node->where('id', $id)
            ->update(['status' => $status,'status_time' => $this->request->time()])){
            return $this->resultArray('','修改成功');
        }
    }

    /**
     * 获取node列表
     * @return array
     */
    public function nodeList()
    {
        $data=\app\common\model\Node::where(1)->field(["id,name"])->select();
        return $this->resultArray('','',$data);
    }
}
