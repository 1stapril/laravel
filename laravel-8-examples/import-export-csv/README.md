# Import and export CSV in Laravel 8 and MySql

[Originally published at](https://medium.com/@harendraverma21/import-and-export-csv-in-laravel-8-and-mysql-bdbec258c6d6)

![Larave 8 Import & Export CSV](https://miro.medium.com/max/2000/1*9ziQCax7ToFmD1q2DQu75w.jpeg)

- [Import and export CSV in Laravel 8 and MySql](#import-and-export-csv-in-laravel8-and-mysql)
  - [Change configuration](#change-configuration)
  - [Create a migration](#create-a-migration)
  - [Migrate tables](#migrate-tables)
  - [Create controller and model](#create-controller-and-model)
  - [Setup routes](#setup-routes)
  - [Create views](#create-views)
    - [Create layout file](#create-layout-file)
    - [Iport view](#iport-view)
      - [1. resources/views/import.blade.php](#1-resourcesviewsimportbladephp)
  - [Summary](#summary)

Hello guys, Here I am going to create a laravel application to Import and Export CSV files with laravel 8 and MySQL database. It is required to enter bulk data into the database and export data into CSV format.

To get started with Import and export first we need to create a Laravel application or you can customize your existing application. To create an app run the following command.

```bash
composer create-project --prefer-dist laravel/laravel laravel-8-import-expoert "8.*"
```

It will create a new laravel 8.* application with `laravel-8-import-export name, now we need to open it via our favorite text editor.

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
php artisan make:migration create_subscribers_table --create=subscribers
```

it will create a file with the name `*_create_subscribers_table.php in` your `database/migration folder. Now open the file and change the code according to the file below to add some fields to your application.

```php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->string('email',255);
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
        Schema::dropIfExists('subscribers');
    }
}

```
## Migrate tables

Now your migration is ready to create a subscribers table with name and email columns. To create a table need to run migrate command.

```bash 

php artisan migrate

```

## Create controller and model

To create a model and controller please run the following command:
```bash
php artisan make:controller SubscriberCtrl --resource --model=Subscriber
```

Now open the `/app/Models/Subscriber.php` file and paste the following code in that file.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;
    protected $fillable = ['email','name'];
}

```

After that open controller file `app/Http/Controllers/SubscriberCtrl.php` and paste the following `

```php

<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberCtrl extends Controller
{
    public function import(Request $request){
        $data = [];
        $data['records'] = Subscriber::latest()->paginate(20);
        return view('import')->with($data);
    }

    public function import_post(Request $request){
        $request->validate([
            'csv' => 'required',
        ]);
  
        $input = $request->all();
  
        if ($csv = $request->file('csv')) {
            $csvDestinationPath = 'uploads/';
            $postCsv = date('YmdHis') . "." . $csv->getClientOriginalExtension();
            $csv->move($csvDestinationPath, $postCsv);
            $csvPath = $csvDestinationPath.$postCsv;

            $users = $this->csvToArray($csvPath);

            foreach($users as $user){
                
                $user = ['name' => $user['Name'], 'email' => $user['Email']];
                
                Subscriber::create($user);
            }

            return redirect('import');
            
        }
    }

    public function export(){

        $list[] = ['Name','Email'];
        $records = Subscriber::select('name','email')->latest()->get();
        foreach($records as $record) {
            $list[] = [$record->name,$record->email];
        }
        $filename = 'file.csv';
        $fp = fopen($filename, 'w');

        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);

        
        @header("Content-type: application/zip");
        @header("Content-Disposition: attachment; filename=$filename");
        echo file_get_contents('file.csv');

        if(file_exists($filename)) {
            unlink($filename);
        } 

        //return redirect('import');

        
        
    }    

    public function csvToArray($filename = '', $delimiter = ','){
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }
}


```

## Setup routes

Change your `routes/web.php` file

```php

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriberCtrl;

Route::get('/', function () {
    return redirect('import');
});

Route::GET('import', [SubscriberCtrl::class, 'import'])->name('import');
Route::POST('import', [SubscriberCtrl::class, 'import_post'])->name('import.post');
Route::GET('export', [SubscriberCtrl::class, 'export'])->name('export');

```

## Create views

Now in this step, we are going to create views for our application. In the `resources/views folder.

### Create layout file

Create `layout.blade.php` in `resources/views` folder.

```php

<!DOCTYPE html>
<html>
    <head>
        <title>Laravel 8 Import & Export CSV</title>
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
        <a class="navbar-brand" href="{{ url('/import') }}">Laravel 8 Import & Export CSV</a>

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
            <a class="nav-link" href="{{ url('/import') }}">Home
                <span class="sr-only">(current)</span>
            </a>
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

### Iport view

Now we need to create a view for import and export CSV.

#### 1. resources/views/import.blade.php

```php

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
So in this laravel application, I have created an application with laravel 8 and MySQL. Which is used to perform import and export operations. I hope you like this and if have any issue please feel free to contact me.

Thank you for reading. If you want to read more posts on laravel please follow me. Thank you again.