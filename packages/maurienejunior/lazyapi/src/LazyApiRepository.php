<?php

namespace maurienejunior\lazyapi;

class LazyApiRepository{
    protected $model = null;
    protected $primaryKey = "id";

    public function findOne($id){
        return $this->model::find($id);
    }

    public function getAll(){
        return $this->model::all();
    }

    public function create($item){
        return $this->model::create($item);
    }

    public function update($item){
        $primaryKey = $this->primaryKey;
        $findItem = $this->findOne($item[$primaryKey]);
        $findItem->update($item);

        return $this->findOne($item[$primaryKey]);
    }

    public function delete($id){
        $this->findOne($id)->delete();
    }
}
