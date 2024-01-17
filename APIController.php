<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Validator;
use Auth;

class APIController extends Controller
{
    public  function getUsers($id=null)
    {
        if(empty($id)){
        $users = User::get();
        return response()->json(["users"=>$users], 200);

        }else{
            $users = User::find($id);
            return response()->json(["users"=>$users], 200);
        }
        
    }

    public function getUsersList(Request $request)
{
    $header = $request->header('Authorization');

    if (empty($header)) {
        $message = "Header Authorization is missing!";
        return response()->json(['status' => false, 'message' => $message], 422);
    } else {
        $bearerToken = "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6ImFuc2hvbyBzaW5naCIsImlhdCI6MTUxNjIzOTAyMn0.02ijUhWcCA32bfmOZm3z-NX1ojbbhur8jOat7vCRFeY";

        if ($header == $bearerToken) {
            $users = User::get();
            return response()->json(["users" => $users], 200);
        } else {
            $message = "Header Authorization is incorrect!";
            return response()->json(['status' => false, 'message' => $message], 422);
        }
    }
}

public  function registerUser(Request $request)
{
    if($request->isMethod('post')){
        $userData = $request->input();
        // echo "<pre>";
        // print_r($userData);
        // die();


        $rules = [
            "name"=>"required",
            "email"=>"required|email|unique:users",
            "password"=>"required"
        ];

        $customMessages = [
            'name.required' =>'Name is required',
            'email.required' =>'Email is  required',
            'email.email'=>"Valid Email is required",
            'email.unique'=>'Email is  already exits in database',
            'password.required'=>'password is  required',
        ];

        $validator = Validator::make($userData, $rules, $customMessages);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }


        // Generate Unique Access Token 
        $accessToken = Str::random(60);
        $user = new User;
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = bcrypt($userData['password']);
        $user->access_token = $accessToken;
        $user->save();

        return response()->json(['status'=>true,
         'message'=>'User register succesfully!',
         'token'=>$accessToken
        ], 201);
    }
}



public  function registerUserWithPassport(Request $request)
{
    if($request->isMethod('post')){
        $userData = $request->input();
        // echo "<pre>";
        // print_r($userData);
        // die();


        $rules = [
            "name"=>"required",
            "email"=>"required|email|unique:users",
            "password"=>"required"
        ];

        $customMessages = [
            'name.required' =>'Name is required',
            'email.required' =>'Email is  required',
            'email.email'=>"Valid Email is required",
            'email.unique'=>'Email is  already exits in database',
            'password.required'=>'password is  required',
        ];

        $validator = Validator::make($userData, $rules, $customMessages);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }



        // Generate Unique Access Token 
        // $accessToken = Str::random(60);
        $user = new User;
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = bcrypt($userData['password']);
        // $user->access_token = $accessToken;
        $user->save();

        if(Auth::attempt(['email'=>$userData['email'], 'password'=>$userData['password']])){
            $user = User::where('email', $userData['email'])->first();
            echo "<pre>";
            print_r(Auth::user()); die();
        }

        return response()->json(['status'=>true,
         'message'=>'User register succesfully!',
         'token'=>$accessToken
        ], 201);
    }
}



public  function LoginUser(Request $request)
{
    if($request->isMethod('post')){
        $userData = $request->input();


        $rules = [
            "email"=>"required|email|exists:users",
            "password"=>"required"
        ];

        $customMessages = [
            'email.required' =>'Email is  required',
            'email.email'=>"Valid Email is required",
            'email.unique'=>'Email is  doest not exits in database',
            'password.required'=>'password is  required',
        ];

        $validator = Validator::make($userData, $rules, $customMessages);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        // Fetch User Details
        $userDetails = User::where('email', $userData['email'])->first();

        // Verify the  password

        if(password_verify($userData['password'], $userDetails->password)){
            // Update Token
            $apiToken = Str::random(60);

            // update Token
            User::where('email', $userData['email'])->update(['api_token'=>$apiToken]);

            return response()->json(['status'=>true, 'message'=>'User Logged in Succesfully!', 'token'=>$apiToken], 201);
        }else{
            return response()->json(["status"=>false,"message"=>"password is  incorrect"], 422);
        }


    }
}



public function logoutUser(Request $request)
{
    $api_token = $request->header('Authorization');
    
    if (empty($api_token)) {
        $message = "User Token is missing in API Header";
        return response()->json(['status' => false, 'message' => $message], 422);
    } else {
        // Remove "Bearer" from the token
        $api_token = str_replace("Bearer ", "", $api_token);

        // Find the user with the given API token
        $user = User::where('api_token', $api_token)->first();

        if ($user) {
            // Update User Token to NULL
            $user->api_token = null;
            $user->save();

            $message = "User logged out successfully!";
            return response()->json(['status' => true, 'message' => $message], 200);
        } else {
            $message = "Invalid user or token";
            return response()->json(['status' => false, 'message' => $message], 401);
        }
    }
}



    public  function addUsers(Request $request)
    {
        if($request->isMethod('post')){
            $userData = $request->input();
            
            // Simple Post API Validations



            // Check User Details
            // if (empty($userData['name']) || empty($userData['email']) || empty($userData['password'])) {
            //     $error_message = "Please Enter Complete User Details";
                
            // }

            // // Check if  Email is  Validate
            // if(!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)){
            //     $error_message = "Please Enter Valid Email";
                
            // }

            // // Check if User Email is  laready Exits

            // $userCount = User::where('email', $userData['email'])->count();
            // if($userCount>0){
            //     $error_message = "Email already exits!";
                
            // }

            // if(isset($error_message) && !empty($error_message)){
            //     return response()->json(["status"=>false, "message"=>$error_message],422);
            // }


            // Advance Post API Validations
            $rules = [
                "name"=>"required",
                "email"=>"required|email|unique:users",
                "password"=>"required"
            ];

            $customMessages = [
                'name.required' =>'Name is required',
                'email.required' =>'Email is  required',
                'email.email'=>"Valid Email is required",
                'email.unique'=>'Email is  already exits in database',
                'password.required'=>'password is  required',
            ];

            $validator = Validator::make($userData, $rules, $customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(), 422);
            }

            
            
            $user = new User;
            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->password = bcrypt($userData['password']);
            $user->save();

            return response()->json(['message'=>'User added succesfully!'],201);
        }
    }

    public function addMultipleUsers(Request $request)
{
    if ($request->isMethod('post')) {
        $userData = $request->input('users');

        $rules = [
            "users.*.name" => "required",
        ];
        

        $customMessages = [
            'users.*.name.required' =>'Name is required',
        ];

        $validator = Validator::make($userData, $rules,$customMessages);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        

        // Check if "users" key exists and is not null
        if ($userData && is_array($userData)) {
            foreach ($userData as $key => $value) {
                $user = new User; 
                $user->name = $value['name'];
                $user->email = $value['email'];
                $user->password = bcrypt($value['password']);
                $user->save();
            }

            return response()->json(['message' => 'Users added successfully!'], 201);
        } else {
            return response()->json(['error' => 'Invalid or missing "users" key in the request.'], 400);
        }
    }
}

    public  function updateUserDetails(Request $request, $id)
    {
        if($request->isMethod('put')){
            $userData = $request->input();
            // echo "<pre>";
            // print_r($userData);
            // die();

            $rules = [
                "name"=>"required",
                "email"=>"required|email|unique:users",
                "password"=>"required"
            ];

            $customMessages = [
                'name.required' =>'Name is required',
                'email.required' =>'Email is  required',
                'email.email'=>"Valid Email is required",
                'email.unique'=>'Email is  already exits in database',
                'password.required'=>'password is  required',
            ];

            $validator = Validator::make($userData, $rules, $customMessages);
            if($validator->fails()){
                return response()->json($validator->errors(), 422);
            }

            User::where('id', $id)->update(['name'=>$userData['name'], 'email'=>$userData['email'], 'password'=>bcrypt($userData['password'])]);

            return response()->json(['message'=>'User updated succesfully!'], 202);
        }

    }

    public function updateUserName(Request $request, $id)
    {
        if($request->isMethod('patch')){
            $userData = $request->input();
            // echo "<pre>";
            // print_r($userData);
            // die();

            User::where('id', $id)
            ->update(['name'=>$userData['name']]);
            return response()->json(['message'=>'User updated succesfully!'], 202);
        }
    }

    public function deleteUser(Request $request, $id)
    {
        User::where('id', $id)->delete();
        return response()->json(['message'=>'User Delete Succesfully!'],202);
    }

    public function deleteUserWithJson(Request $request)
    {
        if($request->isMethod('delete')){
            $userData = $request->all();
            User::where('id', $userData['id'])->delete();
            return response()->json(['message'=>'User Delete Succesfully!'],202);
        }
    }

    public  function deleteMultipleUsers($id)
    {
        $id = explode(",",$id);
        // echo '<pre>';
        // print_r($id);
        // die();
        User::whereIn('id',$id)->delete();
        return response()->json(['message'=>'User Delete Succesfully!'],202);
    }

    public function deletemultipleuserwithjson(Request $request)
    {
        if($request->isMethod('delete')){
            $userData = $request->all();
            User::whereIn('id', $userData['id'])->delete();
            return response()->json(['message'=>'User Delete Succesfully!'],202);
        }

    }

}
