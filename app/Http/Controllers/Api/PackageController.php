<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PackageCollection;
use App\Http\Resources\PackageResource;
use App\Package;

class PackageController extends MasterController
{
    protected $model;

    public function __construct(Package $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function index()
    {
        return $this->sendResponse(new PackageCollection($this->model->all()));
    }

    public function show($id)
    {

        return $this->sendResponse(new PackageResource($this->model->find($id)));
    }

}
