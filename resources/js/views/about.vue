<template>
    <div>
        <h1 class="text-3xl font-bold text-center mb-4">About the project</h1>
        <p>
            Это учебный проект. Он создан исключительно для изучения веб-разработки. Весь контент подготовлен при помощи генераторов случайных тектов и изображений.
        </p>
        <h3 class="text-xl font-bold text-center my-3">Описание функционала проекта</h3>
        <ol class="list-disc">
            <li>
                <span class="font-bold">Для не авторизованных пользователей и пользователей, не иеющих роли "администратор", доступны:</span>
                <ol class="list-decimal">
                    <li>форма для авторизации</li>
                    <li>форма для регистрации</li>
                    <li>просмотр списка постов</li>
                    <li>просмотр страницы поста</li>
                    <li>просмотр статичной страницы</li>
                    <li>просмотр своего профиля</li>
                </ol>
            </li>
            <li>
                <span class="font-bold">Для авторизованных пользователей, не иеющих роли "администратор", доступны также:</span>
                <ol class="list-decimal">
                    <li>просмотр своего профиля</li>
                </ol>
            </li>
            <li>
                <span class="font-bold">Для пользователей, иеющих роль "администратор", доступны:</span>
                <ol class="list-decimal">
                    <li>CRUD для постов</li>
                    <li>CRUD для ролей</li>
                    <li>удаление пользователей и назначение им ролей</li>
                </ol>
            </li>
            <li>
                <span class="font-bold">Телеграм-бот:</span>
                <ol class="list-decimal">
                    <li>Генерация 1 случайного поста</li>
                    <li>Отправка в группу уведомления о публикации нового поста со ссылкой на этот пост</li>
                </ol>
            </li>
        </ol>
    </div>
</template>

<script>
export default {
    name: 'about',
}
</script>

<style scoped>

</style>