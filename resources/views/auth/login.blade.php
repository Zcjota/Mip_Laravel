@extends('auth.contenido')

@section('login')
<div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card-group mb-0">
          <div class="card p-4">
          <form class="form-horizontal was-validated" method="POST" action="{{ route('login')}}">
          {{ csrf_field() }}
              <div class="card-body">
              <h1>Acceder</h1>
              <p class="text-muted">Control de acceso al sistema</p>
              <div class="form-group mb-3{{$errors->has('usuario' ? 'is-invalid' : '')}}">
                <span class="input-group-addon"><i class="icon-user"></i></span>
                <input type="text" value="{{old('usuario')}}" name="usuario" id="usuario" class="form-control" placeholder="Usuario">
                {!!$errors->first('usuario','<span class="invalid-feedback">:message</span>')!!}
              </div>
              <div class="form-group mb-4{{$errors->has('password' ? 'is-invalid' : '')}}">
                <span class="input-group-addon"><i class="icon-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                {!!$errors->first('password','<span class="invalid-feedback">:message</span>')!!}
              </div>
              <div class="row">
                <div class="col-6">
                  <button type="submit" class="btn btn-success px-4">Acceder</button>
                </div>
              </div>
            </div>
          </form>
          </div>

          <div class="card text-white py-5 d-md-down-none" style="width:44%;background-color: #0f4b81;" älign="center">
            <div class="card-body text-center">
              <div>
              <h2>Sistema MIP</h2><br>
                <img src="img/logologin.png" style="width:250px;height:150px;">
                <br><br><br><br>
                <h5>Manejo Integrado de Plagas</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection
