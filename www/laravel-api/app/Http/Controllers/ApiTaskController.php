<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTaskController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::query()
            ->where(['user_id' => Auth::user()->getAuthIdentifier()]);

        $query
            ->when($request->has('status'), function ($query) use ($request) {
                return $query->where('status', $request->input('status'));
            })
            ->when($request->has('priority'), function ($query) use ($request) {
                return $query->where('priority', $request->input('priority'));
            })
            ->when($request->has('title'), function ($query) use ($request) {
                $title = $request->input('title');

                return $query->whereRaw('MATCH (title) AGAINST (? IN BOOLEAN MODE)', [$title]);
            })
            ->when($request->has('description'), function ($query) use ($request) {
                $description = $request->input('description');

                return $query->whereRaw('MATCH (description) AGAINST (? IN BOOLEAN MODE)', [$description]);
            });

        $sortFields = $request->input('sort_fields', ['id']);
        $sortDirections = $request->input('directions', [config('app.sort_direction.asc')]);

        foreach ($sortFields as $key => $sortField) {
            if ($this->isSortable($sortField)) {
                $sortDirection = !empty($sortDirections[$key])
                && in_array($sortDirections[$key], config('app.sort_direction'))
                    ? $sortDirections[$key]
                    : config('app.sort_direction.asc');
                $query->orderBy($sortField, $sortDirection);
            }
        }

        $tasks = $query->get();

        return response()->json(['tasks' => $tasks]);
    }

    /**
     * @return JsonResponse
     */
    public function tree(): JsonResponse
    {
        $tasks = Task::doesntHave('parent')
            ->where(['user_id' => Auth::user()->getAuthIdentifier()])
            ->with('children')
            ->get();

        return response()->json(['tasks' => $tasks]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $task = Task::where(['user_id' => Auth::user()->getAuthIdentifier()])->find($id);

        return response()->json(['tasks' => $task]);
    }

    public function store(Request $request): JsonResponse
    {
    }

    public function update(Request $request): JsonResponse
    {
    }

    public function delete(Request $request): JsonResponse
    {
    }

    private function isSortable(string $field): bool
    {
        return in_array($field, (new Task())->sortable);
    }
}
