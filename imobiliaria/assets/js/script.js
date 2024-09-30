const _menuItensCt = _qs('.menu-itens-container');

function openMenuMobile() {
    _menuItensCt.classList.add('menu-opened')
}

function closeMenuMobile() {
    _menuItensCt.classList.remove('menu-opened')
}

_qs('.menu-mobile-icon').addEventListener('click', function () {
    openMenuMobile();
});

_qs('.menu-itens-container .close-menu').addEventListener('click', function () {
    closeMenuMobile();
});
