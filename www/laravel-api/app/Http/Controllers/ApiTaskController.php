<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiTaskController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::query()
            ->where(['user_id' => $request->user()->id]);

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
     * @param Request $request
     * @return JsonResponse
     */
    public function tree(Request $request): JsonResponse
    {
        $tasks = Task::doesntHave('parent')
            ->where(['user_id' => $request->user()->id])
            ->with('children')
            ->get();

        return response()->json(['tasks' => $tasks]);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $task = Task::where(['user_id' => $request->user()->id])->find($id);
        $status = empty($task) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK;

        return response()->json(['tasks' => $task], $status);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate(Task::rules());
            $task = Task::create([
                'status' => $request->input('status'),
                'priority' => $request->input('priority'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'user_id' => $request->user()->id,
            ]);

            return response()->json(['task' => $task], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        try {
            $request->validate(Task::rules());

            $task = Task::findOrFail($id);

            $this->authorize('update', $task);

            $task->update([
                'status' => $request->input('status'),
                'priority' => $request->input('priority'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'completed_at' => null,
            ]);

            return response()->json(['task' => $task], Response::HTTP_OK);
        } catch (ValidationException | ModelNotFoundException | NotFoundHttpException | AuthorizationException $e) {
            return response()->json(['errors' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function complete(int $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);

            $this->authorize('complete', $task);

            $task->update(['completed_at' => now()]);

            return response()->json(['task' => $task], Response::HTTP_OK);
        } catch (ModelNotFoundException | AuthorizationException $e) {
            return response()->json(['errors' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);

            $this->authorize('delete', $task);

            if ($this->recursiveDelete($task)) {
                return response()->json(['message' => Task::TASK_DELETE_MESSAGE], Response::HTTP_OK);
            } else {
                return response()->json(['message' => Task::TASK_DELETE_ERROR], Response::HTTP_BAD_REQUEST);
            }
        } catch (ModelNotFoundException | AuthorizationException $e) {
            return response()->json(['errors' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @param Task $task
     * @return bool
     */
    protected function recursiveDelete(Task $task): bool
    {
        if ($task->completed_at !== null || $task->child()->whereNotNull('completed_at')->exists()) {
            return false;
        }

        $toDelete = $task->child()->get();

        foreach ($toDelete as $child) {
            $this->recursiveDelete($child);
        }

        $task->delete();

        return true;
    }

    /**
     * @param string $field
     * @return bool
     */
    private function isSortable(string $field): bool
    {
        return in_array($field, (new Task())->sortable);
    }
}
