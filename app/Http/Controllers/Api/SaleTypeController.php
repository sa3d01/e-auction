<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DropDownCollection;

use App\AuctionType;

class SaleTypeController extends MasterController
{
    protected $model;

    public function __construct(AuctionType $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function saleTypes(){
        return $this->sendResponse(new DropDownCollection($this->model->all()));
    }

}
