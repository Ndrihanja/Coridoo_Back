<?php

namespace App\Http\Controllers\API\Projet;

use App\Models\User;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController;
use App\Models\MembreProjet;

class ProjetController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projets = Projet::query()
            ->when(request('user_id'), function ($query) {
                $query->join('membre_projets', 'membre_projets.projet_id', '=', 'projets.id')
                    ->where('membre_projets.user_id', '=', request('user_id'))
                    ->when(request('search'), function ($query) {
                        $query->where('nom', 'LIKE', '%' . request('search') . '%');
                    });
            })
            ->orderBy('projets.updated_at', 'desc')
            ->get(['projets.id', 'nom', 'status_id', 'archive']);

        $all = [
            "recents" => [],
            "archives" => [],
            "tous les projets" => [],
        ];

        //get recent
        if (count($projets) >= 3) {
            for ($i = 0; $i < 3; $i++) {
                if ($projets[$i]->archive == 0) {
                    $all['recents'][] = $projets[$i];
                }
            }
        } else {
            foreach ($projets as $projet) {
                if ($projet->archive == 0) {
                    $all['recents'][] = $projet;
                }
            }
        }

        //get archives
        foreach ($projets as $projet) {
            if ($projet->archive == 1) {
                $all['archives'][] = $projet;
            }
        }

        //get tous
        foreach ($projets as $projet) {
            if ($projet->archive == 0) {
                $all['tous les projets'][] = $projet;
            }
        }

        return $this->sendResponse($all, 'Tous les projets');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required',
            'description' => 'sometimes|string',
            'deadline' => 'required',
            'creator_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $projet = Projet::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'status_id' => 1,
            'creator_id' => $request->creator_id
        ]);

        $membres = [];
        $membres = $request->membres;
        array_push($membres, $request->creator_id);

        $this->ajout($projet, $membres);

        return $this->sendResponse($projet, 'Projet cree avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $projets = Projet::with('membres')
            ->where(['id' => $id])
            ->get();

        return $this->sendResponse($projets, 'Projet trouve ');
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
        $message = $this->modifMembre($id, $request->membres);

        $projet = Projet::find($id);

        return $this->sendResponse($projet, $message);
    }

    public function modifMembre($requestProjet_id, $requestMembres)
    {
        MembreProjet::query()
            ->where('projet_id', '=', $requestProjet_id)
            ->whereNotIn('user_id', $requestMembres)
            ->delete();

        $membres = MembreProjet::query()
            ->where('projet_id', '=', $requestProjet_id)
            ->whereIn('user_id', $requestMembres)
            ->get();

        foreach ($membres as $membre) {
            $key = array_search($membre->user_id, $requestMembres);
            unset($requestMembres[$key]);
        }

        $projet = Projet::find($requestProjet_id);

        $this->ajout($projet, $requestMembres);

        return "Membres mis a jour";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = Projet::find($id)->delete();

        return $this->sendResponse($deleted, 'Projet supprime avec succès.');
    }

    private function ajout(Projet $projet, $requestMembres)
    {
        $membres = User::all()->whereIn('id', $requestMembres);

        $projet->membres()->saveMany($membres);
    }

    public function ajoutMembre(Request $request)
    {
        $projet = Projet::find($request->projet_id);

        $this->ajout($projet, $request->membres);
    }
}
