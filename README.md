# PHP_Laravel12_Update_User_Profile_Using_API

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/Laravel_Sanctum-API_Auth-0D6EFD?style=for-the-badge&logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/REST_API-JSON-28A745?style=for-the-badge&logo=postman&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Postman-API_Testing-FF6C37?style=for-the-badge&logo=postman&logoColor=white" />
  <img src="https://img.shields.io/badge/Auth-Token_Based-6F42C1?style=for-the-badge&logo=securityscorecard&logoColor=white" />
</p>


##  Overview

This project demonstrates how to build a **secure User Authentication and Profile Update API** using **Laravel 12** and **Laravel Sanctum**.

The API follows modern REST standards and is suitable for **web apps, mobile apps, and third-party clients**. It focuses on a **clean and minimal user profile update flow** (name & email only).

---

##  Features

*  User Registration API
*  User Login API
*  Token-based Authentication using Laravel Sanctum
*  Protected Profile Update API
*  Secure Password Hashing
*  Validation & Error Handling
*  Postman-ready API testing
*  Clean, minimal & production-ready structure

---

##  Folder Structure (Important Files)

```
app/
 ├── Http/
 │   └── Controllers/
 │       └── Api/
 │           ├── AuthController.php
 │           └── UserProfileController.php
 ├── Models/
 │   └── User.php

routes/
 └── api.php

config/
 └── auth.php

.env
```

---

---

##  System Requirements

Make sure the following are installed on your system:

* PHP **8.2+**
* Composer (latest)
* MySQL / MariaDB
* Node.js (optional)
* Postman (for API testing)

### Check Versions

```bash
php -v

composer -V
```

---

##  Step 1: Install Laravel 12

Create a new Laravel 12 project:

```bash
composer create-project laravel/laravel laravel12-api
```

Move into project directory and start the server:

```bash
cd laravel12-api

php artisan serve
```

Application URL:

```
http://127.0.0.1:8000
```

---

##  Step 2: Database Configuration

Open the `.env` file and update database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

Run default migrations:

```bash
php artisan migrate
```

---

##  Step 3: Install Laravel Sanctum

Install Sanctum package:

```bash
composer require laravel/sanctum
```

Publish Sanctum configuration:

```bash
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider"
```

Run migrations:

```bash
php artisan migrate
```

---

##  Step 4: Configure Authentication Guard

Open `config/auth.php` and add the **sanctum guard**:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'sanctum' => [
        'driver' => 'sanctum',
        'provider' => 'users',
    ],
],
```

Clear application cache:

```bash
php artisan optimize:clear
```

---

##  Step 5: Update User Model

Open `app/Models/User.php`:

```php
<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

---

##  Step 6: Create Controllers

### Auth Controller

Create controller:

```bash
php artisan make:controller Api/AuthController
```

`AuthController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'token' => $user->createToken('api')->plainTextToken
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'token' => $user->createToken('api')->plainTextToken
        ]);
    }
}
```

---

### Profile Controller

Create controller:

```bash
php artisan make:controller Api/UserProfileController
```

`UserProfileController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }
}
```

---

##  Step 7: API Routes

Open `routes/api.php`:

```php
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserProfileController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/profile', [UserProfileController::class, 'update']);
});
```

---

##  Step 8: Testing with Postman

### STEP 8.1: Register User API

**Method:** POST
**URL:**

```
http://127.0.0.1:8000/api/register
```

**Headers:**

| Key    | Value            |
| ------ | ---------------- |
| Accept | application/json |

**Body (raw → JSON):**

```json
{
  "name": "Hardik",
  "email": "hardik@gmail.com",
  "password": "123456"
}
```

**Success Response:**

```json
{
  "token": "8|xxxxxxxxxxxxxxxxxxxxxxxx"
}
```
<img width="1794" height="616" alt="Screenshot 2026-01-06 171649" src="https://github.com/user-attachments/assets/7b33e2ca-727e-4e86-a360-4c13d88205a6" />

---

### STEP 8.2: Login User API

**Method:** POST
**URL:**

```
http://127.0.0.1:8000/api/login
```

**Headers:**

| Key    | Value            |
| ------ | ---------------- |
| Accept | application/json |

**Body (raw → JSON):**

```json
{
  "email": "john@gmail.com",
  "password": "123456"
}

```

**Success Response:**

```json
{
  "token": "9|xxxxxxxxxxxxxxxxxxxxxxxx"
}
```
<img width="1801" height="586" alt="Screenshot 2026-01-06 175635" src="https://github.com/user-attachments/assets/f825f67d-27be-4b03-966e-8e9abe00e6f1" />

---

### STEP 8.3: Update Profile API (Protected)

**Method:** PUT
**URL:**

```
http://127.0.0.1:8000/api/profile
```

**Headers:**

| Key           | Value             |
| ------------- | ----------------- |
| Accept        | application/json  |
| Authorization | Bearer YOUR_TOKEN |

**Example:**

```
Authorization: Bearer 9|xxxxxxxxxxxxxxxxxxxxxxxx
```

**Body (raw → JSON):**

```json
{
  "name": "John",
  "email": "john@gmail.com"
}
```

**Success Response:**

```json
{
  "status": true,
  "message": "Profile updated successfully",
  "data": {
    "name": "John",
    "email": "john@gmail.com"
  }
}
```
<img width="1795" height="648" alt="Screenshot 2026-01-06 173549" src="https://github.com/user-attachments/assets/7a596cc7-7240-437b-a648-47d3845d4a2a" />

---

##  Common Errors & Fixes

### 401 Unauthenticated

**Reason:**

* Token missing
* Token expired
* Token wrapped in quotes

**Fix:**

```
Authorization: Bearer YOUR_TOKEN
```

---

### 422 Validation Error

**Reason:**

* Invalid input

**Fix:**

* Check JSON body
* Ensure email is unique

---

##  Important Notes

* Always use **raw JSON** (not form-data)
* Always add **Accept: application/json**
* Always login again after clearing cache

---

