<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Hellow ,Sign Up  </title>

    <!-- Font Icon -->
    <link rel="stylesheet" href="{{asset('auth/fonts/material-icon/css/material-design-iconic-font.min.css')}}">

    <!-- Main css -->
    <link rel="stylesheet" href="{{ asset('auth/css/style.css')}}">
</head>
<body>

    <div class="main">

        <!-- Sign up form -->
        <section class="signup">
            <div class="container">
                <div class="signup-content">
                    <div class="signup-form">
                        <h2 class="form-title">Sign up</h2>
                            <form method="POST" class="register-form" id="register-form" action="{{ route('register') }}">
                                @csrf
                        
                            <div class="form-group">
                                <label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="name" value="{{ old('name') }}" id="name" placeholder="Your Name"/>
                            </div>
                            @error('name')
                            <span style="color: red; font-size: 14px;">{{ $message }}</span>
                            @enderror
                            <div class="form-group">
                                <label for="email"><i class="zmdi zmdi-email"></i></label>
                                <input type="email" name="email"   value="{{ old('email') }}" id="email" placeholder="Your Email"/>
                            </div>@error('email')
                            <span style="color: red; font-size: 14px;">{{ $message }}</span>
                            @enderror
                            <div class="form-group">
                                <label for="pass"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="pass" placeholder="Password"/>
                            </div>@error('password')
                            <span style="color: red; font-size: 14px;">{{ $message }}</span>
                            @enderror
                            <div class="form-group">
                                <label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
                                <input type="password" name="password_confirmation" id="re_pass" placeholder="Repeat your password"/>
                            </div>@error('password_confirmation')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                           
                            <div class="form-group">
                                <select id="role" name="role" required 
                                style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; background-color: #fff; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="customer">Customer</option>
                                    <option value="pharmacist">Pharmacist</option>
                                    <option value="administrator">Administrator</option>
                                    <option value="technician">Technician</option>
                                    <option value="inventory_manager">Inventory Manager</option>
                                </select>
                            </div>
                            @error('role')
                            <span style="color: red; font-size: 14px;">{{ $message }}</span>
                            @enderror
                          
                            
                            <div class="form-group form-button">
                                <input type="submit" name="register" id="signup" class="form-submit" value="Register"/>
                            </div>
                        </form>
                    </div>
                    <div class="signup-image">
                        <figure><img src="{{ asset('auth/images/signup-image.jpg')}}" alt="sing up image"></figure>
                        <a href="{{ route('login')}}" class="signup-image-link">I am already member</a>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- JS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->
</html>