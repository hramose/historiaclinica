@extends('layouts.main')

@section('content-header')
	<h1>
		Historias
		<small>Nueva historia ocupacional</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="{{ route('home') }}"><i class="fa fa-home"></i> Escritorio</a></li>
		<li><a href="{{ route('historias.index') }}">Historias</a></li>
		<li class="active">Nueva historia ocupacional</li>
	</ol>
@endsection

@section('content')
	<div class="box box-default">
		<div class="box-header with-border">
		  	<div class="box-header">
		       	<div class="col-md-12 no-padding">
		            <div class="form-group col-md-1">
	              		<img class='profile-user-img img-responsive' src="{{ asset('images/users/'.$paciente->user->imagen) }}" />
		            </div>
		        </div>
		      	<h3 class="box-title">{{ $paciente->user->primernombre.' '.$paciente->user->segundonombre.' '.$paciente->user->primerapellido.' '.$paciente->user->segundoapellido.' : '.$paciente->user->tipodocumento.' '.$paciente->user->numerodocumento }}</h3>
		  	</div>  
		  	<div class="box-tools pull-right">
		   		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
		  	</div>
		    <a href="{{ route('historias.index') }}" class="btn btn-default btn-sm pull-right">Ver Historias de pacientes</a>
		    <a href="{{ route('historias.historia',[$paciente->id,'ocupacional',$medico->id]) }}" class="btn btn-default btn-sm pull-right">Ver lista de Historias de {{ $paciente->user->primernombre.' '.$paciente->user->primerapellido }}</a>
		</div>
  	</div>

  	<!--****************Habitos****************-->
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Hábitos</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            @include('flash::message')
            <div class="row">
                {!! Form::open(['class' => '','method' => 'POST','route' => 'especialidades.store','role' => 'form']) !!}
                <div class="col-md-12">
                    <div class="form-group col-md-2">
                        {!! Form::label('fumador','Fumador') !!}
                        {!! Form::select('fumador',[
                            'No' => 'No',
                            'Si' => 'Si',
                            'Exfumador' => 'Exfumador'
                        ], old('fumador'),['class' => 'form-control','style' => 'width: 100%']) !!}
                    </div>
                  
                    <div class="form-group col-md-3">
                        {!! Form::label('tiempo_fumador_id','Tiempo') !!}
                        {!! Form::select('tiempo_fumador_id',$combos['tiempo_fumadores'], old('tiempo_fumador_id'),['class' => 'form-control','style' => 'width: 100%']) !!}
                    </div>
               
                    <div class="form-group col-md-3">
                        {!! Form::label('cantidad_fumador_id','Cantidas/días') !!}
                        {!! Form::select('cantidad_fumador_id',$combos['cantidad_fumadores'], old('cantidad_fumador_id'),['class' => 'form-control','style' => 'width: 100%']) !!}
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group col-md-2">
                        {!! Form::label('bebedor','Bebedor') !!}
                        {!! Form::select('bebedor',[
                            'No' => 'No',
                            'Si' => 'Si',
                            'Exbebedor' => 'Exbebedor'
                        ], old('bebedor'),['class' => 'form-control','style' => 'width: 100%']) !!}
                    </div>
                 
                    <div class="form-group col-md-3">
                        {!! Form::label('tiempo_licor_id','Tiempo') !!}
                        {!! Form::select('tiempo_licor_id',$combos['tiempo_licores'], old('tiempo_licor_id'),['class' => 'form-control','style' => 'width: 100%']) !!}
                    </div>
                  
                    <div class="form-group col-md-4">
                        {!! Form::label('tipolicor','Tipo de Licor') !!}
                        {!! Form::text('tipolicor',old('tipolicor'),['placeholder' => '','class'=>'form-control']) !!}
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group col-md-3">
                        {!! Form::label('medicamento','Medicamento Psicoactivo') !!}
                        {!! Form::select('medicamento',[
                            'No' => 'No',
                            'Si' => 'Si',
                        ], old('medicamento'),['class' => 'form-control','style' => 'width: 100%']) !!}
                    </div>

                    <div class="form-group col-md-3">
                        {!! Form::label('id_regularidad_medicamento','Regularidad') !!}
                        {!! Form::select('id_regularidad_medicamento',$combos['regularidad_medicamentos'], old('id_regularidad_medicamento'),['class' => 'form-control','style' => 'width: 100%']) !!}
                    </div>

                    <div class="form-group col-md-6">
                        {!! Form::label('nombremedicamento','Cual?') !!}
                        {!! Form::text('nombremedicamento',old('nombremedicamento'),['placeholder' => '','class'=>'form-control']) !!}
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

<!--****************Antecedentes Patológicos****************-->
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Antecedentes Patológicos</h3>
            <div class="box-tools pull-right">
            	<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
       		@include('flash::message')
          	<div class="row">
            	{!! Form::open(['class' => '','method' => 'POST','route' => 'especialidades.store','role' => 'form']) !!}
              	<div class="col-md-12">
                	<table id="example2" class="table table-bordered table-hover">
                  		<thead>
                    		<tr>
								<th>Enfermadad</th>
								<th>Observación</th>
								<th>Familiar</th>
								<th>Personal</th>
								<th>Quitar</th>
                    		</tr>
                  		</thead>
                  		<tbody>
                  		</tbody>
                	</table>
              	</div>
              	<div class="col-md-12">
                	<div class="box-footer">
                      	<button type="button" data-toggle="modal" href="#myAlert3" class="btn btn-primary btn-sm open-modal">Agregar Patología</button>
                  	</div>
              	</div>
            	{!! Form::close() !!}

            	<div class="col-md-12">
                    <div class="box-footer">
                        <a href="{{ route('historias.ocupacional.edit',[$paciente->id,$historia_ocupacional->id]) }}" class="btn btn-default btn-sm ">Datos del Paciente</a>

                        <a href="{{ route('historias.ocupacional.antecedentes',[$paciente->id,$historia_ocupacional->id]) }}" class="btn btn-default btn-sm ">Antecedentes Ocupacionales</a>

                        <a href="{{ route('historias.ocupacional.patologias',[$paciente->id,$historia_ocupacional->id]) }}" class="btn btn-default btn-sm ">Antecedentes Patológicos</a>

                        <a href="{{ route('historias.ocupacional.actual',[$paciente->id,$historia_ocupacional->id]) }}" class="btn btn-default btn-sm ">Información Ocupacional Actual</a>

                        <a href="{{ route('historias.ocupacional.fisicos',[$paciente->id,$historia_ocupacional->id]) }}" class="btn btn-default btn-sm ">Examen Físico</a>

                        <a href="{{ route('historias.ocupacional.examenes',[$paciente->id,$historia_ocupacional->id]) }}" class="btn btn-default btn-sm ">Examenes de laboratorio</a>

                        <a href="{{ route('historias.ocupacional.diagnosticos',[$paciente->id,$historia_ocupacional->id]) }}" class="btn btn-default btn-sm ">Diagnostico médico</a>

                    </div>
                </div>
            </div>
            <!-- /.col -->
      	</div>
          <!-- /.row -->
    </div>
        <!-- /.box-body -->

	<div class="modal fade"  id="myAlert3" tabindex="-1">
      	<div class="modal-dialog">
            <div class="modal-content">
              	<div class="modal-header">
               		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  	<span aria-hidden="true">&times;</span></button>
                	<h4 class="modal-title">Agregando Patología</h4>
              	</div>
              	<div class="modal-body">
                   	<div class="col-md-12">
                        <div class="form-group col-md-12">
							{!! Form::label('enfermedad_id','Enfermedad') !!}
							{!! Form::select('enfermedad_id',$combos['enfermedades'], old('enfermedad_id'),['class' => 'form-control','style' => 'width: 100%']) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-2">
                            {!! Form::label('familiar','Familiar') !!}
                        </div>
                        <div class="form-group col-md-1">
                            {!! Form::checkbox('familiar', null,false,['class'=>'form-control flat-red']) !!}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group col-md-2">
                            {!! Form::label('personal','Personal') !!}
                        </div>
                         <div class="form-group col-md-1">
                            {!! Form::checkbox('personal', null,false,['class'=>'form-control flat-red']) !!}
                         </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group col-md-12">
							{!! Form::label('observacion','Observación') !!}
							{!! Form::text('observacion',old('observacion'),['placeholder' => '','class'=>'form-control']) !!}
                        </div>
                    </div>
              	</div>
              	<div class="modal-footer">
	                <button type="button" class="btn btn-default" data-dismiss="modal">Agregar</button>
	                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancelar</button>
              	</div>
            </div>
      	</div>
    </div>

	
 @endsection   