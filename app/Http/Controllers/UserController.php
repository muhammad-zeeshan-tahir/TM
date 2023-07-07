<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @OA\Get(
     *      path="/TM/public/api/user",
     *      summary="Get current user",
     *      tags={"User"},
     *      security={ {"apiAuth": {} }},
     *      description="Returnss information about the current user if the request is authenticated",*     @OA\Response(response=200, description="OK", @OA\JsonContent()),
     *      @OA\Response(response=400, description="Bad Request", @OA\JsonContent()),
     *      @OA\Response(response=500, description="Server error occured", @OA\JsonContent()),
     *      @OA\Response(response=201, description="Successful created", @OA\JsonContent())
     * )
     */
    public function index()
    {
        $users= User::all();
        return response()->json([
            "status"=> true,
            "data"=> $users,
        ]);
    }

     /**
        * @OA\Post(
        * path="/TM/public/api/user",
        * operationId="Register",
        * tags={"User"},
        * summary="User Register",
        * description="User Register here",
        *     @OA\RequestBody(
        *         @OA\JsonContent(),
        *         @OA\MediaType(
        *            mediaType="multipart/form-data",
        *            @OA\Schema(
        *               type="object",
        *               required={"name","email", "password", "password_confirmation"},
        *               @OA\Property(property="name", type="text"),
        *               @OA\Property(property="email", type="text"),
        *               @OA\Property(property="password", type="password"),
        *               @OA\Property(property="password_confirmation", type="password"),
        *               @OA\Property(property="role", type="text")
        *
        *            ),
        *        ),
        *    ),
        *      @OA\Response(
        *          response=500,
        *          description="Authenticated",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=201,
        *          description="Register Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=200,
        *          description="Register Successfully",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(
        *          response=422,
        *          description="Unprocessable Entity",
        *          @OA\JsonContent()
        *       ),
        *      @OA\Response(response=400, description="Bad request"),
        *      @OA\Response(response=404, description="Resource Not Found"),
        * )
        */
    public function store(Request $request)
    {
        $attributes = $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|confirmed',
            'role' => "required|exists:roles,name"
        ]);

        $attributes['password'] = Hash::make($attributes['password']);
        $attributes             = Arr::except($attributes, 'role');

        $user = User::create($attributes);
        $user->assignRole([$request->role]);

        return response()->json([
            'status' => true,
            'message' => "Successfully Created a new User",
            "data" => $user,
        ]);

    }

    /**
     * @OA\Get(
     *     path="/TM/public/api/user/{id}",
     *     tags={"User"},
     *     summary="Get specific user data ",
     *     security={{"apiAuth":{}}},
     *     operationId="userDetail",
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
            $data = User::where('id', $id)->get();
            return response()->json([
                'status' => true,
                "message" => "Successfully Returned a User",
                "data" => $data,
            ]);
        }
        else {
            return response()->json([
                'status' => true,
                "message" => "User Id is  invalid.",
                "data" => [],
            ]);
        }

    }

    /**
     * @OA\Put(
     *     path="/TM/public/api/user/{id}",
     *     tags={"User"},
     *     summary="Update User",
     *     security={{"apiAuth":{}}},
     *     operationId="UserUpdate",
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
     *                 required={"name","email", "password", "password_confirmation"},
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="password", type="string"),
     *                 @OA\Property(property="password_confirmation", type="string")
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

        $att = $request->validate([
            'name' => 'nullable',
            'email' => 'nullable|unique:users,email',
            'password' => 'nullable|confirmed'
        ]);
        $user = User::where('id',$id)->first();

        $att['password'] = !empty($att['password']) ? Hash::make($att['password']) : $user->password;
        $user->update($att);

        return response()->json([
            'status' =>true,
            "message"=>"Successfully Changed Information",
            "data" => $user,
        ]);

    }

    /**
     * @OA\Delete(
     *     path="/TM/public/api/user/{id}",
     *     tags={"User"},
     *     summary="Delete specific user data ",
     *     security={{"apiAuth":{}}},
     *     operationId="UserDelete",
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

        if(auth()->user()->cannot('delete users'))
            return response()->json([ 'status' => false, "message"=> "Not Authorized"]);
        User::where('id',$id)->delete();
        return response()->json([
            'status' =>true,
            "message"=>"Successfully Deleted",
        ]);

    }
}
