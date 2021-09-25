# CRUD example with Image Upload in Laravel 8

[Originally published at](https://medium.com/@harendraverma21/crud-example-with-image-upload-in-laravel-8-d35cb95575f2)

![Larave 8 CRUD](https://miro.medium.com/max/2000/1*7VyVhGMQgzyYgqChmaIkmw.jpeg)

- [CRUD example with Image Upload in Laravel 8](#crud-example-with-image-upload-in-laravel8)
  - [Change configuration](#change-configuration)
  - [Create a migration](#create-a-migration)
  - [Migrate tables](#migrate-tables)
  - [Create controller and model](#create-controller-and-model)
  - [Setup routes](#setup-routes)
  - [Create views](#create-views)
    - [Create layout file](#create-layout-file)
    - [Posts view](#posts-view)
      - [1. resources/views/post/create.blade.php](#1-resourcesviewspostcreatebladephp)
      - [2. resources/views/post/edit.blade.php](#2-resourcesviewsposteditbladephp)
      - [3. resources/views/post/index.blade.php](#3-resourcesviewspostindexbladephp)
      - [4. resources/views/post/show.blade.php](#4-resourcesviewspostshowbladephp)
  - [Summary](#summary)

Hello developers, Today I am going to create a laravel application to perform crud operations (create, read, update, upload, and delete) with laravel 8 and MySQL database. I hope it may help you to understand laravel 8 and MySQL database how things work.
So let's get started by creating a Laravel application. Run the following command to create a Laravel application.

```bash
composer create-project --prefer-dist laravel/laravel laravel-8-crud-app "8.*"
```

I have created a Laravel application"8.*" with "laravel-8-crud" name, now open this project with your favorite text editor and edit the .env file to change the database configuration.

## Change configuration

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database_name
DB_USERNAME=username
DB_PASSWORD=password
```

Edit and change the credentials according to your configuration.

## Create a migration

Next, we need to create a migration to create a table in the database, to create table run the following command in your application root

```bash
php artisan make:migration create_posts_table --create=posts
```

it will create a file with the name `*_create_posts_table.php in` your `database/migration folder. Now open the file and change the code according to the file below to add some fields to your application.

```php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('body');
            $table->string('image');
            $table->softDeletes('deleted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}

```
## Migrate tables

Now your migration is ready to create a posts table with title, body, and image columns. To create a table need to run migrate command.

```bash 
php artisan migrate
```

## Create controller and model

To create a model and controller please run the following command:
```bash
php artisan make:controller PostCtrl --resource --model=Post
```

Now open the `/app/Models/Post.php` file and paste the following code in that file.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['title','body','image'];
}

```

After that open controller file `app/Http/Controllers/PostCtrl.php` and paste the following `

```php

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
class PostCtrl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = Post::latest()->paginate(5);
    
        return view('post.index',compact('records'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
   
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('post.create');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3|max:255',
            'body' => 'required|min:10|max:4096',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
  
        $input = $request->all();
  
        if ($image = $request->file('image')) {
            $imageDestinationPath = 'uploads/';
            $postImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($imageDestinationPath, $postImage);
            $input['image'] = "$postImage";
        }
    
        Post::create($input);
     
        return redirect()->route('posts.index')->with('success','Post created successfully.');
    }
     
    /**
     * Display the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('post.show',compact('post'));
    }
     
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('post.edit',compact('post'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|min:3|max:255',
            'body' => 'required|min:10|max:4096'
        ]);
  
        $input = $request->all();
  
        if ($image = $request->file('image')) {
            $imageDestinationPath = 'uploads/';
            $postImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($imageDestinationPath, $postImage);
            $input['image'] = "$postImage";
        }else{
            unset($input['image']);
        }
          
        $post->update($input);
    
        return redirect()->route('posts.index')->with('success','Post updated successfully');
    }
  
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index')
        ->with('success','Post deleted successfully');
    }
}

```

## Setup routes

Change your `routes/web.php` file

```php

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostCtrl;

Route::get('/', function () {
    return redirect('/posts');
});
Route::resource('posts', PostCtrl::class);

```

## Create views

Now in this step, we are going to create views for our application. In the `resources/views folder.

### Create layout file

Create `layout.blade.php` in `resources/views` folder.

```php

<!DOCTYPE html>
<html>
    <head>
        <title>Laravel 8 CRUD with Image Upload</title>
        <!-- Google Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
        <!-- Bootstrap core CSS -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css" rel="stylesheet">
        <!-- Material Design Bootstrap -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.19.1/css/mdb.min.css" rel="stylesheet">
    </head>
    <body>


        <!--Navbar-->
        <nav class="navbar navbar-expand-lg navbar-dark primary-color">

        <!-- Navbar brand -->
        <a class="navbar-brand" href="#">Laravel 8 CRUD</a>

        <!-- Collapse button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#basicExampleNav"
        aria-controls="basicExampleNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible content -->
        <div class="collapse navbar-collapse" id="basicExampleNav">

        <!-- Links -->
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
            <a class="nav-link" href="{{ url('/posts') }}">Home
                <span class="sr-only">(current)</span>
            </a>
            </li>

            <!-- Dropdown -->
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">Post</a>
            <div class="dropdown-menu dropdown-primary" aria-labelledby="navbarDropdownMenuLink">
                <a class="dropdown-item" href="{{ url('/posts') }}">List All</a>
                <a class="dropdown-item" href="{{ url('/posts/create') }}">Add New</a>
            </div>
            </li>

        </ul>
        <!-- Links -->

        </div>
        <!-- Collapsible content -->

        </nav>
        <!--/.Navbar-->
    
        <div class="container">
            @yield('content')
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>
    </body>
</html>

```

### Posts view

Now we need to create 4 files for crud operation.

#### 1. resources/views/post/create.blade.php

```php
@extends('layout')
  
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Add New Post</h2>
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
     
<form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
     <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Title:</strong>
                <input type="text" name="title" class="form-control" placeholder="Post Title" value="{{ old('title') }}">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Body:</strong>
                <textarea class="form-control" name="body" placeholder="Post Body">{{ old('body') }}</textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Image:</strong>
                <input type="file" name="image" class="form-control" placeholder="Post Image">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Add Post</button>
        </div>
    </div>
     
</form>
@endsection

```
#### 2. resources/views/post/edit.blade.php

```php

@extends('layout')
     
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit Post</h2>
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
    
    <form action="{{ route('posts.update',$post->id) }}" method="POST" enctype="multipart/form-data"> 
        @csrf
        @method('PUT')
     
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Title:</strong>
                    <input type="text" name="title" value="{{ $post->title }}" class="form-control" placeholder="Post Title">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Body:</strong>
                    <textarea class="form-control" name="body" placeholder="Post Body">{{ $post->body }}</textarea>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Image:</strong>
                    <input type="file" name="image" class="form-control" placeholder="Post Image">
                    <img src="/uploads/{{ $post->image }}" width="200px">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
              <button type="submit" class="btn btn-primary">Update Post</button>
            </div>
        </div>
     
    </form>
@endsection

```

#### 3. resources/views/post/index.blade.php

```php

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

```

#### 4. resources/views/post/show.blade.php

```php

@extends('layout')
   
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2> Show Post</h2>
            </div>
            
        </div>
    </div>
     
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Title:</strong>
                {{ $post->title }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Body:</strong>
                {{ $post->body }}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Image:</strong>
                <img src="/uploads/{{ $post->image }}" width="300px">
            </div>
        </div>
    </div>
@endsection

```

Now, your application let's serve the application.

```bash 
php artisan serve
```

Now you have to open bellow URL with your browser:

```
http://localhost:8000

```
## Summary
So in this laravel application, I have created an application with laravel 8 and MySQL. Which is used to perform crud operations. I hope it is self-explanatory and if have any issue please feel free to contact me.

Thank you for reading. If you want to read more posts on laravel please follow me. Thank you again.