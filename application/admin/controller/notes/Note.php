<?php

namespace app\admin\controller\notes;

use app\common\controller\Backend;

use addons\notes\library\Str;

use addons\notes\model\Note as NoteModel;
use addons\notes\model\Tag as TagModel;
use addons\notes\model\NoteTag as NoteTagModel;

/**
 * 笔记
 *
 * @icon fa fa-list
 */
class Note extends Backend
{
    /**
     * 模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new NoteModel;
        $this->view->assign("statusList", $this->model->getStatusList());
    }
    
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
           
            if ($this->auth->id != 1) {
                $this->model->where('admin_id', '=', $this->auth->id);
            }
            
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['admin'])
                ->where($where)
                ->order($sort, $order)
                ->order('is_top', 'DESC')
                ->limit($offset, $limit)
                ->select();

            $list = collection($list)->toArray();
            
            foreach ($list as $k => $v) {
                $list[$k]['content'] = Str::wordCut($v['content'], 30);
            }
            
            $result = [
                "total" => $total, 
                "rows" => $list,
            ];
        
            return json($result);
        }
        
        return $this->view->fetch();
    }
    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            if ($params) {
                $params['admin_id'] = $this->auth->id;
                $params['edit_time'] = time();
                $params['edit_ip'] = request()->ip(1);
                $params['add_time'] = time();
                $params['add_ip'] = request()->ip(1);
                
                $result = $this->model->validate('\\addons\\notes\\validate\\Note.add')->create($params);
                if ($result === false) {
                    $this->error($this->model->getError());
                }
                
                if (!empty($params['tags'])) {
                    $note_id = $result->id;
                    (new TagModel)->createTags($note_id, explode(',', $params['tags']));
                }
                
                $this->success();
            }
            $this->error();
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        
        if ($this->request->isPost()) {
            $this->token();
            $params = $this->request->post("row/a");
            if ($params) {
                $params['edit_time'] = time();
                $params['edit_ip'] = request()->ip(1);
                
                $result = $row->validate('\\addons\\notes\\validate\\Note.edit')->save($params);
                if ($result === false) {
                    $this->error($row->getError());
                }
                
                (new TagModel)->deleteNoteTagByNoteids($ids);
                if (!empty($params['tags'])) {
                    (new TagModel)->createTags($ids, explode(',', $params['tags']));
                }
                
                $this->success();
            }
            $this->error();
        }
        
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }

    /**
     * 删除
     */
    public function del($ids = "")
    {
        if (!$this->request->isPost()) {
            $this->error(__("Invalid parameters"));
        }
        
        $ids = $ids ? $ids : $this->request->post("ids");
        if (!$ids) {
            $this->error(__('You have no permission'));
        }
        
        $this->model->destroy($ids);
        
        (new TagModel)->deleteNoteTagByNoteids($ids);
        
        $this->success();
    }

    /**
     * 批量更新
     * @internal
     */
    public function multi($ids = "")
    {
        (new TagModel)->deleteNoteTagByNoteids($ids);
        
        return parent::multi($ids);
    }
    
    /**
     * 详情
     */
    public function detail($ids)
    {
        $row = $this->model
            ->alias('n')
            ->join('admin', 'admin.id = n.admin_id', 'LEFT')
            ->field('n.*,nickname')
            ->where(['n.id' => $ids])
            ->find();
            
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        $this->view->assign("row", $row->toArray());
        return $this->view->fetch();
    }

}
