<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Cancel</title>
    <!-- Include a required theme -->
    <link rel="stylesheet" href="{{asset('assets/css/sweet.css')}}">
    <script src="{{asset('assets/js/sweet.js')}}"></script>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #333;
            color: #fff;
            width: 100vw;
            min-height: 100vh;
        }
    </style>
</head>

<body>
    <h2>Your payment has been cancled or incompleted</h2>
    <script>
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Your payment has been cancled or incompleted",
            footer: '<a href="#">Why do I have this issue?</a>',
            confirmButtonText: "Go to shop"
        }).then(()=> {
            window.location.href="";
        });
    </script>
</body>

</html>
