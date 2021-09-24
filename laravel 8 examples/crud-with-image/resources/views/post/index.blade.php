@extends('layout')
     
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Laravel 8 CRUD with Image</h2>
            </div>
            
        </div>
    </div>
    
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
     
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Image</th>
            <th>Title</th>
            <th>Body</th>
            <th>Actions</th>
        </tr>
        @foreach ($records as $record)
        <tr>
            <td>{{ ++$i }}</td>
            <td><img src="/uploads/{{ $record->image }}" width="100px"></td>
            <td>{{ $record->title }}</td>
            <td>{{ substr($record->body,0,200) }}</td>
            <td>
                <a class="btn btn-sm btn-info" href="{{ route('posts.show',$record->id) }}">Show</a>
                <a class="btn btn-sm btn-primary" href="{{ route('posts.edit',$record->id) }}">Edit</a>
                <form action="{{ route('posts.destroy',$record->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
    
    {!! $records->links() !!}
        
@endsection