<?php

namespace App\Http\Controllers\Admin;

use App\Ask;
use Illuminate\Http\Request;

class AskController extends MasterController
{
    public function __construct(Ask $model)
    {
        $this->model = $model;
        $this->route = 'ask';
        parent::__construct();
    }

    public function validation_func($method, $id = null)
    {
        return [
            'ask_ar' => 'required',
            'ask_en' => 'required',
            'answer_ar' => 'required',
            'answer_en' => 'required',
        ];
    }

    public function validation_msg()
    {
        return array(
            'required' => 'يجب ملئ جميع الحقول',
        );
    }

    public function index()
    {
        $rows = $this->model->all();
        $index_fields = ['السؤال' => 'ask','الإجابة' => 'answer'];
        return View('dashboard.ask.index', [
                'rows' => $rows,
                'title' => 'الأسئلة الشائعة',
                'index_fields' => $index_fields,
                'languages' => true,
                'status' => true,
            ]
        );
    }

    public function create()
    {
        return View('dashboard.ask.create', [
            'type' => 'ask',
            'action' => 'admin.ask.store',
            'title' => 'أضافة سؤال',
            'create_fields' => [],
            'languages' => true,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->validation_func(1), $this->validation_msg());
        $data = $request->all();
        $ask['ar'] = $request['ask_ar'];
        $ask['en'] = $request['ask_en'];
        $answer['ar'] = $request['answer_ar'];
        $answer['en'] = $request['answer_en'];
        $data['ask'] = $ask;
        $data['answer'] = $answer;
        $this->model->create($data);
        return redirect()->route('admin.ask.index')->with('created');
    }

    public function update($id, Request $request)
    {
        $this->validate($request, $this->validation_func(2), $this->validation_msg());
        $data = $request->all();
        $ask['ar'] = $request['ask_ar'];
        $ask['en'] = $request['ask_en'];
        $answer['ar'] = $request['answer_ar'];
        $answer['en'] = $request['answer_en'];
        $data['ask'] = $ask;
        $data['answer'] = $answer;
        $this->model->find($id)->update($data);
        return back()->with('updated', 'تم التعديل بنجاح');
    }

    public function show($id)
    {
        $row = Ask::findOrFail($id);
        $edit_fields = [];
        return View('dashboard.ask.show', [
            'row' => $row,
            'action' => 'admin.ask.update',
            'title' => 'سؤال شائع',
            'edit_fields' => $edit_fields,
            'languages' => true,
            'status' => true,
        ]);
    }

    public function activate($id)
    {
        $row = $this->model->find($id);
        if ($row->status == 1) {
            $row->update(
                [
                    'status' => 0,
                ]
            );
        } else {
            $row->update(
                [
                    'status' => 1,
                ]
            );
        }
        $row->refresh();
        $row->refresh();
        return redirect()->back()->with('updated');
    }
}
