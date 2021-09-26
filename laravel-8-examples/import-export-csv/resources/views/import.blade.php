@extends('layout')
  
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Import New CSV File</h2>
        </div>
        
    </div>
</div>
     
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
     
<form action="{{ route('import.post') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
     <div class="row">
        <div class="col-xs-8 col-sm-8 col-md-8">
            <div class="form-group">
                <strong>CSV File:</strong>
                <input type="file" name="csv" placeholder="CSV File">
            </div>
        </div>
        <div class="col-xs-4 col-sm-4 col-md-4 text-right">
            <button type="submit" class="btn btn-sm btn-primary">Import</button>
        </div>
    </div>
     
</form>

<div class="row">
    <div class="col-12 text-right">
        <a href="{{ route('export') }}" class="btn btn-sm btn-success">Export</a>
    </div>
    <div class="col-12">
        <table class="table">
            <tr>
                <th>Name</th>
                <th>Email</th>
            </tr>

            @foreach($records as $record)
                <tr>
                    <td>
                        {{ $record->name }}
                    </td>
                    <td>
                        {{ $record->email }}
                    </td>
                </tr>
            @endforeach

        </table>
    </div>
</div>
@endsection