<?php
use \app\libraries\Dfunc;
use \app\models\DevItemTitleModel;
use \app\models\DevItemContentModel;
use Phalcon\DB\Column as Column;
use \app\models\DevItemHistoryModel;
/**
 * Local variables
 * @var \Phalcon\Mvc\Micro $app
 */

$app->get('/', function () {
    echo 'index';
});

$app->get('/table', function ()  {
    $join = ' AND ';

    $hs_name = !empty($_GET['h']) ? $_GET['h'] : '';
    $sql = sprintf('SELECT * FROM dev_item_title ORDER BY sort_num');
    $news = $this['db']->fetchAll($sql,2);
    $componentArr = ['people_name'=>'','people_status'=>'table-select-opt','system_name'=>'table-select-opt','now_stage'=>'table-select-opt','role'=>'table-select-opt'];
    $ext = true;
    if(!empty($hs_name)){
        $sql = sprintf('SELECT * FROM dev_item_history WHERE people_name=\'%s\' ORDER BY id DESC',$hs_name);
        $historys = $this['db']->fetchAll($sql,2);
        $contents = [];
        $ext = false;

        foreach ($historys as $item) {
            $row = json_decode($item['data_json'],true);
            $row['_history']=true;
            $contents[] = $row;
        }
    }else{
        $Model = new DevItemContentModel();
        $where = [];
        if(!empty($_GET['like'])){
            $like = '%';
            $pp = ' like ';
        }else{
            $like = '';
            $pp = '=';
        }
        foreach ($componentArr as $key => $item) {
            if(!empty($_GET['all'])){
                $where[] = sprintf('%s%s\'%s\'', $key,$pp, $like.$_GET['all'].$like);
                $join = ' OR ';
            }else{
                if (!empty($_GET[$key])) {
                    if (is_array($_GET[$key])) {
                        $where[] = sprintf('%s%s\'%s\'', $key,$pp, $like.$_GET[$key][0].$like);
                    } else {
                        $where[] = sprintf('%s%s\'%s\'', $key,$pp, $like.$_GET[$key].$like);
                    }
                }
            }

        }
        $strWhere = '';

        if(!empty($where)){
            $ext = false;
            $strWhere = ' WHERE '.implode($join,$where);
        }
        $sql = sprintf('SELECT * FROM dev_item_content %s ORDER BY updated_at DESC',$strWhere);
        $contents = $this['db']->fetchAll($sql,2);
    }
//    echo $sql;exit;
    $rows = [];
    $titleArr = [];
    $columns = [];
    $table = [];
    $enum = [];
    $defaultData = [];
    foreach ($news as $key => $new) {
        if($new['type']=='枚举'){
            $enum[$new['en_name']] = array_filter(array_unique(explode('|',$new['type_value'])));
        }
        $new['width'] = !empty($new['width'])?(int)$new['width']:100;
        $new['align'] = !empty($new['align'])?$new['align']:'center';
//        $new['isFrozen'] = !empty($new['is_frozen'])?true:false;
        $new['isFrozen'] = false;
        $rows[$new['en_name']] = $new;
        $titleRow = ['fields'=>[$new['en_name']],'title'=>$new['cn_name'],'titleAlign'=>$new['align']];
        $isEdit = true;
        if(in_array($new['en_name'],array_keys($componentArr))){
            $isEdit = false;
        }
        $cloRow = ['field'=>$new['en_name'],'width'=>$new['width'],'columnAlign'=>$new['align'],'center'=>$new['align'],'isFrozen'=>$new['isFrozen'],'isEdit'=>$isEdit];
        if(in_array($new['en_name'],array_keys($componentArr)) && empty($hs_name)){
            if(!empty($enum[$new['en_name']])){
                $titleRow['filterMultiple'] = false;
                foreach ($enum[$new['en_name']] as $en) {
                    $titleRow['filters'][] = ['label'=>$en,'value'=>$en];
                }
            }
            $cloRow['componentName'] = $componentArr[$new['en_name']];
        }
        $titleArr[] = $titleRow;
        $defaultData[$new['en_name']] = '';
        $columns[] = $cloRow;
    }

    $tableData = [];
    foreach ($contents as $key => $content) {
        $tableData[] = $content;
    }
    if($ext){
        $yTable = array_column($tableData,'id','people_name');
        foreach ($enum['people_name'] as $item) {
            if(!isset($yTable[$item])){
                $row = $defaultData;
                $row['people_name']=$item;
                $tableData[] = $row;
            }
        }
    }
    array_unshift($titleArr,['fields'=>['custome'],'title'=>'排序','titleAlign'=>'center','rowspan'=>1]);
    array_unshift($columns,['field'=>'custome','width'=>100,'columnAlign'=>'center','isFrozen'=>true]);
    $fields = array_keys($rows);
    $enum['all'] = array_column($rows,'cn_name','en_name');

    if(empty($hs_name)){
        $titleArr[] = ['fields'=>['custome-adv'],'title'=>'操作','titleAlign'=>'center'];
        $fields[] = 'custome-adv';
    }
//    [{fields: ['custome'], title: '排序', titleAlign: 'center', rowspan: 2},
//                              {fields: ['name', 'gender', 'height'], title: '基础信息', titleAlign: 'center', colspan: 3},
//                              {fields: ['tel', 'email'], title: '联系方式', titleAlign: 'center', colspan: 2},
//                              {fields: ['hobby','address'], title: '爱好及地址', titleAlign: 'center', rowspan: 2,colspan: 2}]
//    $fields[] = 'custome';

//    $table['title'][] = [['fields'=>$fields,'title'=>'研发管理工具','titleAlign'=>'center','colspan'=>count($fields),'rowspan'=>1]];
//    $table['title'][] = $titleArr;
    $table['title'][] = $titleArr;
//    print_r($table);exit;
    $table['columns'] = $columns;
//    print_r($columns);exit;
    $table['body'] = $tableData;
    $table['enum'] = $enum;
     output($this,200,'',$table);
});

$app->get('/columns', function ()  {
    $sql = sprintf('SELECT * FROM dev_item_title ORDER BY sort_num');
    $news = $this['db']->fetchAll($sql,2);

    $sql = sprintf('select COLUMN_NAME as field,column_comment from INFORMATION_SCHEMA.Columns where table_name=\'dev_item_title\'');
    $cols = $this['db']->fetchAll($sql,2);

    $rows = [];
    $titleArr = [];
    $tableData = [];
    $titleArr1 = [];
    $columns = [];
    $table = [];
    foreach ($cols as $col) {
        if(!in_array($col['field'],['id'])){
            $titleRow = ['fields'=>[$col['field']],'title'=>$col['field'],'titleAlign'=>'center'];
            $titleArr[] = $titleRow;
            $titleRow1 = ['fields'=>[$col['field']],'title'=>$col['column_comment'],'titleAlign'=>'center','titleCellClassName'=>'title-cell-class-name-test2'];
            $titleArr1[] = $titleRow1;
            $isEdit = true;
            if(in_array($col['field'],['updated_at','created_at','en_name','type'])){
                $isEdit = false;
            }
            $isFrozen = false;
            if(in_array($col['field'],['en_name','cn_name'])){
                $isFrozen = true;
            }
            $cloRow = ['field'=>$col['field'],'width'=>150,'columnAlign'=>'center','isEdit'=>$isEdit,'overflowTitle'=>true,'isFrozen'=>$isFrozen];
            if(in_array($col['field'],['type'])){
                $cloRow['componentName'] = 'table-select-opt';
            }
            $columns[] = $cloRow;
        }
    }
    foreach ($news as $new) {
        $new['width'] = !empty($new['width'])?(int)$new['width']:200;
        $new['align'] = !empty($new['align'])?$new['align']:'center';
        $rows[$new['en_name']] = $new;
        $tableData[] = $new;
    }
    array_unshift($titleArr,['fields'=>['custome'],'title'=>'字段','titleAlign'=>'center','isFrozen'=>true]);
    array_unshift($titleArr1,['fields'=>['custome'],'title'=>'操作\说明','titleAlign'=>'center','isFrozen'=>true]);
    array_unshift($columns,['field'=>'custome','width'=>100,'columnAlign'=>'center','isFrozen'=>true]);
//    $titleArr[] = ['fields'=>['custome'],'title'=>'操作','titleAlign'=>'center','rowspan'=>2];
    $table['title'][0] = $titleArr;
    $table['title'][1] = $titleArr1;
    $table['columns'] = $columns;
    $table['body'] = $tableData;
    output($this,200,'',$table);
});
$app->patch('/table', function () {
    $post = file_get_contents('php://input', 'r');
    $post = json_decode($post,true);
    $connection = $this['db'];
    $connection->begin();
    try {
        $id = $post['id'];
        $Model = new DevItemContentModel();
        $DevItemContent = $Model::findFirst("id = $id");
        if($DevItemContent){
            $DevItemContent = $DevItemContent->toArray();
            $Model->id = $id;
            if($Model->delete()){
                $History = new DevItemHistoryModel();
                $History->people_name = $DevItemContent['people_name'];
                $History->data_json = json_encode($DevItemContent, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if($History->save()){
                    $connection->commit();
                    output($this, 200, '成功');
                }
            }
            throw new Exception("操作失败");
        }else{
            throw new Exception("行不存在");
        }

    }catch (\Exception $e){
        $connection->rollback();
        output($this, 400, $e->getMessage());
    }
});
$app->post('/table', function ()  {
    $post = file_get_contents('php://input', 'r');
    $post = json_decode($post,true);
    $connection = $this['db'];
    $connection->begin();
    try {
        if (is_array($post) && !empty($post['id'])) {
            //编辑数据
            $id = $post['id'];
            $is_update = $is_save = false;
            $Model = new DevItemContentModel();
            $DevItemContent = $Model::findFirst("id = $id");
            foreach ($DevItemContent as $key => $item) {
                if ($post[$key] != $item) {
                    $DevItemContent->$key = $post[$key];
                    $is_update = true;
                }
            }
            if($is_update){
                $is_save = $DevItemContent->save();
                if($is_save){
                    $connection->commit();
                    output($this, 200, '');
                }
            }
        } elseif (is_array($post) && empty($post['id'])) {
            //新增数据
            $Model = new DevItemContentModel();
            foreach ($post as $key => $item) {
                if(trim($item)!==""){
                    $Model->$key = $item;
                }
            }
            if ($Model->save()) {
                $connection->commit();
                output($this, 200, '');
            }
        }
    }catch (\Exception $e){
        $connection->rollback();
        output($this, 400, $e->getMessage());
    }
    $connection->rollback();
    output($this,400,'服务器内部错误');
});

$app->post('/columns', function () {
    $post = file_get_contents('php://input', 'r');
    $post = json_decode($post,true);
    $connection = $this['db'];
    $connection->begin();
    $fieldType = ['字符串'=>Column::TYPE_VARCHAR,'数字'=>Column::TYPE_INTEGER,'文本'=>Column::TYPE_TEXT,'枚举'=>Column::TYPE_VARCHAR,'日期'=>Column::TYPE_DATETIME];
    try {
        if (is_array($post) && !empty($post['id'])) {
            $id = $post['id'];
            $Model = new DevItemTitleModel();
            $DevItemTitle = $Model::findFirst("id = $id");
            $is_update = $is_save = false;
            if (!empty($DevItemTitle)) {
                foreach ($DevItemTitle as $key => $item) {
                    if ($post[$key] != $item) {
                        if ($key == 'en_name') {
                            output($this, 400, '`en_name`不能修改');
                        }
                        $is_update = true;
                        $DevItemTitle->$key = $post[$key];
                        if (!in_array($post['type'], array_keys($fieldType))) {
                            output($this, 400, '`type`值有误');
                        }
                    }
                }
                if ($is_update) {
                    //修改数据
                    $is_save = $DevItemTitle->save();
                    if ($is_save) {
                        $sql = sprintf('select COLUMN_NAME as field,column_comment from INFORMATION_SCHEMA.Columns where table_name=\'dev_item_content\'');
                        $cols = $this['db']->fetchAll($sql, 2);
                        $fieldArr = array_column($cols, 'field');
                        $editCol = [
                            "type" => isset($fieldType[$post['type']]) ? $fieldType[$post['type']] : Column::TYPE_VARCHAR,
                            "size" => isset($post['length']) ? $post['length'] : 255,
                            "notNull" => false,
                        ];
                        if (!empty($post['type_value'])) {
                            if($post['type'] == '枚举'){
                                if($post['en_name']!='people_name'){
                                    $arr = explode('|',trim($post['type_value']));
                                    $editCol['default'] = $arr[0];
                                }
                            }else{
                                $editCol['default'] = $post['type_value'];
                            }
                        }
                        if (!in_array($post['en_name'], $fieldArr)) {
                            // 添加一个新的字段
                            $save_col = $this['db']->addColumn(
                                'dev_item_content', null,
                                new Column(
                                    $post['en_name'],
                                    $editCol
                                )
                            );
                        } else {
                            // 修改一个已存在的字段
                            $save_col = $this['db']->modifyColumn(
                                "dev_item_content", null,
                                new Column(
                                    $post['en_name'],
                                    $editCol
                                )
                            );
                        }
                    }
                }
            }
            output($this, 200, '');
            $connection->commit();
        } elseif (is_array($post) && empty($post['id'])) {
            if(!preg_match("/^[0-9a-zA-Z_]{3,12}$/",$post['en_name'])){
                output($this,400,'`en_name`只能是英文字母或数字或_, 且长度必须是3-12个');
            }
            $Model = new DevItemTitleModel();
            foreach ($post as $key => $item) {
                $Model->$key = $item;
            }
            if ($Model->save()) {
                $connection->commit();
                output($this, 200, '');
            }
        }
    }catch (\Exception $e){
        $connection->rollback();
        output($this,400,$e->getMessage());
    }
    $connection->rollback();
    output($this,400,'服务器内部错误');
//    $this->db->begin();
});


$app->get('/test', function () {
    // 设置返回头部内容格式
    //$app->response->setContentType('text/plain')->sendHeaders();

    // 输出文件内容
    //readfile("data.txt");
    echo 'hello,world';
});

/**
 * Not found handler
 */
$app->notFound(function () {
    $this->response->setStatusCode(404, "Not Found")->sendHeaders();
    echo Dfunc::returnJson(404,'Not Found');
});

function output($app,$errorCode,$message='',$data=[],$httpStatusCode=200){
    $return_data = Dfunc::returnJson($errorCode, $message, $data);

    if (!in_array($errorCode, [200]) && $httpStatusCode == 200) {
        $httpStatusCode = 400;
    }
//    self::_accessLog($httpStatusCode, $return_data);
     _response($app,$httpStatusCode, $return_data);
}

function _response($app,$httpStatusCode, $content)
{
    $app->response
            ->setStatusCode($httpStatusCode)
            ->setHeader('Content-Type', 'application/json; charset=UTF-8')
            ->setContent($content)
            ->send();
        exit;
}