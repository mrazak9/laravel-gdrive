<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Files and Folders</title>
</head>

<body>
    <h1>Files and Folders</h1>
    @if ($folderId != env('GOOGLE_DRIVE_FOLDER_ID'))
        <!-- Tampilkan tombol "Back" jika bukan di folder utama -->
        <a href="{{ route('google-drive.folder', $parentFolderId) }}">Back</a>
    @endif
    <ul>
        @foreach ($filesAndFolders as $item)
            <li>
                @if ($item->mimeType == 'application/vnd.google-apps.folder')
                    <strong>Folder:</strong>
                    <a href="{{ route('google-drive.folder', $item->id) }}">{{ $item->name }}</a>
                    <button
                        onclick="copyToClipboard('{{ 'https://drive.google.com/drive/folders/' . $item->id . '/?usp=drive_link' }}')">Copy
                        Link</button>
                @else
                    <strong>File:</strong> {{ $item->name }}
                    <form action="{{ route('google-drive.download', $item->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit">Download</button>
                    </form>
                    <a href="{{ route('google-drive.preview', $item->id) }}" target="_blank">Preview</a>
                    <button
                        onclick="copyToClipboard('{{ 'https://drive.google.com/file/d/' . $item->id . '/view?usp=sharing' }}')">Copy
                        Link</button>

                    <form action="{{ route('google-drive.delete', $item->id) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>

    <script>
        function copyToClipboard(text) {
            var tempInput = document.createElement("input");
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            alert("Link copied to clipboard: " + text);
        }
    </script>
</body>

</html>
