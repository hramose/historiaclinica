<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Crypt;
use Validator;
use Illuminate\Validation\Rule;
use App\Http\Requests;
use Image;
use App\User;
use App\Medico;
use App\Asistente;
use Carbon\Carbon;
use App\Paciente;
use App\Medico_paciente;
use App\Tipo_diagnostico;
use App\Historia_ginecologica;
use App\Ginecologia_antecedente;
use App\Ginecologia_ginecobstetrico;
use App\Ginecologia_exploracion;
use App\Ginecologia_diagnostico;
use App\Ginecologia_incapacidad;
use App\Ginecologia_medicamento;
use App\Ginecologia_procedimiento;
use App\Ginecologia_exploracion_inicial;
use App\Ginecologia_exploracion_periodica;
use App\Ginecologia_exploracion_periodo;

use Auth;


class HistoriasGinecologicaController extends Controller
{
    public function index($paciente_id,$medico_id)
    {
        $medico_paciente = Medico_paciente::where(['paciente_id' => $paciente_id,'especialidad_id' => 3,'medico_id' => $medico_id] )->first();
        if(is_null($medico_paciente)){
            $historia_ginecologicas=array();
        }else{
            $historia_ginecologicas=Historia_ginecologica::where('medico_paciente_id',$medico_paciente->id)->orderby('id','DESC')->get();
        }
        $paciente=Paciente::where('id',$paciente_id)->with('user')->first();
		$medico=Medico::where('id',$medico_id)->with('user')->first();
        if(Auth::user()->roles()->get()->whereIn('id',[1,2])->count()==0)
        {$acciones=false;}else{$acciones=true;}    
        return view('historias.historia.ginecologica.index')->with(['medico' => $medico,'paciente' => $paciente,'historia_ginecologicas' => $historia_ginecologicas,'acciones'=>$acciones]);
    }

      /**
     * .
     * Crea una nueva historia ginecologica con todos los datos hijos por defecto
     * @param $paciente_id,$medico_paciente_id
     */

    public function ginecologica_create($paciente_id,$medico_id)
    {

        $medico_paciente = Medico_paciente::withTrashed()->where(['paciente_id' => $paciente_id,'especialidad_id' => 3,'medico_id' => $medico_id] )->first();
        if(is_null($medico_paciente)){

            $medico_paciente= new Medico_paciente;
            $medico_paciente->paciente()->associate($paciente_id);
            $medico_paciente->medico()->associate($medico_id);
            $medico_paciente->especialidad()->associate(3);
            $medico_paciente->save();
        }else{
            if ($medico_paciente->trashed()) {
                $medico_paciente->restore();
            }
        }

       

 		$Ginecologia_antecedente = Ginecologia_antecedente::where(['id' => $medico_paciente->id] )->first();
 		if(is_null($Ginecologia_antecedente)){

			$Ginecologia_antecedente= new Ginecologia_antecedente;
			$Ginecologia_antecedente->medico_paciente()->associate($medico_paciente);
			$Ginecologia_antecedente->alergias='';
			$Ginecologia_antecedente->ingresos='';
			$Ginecologia_antecedente->traumatismos='';
			$Ginecologia_antecedente->tratamientos='';
			$Ginecologia_antecedente->hta=0;
			$Ginecologia_antecedente->displidemia=0;
			$Ginecologia_antecedente->dm=0;
			$Ginecologia_antecedente->otros='';
			$Ginecologia_antecedente->habitos='';
			$Ginecologia_antecedente->familiares='';
			$Ginecologia_antecedente->situacion='';
			$Ginecologia_antecedente->save();

			$Ginecologia_ginecobstetrico= new Ginecologia_ginecobstetrico;
			$Ginecologia_ginecobstetrico->medico_paciente()->associate($medico_paciente->id);
			$Ginecologia_ginecobstetrico->gestante=0;
			$Ginecologia_ginecobstetrico->fum=Carbon::now();
			$Ginecologia_ginecobstetrico->seguridad=0;
			$Ginecologia_ginecobstetrico->cesarias=0;
			$Ginecologia_ginecobstetrico->partos=0;
			$Ginecologia_ginecobstetrico->abortos=0;
			$Ginecologia_ginecobstetrico->gestaciones=0;
			$Ginecologia_ginecobstetrico->fpp=Carbon::now();
			$Ginecologia_ginecobstetrico->save();
		}
        Historia_ginecologica::where(['medico_paciente_id'=> $medico_paciente->id])->update(['activa' => 0]);
        $Historia_ginecologica= new Historia_ginecologica;
        $Historia_ginecologica->motivo_consulta='';
        $Historia_ginecologica->enfermedad_actual='';
        $Historia_ginecologica->informe='';
        $Historia_ginecologica->analisis='';
        $Historia_ginecologica->recomendaciones='';
        $Historia_ginecologica->medico_paciente()->associate($medico_paciente->id);
        $Historia_ginecologica->activa=1;
        $Historia_ginecologica->save();

        $Ginecologia_exploracion=new Ginecologia_exploracion;
       	$Ginecologia_exploracion->historia_ginecologica()->associate($Historia_ginecologica->id);
        $Ginecologia_exploracion->pa= '';
        $Ginecologia_exploracion->ta= '';
        $Ginecologia_exploracion->fc= '';
        $Ginecologia_exploracion->fr= '';
        $Ginecologia_exploracion->peso= 0;
        $Ginecologia_exploracion->talla= 0;
        $Ginecologia_exploracion->otros= '';
        $Ginecologia_exploracion->aspectogeneral= '';
        $Ginecologia_exploracion->save();

        return redirect()->route('historias.ginecologica.edit',[$medico_paciente->paciente->id,$Historia_ginecologica->id]);
    }

      /**
     * .
     * Eliminar Historia ginecologica
     * @param  $paciente_id,$medico_id,$historia_ginecologica_id
     */
    public function destroy_ginecologica($paciente_id,$medico_id,$historia_ginecologica_id)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($Historia_ginecologica)){
            abort(404);
        }
        $Historia_ginecologica->delete();

        flash('La historia ginecológica se ha eliminado de forma exitosa!', 'danger');
        return redirect()->route('historias.ginecologica.index',[$paciente_id,$Historia_ginecologica->medico_paciente->id]);
    }

  

     /**
     * .
     * Muestra los documentos anexo a la historia ginecologica seleccionada
     * @param  paciente_id,$historia_ginecologica_id
     */
    public function ginecologica_documentos($paciente_id,$historia_ginecologica_id)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($Historia_ginecologica)){
            abort(404);
        }
        $paciente = Paciente::where(['id'=> $Historia_ginecologica->medico_paciente->paciente_id])->with('user')->first();
        $medico = Medico::where(['id'=> $Historia_ginecologica->medico_paciente->medico_id])->with('user')->first();
       

        $files = Storage::disk('ginecologia')->files($historia_ginecologica_id);
        $arrayfiles=array();
        foreach ($files as $file ) {
            
            $nombre= pathinfo(storage_path('app/public/ginecologia'.$historia_ginecologica_id).$file, PATHINFO_BASENAME);
            $size = Storage::disk('ginecologia')->getSize($file);    
            $tipo =pathinfo(storage_path('app/public/ginecologia'.$historia_ginecologica_id).$file, PATHINFO_EXTENSION);
            if($tipo=='pdf')
            {
                $tipo='fa-file-pdf-o';
            }elseif($tipo=='xlsx' || $tipo=='xls'){
                $tipo='fa-file-excel-o';
            }elseif($tipo=='doc' || $tipo=='docx'){
                 $tipo='fa-file-word-o';
            }elseif($tipo=='ppt' || $tipo=='pptx'){
                 $tipo='fa-file-powerpoint-o';
            }elseif($tipo=='jpeg' || $tipo=='bmp' || $tipo=='png' || $tipo=='jpg' ){
                $tipo='fa-file-image-o';
            }
            $arrayfiles[]=['nombre'=>$nombre,'size'=>$this->formatBytes($size),'tipo'=> $tipo,'ruta'=> $file];
        }
        return  view('historias.historia.ginecologica.documentos')->with(['paciente'=>$paciente,'medico'=>$medico,'historia_ginecologica'=>$Historia_ginecologica,'files' => $arrayfiles ]);
    }
    /**
     * .
     * Registra un documento a la historia ginecologica seleccionada
     * @param $request del documento
     */
    public function ginecologica_documentos_store(Request $request)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();
        
         $validator = Validator::make($request->all(), [
            
            'documento' => 'required|mimes:pdf,jpeg,bmp,png,jpg,doc,xls,ppt,docx,xlsx,pptx', 
            
        ]);
        if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.documentos',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id]);
        }else{
            $file = $request->file('documento');
            Storage::disk('ginecologia')->putFileAs($Historia_ginecologica->id, $file,$file->getClientOriginalName());
         
            flash('El documento se guardo de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.documentos',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id]);
        }
    }

    /**
     * .
     * Elimina un documento de la historia ginecologica seleccionada
     * @param paciente_id,$historia_ginecologica_id,$documento
     */
    public function ginecologica_documentos_destroy($paciente_id,$historia_ginecologica_id,$documento)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($Historia_ginecologica)){
            abort(404);
        }
        Storage::disk('ginecologia')->delete(Crypt::decrypt($documento));
        flash('El registro se ha eliminado de forma exitosa!', 'danger');
        return redirect()->route('historias.ginecologica.documentos',[$Historia_ginecologica->medico_paciente->paciente->id,$Historia_ginecologica->id]);

    }

     //ANTECEDENTES
     /**
     * .
     * Muestra los datos básicos de la historia ginecológica seleccionada
     * @param $paciente_id,$historia_ginecologica_id
     */
    public function ginecologica_antecedentes($paciente_id,$historia_ginecologica_id)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($Historia_ginecologica)){
            abort(404);
        }
        $paciente = Paciente::where(['id'=> $Historia_ginecologica->medico_paciente->paciente_id])->with('user')->first();
        $medico = Medico::where(['id'=> $Historia_ginecologica->medico_paciente->medico_id])->with('user')->first();

		$Ginecologia_antecedente = Ginecologia_antecedente::where(['medico_paciente_id' => $Historia_ginecologica->medico_paciente->id] )->first();
        if(!is_null($Ginecologia_antecedente))
        {
			$alergias=$Ginecologia_antecedente->alergias;
			$ingresos=$Ginecologia_antecedente->ingresos;
			$traumatismos=$Ginecologia_antecedente->traumatismos;
			$tratamientos=$Ginecologia_antecedente->tratamientos;
			$hta=$Ginecologia_antecedente->hta;
			$displidemia=$Ginecologia_antecedente->displidemia;
			$dm=$Ginecologia_antecedente->dm;
			$otros=$Ginecologia_antecedente->otros;
			$habitos=$Ginecologia_antecedente->habitos;
			$familiares=$Ginecologia_antecedente->familiares;
			$situacion=$Ginecologia_antecedente->situacion;
        }else{

        	$alergias='';
			$ingresos='';
			$traumatismos='';
			$tratamientos='';
			$hta=0;
			$displidemia=0;
			$dm=0;
			$otros='';
			$habitos='';
			$familiares='';
			$situacion='';


        }
        $datos=[
           
			'alergias'=>$alergias,'ingresos'=>$ingresos,'traumatismos'=>$traumatismos,'tratamientos'=>$tratamientos,'hta'=>$hta,'displidemia'=>$displidemia,'dm'=>$dm,'otros'=>$otros,'habitos'=>$habitos,'familiares'=>$familiares,'situacion'=>$situacion      
        ];

        return  view('historias.historia.ginecologica.antecedentes')->with(['paciente'=>$paciente,'medico'=>$medico,'historia_ginecologica'=>$Historia_ginecologica,'datos'=> $datos ]);
    }


     /**
     * .
     * Actualiza los datos básicos de la historia ginecologica seleccionada
     * @param $request con los datos de actualización
     */
    public function ginecologica_antecedentes_store(Request $request)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();
        
         $validator = Validator::make($request->all(), [

         	'alergias' => 'string|max:500',
			'ingresos' => 'string|max:500',
			'traumatismos' => 'string|max:500',
			'tratamientos' => 'string|max:500',
			'hta' => 'integer',
			'displidemia' => 'integer',
			'dm' => 'integer',
			'otros' => 'string|max:500',
			'habitos' => 'string|max:500',
			'familiares' => 'string|max:500',
			'situacion' => 'string|max:5000',

         
           
        ]);
        if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.antecedentes',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id]);
        }else{
            
           
            $Ginecologia_antecedente = Ginecologia_antecedente::where(['medico_paciente_id' => $Historia_ginecologica->medico_paciente->id] )->first();
	 		if(is_null($Ginecologia_antecedente)){
				$Ginecologia_antecedente= new Ginecologia_antecedente;
			}
			$Ginecologia_antecedente->medico_paciente()->associate($Historia_ginecologica->medico_paciente->id);
			$Ginecologia_antecedente->alergias= $request->alergias;
			$Ginecologia_antecedente->ingresos=$request->ingresos;
			$Ginecologia_antecedente->traumatismos=$request->traumatismos;
			$Ginecologia_antecedente->tratamientos=$request->tratamientos;
			$Ginecologia_antecedente->hta=$request->hta;
			$Ginecologia_antecedente->displidemia=$request->displidemia;
			$Ginecologia_antecedente->dm=$request->dm;
			$Ginecologia_antecedente->otros=$request->otros;
			$Ginecologia_antecedente->habitos=$request->habitos;
			$Ginecologia_antecedente->familiares=$request->familiares;
			$Ginecologia_antecedente->situacion=$request->situacion;
			$Ginecologia_antecedente->save();


            flash('Se ha registrado la información de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.antecedentes',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id]);
        }
    }

      //ANTECEDENTE GINECOBSTETRICA
     /**
     * .
     * Muestra los datos básicos de la historia ginecológica seleccionada
     * @param $paciente_id,$historia_ginecologica_id
     */
    public function ginecologica_ginecobstetrica($paciente_id,$historia_ginecologica_id)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($Historia_ginecologica)){
            abort(404);
        }
        $paciente = Paciente::where(['id'=> $Historia_ginecologica->medico_paciente->paciente_id])->with('user')->first();
        $medico = Medico::where(['id'=> $Historia_ginecologica->medico_paciente->medico_id])->with('user')->first();

		$Ginecologia_ginecobstetrico = Ginecologia_ginecobstetrico::where(['medico_paciente_id' => $Historia_ginecologica->medico_paciente->id] )->first();
        if(!is_null($Ginecologia_ginecobstetrico))
        {

        	$gestante=$Ginecologia_ginecobstetrico->gestante;
			$fum=$Ginecologia_ginecobstetrico->fum;
			$seguridad=$Ginecologia_ginecobstetrico->seguridad;
			$cesarias=$Ginecologia_ginecobstetrico->cesarias;
			$partos=$Ginecologia_ginecobstetrico->partos;
			$abortos=$Ginecologia_ginecobstetrico->abortos;
			$gestaciones=$Ginecologia_ginecobstetrico->gestaciones;
			$fpp=$Ginecologia_ginecobstetrico->fpp;
			
        }else{

			$gestante=date('d/m/Y');
			$fum=0;
			$seguridad=0;
			$cesarias=0;
			$partos=0;
			$abortos=0;
			$gestaciones=0;
			$fpp=date('d/m/Y');
        }


        $datos=[
           
			'gestante'=>$gestante,'fum'=>$fum,'seguridad'=>$seguridad,'cesarias'=>$cesarias,'partos'=>$partos,'abortos'=>$abortos,'gestaciones'=>$gestaciones,'fpp'=>$fpp     
        ];

        return  view('historias.historia.ginecologica.ginecobstetricos')->with(['paciente'=>$paciente,'medico'=>$medico,'historia_ginecologica'=>$Historia_ginecologica,'datos'=> $datos ]);
    }


     /**
     * .
     * Actualiza los datos básicos de la historia ginecologica seleccionada
     * @param $request con los datos de actualización
     */
    public function ginecologica_ginecobstetrica_store(Request $request)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();
        
         $validator = Validator::make($request->all(), [

         	'gestante' => 'required|boolean', 
			'fum' => 'required|date_format:d/m/Y',  
			'seguridad' => 'required|boolean', 
			'cesarias' => 'required|string|max:500',
			'partos' => 'required|integer',
			'abortos' => 'required|integer',
			'gestaciones' => 'required|integer',
			'fpp' => 'required|date_format:d/m/Y',  
			        
           
        ]);
        if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.ginecobstetrica',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id]);
        }else{
            $Ginecologia_ginecobstetrico = Ginecologia_ginecobstetrico::where(['medico_paciente_id' => $Historia_ginecologica->medico_paciente->id] )->first();
	 		if(is_null($Ginecologia_ginecobstetrico)){
				$Ginecologia_ginecobstetrico= new Ginecologia_ginecobstetrico;
			}
			$Ginecologia_ginecobstetrico->medico_paciente()->associate($Historia_ginecologica->medico_paciente->id);
			$Ginecologia_ginecobstetrico->gestante=$request->gestante;
			$Ginecologia_ginecobstetrico->fum= Carbon::createFromFormat('d/m/Y',$request->fum);
			$Ginecologia_ginecobstetrico->seguridad=$request->seguridad;
			$Ginecologia_ginecobstetrico->cesarias=$request->cesarias;
			$Ginecologia_ginecobstetrico->partos=$request->partos;
			$Ginecologia_ginecobstetrico->abortos=$request->abortos;
			$Ginecologia_ginecobstetrico->gestaciones=$request->gestaciones;
			$Ginecologia_ginecobstetrico->fpp=Carbon::createFromFormat('d/m/Y',$request->fpp);
			$Ginecologia_ginecobstetrico->save();
            flash('Se ha registrado la información de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.ginecobstetrica',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id]);
        }
    }


      //DATOS DE LA CONSULTA
     /**
     * .
     * Muestra los datos básicos de la historia ginecológica seleccionada
     * @param $paciente_id,$historia_ginecologica_id
     */
    public function ginecologica_edit($paciente_id,$historia_ginecologica_id)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($Historia_ginecologica)){
            abort(404);
        }
        $paciente = Paciente::where(['id'=> $Historia_ginecologica->medico_paciente->paciente_id])->with('user')->first();
        $medico = Medico::where(['id'=> $Historia_ginecologica->medico_paciente->medico_id])->with('user')->first();

        $combos=array();
        
        $resultados=[
           
            'motivo_consulta' => $Historia_ginecologica->motivo_consulta,
            'enfermedad_actual' => $Historia_ginecologica->enfermedad_actual,
         
        ];

        return  view('historias.historia.ginecologica.ginecologica')->with(['paciente'=>$paciente,'medico'=>$medico,'historia_ginecologica'=>$Historia_ginecologica,'combos'=>$combos,'resultados'=> $resultados ]);
    }


     /**
     * .
     * Actualiza los datos básicos de la historia ginecologica seleccionada
     * @param $request con los datos de actualización
     */
    public function ginecologica_edit_store(Request $request)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();
        
         $validator = Validator::make($request->all(), [
            'motivo_consulta' => 'required|string|max:500',
            'enfermedad_actual' => 'required|string|max:500', 
           
        ]);
        if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.edit',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id]);
        }else{
            
            $Historia_ginecologica->motivo_consulta=$request->motivo_consulta;
            $Historia_ginecologica->enfermedad_actual=$request->enfermedad_actual;
            $Historia_ginecologica->save();


            flash('Se ha registrado la información de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.edit',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id]);
        }
    }

    //EXPLORACION FISICA
      /**
     * .
     * Muestra los datos de los examenes fisicos  de la historia ginecologica seleccionada
     * @param $paciente_id,$historia_ocupacional_id
     */
    public function ginecologica_fisicos($paciente_id,$historia_ginecologica_id)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($historia_ginecologica)){
            abort(404);
        }
        $paciente = Paciente::where(['id'=> $historia_ginecologica->medico_paciente->paciente_id])->with('user')->first();
        $medico = Medico::where(['id'=> $historia_ginecologica->medico_paciente->medico_id])->with('user')->first();
        $datos=array();
        $Ginecologia_exploracion = Ginecologia_exploracion::where(['historia_ginecologica_id'=> $historia_ginecologica->id])->first();
        if($Ginecologia_exploracion)
        {
          
            $peso=$Ginecologia_exploracion->peso;
            $talla=$Ginecologia_exploracion->talla;
            $pa=$Ginecologia_exploracion->pa;
            $ta=$Ginecologia_exploracion->ta;
            $fc=$Ginecologia_exploracion->fc;
            $fr=$Ginecologia_exploracion->fr;
            $otros=$Ginecologia_exploracion->otros;
            $aspectogeneral=$Ginecologia_exploracion->aspectogeneral;

        }else{

            $lateralidad_id=1;
            $peso='0.00';
            $talla='0.00';
            $pa='';
            $ta='';
            $fc='';
            $fr='';
            $otros='';
            $aspectogeneral='';
        }
       
        $datos=[ 'peso'=>$peso, 'talla'=>$talla, 'pa'=>$pa, 'ta'=>$ta, 'fc'=>$fc, 'fr'=>$fr, 'otros'=>$otros, 'aspectogeneral'=>$aspectogeneral];
        return  view('historias.historia.ginecologica.fisicos')->with(['paciente'=>$paciente,'medico'=>$medico,'historia_ginecologica'=>$historia_ginecologica, 'datos' =>  $datos]);
    }
    /**
     * .
     * Actualiza los datos del examen fisico de la historia ginecologica seleccionada
     * @param $request con los datos del examen fisico
     */
    public function ginecologica_fisicos_store(Request $request)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();
       
        $validator = Validator::make($request->all(), [
      
            'peso' => 'required|numeric', 
            'talla' => 'required|numeric', 
            'pa' => 'required|string|max:10', 
            'ta' => 'required|string|max:10', 
            'fc' => 'required|string|max:10', 
            'fr' => 'required|string|max:10', 
            'otros' => 'required|string|max:500', 
            'aspectogeneral' => 'required|string|max:2000', 
            
        ]);
        if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.fisicos',[$historia_ginecologica->medico_paciente->id,$historia_ginecologica->id])->withInput();
        }else{

            $Ginecologia_exploracion = Ginecologia_exploracion::where(['historia_ginecologica_id' => $historia_ginecologica->id])->first();
          
            if(is_null($Ginecologia_exploracion))
            {
                $Ginecologia_exploracion=new Ginecologia_exploracion;
            }
           
            $Ginecologia_exploracion->peso= $request->peso;
            $Ginecologia_exploracion->talla= $request->talla;
            $Ginecologia_exploracion->pa= $request->pa;
            $Ginecologia_exploracion->ta= $request->ta;
            $Ginecologia_exploracion->fc= $request->fc;
            $Ginecologia_exploracion->fr= $request->fr;
            $Ginecologia_exploracion->otros= $request->otros;
            $Ginecologia_exploracion->aspectogeneral= $request->aspectogeneral;
            $Ginecologia_exploracion->historia_ginecologica()->associate($historia_ginecologica);
            $Ginecologia_exploracion->save();

            flash('Se ha registrado la información de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.fisicos',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id]);
        }
    }

     /**
     * .
     * Actualiza el analisis de la historia ginecológica seleccionada
     * @param $request con el analisis
     */
      public function ginecologica_analisis_store(Request $request)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();
        $validator = Validator::make($request->all(), [
            'analisis' => 'string|max:2500',   
            
        ]);

         if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.diagnosticos',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id])->withInput();
        }else{
            $historia_ginecologica->analisis=$request->analisis;
            $historia_ginecologica->save();
            flash('Se ha registrado la información de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.diagnosticos',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id]);

       }
    }
     //DIAGNOSTICOS
     /**
     * .
     * Muestra los datos del diagnostico de la historia ginecológica seleccionada
     * @param $paciente_id,$historia_historia_ginecologica_id
     */
    public function ginecologica_diagnosticos($paciente_id,$historia_ginecologica_id)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($historia_ginecologica)){
            abort(404);
        }
        $paciente = Paciente::where(['id'=> $historia_ginecologica->medico_paciente->paciente_id])->with('user')->first();
        $medico = Medico::where(['id'=> $historia_ginecologica->medico_paciente->medico_id])->with('user')->first();

        $combos=array();
        $tipo_diagnosticos=array();
        $tipo_diagnosticos_query = Tipo_diagnostico::orderby('codigo')->get();
        foreach ($tipo_diagnosticos_query as $tipo_diagnostico) {
            $tipo_diagnosticos[$tipo_diagnostico->id]=$tipo_diagnostico->codigo.' > '.$tipo_diagnostico->descripcion;
        }
        $diagnosticos= Ginecologia_diagnostico::where(['historia_ginecologica_id' => $historia_ginecologica->id])->with('tipo_diagnostico')->orderBy('id')->get();
       
        $combos=[ 'tipo_diagnosticos' => $tipo_diagnosticos,'diagnosticos'=>$diagnosticos];

        return  view('historias.historia.ginecologica.diagnosticos')->with(['paciente'=>$paciente,'medico'=>$medico,'historia_ginecologica'=>$historia_ginecologica,'combos'=>$combos ]);
    }
        /**
     * .
     * Registra los datos del diagnostico individual de la historia ginecologica seleccionada
     * @param $request los datos del diagnostico individual
     */
    public function ginecologica_diagnosticos_store(Request $request)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();


        $validator = Validator::make($request->all(), [
            'concepto' => 'required|string|max:250', 
            'tipo_diagnostico_id' => 'required|exists:tipo_diagnosticos,id',   
            
        ]);
        if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.diagnosticos',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id])->withInput();
        }else{
            
            $Ginecologia_diagnostico=new Ginecologia_diagnostico;
            $Ginecologia_diagnostico->tipo_diagnostico()->associate($request->tipo_diagnostico_id);
            $Ginecologia_diagnostico->historia_ginecologica()->associate($historia_ginecologica);
            $Ginecologia_diagnostico->concepto=$request->concepto;
            $Ginecologia_diagnostico->save();
            flash('Se ha registrado el diagnóstico de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.diagnosticos',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id]);
        }
    }
    /**
     * .
     * Elimina un diagnostico individual de la historia ginecologica seleccionada
     * @param $paciente_id,$historia_ginecologica_id,$diagnostico_id
     */
    public function ginecologica_diagnosticos_destroy($paciente_id,$historia_ginecologica_id,$diagnostico_id)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($historia_ginecologica)){
            abort(404);
        }
        $Ginecologia_diagnostico = Ginecologia_diagnostico::find($diagnostico_id);
        $Ginecologia_diagnostico->delete();
        flash('El registro se ha eliminado de forma exitosa!', 'danger');
        return redirect()->route('historias.ginecologica.diagnosticos',[$historia_ginecologica->medico_paciente->paciente->id,$historia_ginecologica->id]);

    }

     //PROCEDIMIENTOS
    /**
     * .
     * Muestra las recomendaciones de la historia ginecológica seleccionada
     * @param $paciente_id,$historia_ginecologica_id,$diagnostico_id
     */
    public function ginecologica_procedimientos($paciente_id,$historia_ginecologica_id)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($historia_ginecologica)){
            abort(404);
        }
        $paciente = Paciente::where(['id'=> $historia_ginecologica->medico_paciente->paciente_id])->with('user')->first();
        $medico = Medico::where(['id'=> $historia_ginecologica->medico_paciente->medico_id])->with('user')->first();

        $combos=array();
        $medicamentos= Ginecologia_medicamento::where(['historia_ginecologica_id' => $historia_ginecologica->id])->orderBy('id')->get();

        $procedimientos= Ginecologia_procedimiento::where(['historia_ginecologica_id' => $historia_ginecologica->id])->orderBy('id')->get();

        $Ginecologia_incapacidad = Ginecologia_incapacidad::where(['historia_ginecologica_id' => $historia_ginecologica->id] )->first();
        if(!is_null($Ginecologia_incapacidad))
        {
            $fechainicial=$Ginecologia_incapacidad->fechainicial;
            $fechafinal=$Ginecologia_incapacidad->fechafinal;
            $observacion=$Ginecologia_incapacidad->observacion;
            
        }else{

            $fechainicial=Carbon::createFromFormat('d/m/Y',date('d/m/Y')); 
            $fechafinal=Carbon::createFromFormat('d/m/Y',date('d/m/Y')); 
            $observacion='';
        }
        $combos=[
           
            'fechainicial'=>$fechainicial,'fechafinal'=>$fechafinal,'observacion'=>$observacion,'medicamentos'=>$medicamentos,'procedimientos'=>$procedimientos
        ];

        return  view('historias.historia.ginecologica.procedimientos')->with(['paciente'=>$paciente,'medico'=>$medico,'historia_ginecologica'=>$historia_ginecologica,'combos'=>$combos ]);
    }

    /**
     * .
     * Actualiza las recomendaciones de la historia ginecológica seleccionada
     * @param $request con el procedimiento
     */
    public function ginecologica_procedimientos_store(Request $request)
    {
         $historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();


        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:250', 
            'observacion' => 'string|max:2500',   
            
        ]);
        if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.procedimientos',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id])->withInput();
        }else{
            
            $Ginecologica_procedimiento=new Ginecologia_procedimiento;
            $Ginecologica_procedimiento->historia_ginecologica()->associate($historia_ginecologica);
            $Ginecologica_procedimiento->descripcion=$request->descripcion;
            $Ginecologica_procedimiento->observacion=$request->observacion;
            $Ginecologica_procedimiento->save();
            flash('Se ha registrado el procedimiento de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.procedimientos',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id]);
        }

    }

     /**
     * .
     * Elimina un medicamento individual de la historia ginecologica seleccionada
     * @param $paciente_id,$historia_ginecologica_id,$medicamento_id
     */
    public function ginecologica_procedimientos_destroy($paciente_id,$historia_ginecologica_id,$procedimiento_id)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($historia_ginecologica)){
            abort(404);
        }
        $Ginecologia_procedimiento = Ginecologia_procedimiento::find($procedimiento_id);
        $Ginecologia_procedimiento->delete();
        flash('El registro se ha eliminado de forma exitosa!', 'danger');
        return redirect()->route('historias.ginecologica.procedimientos',[$historia_ginecologica->medico_paciente->paciente->id,$historia_ginecologica->id]);
    }

    /**
     * .
     * Registra los datos del medicamento de la historia ginecologica seleccionada
     * @param $request los datos del medicamento individual
     */
    public function ginecologica_medicamentos_store(Request $request)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:250', 
            'dosis' => 'required|string|max:250',   
            'observacion' => 'string|max:2500',   
            
        ]);
        if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.procedimientos',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id])->withInput();
        }else{
            
            $Ginecologia_medicamento=new Ginecologia_medicamento;
            $Ginecologia_medicamento->historia_ginecologica()->associate($historia_ginecologica);
            $Ginecologia_medicamento->descripcion=$request->descripcion;
            $Ginecologia_medicamento->dosis=$request->dosis;
            $Ginecologia_medicamento->observacion=$request->observacion;
            $Ginecologia_medicamento->save();
            flash('Se ha registrado el medicamento de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.procedimientos',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id]);
        }
    }
    /**
     * .
     * Elimina un medicamento individual de la historia ginecologica seleccionada
     * @param $paciente_id,$historia_ginecologica_id,$medicamento_id
     */
    public function ginecologica_medicamentos_destroy($paciente_id,$historia_ginecologica_id,$medicamento_id)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($historia_ginecologica)){
            abort(404);
        }
        $Ginecologia_medicamento = Ginecologia_medicamento::find($medicamento_id);
        $Ginecologia_medicamento->delete();
        flash('El registro se ha eliminado de forma exitosa!', 'danger');
        return redirect()->route('historias.ginecologica.procedimientos',[$historia_ginecologica->medico_paciente->paciente->id,$historia_ginecologica->id]);

    }
    //RECOMENDACIONES
    /**
     * .
     * Actualiza las recomendaciones de la historia ginecológica seleccionada
     * @param $request con la recomendación
     */
    public function ginecologica_recomendaciones_store(Request $request)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();
        $validator = Validator::make($request->all(), [
            'recomendaciones' => 'string|max:2500',   
            
        ]);

         if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.procedimientos',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id])->withInput();
        }else{

            $historia_ginecologica->recomendaciones=$request->recomendaciones;
            $historia_ginecologica->save();
            flash('Se ha registrado la información de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.procedimientos',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id]);

       }
    }

      //INCAPACIDAD
     /**
     * .
     * Actualiza los datos de incapacidad de la historia ginecologica seleccionada
     * @param $request con los datos de actualización
     */
    public function ginecologica_incapacidad_store(Request $request)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();
        
         $validator = Validator::make($request->all(), [

         	'fechainicial' => 'required|date_format:d/m/Y',  
			'fechafinal' => 'required|date_format:d/m/Y',  
			'observacion' => 'string|max:2500',

         
           
        ]);
        if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.procedimientos',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id])->withInput();

        }else{
            
           
           $Ginecologia_incapacidad = Ginecologia_incapacidad::where(['historia_ginecologica_id' => $Historia_ginecologica->id] )->first();
	 		if(is_null($Ginecologia_incapacidad)){
				$Ginecologia_incapacidad= new Ginecologia_incapacidad;
			}
			$Ginecologia_incapacidad->historia_ginecologica()->associate($Historia_ginecologica->id);
			$Ginecologia_incapacidad->fechainicial= Carbon::createFromFormat('d/m/Y',$request->fechainicial);
			$Ginecologia_incapacidad->fechafinal=Carbon::createFromFormat('d/m/Y',$request->fechafinal);
			$Ginecologia_incapacidad->observacion=$request->observacion;
			$Ginecologia_incapacidad->save();


            flash('Se ha registrado la información de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.procedimientos',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id]);
        }
    }

     /**
     * .
     * Muestra las gestaciones registradas de la paciente
     * @param $paciente_id,$historia_ginecologica_id
     */
    public function ginecologica_gestaciones($paciente_id,$historia_ginecologica_id)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id' => $historia_ginecologica_id] )->with('medico_paciente.ginecologia_ginecobstetrico')->first();
        if(is_null($historia_ginecologica)){
            abort(404);
        }
        $paciente = Paciente::where(['id'=> $historia_ginecologica->medico_paciente->paciente_id])->with('user')->first();
        $medico = Medico::where(['id'=> $historia_ginecologica->medico_paciente->medico_id])->with('user')->first();
        $ginecologia_exploracion_inicial=Ginecologia_exploracion_inicial::where('medico_paciente_id', '=', $historia_ginecologica->medico_paciente->id)->orderBy('id','DESC')->get();
        $combos=['ginecologia_exploracion_iniciales'=>$ginecologia_exploracion_inicial];
        return  view('historias.historia.ginecologica.gestaciones')->with(['paciente'=>$paciente,'medico'=>$medico,'historia_ginecologica'=>$historia_ginecologica,'combos'=>$combos ]);
     
    }
    /**
     * .
     * Registra una gestación vacía de la historia seleccionada
     * @param $paciente_id,$historia_ginecologica_id
     */
    public function ginecologica_gestaciones_store($paciente_id,$historia_ginecologica_id)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id' => $historia_ginecologica_id] )->with('medico_paciente.ginecologia_ginecobstetrico')->first();

        if($Historia_ginecologica->medico_paciente->ginecologia_ginecobstetrico->gestante==1){
            $fum=$Historia_ginecologica->medico_paciente->ginecologia_ginecobstetrico->fum;
            $semanas=$fum->diffInWeeks(Carbon::now());
            if($semanas!=0){
                $dias=$fum->diffInDays(Carbon::now()->subWeeks($semanas));      
            }else{
                $dias=$fum->diffInDays(Carbon::now());
            }
            $semanaamenorrea='';
            if($semanas!=0)
            {
                 $semanaamenorrea=$semanaamenorrea.$semanas.' semana(s)';
            }
            if($dias!=0)
            {
                if($semanaamenorrea!='')
                {
                    $semanaamenorrea=$semanaamenorrea.' y ';
                }
                $semanaamenorrea=$semanaamenorrea.$dias.' día(s)';
            }
            
            Ginecologia_exploracion_inicial::where('medico_paciente_id', '=', $Historia_ginecologica->medico_paciente->id)->update(['activa' => 0]);
            $Ginecologia_exploracion_inicial=new Ginecologia_exploracion_inicial;
            $Ginecologia_exploracion_inicial->medico_paciente()->associate($Historia_ginecologica->medico_paciente->id);
            $Ginecologia_exploracion_inicial->semanaamenorrea=$semanaamenorrea;
            $Ginecologia_exploracion_inicial->sacogestacional='';
            $Ginecologia_exploracion_inicial->formasaco='';
            $Ginecologia_exploracion_inicial->visualizacionembrion=0;
            $Ginecologia_exploracion_inicial->numeroembriones=0;
            $Ginecologia_exploracion_inicial->actividadmotora=0;
            $Ginecologia_exploracion_inicial->actividadcardiaca=0;
            $Ginecologia_exploracion_inicial->longitud=0;
            $Ginecologia_exploracion_inicial->corionanterior=0;
            $Ginecologia_exploracion_inicial->corionposterior=0;
            $Ginecologia_exploracion_inicial->corioncervix=0;
            $Ginecologia_exploracion_inicial->ecocardiagrama='';
            $Ginecologia_exploracion_inicial->observaciones='';
            $Ginecologia_exploracion_inicial->fechaparto=$Historia_ginecologica->medico_paciente->ginecologia_ginecobstetrico->fpp;
            $Ginecologia_exploracion_inicial->activa=1;
            $Ginecologia_exploracion_inicial->save();
            flash('Se ha registrado la información de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.gestaciones',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id])->withInput();
        }else{

            flash('La paciente no tiene el estatus de gestante activo!', 'danger');
            return redirect()->route('historias.ginecologica.gestaciones',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id]);

        }
    }
     /**
     * .
     * Elimnia una gestación de la historia
     * @param $paciente_id,$historia_ginecologica_id,$ginecologia_exploracion_inicial_id
     */
    public function ginecologica_destroy_gestaciones($paciente_id,$historia_ginecologica_id,$ginecologia_exploracion_inicial_id)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($historia_ginecologica)){
            abort(404);
        }
        $Ginecologia_exploracion_inicial = Ginecologia_exploracion_inicial::find($ginecologia_exploracion_inicial_id);
        $Ginecologia_exploracion_inicial->delete();
        flash('El registro se ha eliminado de forma exitosa!', 'danger');
        return redirect()->route('historias.ginecologica.gestaciones',[$historia_ginecologica->medico_paciente->paciente->id,$historia_ginecologica->id]);
    }
    /**
     * .
     * Muestra las exploraciones de la gestación seleccioanda
     * @param $paciente_id,$historia_ginecologica_id,$ginecologia_exploracion_inicial_id
     */
    public function ginecologica_exploraciones($paciente_id,$historia_ginecologica_id,$ginecologia_exploracion_inicial_id)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id' => $historia_ginecologica_id] )->with('medico_paciente.ginecologia_ginecobstetrico')->first();
        if(is_null($historia_ginecologica)){
            abort(404);
        }
      
        $paciente = Paciente::where(['id'=> $historia_ginecologica->medico_paciente->paciente_id])->with('user')->first();
        $medico = Medico::where(['id'=> $historia_ginecologica->medico_paciente->medico_id])->with('user')->first();

        $ginecologia_exploracion_inicial=Ginecologia_exploracion_inicial::where('id', '=', $ginecologia_exploracion_inicial_id)->first();

        if($historia_ginecologica->medico_paciente->ginecologia_ginecobstetrico->gestante==0 || $ginecologia_exploracion_inicial->activa==0){
            $disabled="disabled";
        }else{
            $disabled='';
        }

        $ginecologia_exploracion_periodica=Ginecologia_exploracion_periodica::where('ginecologia_exploracion_inicial_id', '=', $ginecologia_exploracion_inicial_id)->with('ginecologia_exploracion_periodo')->orderBy('id','DESC')->get();

        $ginecologia_exploracion_periodo=Ginecologia_exploracion_periodo::all()->pluck('descripcion','id');

        $combos=['ginecologia_exploracion_inicial'=>$ginecologia_exploracion_inicial,'ginecologia_exploracion_periodicas'=>$ginecologia_exploracion_periodica, 'ginecologia_exploracion_periodo'=> $ginecologia_exploracion_periodo];
        
        return  view('historias.historia.ginecologica.gestaciones.periodos')->with(['paciente'=>$paciente,'medico'=>$medico,'historia_ginecologica'=>$historia_ginecologica,'combos'=>$combos,'disabled'=>$disabled ]);
     
    }

     /**
     * .
     * Actualiza la exploración inicial de la gestación seleccioanda
     * @param $request con los datos de la exploración inicial
     */

    public function ginecologica_exploraciones_store(Request $request)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente')->first();
        
         $validator = Validator::make($request->all(), [

            'sacogestacional' => 'required|string|max:10',  
            'formasaco' => 'required|string|max:20',  
            'visualizacionembrion' => 'required|integer|max:1',  
            'numeroembriones' => 'required|integer',
            'actividadmotora' => 'required|integer|max:1',  
            'actividadcardiaca' =>'required|integer|max:1',  
            'longitud' => 'required|integer',
            'corionanterior' => 'required|integer|max:1',  
            'corionposterior' => 'required|integer|max:1',  
            'corioncervix' => 'required|integer|max:1',  
            'ecocardiagrama' => 'string|max:2500',
            'observaciones' => 'string|max:2500',
           
        ]);
        if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.exploraciones',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id,$request->ginecologia_exploracion_inicial_id])->withInput();
        }else{
            
           
           $Ginecologia_exploracion_inicial = Ginecologia_exploracion_inicial::where(['id' => $request->ginecologia_exploracion_inicial_id] )->first();
            if(!is_null($Ginecologia_exploracion_inicial)){

                
                $Ginecologia_exploracion_inicial->sacogestacional=$request->sacogestacional;
                $Ginecologia_exploracion_inicial->formasaco=$request->formasaco;
                $Ginecologia_exploracion_inicial->visualizacionembrion=$request->visualizacionembrion;
                $Ginecologia_exploracion_inicial->numeroembriones=$request->numeroembriones;
                $Ginecologia_exploracion_inicial->actividadmotora=$request->actividadmotora;
                $Ginecologia_exploracion_inicial->actividadcardiaca=$request->actividadcardiaca;
                $Ginecologia_exploracion_inicial->longitud=$request->longitud;
                $Ginecologia_exploracion_inicial->corionanterior=$request->corionanterior;
                $Ginecologia_exploracion_inicial->corionposterior=$request->corionposterior;
                $Ginecologia_exploracion_inicial->corioncervix=$request->corioncervix;
                $Ginecologia_exploracion_inicial->ecocardiagrama=$request->ecocardiagrama;
                $Ginecologia_exploracion_inicial->observaciones=$request->observaciones;
                $Ginecologia_exploracion_inicial->save();
             }


            flash('Se ha registrado la información de forma exitosa!', 'success');
            return redirect()->route('historias.ginecologica.exploraciones',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id,$request->ginecologia_exploracion_inicial_id]);
        }
    }
     /**
     * .
     * Registra una nueva exploración periodica de la gestación seleccioanda
     * @param $request con los datos de la exploración
     */
    public function ginecologica_exploraciones_periodicas_store(Request $request)
    {
        $Historia_ginecologica = Historia_ginecologica::where(['id' => $request->historia_ginecologica_id] )->with('medico_paciente.ginecologia_ginecobstetrico')->first();
        
         $validator = Validator::make($request->all(), [

           
            'ginecologia_exploracion_periodo_id' =>'required|exists:ginecologia_exploracion_periodos,id',
            'situacionfetal' =>'string|max:250',
            'dorso' =>'string|max:250',
            'dbp' =>'required|numeric', 
            'lf' =>'required|numeric', 
            'pabdominal' =>'required|numeric', 
            'actividadmotora' =>'required|integer|max:1',  
            'actividadcardiaca' =>'required|integer|max:1',  
            'actividadrespiratoria' =>'required|integer|max:1',  
            'semanaecografia' =>'required|integer', 
            'localizacion' =>'required|string|max:250',
            'madurez' =>'required|string|max:250',
            'liquidovolumen' =>'required|string|max:250',
            'liquidoobservaciones' =>'required|string|max:250',
           
        ]);
        if ($validator->fails()) {
            flash(implode('<br>',$validator->errors()->all()), 'danger');
            return redirect()->route('historias.ginecologica.exploraciones',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id,$request->ginecologia_exploracion_inicial_id])->withInput();
        }else{
            
           
           $Ginecologia_exploracion_periodica = Ginecologia_exploracion_periodica::where(['ginecologia_exploracion_inicial_id' => $request->ginecologia_exploracion_inicial_id,'ginecologia_exploracion_periodo_id' => $request->ginecologia_exploracion_periodo_id] )->first();

            if(is_null($Ginecologia_exploracion_periodica)){

                $fum=$Historia_ginecologica->medico_paciente->ginecologia_ginecobstetrico->fum;
                $semanas=$fum->diffInWeeks(Carbon::now());
                if($semanas!=0){
                    $dias=$fum->diffInDays(Carbon::now()->subWeeks($semanas));      
                }else{
                    $dias=$fum->diffInDays(Carbon::now());
                }
                $semanaamenorrea='';
                if($semanas!=0)
                {
                     $semanaamenorrea=$semanaamenorrea.$semanas.' semana(s)';
                }
                if($dias!=0)
                {
                    if($semanaamenorrea!='')
                    {
                        $semanaamenorrea=$semanaamenorrea.' y ';
                    }
                    $semanaamenorrea=$semanaamenorrea.$dias.' día(s)';
                }
                $Ginecologia_exploracion_periodica=new Ginecologia_exploracion_periodica;
                $Ginecologia_exploracion_periodica->ginecologia_exploracion_periodo()->associate($request->ginecologia_exploracion_periodo_id);
                 $Ginecologia_exploracion_periodica->ginecologia_exploracion_inicial()->associate($request->ginecologia_exploracion_inicial_id);
                $Ginecologia_exploracion_periodica->semanaamenorrea=$semanaamenorrea;
                $Ginecologia_exploracion_periodica->situacionfetal=$request->situacionfetal;
                $Ginecologia_exploracion_periodica->dorso=$request->dorso;
                $Ginecologia_exploracion_periodica->dbp=$request->dbp;
                $Ginecologia_exploracion_periodica->lf=$request->lf;
                $Ginecologia_exploracion_periodica->pabdominal=$request->pabdominal;
                $Ginecologia_exploracion_periodica->actividadmotora =$request->actividadmotora;
                $Ginecologia_exploracion_periodica->actividadcardiaca=$request->actividadcardiaca;
                $Ginecologia_exploracion_periodica->actividadrespiratoria =$request->actividadrespiratoria;
                $Ginecologia_exploracion_periodica->semanaecografia=$request->semanaecografia;
                $Ginecologia_exploracion_periodica->localizacion=$request->localizacion;
                $Ginecologia_exploracion_periodica->madurez=$request->madurez;
                $Ginecologia_exploracion_periodica->liquidovolumen=$request->liquidovolumen;
                $Ginecologia_exploracion_periodica->liquidoobservaciones=$request->liquidoobservaciones;
                $Ginecologia_exploracion_periodica->save();
                flash('Se ha registrado la información de forma exitosa!', 'success');
                return redirect()->route('historias.ginecologica.exploraciones',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id,$request->ginecologia_exploracion_inicial_id]);

             }else{

                flash('Ya exite un registro de exploración para ese periodo!', 'danger');
                return redirect()->route('historias.ginecologica.exploraciones',[$Historia_ginecologica->medico_paciente->paciente_id,$Historia_ginecologica->id,$request->ginecologia_exploracion_inicial_id])->withInput();
           
            }
        }
    }
    /**
     * .
     * Elimina una exploración periodica de la gestación seleccioanda
     * @param $paciente_id,$historia_ginecologica_id,$ginecologia_exploracion_inicial_id,$ginecologia_exploracion_periodica_id
     */
    public function ginecologica_destroy_exploraciones($paciente_id,$historia_ginecologica_id,$ginecologia_exploracion_inicial_id,$ginecologia_exploracion_periodica_id)
    {
        $historia_ginecologica = Historia_ginecologica::where(['id'=>$historia_ginecologica_id,'activa'=>1])->with('medico_paciente')->first();
        if(is_null($historia_ginecologica)){
            abort(404);
        }
        $Ginecologia_exploracion_periodica = Ginecologia_exploracion_periodica::find($ginecologia_exploracion_periodica_id);
        $Ginecologia_exploracion_periodica->delete();
        flash('El registro se ha eliminado de forma exitosa!', 'danger');
        return redirect()->route('historias.ginecologica.exploraciones',[$historia_ginecologica->medico_paciente->paciente_id,$historia_ginecologica->id,$ginecologia_exploracion_inicial_id])->withInput();
           
    }

   
 


}
