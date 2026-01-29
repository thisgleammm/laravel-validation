<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
</head>
<body>
    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="/form" method="POST">
        @csrf
        <label>Username : <input type="text" name="username"></label><br>
        <label>Password : <input type="password" name="password"></label>
        <input type="submit" value="login">
    </form>
</body>
</html>