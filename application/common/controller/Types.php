<?php

namespace app\common\controller;

use app\common\exception\ProcessException;
use app\common\model\Menu;
use Exception;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\Validate;
use think\Request;
use app\common\model\Articletype;
use app\common\model\Producttype;
use app\common\model\QuestionType;
use app\common\model\TypeTag;

class Types extends CommonLogin
{
    private $model_name;
    private $menu_flag;

    /**
     * Types constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct();
        $module = $request->param('module_type');
        if ($module == 'article') {
            $this->model = new Articletype();
            $this->model_name = 'articletype';
            $this->menu_flag = 3;
        } elseif ($module == 'product') {
            $this->model = new Producttype();
            $this->model_name = 'producttype';
            $this->menu_flag = 5;
        } elseif ($module == 'question') {
            $this->model = new QuestionType();
            $this->model_name = 'question_type';
            $this->menu_flag = 2;
        } else {
            $this->model = new Articletype();
            $this->model_name = 'articletype';
            $this->menu_flag = 3;
        }
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $request = $this->getLimit();
        $name = $this->request->get('name');
        $id = $this->request->get('id');
        $tag_id = $this->request->get('tag_id');
        $where = [];
        if (!empty($name)) {
            $where["name"] = ["like", "%$name%"];
        }
        if (!empty($id)) {
            $where["id"] = $id;
        }
        if (!empty($tag_id)) {
            $where["tag_id"] = $tag_id;
        }
        $user = $this->getSessionUserInfo();
        $where["node_id"] = $user["node_id"];
        $data = $this->getTypeList($request["limit"], $request["rows"], $where);
        return $this->resultArray($data);
    }

    /**
     * @param $limit
     * @param $rows
     * @param $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTypeList($limit, $rows, $where)
    {
        $count = $this->model->where($where)->count();
        $data = $this->model->limit($limit, $rows)->field('create_time,update_time', true)->where($where)->order('id desc')->select();
        return [
            "total" => $count,
            "rows" => $data
        ];
    }

    /**
     * @param $id
     * @return array
     * @throws \think\exception\DbException
     */
    public function read($id)
    {
        return $this->resultArray($this->model->field('create_time,update_time', true)->find($id)->toArray());
    }

    /**
     * 保存新建的资源
     *
     * @return array
     */
    public function save()
    {
        $rule = [
            ["name", "require", "请输入文章分类名"],
            ["tag_id", "require", "请输入或选择分类"],
            ["alias", "require|unique:" . $this->model_name . ",alias^node_id", "请输入此分类的英文名|英文名重复"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->post();
        unset($data['module_type']);
        $user = $this->getSessionUserInfo();
        $data['node_id'] = $user['node_id'];
        Db::startTrans();
        try {
            if (isset($data['tag_name']) && $data['tag_name']) {
                $Type_Tag = new TypeTag();
                $data['tag_id'] = $Type_Tag->getTagIdByName($data['tag_name']);
            }
            unset($data['tag_name']);
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            if (!$this->model->create($data)) {
                Common::processException('类型创建失败');
            }
            Db::commit();
        } catch (ProcessException $e) {
            Db::rollback();
            return $this->resultArray("failed", $e->getMessage());
        } catch (Exception $e) {
            Db::rollback();
            return $this->resultArray("failed", '添加失败');
        }
        return $this->resultArray('success', "添加成功");
    }

    /**
     * 保存更新的资源
     *
     * @param  int $id
     * @return array
     */
    public function update($id)
    {
        //
        $rule = [
            ["name", "require", "请输入文章分类名"],
            ["tag_id", "require", "请输入或选择分类"],
            ["alias", "require", "请输入此分类的英文名"]
        ];
        $validate = new Validate($rule);
        $data = $this->request->put();
        unset($data['module_type']);
        unset($data['create_time']);
        $data['update_time'] = time();
        Db::startTrans();
        try {
            if (isset($data['tag_name']) && $data['tag_name']) {
                $Type_Tag = new TypeTag();
                $data['tag_id'] = $Type_Tag->getTagIdByName($data['tag_name']);
            }
            unset($data['tag_name']);
            if (!$validate->check($data)) {
                Common::processException($validate->getError());
            }
            if (!$this->publicUpdate($this->model, $data, $id)) {
                Common::processException('类型修改失败');
            }
            Db::commit();
        } catch (ProcessException $e) {
            Db::rollback();
            return $this->resultArray("failed", $e->getMessage());
        } catch (Exception $e) {
            Db::rollback();
            return $this->resultArray("failed", '修改失败');
        }
        return $this->resultArray('success', "修改成功");
    }


    /**
     * 获取文章分类
     * @return array
     * @throws \app\common\exception\ProcessException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getType()
    {
        $user_info = $this->getSessionUserInfo();
        if ($user_info['user_type_name'] == 'node') {
            $data = $this->getTypeByNodeId($user_info['node_id']);
        } elseif ($user_info['user_type_name'] == 'site') {
            if(!is_array($user_info['menu'])){
                Common::processException('未知错误');
            }
            $type_ids = (new Menu())->getSiteTypeIds($user_info['menu'], $this->menu_flag);
            $data = $this->getTypeByIdArray($type_ids);
        } else {
            Common::processException('未知错误');
        }
        $dates = [];
        /** @var array $data */
        foreach ($data as $k => $v) {
            if (!$v['tag']) {
                $dates['未定义'][] = ['id' => $v['id'], 'name' => $v['name']];
            } else {
                $dates[$v['tag']][] = ['id' => $v['id'], 'name' => $v['name']];
            }
        }
        return $this->resultArray('success', '获取成功', $dates);
    }

    /**
     * @param $node_id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTypeByNodeId($node_id)
    {
        $where['type.node_id'] = $node_id;
        return $this->getTypeByWhere($where);
    }

    /**
     * @param $ids
     * @return false|\PDOStatement|string|\think\Collection
     * @throws DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTypeByIdArray($ids)
    {
        $where['type.id'] = ['in',$ids];
        return $this->getTypeByWhere($where);
    }

    /**
     * @param $where
     * @return false|\PDOStatement|string|\think\Collection
     * @throws DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function getTypeByWhere($where){
        $data =$this->model->alias('type')->field('type.id,name,tag_id,tag')->join('type_tag','type_tag.id = tag_id','LEFT')->where($where)->select();
        return $data;
    }

    /**
     * 统计文章
     * @return array
     */
    //TODO oldfunction
    public function ArticleCount()
    {
        $count = [];
        $name = [];
        foreach ($this->countArticle() as $item) {
            $count[] = $item["count"];
            $name[] = $item["name"];
        }
        $arr = ["count" => $count, "name" => $name];
        return $this->resultArray('', '', $arr);
    }

    //TODO oldfunction
    public function countArticle()
    {
        $user = $this->getSessionUserInfo();
        $where = [
            'node_id' => $user["node_id"],
        ];
        $articleTypes = \app\common\model\Articletype::all($where);
        foreach ($articleTypes as $item) {
            yield $this->foreachArticle($item);
        }


    }

    //TODO oldfunction
    public function foreachArticle($articleType)
    {
        $count = \app\common\model\Article::where(["articletype_id" => $articleType->id])->count();
        return ["count" => $count, "name" => $articleType->name];

    }

}
