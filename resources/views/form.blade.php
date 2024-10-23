<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form</title>
</head>

<body>
    <form id="myForm" method="post"
        enctype="multipart/form-data">
        <input type="text" name="title" placeholder="title">
        <input type="text" name="price" placeholder="price">
        <input type="text" name="description" placeholder="description">
        <input type="text" name="call_to_action" placeholder="call">
        <input type="text" name="status" placeholder="active">
        <input type="file" name="file" placeholder="">
        <input type="file" name="cover" placeholder="">
        <button type="submit">Submit Data</button>
    </form>
    <script src="https://rent.abdursoft.com/axios/axios.js"></script>
    <script>
        document.getElementById('myForm').addEventListener('submit', (e) => {
            e.preventDefault();
            var myForm = document.getElementById('myForm');
            axios.post('https://audio.abdursoft.com/api/v1/admin/audio-guide', new FormData(myForm), {
                withCredentials: true,
                headers:{
                    Authorization:"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhdWRpby5hYmR1cnNvZnQuY29tIiwicm9sZSI6ImFkbWluIiwiaWF0IjoxNzI5NjEzNTcyLCJuYmYiOjE3Mjk2MTM1ODIsImV4cCI6MTcyOTYxNzE3MiwiaWQiOjEsImVtYWlsIjoiYWRtaW5AZ21haWwuY29tIn0.5n1_uVb-f3Wslrfwew6XW9sPNr405qFyqRegaukkDro"
                }
            } )
                .then(async (response) => {
                    if (response.status === 201) {
                        console.log(response.data.message);
                    } else {
                        console.log(response.data.message);
                    }
                }).catch((error) => {
                    console.log(error.response.data.message);
                })
        })
    </script>
</body>

</html>
