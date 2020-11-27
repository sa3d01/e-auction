<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DropDownCollection;

use App\AuctionType;

class AuctionTypeController extends MasterController
{
    protected $model;

    public function __construct(AuctionType $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function auctionTypes(){
        return $this->sendResponse(new DropDownCollection($this->model->all()));
    }

}
