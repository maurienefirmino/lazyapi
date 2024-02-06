<?php

namespace maurienejunior\lazyapi;

use DB;
use Exception;

class LazyApiService{

    public function findOne($id){
        try{
            return LazyApiHttp::ok($this->repository->findOne($id));
        }catch(Exception $ex){
            return LazyApiHttp::erro($ex);
        }
    }

    public function getAll(){
        try{
            return LazyApiHttp::ok($this->repository->getAll());
        }catch(Exception $ex){
            return LazyApiHttp::erro($ex);
        }
    }

    public function store($item){
        DB::beginTransaction();
        try{
            $item = $this->modifyItensBeforeSave($item);
            $validate = $this->validate($item);
            if($validate){
                return LazyApiHttp::forbidden(['message'=>$validate]);
            }
            $obj = $this->repository->create($item);
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
