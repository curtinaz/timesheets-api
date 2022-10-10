<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Access;
use App\Models\Team;
use Helpers\RES;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamsController extends Controller
{
    /**
     * @OA\Post(
     *      tags={"Teams"},
     *      security={{"bearerAuth":{}}},
     *      summary="Create team",
     *      path="/api/teams",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 example={
     *                     "name":"Project name",
     *                     "color":"#ff0000"
     *                }
     *             )
     *         )
     *      ),
     *  )
     */
    public function createTeam(CreateTeamRequest $req)
    {
        $user = Auth::user();

        $team = Team::create([
            "name" => $req->name,
            "color" => $req->color
        ]);

        Access::create([
            "user_id" => $user->id,
            "team_id" => $team->id,
            "role" => 'admin',
            "is_active" => true
        ]);

        RES::CREATED($team);
    }

    /**
     * @OA\Patch(
     *      tags={"Teams"},
     *      security={{"bearerAuth":{}}},
     *      summary="Update team",
     *      path="/api/teams/{team_id}",
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
     *                     "name":"Project name",
     *                     "color":"#ff0000"
     *                }
     *             )
     *         )
     *      ),
     *  )
     */
    public function updateTeam(UpdateTeamRequest $req)
    {
        $user = Auth::user();

        if(!$team = Team::find($req->team_id)) {
            return RES::NOTFOUND('Team not found');
        }

        if(!$access = Access::where('team_id', $team->id)->where('user_id', $user->id)->where('role', 'admin')->first()) {
            return RES::UNAUTHORIZED('Only administrators can update this team');
        }

        // Fields able to be updated
        $req->name ? $team->name = $req->name : null;
        $req->color ? $team->color = $req->color : null;

        $team->save();

        return RES::OK($team);
    }
}
