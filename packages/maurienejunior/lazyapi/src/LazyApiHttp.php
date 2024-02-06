<?php

namespace maurienejunior\lazyapi;

use Illuminate\Http\Response;

class LazyApiHttp
{
    public static function ok($content){
        return response()->json($content, Response::HTTP_OK);
    }

    public static function notFound(string $id){
        return response()->json(['message' => 'Nenhum registro encontrado com o id=' . $id], Response::HTTP_NOT_FOUND);
    }

    public static function erro(\Exception $ex){
        return response()->json(['message' => 'Erro interno', 'devMsg' => $ex->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function create($content){
        return response()->json($content, Response::HTTP_CREATED);
    }

    public static function forbidden($content){
        return response()->json($content, Response::HTTP_FORBIDDEN);
    }
}
