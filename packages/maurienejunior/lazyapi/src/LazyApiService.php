<?php

namespace maurienejunior\lazyapi;

use DB;
use Exception;

class LazyApiService{

    protected $repository;

    public function __construct($repository){
        $this->repository = $repository;
    }

    public function getAllWithFilter(){
        $data = $this->repository->getModel()::query();

        foreach(request()->query() as $key=>$search){

            $orExpression = explode("||",$key);

            $key = $orExpression[0];

            $search = explode("|",$search);

            if(count(explode("|",$key)) == 1){

                switch($search[0]){
                    case '>':
                    case '<':
                    case '<=':
                    case '>=':
                    case '=':
                    case '<>':
                    case 'ilike':
                        if(count($orExpression) > 1){
                            $data->where(function($query) use ($key, $search, $orExpression){
                                $query->where($key,$search[0],$search[1]);
                                foreach($orExpression as $expression){
                                    $query->orWhere($expression,$search[0],$search[1]);
                                } 
                            });
                        }else{
                            $data->where($key,$search[0],$search[1]);
                        }
                    break;
                    case 'between':
                        $data->whereBetween($key,[$search[1],$search[2]]);
                        break;
                    default:
                    break;
                }
            }


            $fields = explode("|",$key);

            if(count($fields) > 1){

                $relationship = $fields[0];
                $relationship_field = $fields[1];
                $operator = $search[0];
                $search1 = $search[1];
                $search2 = '';

                if(isset($search[2])){
                    $search2 = $search[2];
                }

                switch($operator){
                    case '>':
                    case '<':
                    case '<=':
                    case '>=':
                    case '=':
                    case '<>':
                    case 'ilike':
                        if(count($orExpression) > 1){
                            $data->where(function($query) use ($orExpression, $operator, $search1){
                                foreach($orExpression as $expression){
                                    $anotherRelationship = explode("|",$expression);
                                    $anotherRelationship_field = $anotherRelationship[1];
                                    $anotherRelationship = $anotherRelationship[0];
                                    $query->orWhereRelation($anotherRelationship, $anotherRelationship_field, $operator, $search1);
                                } 
                            });
                        }else{
                            $data->whereRelation($relationship, $relationship_field, $operator, $search1);
                        }
                    break;
                    case 'between':
                        $data->with($relationship)->whereHas($relationship, function($query) use ($relationship_field, $search1, $search2){
                            return $query->whereBetween($relationship_field,[$search1,$search2]);
                        });
                        break;
                    default:
                    break;
                }
            }

            if($key == 'search'){
                $fieldsToSearch = $this->repository->getFieldsToSearch();
                    $search = $search[0];
                    $data->where(function($query) use ($fieldsToSearch, $search){
                        foreach($fieldsToSearch as $field){

                            if((int)$field['size'] < strlen($search)) continue;

                            if(is_numeric($search)){
                                $query->orWhere($field['field'], '=', $search);
                            }else{
                                $query->orWhere($field['field'], 'ilike', '%'.$search.'%');
                            }

                        }
                    });
            }
        }

        $data = $this->repository->modifyData($data);

        if(request()->paginate){
            $this->repository->setPaginate(request()->paginate);
        }

        if($this->repository->getPaginate()){
            $registerByPage = 10;

            if (request()->registerByPage) $registerByPage = request()->registerByPage;
            return $data->paginate($registerByPage);
        }

        return $data->get();
    }

    public function findOne(string $id){
        $data = $this->repository->findOne($id);
        if($data){
            return LazyApiHttp::ok($data);
        }

        return LazyApiHttp::notFound($id);

    }

    public function getAll(){

        if(request()->query()){

            return LazyApiHttp::ok($this->getAllWithFilter());
        }

        return LazyApiHttp::ok($this->repository->getAll());
    }

    public function store($item){
        DB::beginTransaction();
        try{
            $item = $this->modifyItensBeforeSave($item);
            $validate = $this->validate($item);
            if($validate){
                return LazyApiHttp::forbidden(['message'=>$validate]);
            }
            $obj = $this->repository->store($item);
            $this->afterSave($obj, $item);
            DB::commit();
            return LazyApiHttp::ok($obj);

        }catch(Exception $ex){
            DB::rollback();
            return LazyApiHttp::erro($ex);
        }
    }

    public function update($item){
        DB::beginTransaction();
        try{
            $item = $this->modifyItensBeforeUpdate($item);
            $validate = $this->validate($item);

            if($validate){
                return LazyApiHttp::forbidden(['message'=>$validate]);
            }
            $this->beforeUpdate($item);
            $obj = $this->repository->update($item);
            $this->afterUpdate($obj, $item);
            DB::commit();
            return LazyApiHttp::ok($obj);
        }catch(Exception $ex){
            DB::rollback();
            return LazyApiHttp::erro($ex);
        }
    }

    public function delete($id){

        $register = $this->repository->findOne($id);

        if(!$register){ return LazyApiHttp::notFound($id); }

        DB::beginTransaction();
        try{
            $this->beforeDelete($id);
            DB::commit();
            return LazyApiHttp::ok($this->repository->delete($id));
        }catch(Exception $ex){
            DB::rollback();
            return LazyApiHttp::erro($ex);
        }
    }

    //Alguma validação antes de salvar/editar
    public function validate($item){ return null; }
    //Alguma motificação no obj antes de salvar
    public function modifyItensBeforeSave($item){ return $item; }
    //Alguma motificação no obj antes de editar
    public function modifyItensBeforeUpdate($item){ return $item; }
    //Alguma ação depois de salvar
    public function afterSave($obj, $item){  }
    //Alguma ação depois de editar
    public function afterUpdate($obj, $item){  }
    //Alguma ação antes de editar
    public function beforeUpdate($item){ return null; }
    //Alguma ação antes de deletar
    public function beforeDelete($id){ return null; }


}
