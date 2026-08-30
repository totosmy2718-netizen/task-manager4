<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Http\Request;


class TaskController extends Controller
{
    /**
     * タスク一覧を表示
     */
    public function index()
    {
        $tasks = auth()->user()->tasks()
            ->with('category')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * タスク作成フォームを表示
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('tasks.create', compact('categories'));
    }

    /**
     * タスクを新規作成
     */
    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        Task::create($validated);

        return redirect()->route('tasks.index')
            ->with('success', 'タスクを作成しました。');
    }

    /**
     * タスク詳細を表示
     */
    public function show(Task $task)
    {
        // Policyによる認可チェック
        $this->authorize('view', $task);

        $task->load('category');

        return view('tasks.show', compact('task'));
    }

    /**
     * タスク編集フォームを表示
     */
    public function edit(Task $task)
    {
        // Policyによる認可チェック
        $this->authorize('update', $task);

        $categories = Category::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'categories'));
    }

    /**
     * タスクを更新
     */
    public function update(TaskRequest $request, Task $task)
    {
        // Policyによる認可チェック
        $this->authorize('update', $task);

        $task->update($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'タスクを更新しました。');
    }

    /**
     * タスクを削除
     */
    public function destroy(Task $task)
    {
        // Policyによる認可チェック
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'タスクを削除しました。');
    }

    /**
     * 検索フォーム
     */
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $tasks = Task::where('user_id', auth()->id())
            ->search($keyword)
            ->get();
        return view('tasks.index', compact('tasks'));
    }

    /**
     * CSV出力
     */
    public function export(Request $request)
    {
        $keyword = $request->input('keyword');

        $tasks = Task::where('user_id', auth()->id())
            ->search($keyword)
            ->get();

        $csvData = "ID,タイトル,内容\n";

        foreach ($tasks as $task) {
            $csvData .= "{$task->id},{$task->title},{$task->description}\n";
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="tasks.csv"');
    }

}
