<?php

namespace App\Http\Controllers;

use App\AbogadoCaso;
use App\Avence;
use App\Caso;
use App\Cita;
use App\Cliente;
use App\Espediente;
use App\Observacion;
use App\Persona;
use Illuminate\Http\Request;

class CasoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $casos=Caso::all();
        foreach($casos as $caso){
            $clie=Persona::where('id','=',$caso->id_cliente)->first();
            $caso['nombre_cliente']=$clie->nombre." ".$clie->apellido;
        }
        return view('proceso.listar',compact('casos'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clientes=Persona::where('tipo','=','cliente')->get();
        return view('proceso.create',compact('clientes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $caso=new Caso();
        $id=session('users')['id'];


        $caso->id_cliente=$request->get('cliente');
        $caso->nombre_juez=$request->get('nombre_juez');
        $caso->descripcion=$request->get('descripcion');
        $caso->tipo=$request->get('tipo_caso');
        if($request->get('fecha_ini')!=""){
            $fechaP=explode("/",$request->get('fecha_ini'));
            $caso->fecha_inicio=$fechaP[2]."-".$fechaP[0]."-".$fechaP[1];
        }
        if($request->get('radicado')!="")
            $caso->radicado=$request->get('radicado');
        $caso->save();
        AbogadoCaso::create(['id_abogado'=>$id,'id_caso'=>$caso->id]);

        $clientes=Persona::where('tipo','=','cliente')->get();
        return view('proceso.create',compact('clientes'))->with("msj","Se registro correctamente el cliente");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Caso  $caso
     * @return \Illuminate\Http\Response
     */
    public function show(Caso $caso)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Caso  $caso
     * @return \Illuminate\Http\Response
     */
    public function edit(Caso $caso,$id)
    {
        $abogadoCaso=AbogadoCaso::where('id_caso',$id)->first();
        $proceso=Caso::where('id',$id)->first();
        $clientes=Persona::where('tipo','cliente')->get();
        $espedientes=Espediente::where('id_caso',$id)->get();
        $citas=Cita::where('id_abogado_caso',$abogadoCaso->id)->get();
        $observaciones=Observacion::where('id_abogado_caso',$abogadoCaso->id)->get();
        $avances=Avence::where('id_abogado_caso',$abogadoCaso->id)->get();
        //dd($clientes);
        return view('proceso.edit',compact('clientes','proceso','espedientes','citas','observaciones','avances'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Caso  $caso
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Caso $caso)
    {
        $caso=Caso::findOrFail($request->id);

        $request['estado']=(!isset($request->estado))?false:true;

        $caso->fill($request->all());
        $caso->update();
        dd($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Caso  $caso
     * @return \Illuminate\Http\Response
     */
    public function destroy(Caso $caso)
    {
        //
    }

////////////////////////////CITAS////////////////////////////////////
    public function createCita(Request $request){

        $id=session('users')['id'];
        $abogadocaso=AbogadoCaso::where('id_caso',$request->id_proceso)
            ->where('id_abogado',$id)->first();
        $request['id_abogado_caso']=$abogadocaso->id;
        $fechaP=explode("/",$request->fecha);
        $request['fecha']=$fechaP[2]."-".$fechaP[0]."-".$fechaP[1];
        Cita::create($request->all());
        dd($request->all());
    }
    public function showCita($id){
        $cita=Cita::where('id',$id)->first();
        return response()->json([
            $cita->toArray()
        ],200);
    }

    public function updateCita(Request $request){
        $cita=Cita::findOrFail($request->id);
        $cita->fill($request->all());
        $cita->update();
        return response()->json([
            Cita::where('id_abogado_caso',$cita->id_abogado_caso)->get()
        ],200);
    }

    public function deleteCita(Request $request){

    }

///////////////////////OBSERVACIONES//////////////////////////////////////////////
    public function createObservacion(Request $request){
        $id=session('users')['id'];
        $abogadocaso=AbogadoCaso::where('id_caso',$request->id_proceso)
            ->where('id_abogado',$id)->first();
        $request['id_abogado_caso']=$abogadocaso->id;
        $request['fecha']=date('Y-m-d');
        Observacion::create($request->all());
        dd($request->all());
    }
    public function showObservacion($id){
        $res=Observacion::where('id',$id)->first();
        return response()->json([
            $res->toArray()
        ],200);
    }

    public function updateObservacion(Request $request){
        $res=Observacion::findOrFail($request->id);
        $res->fill($request->all());
        $res->update();
        return response()->json([
            Observacion::where('id_abogado_caso',$res->id_abogado_caso)->get()
        ],200);
    }

    public function deleteObservacion(Request $request){

    }
////////////////////////AVANCES////////////////////////////////////////
    public function createAvance(Request $request){
        $id=session('users')['id'];
        $abogadocaso=AbogadoCaso::where('id_caso',$request->id_proceso)
            ->where('id_abogado',$id)->first();
        $request['id_abogado_caso']=$abogadocaso->id;
        $request['fecha']=date('Y-m-d');
        $request['tipo']="abogado";
        Avence::create($request->all());
        return response()->json([
            "msg"=>"Success",
            "res"=>$request->toArray()
        ],200);
    }
    public function showAvance($id){
        $cita=Avence::where('id',$id)->first();
        return response()->json([
            $cita->toArray()
        ],200);
    }

    public function updateAvance(Request $request){
        $avance=Avence::findOrFail($request->id);
        $avance->fill($request->all());
        $avance->update();
        $avances=Avence::where('id_abogado_caso',$avance->id_abogado_caso)->get();
        return response()->json([
            $avances
        ],200);
    }

    public function deleteAvance(Request $request){

    }

///////////////////////EXPEDIENTES//////////////////////////////
    public function createExpedientes(Request $request){

        $request['id_caso']=$request->get('id_proceso');
        $request['fecha']=date('Y-m-d');
        //$request['tipo']="abogoado";
        Espediente::create($request->all());

        return response()->json([
            "msg"=>"Success",
            "res"=>$request->toArray()
        ],200);
    }

    public function showExpediente($id){
        $expediente=Espediente::where('id',$id)->first();
        return response()->json([
            $expediente->toArray()
        ],200);
    }

    public function updateExpediente(Request $request){
        $espe=Espediente::findOrFail($request->id);
        $espe->fill($request->all());
        $espe->update();
        $id_caso=$espe->id_caso;
        $exp=Espediente::where('id_caso',$id_caso)->get();
        return response()->json([
            $exp
        ],200);
    }

    public function deleteExpediente(Request $request){

    }

    ///////////////////////////////////////////////////////


}
