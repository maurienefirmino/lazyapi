<?php

namespace maurienejunior\lazyapi;

class LazyApiRepository{
    protected $model = null;
    protected $primaryKey = "id";

    protected $paginate = false;

    protected $fieldsToSearch = [];

    protected $relationships = [];

    public function findOne($id){
        return $this->model::find($id);
    }

    public function findByPrimaryKeys($arr_primary_keys){
        return $this->model::whereIn($this->primaryKey, $arr_primary_keys)->get();
    }

    public function getModel(){
        return $this->model;
    }

    public function getPaginate(){
        return $this->paginate;
    }

    public function getFieldsToSearch(){
        return $this->fieldsToSearch;
    }

    public function getRelationships(){
        return $this->relationships;
    }

    public function getAll()
    {
        if($this->paginate){
            $registerByPage = 10;
            if (request()->registerByPage) $registerByPage = request()->registerByPage;

            return $this->model::paginate($registerByPage);
        }

      return $this->model::all();
    }

    public function store($item)
    {
        return $this->model::create($item);
    }

    public function update($request)
    {
        $primaryKey = $this->primaryKey;
        $findItem = $this->findOne($request[$primaryKey]);
        $findItem->update($request);

        return $findItem;
    }

    public function delete($id){
        $this->findOne($id)->delete();
    }

    //Override
    public function modifyData($data){
        return $data;
    }
}
