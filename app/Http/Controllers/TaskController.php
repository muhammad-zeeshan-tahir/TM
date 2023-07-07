<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{

    /**
     * @OA\Get(
     *      path="/TM/public/api/task",
     *      summary="Get all task",
     *      tags={"Task"},
     *      security={ {"apiAuth": {} }},
     *      description="Returns information about the tasks",
     *      @OA\Response(response=200, description="OK", @OA\JsonContent()),
     *      @OA\Response(response=400, description="Bad Request", @OA\JsonContent()),
     *      @OA\Response(response=500, description="Server error occured", @OA\JsonContent()),
     *      @OA\Response(response=201, description="Successful created", @OA\JsonContent())
     * )
     */
    public function index(Request $request)
    {
        $tasks = Task::all();
        if($tasks->isEmpty()) return response()->json(['status'=>false, 'message'=>"No Tasks yet"]);

        return response()->json([
            'status' => true,
            'message' => "successfully returned Tasks",
            'data' => $tasks
        ]);
    }


    /**
     * @OA\Post(
     * path="/TM/public/api/task",
     * operationId="CreateTask",
     * tags={"Task"},
     * summary="Create Task",
     * description="Create Task here",
     * security={ {"apiAuth": {} }},
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"assignee_user_id","title", "description"},
     *               @OA\Property(property="assignee_user_id", type="text"),
     *               @OA\Property(property="title", type="text"),
     *               @OA\Property(property="description", type="text")
     *            ),
     *        ),
     *    ),
     *    @OA\Response(response=200, description="OK", @OA\JsonContent()),
     *    @OA\Response(response=400, description="Bad Request", @OA\JsonContent()),
     *    @OA\Response(response=500, description="Server error occured", @OA\JsonContent()),
     *    @OA\Response(response=201, description="Successful created", @OA\JsonContent()),
     *    @OA\Response(response=422, description="Unprocessable Entity",@OA\JsonContent())
     *
     * )
     */
    public function store(Request $request)
    {
        if(Auth::user()->getRoleNames()[0]!='admin')
            return response()->json(['status'=>false, 'message'=>"Permission denied."]);

        $attributes = $request->validate([
            'assignee_user_id' => 'required|exists:users,id',
            'title' => 'required',
            'description' => 'required',
        ]);

        $task = Task::create($attributes);
        return response()->json([
            'status' =>true,
            "message"=>"Successfully Created a new Task",
            "data" => $task,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/TM/public/api/task/{id}",
     *     tags={"Task"},
     *     summary="Get specific task data ",
     *     security={{"apiAuth":{}}},
     *     operationId="taskDetail",
     *     @OA\Parameter(
     *          name="id",
     *          description="Id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Bad Request", @OA\JsonContent()),
     *     @OA\Response(response=500, description="Server error occured", @OA\JsonContent()),
     *     @OA\Response(response=201, description="Successfull created", @OA\JsonContent()),
     * )
     */
    public function show($id=null)
    {
        if($id){
            $data = Task::where('id', $id)->get();
            return response()->json([
                'status' => true,
                "message" => "Successfully Returned a Task",
                "data" => $data,
            ]);
        }
        else {
            return response()->json([
                'status' => true,
                "message" => "Task Id is  invalid.",
                "data" => [],
            ]);
        }
    }

    /**
     * @OA\Put(
     *     path="/TM/public/api/task/{id}",
     *     tags={"Task"},
     *     summary="Update Task",
     *     security={{"apiAuth":{}}},
     *     operationId="TaskUpdate",
     *     @OA\Parameter(
     *          name="id",
     *          description="Id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\RequestBody(
     *       @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"assignee_user_id","title", "description"},
     *                 @OA\Property(property="assignee_user_id", type="string"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string")
     *             )
     *          )
     *     ),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Bad Request", @OA\JsonContent()),
     *     @OA\Response(response=500, description="Server error occured", @OA\JsonContent()),
     *     @OA\Response(response=201, description="Successful created", @OA\JsonContent()),
     * )
     */
    public function update(Request $request, $id=null)
    {
        if(Auth::user()->getRoleNames()[0]!='admin')
            return response()->json(['status'=>false, 'message'=>"Permission denied."]);

        $attributes = $request->validate([
            'assignee_user_id' => 'required|exists:users,id',
            'title' => 'required',
            'description' => 'nullable'
        ]);
        $task = Task::where('id',$id)->first();
        $task->update($attributes);

        return response()->json([
            'status' =>true,
            "message"=>"Successfully Updated a Task",
            "data" => $task
        ]);

    }


    /**
     * @OA\Delete(
     *     path="/TM/public/api/task/{id}",
     *     tags={"Task"},
     *     summary="Delete specific task ",
     *     security={{"apiAuth":{}}},
     *     operationId="taskDelete",
     *     @OA\Parameter(
     *          name="id",
     *          description="Id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Bad Request", @OA\JsonContent()),
     *     @OA\Response(response=500, description="Server error occured", @OA\JsonContent()),
     *     @OA\Response(response=201, description="Successfull created", @OA\JsonContent()),
     * )
     */
    public function destroy($id=null)
    {
        if(Auth::user()->getRoleNames()[0]!='admin')
            return response()->json(['status'=>false, 'message'=>"Permission denied."]);

        Task::where('id', $id)->delete();
        return response()->json([
            'status' =>true,
            "message"=>"Successfully Deleted a Task",
        ]);

    }
}
