<?php

namespace app\api\controller;


use app\api\model\BookCombo as BookComoModel;

class BookCombo extends Base
{
    public function getAll(){
        $book = BookComoModel::getAll();
        return $book;
    }
}