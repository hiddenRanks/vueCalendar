<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function postTodo(Request $req)
    {
        $data = $req->only('name', 'date');
        $result = auth()->user()->todo()->create($data);
        return response()->json(['success'=>true, 'result'=>$result]);
    }

    public function getTodoList(Request $req)
    {
        $list = auth()->user()->todo()->get();
        return response()->json(['success'=>true, 'list'=>$list]);
    }

    public function deleteTodo(Request $req)
    {
        $id = $req->only('id');
        $result = auth()->user()->todo()->where('id', $id)->delete();

        return response()->json(['success'=>true, 'result'=>$result]);
    }

    public function complTodo(Request $req)
    {
        $id = $req->only('id');
        $result = auth()->user()->todo()->where('id', $id)->update(['complete'=>1]);

        return response()->json(['success'=>true, 'result'=>$result]);
    }

    public function calendarList(Request $req)
    {
        $date = $req->only('date');
        $result = auth()->user()->todo()->where('date', $date)->get();

        return response()->json(['success'=>true, 'result'=>$result]);
    }
}
