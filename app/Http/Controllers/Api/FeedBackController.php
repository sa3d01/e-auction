<?php

namespace App\Http\Controllers\Api;

use App\FeedBack;
use App\Http\Resources\FeedBackCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedBackController extends MasterController
{
    protected $model;

    public function __construct(FeedBack $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['feed_back' => 'required'], $this->validation_messages());
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;
        $this->model->create($data);
        return $this->sendResponse(new FeedBackCollection($this->model->where('status', 'approved')->latest()->get()));
    }

    public function validation_messages()
    {
        return array(
            'required' => ':attribute يجب ادخال الـ',
        );
    }

    public function index()
    {
        return $this->sendResponse(new FeedBackCollection($this->model->where('status', 'approved')->latest()->get()));
    }
}
