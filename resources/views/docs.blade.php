<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @vite('resources/css/app.css')

    <title>Documentation</title>
</head>
<body class="bg-gray-100">

<div x-data="pageDocs" :class="showPopup ? '' : ''">
    <template x-if="!showPopup">
        <div class="mt-[30vh] mx-auto flex text-center justify-center">
            <button @click="openPopup('{{ url("/api/docs") }}')" class="btn btn-redoc btn-lg mx-5">ReDoc</button>
            <button @click="openPopup('{{ url("/api/documentation") }}')" class="btn btn-swagger btn-lg mx-5">Swagger</button>
        </div>
    </template>

    <template x-if="showPopup">
        <div class="mx-auto block absolute top-[25vh] left-[30%] right-[30%] bg-white border-4 border-solid border-blue-500 rounded-lg p-[30px]">
            <div class="flex flex-col">
                <div class="">
                    Please, enter your access key:
                </div>
                <div class="my-4">
                    <input type="text" class="border-2 border-solid border-blue-700 rounded-lg w-full" x-ref="inviteCode">
                </div>
            </div>
            <button class="btn btn-primary" @click="checkAccessCode()">Enter</button>
{{--            <button class="btn btn-primary" @click="closeModel()">Close</button>--}}
        </div>
    </template>
</div>

@vite('resources/js/pages/documentation.js')
</body>
</html>
