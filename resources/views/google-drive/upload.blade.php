<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File to Google Drive</title>
</head>

<body>
    <h1>Upload File to Google Drive</h1>
    <form action="{{ route('google-drive.upload') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file">
        <button type="submit">Upload</button>
    </form>
</body>

</html>
