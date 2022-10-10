<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projects\CreateProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Models\Access;
use App\Models\Project;
use App\Models\Team;
use Helpers\RES;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{
    /**
     * @OA\Post(
     *      tags={"Projects"},
     *      security={{"bearerAuth":{}}},
     *      summary="Create team",
     *      path="/api/teams/{team_id}/projects",
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
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 example={
     *                     "name":"Project name"
     *                }
     *             )
     *         )
     *      ),
     *  )
     */
    public function createProject(CreateProjectRequest $req)
    {

        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!$access = Access::where('team_id', $team->id)->where('user_id', $user->id)->where('role', 'admin')->first()) {
            return RES::UNAUTHORIZED('Only administrators can create a project on this team');
        }

        $project = Project::create([
            "team_id" => $team->id,
            "name" => $req->name
        ]);

        return RES::CREATED($project);
    }

    /**
     * @OA\Patch(
     *      tags={"Projects"},
     *      security={{"bearerAuth":{}}},
     *      summary="Update project",
     *      path="/api/teams/{team_id}/projects/{project_id}",
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
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 example={
     *                     "name":"Project name"
     *                }
     *             )
     *         )
     *      ),
     *  )
     */
    public function updateProject(UpdateProjectRequest $req)
    {
        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!$project = Project::where('team_id', $team->id)->where('id', $req->project_id)->first()) {
            return RES::NOTFOUND('Project not found');
        }

        if (!$access = Access::where('team_id', $team->id)->where('user_id', $user->id)->where('role', 'admin')->first()) {
            return RES::UNAUTHORIZED('Only administrators can update a project on this team');
        }

        // Fields able to be updated
        $req->name ? $project->name = $req->name : null;

        $project->save();

        return RES::OK($project);
    }

    /**
     * @OA\Get(
     *      tags={"Projects"},
     *      security={{"bearerAuth":{}}},
     *      summary="Get team projects",
     *      path="/api/teams/{team_id}/projects",
     *      @OA\Parameter(
     *         in="path",
     *         name="team_id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      )
     *  )
     */
    public function getProjects(Request $req)
    {
        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!$access = Access::where('team_id', $team->id)->where('user_id', $user->id)->where('role', 'admin')->first()) {
            return RES::UNAUTHORIZED('Only administrators can create a project on this team');
        }

        if (!$projects = Project::where('team_id', $req->team_id)->get()) {
            return RES::NOTFOUND('No projects found for this team');
        }

        return RES::CREATED($projects);
    }


    /**
     * @OA\Get(
     *      tags={"Projects"},
     *      security={{"bearerAuth":{}}},
     *      summary="Show project",
     *      path="/api/teams/{team_id}/projects/{project_id}",
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
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      )
     *  )
     */
    public function showProject(Request $req)
    {

        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!$access = Access::where('team_id', $team->id)->where('user_id', $user->id)->where('role', 'admin')->first()) {
            return RES::UNAUTHORIZED('Only administrators can create a project on this team');
        }

        if (!$project = Project::where('team_id', $req->team_id)->where('id', $req->project_id)->with(['sections.tasks'])->get()) {
            return RES::NOTFOUND('Project not found');
        }

        return RES::CREATED($project);
    }
}
