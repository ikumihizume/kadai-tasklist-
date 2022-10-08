<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得,tasklists()であってるかなあ、、tasks()maybe
            $tasks = $user->tasklists()->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'tasklists' => $tasks,
            ];
        }

        // Welcomeビューでそれらを表示
        return view('welcome', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $tasks = new Task;

        // タスク作成ビューを表示
        return view('tasks.create', [
            'task' => $tasks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         // バリデーション
        $request->validate([
            'status' => 'required|max:10',   
            'content' => 'required|max:255',
        ]);
        
         // タスクを作成
        //$task = new Task;
        //$task->status = $request->status;    // 追加
        //$task->content = $request->content;
        //$task->save();

        // トップページへリダイレクトさせる
        //return redirect('/');
        
        // この辺がなんとも怪しい認証済みユーザ（閲覧者）の投稿として作成（リクエストされた値をもとに作成）
        $request->user()->tasklists()->create([
            'status' => $request->status,
            'content' => $request->content,
        ]);

        // 前のURLへリダイレクトさせる
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);

        // メッセージ詳細ビューでそれを表示
        return view('tasks.show', [
            'task' => $task,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);

        // メッセージ編集ビューでそれを表示
        return view('tasks.edit', [
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:255',
        ]);
        
        // idの値でメッセージを検索して取得
        $task = Task::findOrFail($id);
        // メッセージを更新
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // idの値でメッセージを検索して取得
        //$task = Task::findOrFail($id);
        // メッセージを削除
        //$task->delete();

        // トップページへリダイレクトさせる
        //return redirect('/');
        
        // idの値で投稿を検索して取得
        $tasklist = \App\Task::findOrFail($id);

        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (\Auth::id() === $tasklist->user_id) {
            $tasklist->delete();
        }

        // 前のURLへリダイレクトさせる
        //return back();
        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
