<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\CreateTaskRequest;
use App\Http\Requests\Tasks\GetTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Models\Access;
use App\Models\Project;
use App\Models\Section;
use App\Models\Task;
use App\Models\Team;
use Exception;
use Helpers\RES;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{

    /**
     * @OA\Post(
     *      tags={"Tasks"},
     *      security={{"bearerAuth":{}}},
     *      summary="Create task",
     *      path="/api/teams/{team_id}/projects/{project_id}/sections/{section_id}/tasks",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="team_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="project_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="section_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 example={
     *                     "title":"Bake a cake",
     *                     "description":"Need to buy a oven first"
     *                }
     *             )
     *         )
     *      ),
     *  )
     */
    public function createTask(CreateTaskRequest $req)
    {

        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!Access::where('team_id', $team->id)->where('user_id', $user->id)->first()) {
            return RES::UNAUTHORIZED('You are not allowed to do this action');
        }

        if (!$project = Project::where('team_id', $team->id)->where('id', $req->project_id)->first()) {
            return RES::NOTFOUND('Project not found');
        }

        if (!$section = Section::where('project_id', $project->id)->where('id', $req->section_id)->first()) {
            return RES::NOTFOUND('Section not found');
        }

        $task = Task::create([
            "title" => $req->title,
            "description" => $req->description,
            "section_id" => $section->id,
            "dependecy_task_id" => $req->dependecy_task_id
        ]);

        return RES::CREATED($task);
    }

    /**
     * @OA\Get(
     *      tags={"Tasks"},
     *      security={{"bearerAuth":{}}},
     *      summary="Get task",
     *      path="/api/teams/{team_id}/projects/{project_id}/sections/{section_id}/tasks/{task_id}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="team_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="project_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="section_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="task_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      )
     *  )
     */
    public function getTask(GetTaskRequest $req)
    {
        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!Access::where('team_id', $team->id)->where('user_id', $user->id)->first()) {
            return RES::UNAUTHORIZED('You are not allowed to do this action');
        }

        if (!$project = Project::where('team_id', $team->id)->where('id', $req->project_id)->first()) {
            return RES::NOTFOUND('Project not found');
        }

        if (!$section = Section::where('project_id', $project->id)->where('id', $req->section_id)->first()) {
            return RES::NOTFOUND('Section not found');
        }

        if (!$task = Task::where('id', $req->task_id)->where('section_id', $section->id)->first()) {
            return RES::NOTFOUND('Task not found');
        }

        return $task;
    }

    /**
     * @OA\Delete(
     *      tags={"Tasks"},
     *      security={{"bearerAuth":{}}},
     *      summary="Delete task",
     *      path="/api/teams/{team_id}/projects/{project_id}/sections/{section_id}/tasks/{task_id}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="team_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="project_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="section_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="task_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      )
     *  )
     */
    public function deleteTask(Request $req)
    {
        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!Access::where('team_id', $team->id)->where('user_id', $user->id)->first()) {
            return RES::UNAUTHORIZED('You are not allowed to do this action');
        }

        if (!$project = Project::where('team_id', $team->id)->where('id', $req->project_id)->first()) {
            return RES::NOTFOUND('Project not found');
        }

        if (!$section = Section::where('project_id', $project->id)->where('id', $req->section_id)->first()) {
            return RES::NOTFOUND('Section not found');
        }

        if (!$task = Task::where('id', $req->task_id)->where('section_id', $section->id)->first()) {
            return RES::NOTFOUND('Task not found');
        }

        $task->delete();

        return RES::ACCEPTED("Task was successfully deleted");
    }

    /**
     * @OA\Patch(
     *      tags={"Tasks"},
     *      security={{"bearerAuth":{}}},
     *      summary="Update task",
     *      path="/api/teams/{team_id}/projects/{project_id}/sections/{section_id}/tasks/{task_id}",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="team_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="project_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="section_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *         in="path",
     *         name="task_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 example={
     *                     "title":"Bake a cake",
     *                     "description":"Need to buy a oven first"
     *                }
     *             )
     *         )
     *      )
     *  )
     */
    public function updateTask(UpdateTaskRequest $req)
    {

        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!Access::where('team_id', $team->id)->where('user_id', $user->id)->first()) {
            return RES::UNAUTHORIZED('You are not allowed to do this action');
        }

        if (!$project = Project::where('team_id', $team->id)->where('id', $req->project_id)->first()) {
            return RES::NOTFOUND('Project not found');
        }

        if (!$section = Section::where('project_id', $project->id)->where('id', $req->section_id)->first()) {
            return RES::NOTFOUND('Section not found');
        }

        if (!$task = Task::where('id', $req->task_id)->where('section_id', $section->id)->first()) {
            return RES::NOTFOUND('Task not found');
        }

        $req->title ? $task->title = $req->title : null;
        $req->description ? $task->description = $req->description : null;
        $req->section_id ? $task->section_id = $req->section_id : null;
        $req->dependecy_task_id ? $task->dependecy_task_id = $req->dependecy_task_id : null;

        $task->save();

        return RES::ACCEPTED($task);
    }
}
