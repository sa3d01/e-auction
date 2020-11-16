<?php

namespace App\Http\Controllers\Api;

use App\DropDown;
use App\Http\Resources\DropDownCollection;

class DropDownController extends MasterController
{
    protected $model;

    public function __construct(DropDown $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function categories(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('Category')->get()));
    }

    public function marks(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('Mark')->get()));
    }

    public function models($parent_id){
        return $this->sendResponse(new DropDownCollection($this->model->where('parent_id',$parent_id)->get()));
    }

    public function itemStatus(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('ItemStatus')->get()));
    }

    public function partners(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('Partner')->get()));
    }

    public function cities(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('City')->get()));
    }
}
