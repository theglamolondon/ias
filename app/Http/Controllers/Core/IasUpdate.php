<?php
/**
 * Created by PhpStorm.
 * User: SUPPORT.IT
 * Date: 20/05/2019
 * Time: 11:42
 */

namespace App\Http\Controllers\Core;


use App\Http\Controllers\Controller;
use App\PieceComptable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\UnauthorizedException;

class IasUpdate extends Controller {

	public static function checkDataBaseMAJ(){
		$instructions = self::getFileDBupdate();
		$sqls = explode(';', $instructions);
		unset($sqls[count($sqls)-1]); //On supprime la dernière valeur qui est null

		foreach ($sqls as $sql){
			try{
				DB::select(trim($sql));
			}catch (\Exception $e){
				$e->getMessage();
			}
		}
	}

	/**
	 * @return string
	 */
	private static function getFileDBupdate(): string {
		return file_get_contents(base_path().'/DB.updt');
	}

	public function runUpdate(){
	    if(request()->has("token")){

	        $token = request()->query("token");

            if($token == "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWV9.TJVA95OrM7E2cBab30RMHrHDcEfxjoYZgeFONFh7HgQ"){
                try{
                    Schema::table('lignepiece', function (Blueprint $table) {
                        $table->integer('quantite_periode')->default(1);
                        $table->string('periode')->nullable(true);
                    });
                }catch (\Exception $e ){

                }

                try{
                    Schema::table('piececomptable', function (Blueprint $table) {
                        $table->smallInteger('type_piece')
                            ->nullable(false)
                            ->default(PieceComptable::TYPE_FACTURE_PIECE);
                    });
                }catch (\Exception $e ){

                }


                return redirect("/") ;
            }
        }
        throw new AuthorizationException();
    }
}