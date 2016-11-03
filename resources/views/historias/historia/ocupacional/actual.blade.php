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

<!--****************Información Ocupacional Actual****************-->
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Información Ocupacional Actual</h3>
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
                    <div class="form-group col-md-4">
                        {!! Form::label('cargoactual','Cargo Actual') !!}
                        {!! Form::text('cargoactual',old('cargoactual'),['placeholder' => '','class'=>'form-control']) !!}
                    </div>

                    <div class="form-group col-md-4">
                        {!! Form::label('turno_id','Tipo de Examen') !!}
                        {!! Form::select('turno_id',$combos['turnos'], old('turno_id'),['class' => 'form-control','style' => 'width: 100%']) !!}
                    </div>

                    <div class="form-group col-md-4">
                        {!! Form::label('actividad_id','Actividad') !!}
                        {!! Form::select('actividad_id',$combos['actividades'], old('actividad_id'),['class' => 'form-control','style' => 'width: 100%']) !!}
                    </div>
                     <!-- /.form-group -->
                </div>

                <div class="col-md-12">
                    <table id="example2" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Tipo de Factor</th>
                                <th>Descripción</th>
                                <th>Tiempo de Exposición</th>
                                <th>Medidas de Control</th>
                                <th>Quitar</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">
                    <div class="box-footer">
                        <button type="button" data-toggle="modal" href="#myAlert2" class="btn btn-default btn-sm open-modal">Agregar Factor de Riesgo</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade"  id="myAlert2" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Agregando Factores de Riesgos</h4>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            {!! Form::label('factor_riesgo_id','Tipo de Riesgo') !!}
                            {!! Form::select('factor_riesgo_id',$combos['factor_riesgos'], old('factor_riesgo_id'),['class' => 'form-control','style' => 'width: 100%']) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-md-12">
                            {!! Form::label('otro','Otro tipo de Riesgo') !!}
                            {!! Form::text('otro',old('otro'),['placeholder' => '','class'=>'form-control','disabled'=>'form-disabled']) !!}
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="form-group col-md-6">
                            {!! Form::label('tiempoexposicion','Tiempo de Exposición') !!}
                            {!! Form::text('tiempoexposicion',old('tiempoexposicion'),['placeholder' => '','class'=>'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="form-group col-md-12">
                            {!! Form::label('medidacontrol','Medidas de Control') !!}
                            {!! Form::text('medidacontrol',old('medidacontrol'),['placeholder' => '','class'=>'form-control']) !!}
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