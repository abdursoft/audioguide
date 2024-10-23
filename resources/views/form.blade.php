<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
        <input type="text" name="theme" placeholder="">
        <input type="text" name="remark" placeholder="">
        <input type="text" name="questions[]" placeholder="Question">
        <input type="text" name="answers[]" placeholder="Answer">
        <div class="faqs">

        </div>
        <button type="button" onclick="addFaq()">Add Faq</button>
        <button type="submit">Submit Data</button>
    </form>
    <script src="https://rent.abdursoft.com/axios/axios.js"></script>
    <script>
        document.getElementById('myForm').addEventListener('submit', (e) => {
            e.preventDefault();
            var myForm = document.getElementById('myForm');
            axios.post('/api/v1/admin/audio-guide', new FormData(myForm), {
                withCredentials: true,
                headers:{
                    Authorization:"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiIxMjcuMC4wLjEiLCJyb2xlIjoiYWRtaW4iLCJpYXQiOjE3Mjk2OTEzMDMsIm5iZiI6MTcyOTY5MTMxMywiZXhwIjoxNzI5Njk0OTAzLCJpZCI6MSwiZW1haWwiOiJhZG1pbkBnbWFpbC5jb20ifQ.ird7Jo8bj-RhS6WSK89sn0yZ7Oqgi3jjCIs8wYUHsOg"
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

        let number = 0;
        function addFaq(){
            number++;
            $(".faqs").append(`
                <div id="faq_${number}">
                    <input type="text" name="questions[]" placeholder="Question">
                    <input type="text" name="answers[]" placeholder="Answer">
                    <button type="button" onclick="removeFaq('faq_${number}')">Remove Faq</button>
                </div>
            `);
        }

        function removeFaq(element){
            $("#"+element).remove();
        }
    </script>
</body>

</html>
