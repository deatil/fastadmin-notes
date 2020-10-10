<?php

namespace app\admin\controller\notes;

use app\common\controller\Backend;

use addons\notes\model\Note as NoteModel;
use addons\notes\model\Tag as TagModel;
use addons\notes\model\NoteTag as NoteTagModel;

/**
 * 快速笔记
 *
 * @icon fa fa-list
 */
class Fast extends Backend
{
    /**
     * 模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new NoteModel;
    }
    
    /**
     * 查看
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        
        if (!$this->request->isAjax()) {
            $this->error(__('ERROR'));
        }
        
        $total = $this->model
            ->where('admin_id', '=', $this->auth->id)
            ->where('status', '=', 1)
            ->count();

        $list = $this->model
            ->with(['admin'])
            ->where('admin_id', '=', $this->auth->id)
            ->where('status', '=', 1)
            ->order('edit_time', 'DESC')
            ->order('is_top', 'DESC')
            ->limit(0, 15)
            ->select();

        $list = collection($list)->toArray();
        $result = [
            "code" => 1, 
            "total" => $total, 
            "rows" => $list,
        ];
    
        return json($result);
    }
    
    /**
     * 笔记
     */
    public function note()
    {
        $params = $this->request->post("row/a");
        if (!$params) {
            $this->error();
        }
        
        if (!empty($params['id'])) {
            return $this->edit();
        } else {
            return $this->add();
        }
    }
    
    /**
     * 添加
     */
    public function add()
    {
        if (!$this->request->isPost()) {
            $this->error(__('ERROR'));
        }
        
        $params = $this->request->post("row/a");
        if (!$params) {
            $this->error();
        }
        
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
        
        $this->success(__('Fast note success'), null, [
            'id' => $result->id,
        ]);
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        if (!$this->request->isPost()) {
            $this->error();
        }
        
        $params = $this->request->post("row/a");
        if (empty($params)) {
            $this->error();
        }
        
        $ids = $params['id'];
        
        $row = $this->model->get(['id' => $ids]);
        if (!$row) {
            $this->error(__('No Results were found'));
        }
        
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
        
        $this->success(__('Fast note success'));
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

}
