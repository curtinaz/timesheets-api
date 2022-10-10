<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sections\CreateSectionRequest;
use App\Http\Requests\Sections\DeleteSectionRequest;
use App\Http\Requests\Sections\UpdateSectionRequest;
use App\Models\Access;
use App\Models\Project;
use App\Models\Section;
use App\Models\Team;
use Helpers\RES;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionsController extends Controller
{
    /**
     * @OA\Post(
     *      tags={"Sections"},
     *      security={{"bearerAuth":{}}},
     *      summary="Create a Section",
     *      path="/api/teams/{team_id}/projects/{project_id}/sections",
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
     *                     "name":"Section name"
     *                }
     *             )
     *         )
     *      ),
     *  )
     */
    public function createSection(CreateSectionRequest $req)
    {
        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!Access::where('team_id', $team->id)->where('user_id', $user->id)->where('role', 'admin')->first()) {
            return RES::UNAUTHORIZED('Only administrators can create a section on this team');
        }

        if (!$project = Project::where('team_id', $team->id)->where('id', $req->project_id)->first()) {
            return RES::NOTFOUND('Project not found');
        }

        $section = Section::create([
            "project_id" => $project->id,
            "name" => $req->name
        ]);

        return RES::CREATED($section);
    }


    /**
     * @OA\Patch(
     *      tags={"Sections"},
     *      security={{"bearerAuth":{}}},
     *      summary="Patch a section",
     *      path="/api/teams/{team_id}/projects/{project_id}/sections/{section_id}",
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
     *                     "name":"Section name"
     *                }
     *             )
     *         )
     *      ),
     *  )
     */
    public function updateSection(UpdateSectionRequest $req)
    {
        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!Access::where('team_id', $team->id)->where('user_id', $user->id)->where('role', 'admin')->first()) {
            return RES::UNAUTHORIZED('Only administrators can create a section on this team');
        }

        if (!$project = Project::where('team_id', $team->id)->where('id', $req->project_id)->first()) {
            return RES::NOTFOUND('Project not found');
        }

        if (!$section = Section::where('project_id', $project->id)->where('id', $req->section_id)->first()) {
            return RES::NOTFOUND('Section not found');
        }

        $req->name ? $section->name = $req->name : null;
        $section->save();

        return RES::OK("Section was successfully updated");
    }


    /**
     * @OA\Delete(
     *      tags={"Sections"},
     *      security={{"bearerAuth":{}}},
     *      summary="Delete section",
     *      path="/api/teams/{team_id}/projects/{project_id}/sections/{section_id}",
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
     *                     "name":"Project name"
     *                }
     *             )
     *         )
     *      ),
     *  )
     */
    public function deleteSection(DeleteSectionRequest $req)
    {
        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!Access::where('team_id', $team->id)->where('user_id', $user->id)->where('role', 'admin')->first()) {
            return RES::UNAUTHORIZED('Only administrators can create a section on this team');
        }

        if (!$project = Project::where('team_id', $team->id)->where('id', $req->project_id)->first()) {
            return RES::NOTFOUND('Project not found');
        }

        if (!$section = Section::where('project_id', $project->id)->where('id', $req->section_id)->first()) {
            return RES::NOTFOUND('Section not found');
        }

        $section->delete();

        return RES::ACCEPTED("Section successfully deleted");
    }

    /**
     * @OA\Get(
     *      tags={"Sections"},
     *      security={{"bearerAuth":{}}},
     *      summary="Get all sections of the project",
     *      path="/api/teams/{team_id}/projects/{project_id}/sections",
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
     *      )
     *  )
     */
    public function getSections(Request $req)
    {
        $user = Auth::user();

        if (!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if (!Access::where('team_id', $team->id)->where('user_id', $user->id)->where('role', 'admin')->first()) {
            return RES::UNAUTHORIZED('Only administrators can create a section on this team');
        }

        if (!$project = Project::where('team_id', $team->id)->where('id', $req->project_id)->first()) {
            return RES::NOTFOUND('Project not found');
        }

        if (!$sections = Section::where('project_id', $project->id)->get()) {
            return RES::NOTFOUND('No sections found for this project');
        }

        return RES::OK($sections);
    }
}
