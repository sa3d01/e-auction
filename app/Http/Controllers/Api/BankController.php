<?php

namespace App\Http\Controllers\Api;

use App\Bank;

class BankController extends MasterController
{
    protected $model;

    public function __construct(Bank $model)
    {
        $this->model = $model;
        parent::__construct();
    }

    function lang()
    {
        if (\request()->header('lang')) {
            return \request()->header('lang');
        } else {
            return 'ar';
        }
    }

    public function index()
    {
        $banks = Bank::all();
        $result = [];
        foreach ($banks as $bank) {
            $arr['id'] = (int)$bank->id;
            $arr['name'] = $bank->name[$this->lang()];
            $arr['iban_number'] = $bank->iban_number;
            $result[] = $arr;
        }
        return $this->sendResponse($result);
    }
}
