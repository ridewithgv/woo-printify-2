<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <!-- Styles -->
        
    </head>
    <body class="antialiased">
        <section class="">
            <!-- Jumbotron -->
            <div class="px-4 py-5 px-md-5 text-center text-lg-start" style="background-color: hsl(0, 0%, 96%)">
              <div class="container d-flex align-items-center justify-content-center">
                
          
                  <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="card">
                      <div class="card-header">
                        <h4 class="">
                            Login
                          </h4>
                      </div>
                      <div class="card-body py-5 px-md-5">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="post" action="{{route('login.attempt')}}">
                            @csrf
                            
                          <!-- Email input -->
                          <div class="form-outline mb-4">
                              <label class="form-label"  for="form3Example3">Email address</label>
                            <input type="email" name="email" id="form3Example3" class="form-control" />
                          </div>
          
                          <!-- Password input -->
                          <div class="form-outline mb-4">
                              <label class="form-label"  for="form3Example4">Password</label>
                            <input type="password" name="password" id="form3Example4" class="form-control" />
                          </div>
          
                          <!-- Submit button -->
                          <button type="submit" class="btn btn-primary btn-block mb-4">
                            Sign In
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Jumbotron -->
          </section>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>
