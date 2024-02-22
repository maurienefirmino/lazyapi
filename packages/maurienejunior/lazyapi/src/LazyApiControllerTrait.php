<?php

namespace maurienejunior\lazyapi;

use Illuminate\Http\Request;
trait LazyApiControllerTrait{

    public function __construct($service){
        $this->service = $service;
    }

    public function getAll(){
        return $this->service->getAll();
    }
    public function findOne(string $id){
        return $this->service->findOne($id);
    }

    public function store(Request $data){
        return $this->service->store($data->all());
    }

    public function update(Request $data){
        return $this->service->update($data->all());
    }

    public function delete(string $id){
        return $this->service->delete($id);
    }

    public function updatePost(Request $data){
        return $this->service->update($data->all());
    }
}
