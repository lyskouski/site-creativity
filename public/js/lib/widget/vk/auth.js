VK.init({apiId: 5223417});

VK.Widgets.Auth("vk_auth", {width: "200px", onAuth: function (data) {
    Vaviorka.registry.trigger('Request/Pjax', 'submit', [
        null, 
        '/' + Vaviorka.ui.getLanguage() + '/log/auth/vk.json',
        data // uid, first_name, last_name, photo, photo_rec, hash. 
    ]);
}});
