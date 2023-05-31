<!DOCTYPE html>
<html class="wide wow-animation" lang="en">
<head>
    <title>amoCRM</title>
    <meta name="viewport" content="width=device-width height=device-height initial-scale=1.0">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="relative min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8 bg-gray-500 bg-no-repeat bg-cover relative items-center"
     style="background-color: aquamarine;">
    <div class="absolute bg-black opacity-60 inset-0 z-0"></div>
    <div class="sm:max-w-lg w-full p-10 bg-white rounded-xl z-10">
        <div class="text-center">
            <h2 class="mt-5 text-3xl font-bold text-gray-900">
                Отправка формы
            </h2>
            <p class="mt-2 text-sm text-gray-400">Отправьте форму.</p>
        </div>
        <?php if(isset($error)): ?>
            <p style="color: red;" class="text-center"><?php echo $error; ?></p>
        <?php endif; ?>
        <form class="mt-8 space-y-3" action="/store" method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 space-y-2">
                <label class="text-sm font-bold text-gray-500 tracking-wide">Имя</label>
                <input required name="name" class="text-base p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500" type="text" placeholder="Укажите, пожалуйста, ваше ФИО">
            </div>
            <div class="grid grid-cols-1 space-y-2">
                <label class="text-sm font-bold text-gray-500 tracking-wide">Почта</label>
                <input required name="email" class="text-base p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500" type="email" placeholder="Укажите, пожалуйста, вашу почту">
            </div>
            <div class="grid grid-cols-1 space-y-2">
                <label class="text-sm font-bold text-gray-500 tracking-wide">Телефон</label>
                <input required name="phone" data-phone-pattern class="text-base p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500" type="tel">
            </div>
            <div class="grid grid-cols-1 space-y-2">
                <label class="text-sm font-bold text-gray-500 tracking-wide">Цена</label>
                <input required name="price" class="text-base p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500" type="number" placeholder="Укажите, пожалуйста, цену">
            </div>
            <div>
                <button type="submit" class="my-5 w-full flex justify-center bg-blue-500 text-gray-100 p-4  rounded-full tracking-wide
                                    font-semibold  focus:outline-none focus:shadow-outline hover:bg-blue-600 shadow-lg cursor-pointer transition ease-in duration-300">
                    Отправить
                </button>
            </div>
        </form>
    </div>
</div>
<script src="/js/script.js"></script>
</body>
</html>
