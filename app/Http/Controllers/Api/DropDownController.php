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
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('Category')->active()->get()));
    }

    public function marks(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('Mark')->whereHas('childs')->active()->get()));
    }

    public function models($parent_id){
        return $this->sendResponse(new DropDownCollection($this->model->where('parent_id',$parent_id)->active()->get()));
    }

    public function itemStatus(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('ItemStatus')->active()->get()));
    }

    public function partners(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('Partner')->active()->get()));
    }

    public function cities(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('City')->active()->get()));
    }

    public function fetes(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('Fetes')->active()->get()));
    }

    public function colors(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('Color')->active()->get()));
    }

    public function scanStatus(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('scanStatus')->active()->get()));
    }

    public function paperStatus(){
        return $this->sendResponse(new DropDownCollection($this->model->whereClass('paperStatus')->active()->get()));
    }
}
