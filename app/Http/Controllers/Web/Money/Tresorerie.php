<?php
/**
 * Created by PhpStorm.
 * User: SUPPORT.IT
 * Date: 04/09/2018
 * Time: 09:13
 */

namespace App\Http\Controllers\Web\Money;


use App\Compte;
use App\Service;
use App\Utilisateur;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

trait Tresorerie
{
	/**
	 * @param int $value
	 *
	 * @return bool
	 */
	private function checkExistUser(int $value)
	{
		if(is_numeric($value) && $value != -1 ){
			try{
				Utilisateur::findOrFail($value);
				return true;
			}catch (\Exception $e){
				return false;
			}
		}
		return false;
	}

	private function getListCompteByUser()
	{
		$comptes = Compte::with('utilisateur');

		if(!in_array(Auth::user()->employe->service->code, [Service::DG, Service::COMPTABILITE, Service::INFORMATIQUE])){
			$comptes = $comptes->where('employe_id','=',Auth::id());
		}

		return $comptes->with(['lignecompte' => function ($query){
								$query->orderBy('dateaction','desc');
								//Peut y avoir des problèmes de performances
						}])->get();
	}

	/**
	 * @param Builder $builder
	 * @param Request $request
	 * @param $du
	 * @param $au
	 * @return Builder
	 */
	private function extractData(Builder $builder, Request $request, &$du, &$au)
	{
		if($request->has("debut") && $request->has("fin")){
			$du = Carbon::createFromFormat('d/m/Y', $request->query('debut'));
			$au = Carbon::createFromFormat('d/m/Y', $request->query('fin'));
			$builder->whereBetween('dateaction', [$du->setTime(0,0,0)->toDateTimeString(), $au->setTime(23,59,59)->toDateTimeString()]);
		}else{
			$du = Carbon::now()->firstOfMonth();
			$au = Carbon::now();
		}

		if($request->query('objet'))
		{
			$objet = $request->query('objet');
			$builder->where('objet','like',"%$objet%");
		}


		return $builder;
	}

	private function validRequest(Request $request){
		$this->validate($request, [
			'dateoperation' => 'required|date_format:d/m/Y',
			'objet' => 'required',
			'montant' => 'required|integer',
			'observation' => 'present',
			'sens' => 'required|in:1,-1',
			'compte_id' => 'required|exists:compte,id'
		]);
	}

	/**
	 * @param string $slug
	 * @throws ModelNotFoundException
	 * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|Compte
	 */
	private function getSousCompteFromSlug(string $slug, $lazy = false)
	{
		$query = Compte::where('slug','=' ,$slug);

		if($lazy === true)
		{
			$query = $query->with('lignecompte','utilisateur.employe');
		}

		return $query->firstOrFail();
	}
}