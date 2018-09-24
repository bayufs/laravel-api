<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Meeting;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $meetings = Meeting::orderBy('id','DESC')->get();
        foreach($meetings as $meeting) {
            $meeting->view_meeting = [
                'href' => 'api/v1/meeting/'.$meeting->id,
                'method' => 'GET'
            ];
        }

        $response = [
            'massage' => 'List of all meeting',
            'meetings' => $meetings,
        ];

        return response()->json($response, 200);
    }

    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required',
            'user_id' => 'required'
        ]);

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

        $meeting = new Meeting([
            'time' => $time,
            'description' => $description,
            'title' => $title,
            
        ]);

        if($meeting->save()) {
            //attach di sini maksudnya melakukan insert juga kedalam table vipot dengan id = user_id
            $meeting->users()->attach($user_id);
            $meeting->signin = [
                'href' => 'api/v1/meeting',
                'method' => 'POST',
            ];

            // Response dalam bentuk array jika berhasil created data meeting
            $response = [
                'massage' => 'Meeting Created',
                'user' => $meeting,
            ];

            return response()->json($response, 201);

        }

        $response = [
            'massage' => 'Error during creating meeting',
        ];

         // Response dalam bentuk array jika TIDAK berhasil created data meeting
        return response()->json($response, 404);
    }
            

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $meeting = Meeting::with('users')->where('id', $id)->firstOrFail();
        $meeting->view_meetings = [
            'href' => 'api/v1/meeting',
            'method' => 'GET'
        ];

        $response = [
            'massage' => "Meeting information",
            'meeting' => $meeting
        ];

        return response()->json($response, 200);
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
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'time' => 'required',
            'user_id' => 'required'
        ]);

        $title = $request->input('title');
        $description = $request->input('description');
        $time = $request->input('time');
        $user_id = $request->input('user_id');

        $meeting = Meeting::with('users')->findOrfail($id);
        if(!$meeting->user()->where('users.id', $user_id)->first()) {
            return response()->json([
                'massage' => 'User not registerd on meeting, update not allowed'
            ], 401);
        }

        $meeting->time = $time;
        $meeting->title = $title;
        $meeting->description = $description;

        if(!$meeting->update()) {
            return response()->json([
                'massage' => 'Fail update meeting'
            ], 200);
        }

        $meeting->view_meeting = [
            'href'=> 'api/v1/meeting'.$meeting->id,
            'method' => 'GET'
        ];

        $response = [
            'massage' => 'Meeting updaated',
            'meeting' => $meeting
        ];

        return response()->json($response, 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $users = $meeting->users;
        $meeting->users()->detach();

        if(!$meeting->delete()) {
            foreach ($users as $user) {
               $meeting->users()->attach($user);
            }
            return response()->json([
                'massage' => 'Fail delete meeting',
            ], 404);
        }

        $response = [
            'massage' => 'Meeting deleted',
            'create' => [
                'href' => 'api/v1/meeting',
                'method' => 'POST',
                'params' => 'title, description, time'
            ]
        ];

        return response()->json($response, 200);
    }
}
