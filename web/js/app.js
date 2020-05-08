let mensajes = 0;
let solicitudes = 0;
let seguidores;
let songs = [];
let actualSong = 0;
let playlist = [];
let offset = 10;
let interval;
let firstTime = true;

const PERFIL = 'perfil';
const BANNER = 'banner';

checkTheme();

setInterval(() => {
    $('.alert-box .hide').remove();
}, 120000);

$('.left-animation').animate({
    marginLeft: 0
}, 1200);

$('.opacity-animation').animate({
    opacity: 1
}, 1000);

$(window).on('pjax:start', function (){
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
    setTimeout(function () {
        getFollowersData()
        checkTheme();
        if (solicitudes > 0) {
            $('.notifications-number').html(solicitudes);
        }
        if ($('#chat-page').length) {
            getStatusFromUsers();
        }
        $(".owl-carousel-index").owlCarousel({
            loop: true,
            autoplay:true,
            autoplayTimeout:4000,
            items : 1
        });
    }, 500);
});

function readURL(input, target) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function(e) {
            if (target == PERFIL) {
                $('.profile-img').attr('src', e.target.result);
                $('.profile-img').css('width', '150px');
            } else {
                $('.user-search-banner').attr('src', e.target.result);
                $('.user-search-banner').css('width', '1110px');
            }
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$('.file-input').on('change', function ev(e) {
    readURL(this, PERFIL);
});

$('.file-input-banner').on('change', function ev(e) {
    readURL(this, BANNER);
});

$('body').on('show.bs.modal', '.modal.chat', function (e) {
    let id = $(this).attr('id').split('-')[1];
    updateChatHistory(id);
    interval = setInterval(function(){
        updateChatHistory(id);
    }, 5000);
});

$('body').on('hide.bs.modal', '.modal', function (e) {
    clearInterval(interval);
});

function checkTheme() {
    if ($('#darkSwitch').length) {
        initTheme();
    }
}

$('body').on('change', '#darkSwitch', function ev(e) {
    resetTheme();
});

function initTheme() {
    const darkThemeSelected = localStorage.getItem('darkSwitch') !== null && localStorage.getItem('darkSwitch') === 'dark';
    $('#darkSwitch')[0].checked = darkThemeSelected;
    darkThemeSelected ? $('body').attr('data-theme', 'dark') : $('body').removeAttr('data-theme');
}

function resetTheme() {
    if ($('#darkSwitch')[0].checked) {
        $('body').attr('data-theme', 'dark');
        localStorage.setItem('darkSwitch', 'dark');
    } else {
        $('body').removeAttr('data-theme');
        localStorage.removeItem('darkSwitch');
    }
}


function getCookie(name) {
    let dc = document.cookie;
    let prefix = name + "=";
    let begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
        end = dc.length;
        }
    }
    return decodeURI(dc.substring(begin + prefix.length, end));
}

$('body').on('click', '.play-btn', function ev(e) {
    songs = [];
    let cancion_id = $(this).attr('id').split('-')[1];
    $.ajax({
        method: 'GET',
        url: '/index.php?r=canciones%2Fget-song-data',
        data: {
            cancion_id: cancion_id
        },
        success: function (data) {
            removeActualData();
            GreenAudioPlayer.init({
                selector: '.player',
                stopOthersOnPlay: true,
                showDownloadButton: true,
            });
            addNewData(data);
            $('.play-pause-btn').trigger('click');
            $('.audio-player').css('display', 'flex');
            if (firstTime) {
                $('.audio-player').effect('slide','slow');
                firstTime = false;
            }
            $('.player').css('display', 'flex');
            let audio = $('#audio')[0];
            audio.addEventListener('ended', () =>  {
                if (songs.length > 0) {
                    let cancion = songs.shift();
                    $('.info-song img').attr('src', cancion.url_portada);
                    $('.player audio source').attr('src', cancion.url_cancion);
                    $('.artist-info p').html(cancion.titulo);
                    $('.artist-info small').html(cancion.album);
                    audio.load();
                    $('.play-pause-btn').trigger('click');
                }
            });
        }
    });
});

$('body').on('click', '.like-btn', function ev(e) {
    let cancion_id = $(this).attr('id').split('-')[1];
    $.ajax({
        'method': 'POST',
        url: '/index.php?r=likes%2Flike&cancion_id=' + cancion_id,
        success: function (data) {
            if (data.class == 'far') {
                $('#outerlike-' + cancion_id + ' i').removeClass('fas');
                $('#outerlike-' + cancion_id + ' i').addClass('far');
                $('#like-' + cancion_id + ' i').removeClass('fas');
                $('#like-' + cancion_id + ' i').addClass('far');
            } else {
                $('#outerlike-' + cancion_id + ' i').removeClass('far');
                $('#outerlike-' + cancion_id + ' i').addClass('fas');
                $('#like-' + cancion_id + ' i').removeClass('far');
                $('#like-' + cancion_id + ' i').addClass('fas');
            }
            $('.like-btn ~ p span').html(data.likes);
        }
    });
});

$('body').on('click', '.cancion', function ev(e) {
    let cancion_id = $(this).data('target').split('-')[1];
    $('.character-count').html('0');
    $('.text-area-comment').val('');
    $('#like-' + cancion_id + ' i').removeClass('fas far');
    $.ajax({
        'method': 'POST',
        url: '/index.php?r=likes%2Fget-data&cancion_id=' + cancion_id,
        success: function (data) {
            $('#like-' + cancion_id + ' i').addClass(data.class);
            $('.like-btn ~ p span').html(data.likes);
        }
    });

    $.ajax({
        method: 'GET',
        url: '/index.php?r=canciones%2Fcomentarios',
        data: {
            cancion_id: cancion_id
        },
        success: function (data) {
            $('.row-comments').empty();
            data.comentarios.forEach(element => {
                $('.row-comments').append(`
                    <div class="col-12 mt-3" id="comentario-${element.comentario_id}">
                        <div class="row">
                            <a href="/index.php?r=usuarios%2Fperfil&id=${element.id}">
                                <img class="user-search-img" src="${element.url_image}" alt="perfil" width="50px" height="50px">
                            </a>
                            <div class="col">
                                <a href="/index.php?r=usuarios%2Fperfil&id=${element.id}">${element.login}</a>
                                <small class="ml-1 comment-time">${element.created_at}</small>
                                <p class="m-0">
                                    ${element.comentario}
                                    ${(data.owner || data.loggedUserId == element.id) ? '<button class="btn d-inline outline-transparent delete-comment-btn" data-comentario="' + element.comentario_id + '"><i class="fas fa-trash text-danger"></i></button>' : ''}
                                </p>
                            </div>
                        </div>
                    </div>
                `);
            });
        }
    });

});

$('body').on('click', '.playlist-btn', function ev(e) {
    let usuario_id = $(this).data('user');
    let cancion_id = $(this).data('song');
    $.ajax({
        method: 'GET',
        url: '/index.php?r=usuarios%2Fget-playlists',
        data: {
            usuario_id: usuario_id,
            cancion_id: cancion_id
        },
        success: function (data) {
            $('.row-playlists').html('');
            data.playlists.forEach(element => {
                $('.row-playlists').append(`
                    <div class="col-4">
                        <img src="${data.playlistsPortadas[element.id]}" class="img-fluid">
                        <h5 class="d-inline-block">${element.titulo}</h5>
                        <button data-song="${cancion_id}" data-playlist="${element.id}" class="ml-auto action-btn outline-transparent add-playlist-btn"><i class="far fa-plus-square"></i></button>
                    </div>
                `);
            });
            $('.add-playlist-btn').on('click', function ev(e) {
                let cancion_id = $(this).data('song');
                let playlist_id = $(this).data('playlist');
                $.ajax({
                    method: 'POST',
                    url: '/index.php?r=canciones-playlist%2Fagregar',
                    data: {
                        cancion_id: cancion_id,
                        playlist_id: playlist_id,
                    },
                    success: function (data) {
                        $('.alert-box').prepend(`
                            <div class="toast mb-2" data-delay="5000">
                                <div class="toast-header">
                                    <strong class="mr-auto">MUS!C NOW</strong>
                                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="toast-body">
                                    <span class="font-weight-bold">${data.cancion}</span>${' ' + data.message + ' '}<span class="font-weight-bold">${data.playlist}</span>!
                                </div>
                            </div>
                        `);
                        $('.toast').not('.hide').toast('show');
                    }
                });
            });
        }
    });
});

$('body').on('click', '.comment-btn', function ev(e) {
    let cancion_id = $(this).attr('id').split('-')[1];
    let comentario = $('#text-area-comment-' + cancion_id).val();
    if (comentario.length > 255 || comentario.length == 0) {
        $('.invalid-feedback').show();
    } else {
        $('.invalid-feedback').hide();
        $.ajax({
            'method': 'POST',
            url: '/index.php?r=comentarios%2Fcomentar&cancion_id=' + cancion_id,
            data: {
                comentario: comentario,
            },
            success: function (data) {
                $('.row-comments').prepend(`
                    <div class="col-12 mt-3" id="comentario-${data.comentario_id}">
                        <div class="row">
                            <a href="/index.php?r=usuarios%2Fperfil&id=${data.usuario_id}">
                                <img class="user-search-img" src="${data.url_image}" alt="perfil" width="50px" height="50px">
                            </a>
                            <div class="col">
                                <a href="/index.php?r=usuarios%2Fperfil&id=${data.usuario_id}">${data.login}</a>
                                <small class="ml-1 comment-time">${data.created_at}</small>
                                <p>
                                    ${data.comentario}
                                    <button class="btn d-inline outline-transparent delete-comment-btn" data-comentario="${data.comentario_id}"><i class="fas fa-trash text-danger"></i></button>
                                </p>
                            </div>
                        </div>
                    </div>
                `);
                $('.text-area-comment').val('');
            }
        });
    }
});

$('body').on('keydown', '.text-area-comment', function ev(e) {
    let key = (event.keyCode ? event.keyCode : event.which);
    if (key == 13) {
        let cancion_id = $('.comment-btn').attr('id').split('-')[1];
        let comentario = $('#text-area-comment-' + cancion_id).val();
        if (comentario.length > 255 || comentario.length == 0) {
            $('.text-area-comment').html('');
            $('.invalid-feedback').show();
        } else {
            $('.invalid-feedback').hide();
            $.ajax({
                'method': 'POST',
                url: '/index.php?r=comentarios%2Fcomentar&cancion_id=' + cancion_id,
                data: {
                    comentario: comentario,
                },
                success: function (data) {
                    $('.row-comments').prepend(`
                        <div class="col-12 mt-3" id="comentario-${data.comentario_id}">
                            <div class="row">
                                <a href="/index.php?r=usuarios%2Fperfil&id=${data.usuario_id}">
                                    <img class="user-search-img" src="${data.url_image}" alt="perfil" width="50px" height="50px">
                                </a>
                                <div class="col">
                                    <a href="/index.php?r=usuarios%2Fperfil&id=${data.usuario_id}">${data.login}</a>
                                    <small class="ml-1 comment-time">${data.created_at}</small>
                                    <p>
                                        ${data.comentario}
                                        <button class="btn d-inline outline-transparent delete-comment-btn" data-comentario="${data.comentario_id}"><i class="fas fa-trash text-danger"></i></button>
                                    </p>
                                </div>
                            </div>
                        </div>
                    `);
                    $('.text-area-comment').val('');
                }
            });
        }
    }
});

$('body').on('keyup', '.text-area-comment', function ev(e) {
    let longitud = $('.text-area-comment').val().length;
    if (longitud >= 0 && longitud <= 255) {
        $('.character-count').html(longitud);
    } else {
        $('.character-count').effect('bounce');
    }
});

$('body').on('click', '.add-btn', function ev() {
    let cancion_id = $(this).data('song');
    $.ajax({
        method: 'GET',
        url: '/index.php?r=canciones%2Fget-song-data&cancion_id=' + cancion_id,
        success: function (data) {
            $('.alert-box').prepend(`
                <div class="toast mb-2" data-delay="5000">
                    <div class="toast-header">
                        <strong class="mr-auto">MUS!C NOW</strong>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body">
                        <span class="font-weight-bold">${data.titulo}</span>${' ' + data.message}!
                    </div>
                </div>
            `);
            $('.toast').not('.hide').toast('show');
            songs.push({
                url_cancion: data.url_cancion,
                url_portada: data.url_portada,
                titulo: data.titulo,
                album: data.album,
            });
            let audio = $('#audio')[0];
            if (audio.paused) {
                removeActualData();
                initAudioPlayer();
                $('.play-pause-btn').trigger('click');
                addInfoSongQueue();
            }
        }
    });
});

$('body').on('click', '.play-playlist-btn', function ev(e) {
    playlist = [];
    actualSong = 0;
    let playlist_id = $(this).attr('id');
    $.ajax({
        method: 'GET',
        url: '/index.php?r=playlists%2Fget-songs&playlist_id=' + playlist_id,
        success: function(data) {
            data.forEach(element => {
                playlist.push({
                    id: element.id,
                    url_cancion: element.url_cancion,
                    url_portada: element.url_portada,
                    titulo: element.titulo,
                    album: element.album_id,
                });
            });
            removeActualData();
            initAudioPlayer();
            refreshSongPlaylist();
            $('.play-pause-btn').trigger('click');
        }
    })
});

$('body').on('click', '.play-album-btn', function ev(e) {
    playlist = [];
    actualSong = 0;
    let album_id = $(this).attr('id');
    $.ajax({
        method: 'GET',
        url: '/index.php?r=albumes%2Fget-songs&album_id=' + album_id,
        success: function(data) {
            data.forEach(element => {
                playlist.push({
                    id: element.id,
                    url_cancion: element.url_cancion,
                    url_portada: element.url_portada,
                    titulo: element.titulo,
                    album: element.album_id,
                });
            });
            removeActualData();
            initAudioPlayer();
            refreshSongPlaylist();
            $('.play-pause-btn').trigger('click');
        }
    })
});

$('body').on('click', '.add-videoclip-btn', function ev(e) {
    e.preventDefault();
    let link = $('#add-videoclip-form #link').val();
    if (link == '') {
        $('.invalid-videoclip').show();
    } else if (!/.*www.youtube.com\/watch\?v=.{11}\b/.test(link)) {
        $('.invalid-videoclip').show();
    } else {
        $.ajax({
            method: 'POST',
            url: '/index.php?r=videoclips%2Fagregar',
            data: {
                link: link
            },
            success: function (data) {
                if ($('.videoclip-warning').length) {
                    $('.videoclip-warning').remove();
                    $('#videoclips').append(`
                        <div class="row row-videoclips">
                            <div id="video-${data.videoclip.id}" class="col-12 col-lg-6 mb-4 fall-animation">
                                <button data-id="${data.videoclip.id}" class="action-btn remove-videoclip-btn outline-transparent mb-4"><i class="fas fa-trash"></i></button>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item" src="${data.videoclip.link}" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    $('.row-videoclips').prepend(`
                        <div id="video-${data.videoclip.id}" class="col-12 col-lg-6 mb-4 fall-animation">
                            <button data-id="${data.videoclip.id}" class="action-btn remove-videoclip-btn outline-transparent mb-4"><i class="fas fa-trash"></i></button>
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe class="embed-responsive-item" src="${data.videoclip.link}" allowfullscreen></iframe>
                            </div>
                        </div>
                    `);
                }
                $('#add-videoclip-form').trigger("reset");
                $('#videoclip-modal').modal('hide');
                $('.alert-box').prepend(`
                    <div class="toast mb-2" data-delay="5000">
                        <div class="toast-header">
                            <strong class="mr-auto">MUS!C NOW</strong>
                            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="toast-body">
                            <span class="font-weight-bold">¡${data.message}!</span>
                        </div>
                    </div>
                `);
                $('.toast').not('.hide').toast('show');
                $('.invalid-videoclip').hide();

            }
        });
    }
});

function initAudioPlayer() {
    GreenAudioPlayer.init({
        selector: '.player',
        stopOthersOnPlay: true,
        showDownloadButton: true,
    });
}

function addInfoSongQueue() {
    let cancion = songs.shift();
    addNewData(cancion);
    $('.audio-player').css('display', 'flex');
    $('.player').css('display', 'flex');
    let audio = $('#audio')[0];
    audio.addEventListener('ended', () =>  {
        if (songs.length > 0) {
            let cancion = songs.shift();
            addNewData(cancion);
            audio.load();
            $('.play-pause-btn').trigger('click');
        }
    });
}

function refreshSongPlaylist() {
    let cancion = playlist[actualSong];
    addNewData(cancion);
    $('.audio-player').css('display', 'flex');
    $('.player').css('display', 'flex');
    let audio = $('#audio')[0];
    audio.addEventListener('ended', () =>  {
        if (actualSong < playlist.length - 1) {
            actualSong++;
            let cancion = playlist[actualSong];
            addNewData(cancion);
            audio.load();
            $('.play-pause-btn').trigger('click');
        }
    });
}

function addNewData(cancion) {
    $('.info-song img').attr('src', cancion.url_portada);
    $('.player audio source').attr('src', cancion.url_cancion);
    $('.artist-info p').html(cancion.titulo);
    $('.artist-info small').html(cancion.album);
    $.ajax({
        method: 'POST',
        url: '/index.php?r=canciones%2Fadd-visualization',
        data: {
            cancion_id: cancion.id
        }
    });
}

$('body').on('click', '.backward-btn', function ev(e) {
    if (actualSong > 0) {
        actualSong--;
        removeActualData();
        initAudioPlayer();
        let cancion = playlist[actualSong];
        addNewData(cancion);
        $('.play-pause-btn').trigger('click');
        audio.addEventListener('ended', () =>  {
            if (actualSong < playlist.length - 1) {
                actualSong++;
                let cancion = playlist[actualSong];
                addNewData(cancion);
                audio.load();
                $('.play-pause-btn').trigger('click');
            }
        });
    }
});

$('body').on('click', '.forward-btn', function ev(e) {
    if (actualSong < playlist.length - 1) {
        actualSong++;
        removeActualData();
        initAudioPlayer();
        let cancion = playlist[actualSong];
        addNewData(cancion);
        $('.play-pause-btn').trigger('click');
    } else if (songs.length > 0) {
        removeActualData();
        initAudioPlayer();
        let cancion = songs.shift();
        let audio = $('#audio')[0];
        addNewData(cancion);
        audio.load();
        $('.play-pause-btn').trigger('click');
        audio.addEventListener('ended', () =>  {
            if (songs.length > 0) {
                let cancion = songs.shift();
                addNewData(cancion);
                audio.load();
                $('.play-pause-btn').trigger('click');
            }
        });
    }
});

function removeActualData() {
    if ($('.loading').length) {
        $('.loading').remove();
        $('.play-pause-btn').remove();
        $('.controls').remove();
        $('.volume').remove();
        $('.download').remove();
    }
}

if (getCookie('cookie-accept') == null) {
    $( document ).ready(function() {
        let strings = ['CookieMessage'];
        $.ajax({
            method: 'GET',
            url: '/index.php?r=site%2Fget-translate',
            data: {
                strings: strings
            },
            success: function (data) {
                krajeeDialogCust2.confirm(data[0], function (result) {
                    if (result) {
                        window.location="/index.php?r=site%2Fcookie";
                    } else {
                        window.location="http://google.es";
                    }
                });
            }
        })
    });
}

function getFollowersNumber() {
    $.ajax({
        method: 'GET',
        url: '/index.php?r=usuarios%2Fget-followers-data',
        success: function (data) {
            seguidores = data;
        }
    });
}

function getNewNotifications() {
    // NUEVOS SEGUIDORES
    let strings = ['followMessage'];
    $.ajax({
        method: 'GET',
        url: '/index.php?r=site%2Fget-translate',
        data: {
            strings: strings
        },
        success: function (message) {
            $.ajax({
                method: 'GET',
                url: '/index.php?r=usuarios%2Fget-new-followers&total=' + seguidores,
                success: function (data) {
                    if (data.count > seguidores) {
                        data.seguidores.forEach(element => {
                            $('.alert-box').prepend(`
                                <div class="toast mb-2" data-delay="5000">
                                    <div class="toast-header">
                                        <img src="${element.url_image}" class="rounded mr-2 navbar-logo" alt="profile-img">
                                        <strong class="mr-auto">${element.login}</strong>
                                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="toast-body">
                                        ¡<span class="font-weight-bold">${element.login}</span> ${message[0]}!
                                    </div>
                                </div>
                            `);
                        });
                        $('.toast').not('.hide').toast('show');
                    }
                    seguidores = data.count;
                }
            });
        }
    });

    // NUEVOS MENSAJES
    $.ajax({
        method: 'GET',
        url: '/index.php?r=usuarios%2Fget-no-read-messages&total=' + mensajes,
        success: function (data) {
            if (data.count > 0) {
                $('.messages-number').html(data.count);
            } else {
                $('.messages-number').html('');
            }
            if (data.count > mensajes) {
                let notification = $('.chat-notification')[0];
                if (notification.paused) {
                    notification.play();
                }
                data.mensajes.forEach(element => {
                    let time = element.created_at.split(' ')[1];
                    $('.alert-box').prepend(`
                        <div class="toast mb-2" data-delay="5000">
                            <div class="toast-header">
                                <img src="${element.url_image}" class="rounded mr-2 navbar-logo" alt="profile-img">
                                <strong class="mr-auto">${element.login}</strong>
                                <small class="ml-3">${time}</small>
                                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="toast-body">
                                ${element.mensaje}
                            </div>
                        </div>
                    `);
                });
                $('.toast').not('.hide').toast('show');
            }
            mensajes = data.count;
        }
    });

    // NUEVAS SOLICITUDES
    getNewRequests();
}

function getNewRequests() {
    $.ajax({
        method: 'GET',
        url: '/index.php?r=solicitudes-seguimiento%2Fget-total-solicitudes',
        success: function (data) {
            if (data.total != 0) {
                $('.notifications-number').html(data.total);
            };
            if (data.total > solicitudes) {
                $('.alert-box').prepend(`
                    <div class="toast mb-2" data-delay="5000">
                        <div class="toast-header">
                            <strong class="mr-auto">MUS!C NOW</strong>
                            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="toast-body">
                            <span>${data.message}!</span>
                        </div>
                    </div>
                `);
                $('.toast').not('.hide').toast('show');
            }
            else if (data.total == 0) {
                $('.notifications-number').html('');
            }
            solicitudes = data.total;
        }
    });
}

$('body').on('click', '.follow', function ev(e) {
    $.ajax({
        'method': 'POST',
        'url': '/index.php?r=seguidores%2Ffollow&seguido_id=' + $('.user-id').text(),
        success: function (data) {
            $('.follow').html(data.textButton);
            $('#seguidores').html(data.seguidores);
        }
    });
});

function getFollowersData() {
    if ($('.user-id').length) {
        $.ajax({
            'method': 'GET',
            'url': '/index.php?r=seguidores%2Fget-data&seguido_id=' + $('.user-id').text(),
            success: function (data) {
                $('.follow').html(data.textButton);
                $('#seguidores').html(data.seguidores);
                $('#seguidos').html(data.seguidos);
            },
        });
    }
}

$('body').on('click', '.like-list', function ev(e) {
    let cancion_id = $(this).data('song');
    $.ajax({
        method: 'GET',
        url: '/index.php?r=canciones/get-likes&cancion_id=' + cancion_id,
        success: function (data) {
            $('.like-row').html('');
            data.forEach(element => {
                $('.like-row').append(`
                    <div class="col-12">
                            <img src="${element.url_image}" class="d-inline-block user-search-img my-auto" width="30px" alt="like">
                            <p class="d-inline-block my-auto">${element.login}</p>
                    </div>
                `);
            });
        }
    });
});

$('body').on('click', '.remove-videoclip-btn', function ev(e) {
    let id = $(this).data('id');
    let strings = ['Are you sure you want to delete this item?'];
    $.ajax({
        method: 'GET',
        url: '/index.php?r=site%2Fget-translate',
        data: {
            strings: strings
        },
        success: function (data) {
            krajeeDialogCust2.confirm(data[0], function (result) {
                if (result) {
                    $.ajax({
                        method: 'POST',
                        url: '/index.php?r=videoclips%2Feliminar',
                        data: {
                            id: id
                        },
                        success: function (data) {
                            $('#video-' + data).addClass('fall');
                            $('#video-' + data).on('transitionend', function ev(e) {
                                $('#video-' + data).remove();
                            });
                        }
                    });
                }
            });
        }
    });
});

$('body').on('click', '.hide-player', function ev(e) {
    $('.hide-player i').toggleClass('rotate-190');
    $('.full-player').toggle('slide');
});

$('body').on('click', '.filter-btn', function ev(e) {
    $('.filters').toggle('blind');
});

$(window).on('scroll', function () {
    if ($('.owl-carousel-index').length) {
        let scrollHeight = $(document).height();
        let scrollPosition = $(window).height() + $(window).scrollTop();
        if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
            let strings = ['Comment', 'MaxChar'];
            $.ajax({
                    method: 'GET',
                    url: '/index.php?r=site%2Fget-translate',
                    data: {
                        strings: strings
                    },
                    success: function (strings) {
                        $.ajax({
                            method: 'GET',
                            url: '/index.php?r=site%2Fget-more-posts',
                            data: {
                                offset: offset
                            },
                            success: function (data) {
                                offset = offset + 10;
                                data.canciones.forEach(element => {
                                    $('.canciones-container').append(`
                                        <article class="card mb-3" itemscope itemtype="https://schema.org/MusicRecording">
                                            <div class="card-header">
                                                <a href="index.php?r=usuarios%2Fperfil&id=${element.usuario_id}" itemprop="byArtist" itemscope itemtype="https://schema.org/Person">
                                                    <img class="user-search-img" width="40px" alt="logo" src="${element.url_image}" itemprop="image">
                                                    <span class="ml-3" itemprop="name">${element.login}</span>
                                                </a>
                                            </div>
                                            <div class="card-body py-0">
                                                <div class="row">
                                                    <div class="col">
                                                        <div class="song-container mt-3">
                                                            <div class="box-3">
                                                                <img class="img-fluid" alt="portada" src="${element.url_portada}" itemprop="image">
                                                                <div class="share-buttons">
                                                                    <button id="play-${element.id}" class="action-btn play-btn outline-transparent"><i class="fas fa-play"></i></button>
                                                                    <button id="outerlike-${element.id}" class="action-btn outline-transparent bubbly-button like-btn"><i class="${data.likes.includes(element.id) ? 'fas' : 'far'} fa-heart red-hearth"></i></button>
                                                                    <button class="action-btn outline-transparent cancion" data-toggle="modal" data-target="#song-${element.id}"><i class="far fa-comment"></i></button>
                                                                    <button data-song="${element.id}" class="action-btn outline-transparent add-btn"><i class="fas fa-plus"></i></button>
                                                                    <button data-song="${element.id}" data-user="${data.usuario.id}" class="action-btn outline-transparent playlist-btn" data-toggle="modal" data-target="#playlist"><i class="fas fa-music"></i></button>
                                                                </div>
                                                                <div class="layer"></div>
                                                            </div>
                                                        </div>
                                                        <div class="w-100 my-3 text-truncate">
                                                            <h4 class="my-2" itemprop="name">${element.titulo}</h4>
                                                            ${(element.album_id !== null) ?
                                                                `<div class="my-2">
                                                                    <div class="w-100"></div>
                                                                    <span itemprop="inAlbum">${element.album_titulo}</span>
                                                                </div>` : ''
                                                            }
                                                            ${(element.explicit) ?
                                                                `<div class="my-2">
                                                                    <div class="w-100"></div>
                                                                    <span class="badge explicit-badge">EXPLICIT</span>
                                                                </div>` : ''
                                                            }
                                                        </div>
                                                        <div class="modal fade" id="song-${element.id}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-8">
                                                                                <div class="row">
                                                                                    <img class="img-fluid col-12" alt="profile-image" src="${element.url_portada}">
                                                                                    <div class="col-12 mt-4">
                                                                                        <input id="text-area-comment-${element.id}" class="form-control text-area-comment" cols="30" rows="3" placeholder="${strings[0]}"></input>
                                                                                        <div class="invalid-feedback">${strings[1]}</div>
                                                                                        <div class="mt-3">
                                                                                            <button class="btn btn-sm main-yellow comment-btn" id="comment-${element.id}" type="button">${strings[0]}</button>
                                                                                            <button type="button" id="like-${element.id}" class="btn-lg outline-transparent d-inline-block like-btn p-0 mx-2"><i class="fa-heart red-hearth"></i></button>
                                                                                            <p class="d-inline-block"><span></span> like/s</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-4 ">
                                                                                <div class="row">
                                                                                    <div class="col-12 custom-overflow">
                                                                                        <!-- COMENTARIOS  -->
                                                                                        <div class="row row-comments">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                    `);
                                });
                            }
                        });
                    }
            });
        }
    } else {
        offset = 10;
    }
});

$('body').on('submit', '.create-form', function ev(e) {
    $('.form-group button').html('<i class="fas fa-spinner"></i>');
});

$('body').on('keyup', '.usuarios-update input', function ev(e) {
    let target = $(this).data('target');
    if (target !== undefined && $('.update-' + target).length) {
        $('.update-' + target).text($(this).val());
    }
});

$('body').on('click', '.delete-comment-btn', function ev(e) {
    let comentario_id = $(this).data('comentario');
    let strings = ['Are you sure you want to delete this item?'];
    $.ajax({
        method: 'GET',
        url: '/index.php?r=site%2Fget-translate',
        data: {
            strings: strings,
        },
        success: function(strings) {
            krajeeDialogCust2.confirm(strings[0], function (result) {
                if (result) {
                    $.ajax({
                        method: 'POST',
                        url: '/index.php?r=comentarios%2Fdelete&id=' + comentario_id,
                        success: function (data) {
                            $('.row-comments #comentario-' + comentario_id).remove();
                        }
                    });
                }
            });
        }
    });
});

function getStatusFromUsers() {
    $.ajax({
        method: 'GET',
        url: '/index.php?r=usuarios%2Festados',
        success: function (data) {
            data.forEach(element => {
                let id = element.id;
                if (element.estado_id == 'online') {
                    $('#' + id + ' .status').removeClass('badge-danger');
                    $('#' + id + ' .status').addClass('badge-success');
                } else {
                    $('#' + id + ' .status').removeClass('badge-success');
                    $('#' + id + ' .status').addClass('badge-danger');
                }
                $('#' + id + ' .status').html(element.estado_id);
                getNoReadMessages(id);
            });
        }
    });
}

function getNoReadMessages(receptor_id) {
    $.ajax({
        method: 'GET',
        url: '/index.php?r=usuarios%2Fget-no-read-messages&receptor_id=' + receptor_id,
        success: function (data) {
            if (data != 0) {
                $('#messages-number-' + receptor_id).html(data);
            } else {
                $('#messages-number-' + receptor_id).html('');
            }
        }
    });
}

function getMessagesFromChat(receptor_id, refresh) {
    $.ajax({
        method: 'POST',
        url: '/index.php?r=chat%2Fget-chat&receptor_id=' + receptor_id + '&refresh=' + refresh,
        success: function (data) {
            $('#chat-history-' + receptor_id).html('');
            data.historial.forEach(element => {
                if (element.emisor_id != receptor_id) {
                    $('#chat-history-' + receptor_id).append(`
                        <p class="ml-5 message my-message">${element.mensaje}<small class="pl-2">${element.created_at}<i class="fas fa-check-double pl-2 read-tick"></i></small></p>
                    `);
                    if (element.estado_id == 4) {
                        $('.read-tick').addClass('read-message');
                    }
                } else {
                    $('#chat-history-' + receptor_id).append(`
                        <p class="mr-5 message other-message">${element.mensaje}<small class="pl-2">${element.created_at}</small></p>
                    `);
                }
            });
        }
    });
}

function updateChatHistory(id) {
    getMessagesFromChat(id, false);
}

$('body').on('click', '.send-chat', function ev(e) {
    let receptor_id = $(this).attr('id');
    let mensaje = $('#chat-message-' + receptor_id).val().trim();
    $.ajax({
        method: 'POST',
        url: '/index.php?r=chat%2Fsend-chat',
        data: {
            receptor_id: receptor_id,
            mensaje: mensaje
        },
        success: function(data) {
            $('#chat-message-' + receptor_id).val('');
            $('#chat-history-' + receptor_id).html('')
            data.forEach(element => {
                if (element.emisor_id != receptor_id) {
                    $('#chat-history-' + receptor_id).append(`
                        <p class="ml-5 message my-message">${element.mensaje}<small class="pl-2">${element.created_at}<i class="fas fa-check-double pl-2 read-tick"></i></small></p>
                    `);
                    if (element.estado_id == 4) {
                        $('.read-tick').addClass('read-message');
                    }
                } else {
                    $('#chat-history-' + receptor_id).append(`
                        <p class="mr-5 message other-message">${element.mensaje}<small class="pl-2">${element.created_at}</small></p>
                    `);
                }
            });
            $('#chat-history-' + receptor_id).scrollTop($('#chat-history-' + receptor_id)[0].scrollHeight);
        }
    });
});

$('body').on('keydown', '.chat-input', function ev(e) {
    let key = (event.keyCode ? event.keyCode : event.which);
    if (key == 13) {
        let receptor_id = $(this).attr('id').split('-')[2];
        let mensaje = $('#chat-message-' + receptor_id).val().trim();
        $.ajax({
            method: 'POST',
            url: '/index.php?r=chat%2Fsend-chat',
            data: {
                receptor_id: receptor_id,
                mensaje: mensaje
            },
            success: function(data) {
                $('#chat-message-' + receptor_id).val('');
                $('#chat-history-' + receptor_id).html('')
                data.forEach(element => {
                    if (element.emisor_id != receptor_id) {
                        $('#chat-history-' + receptor_id).append(`
                            <p class="ml-5  message my-message">${element.mensaje}<small class="pl-2">${element.created_at}<i class="fas fa-check-double pl-2 read-tick"></i></small></p>
                        `);
                        if (element.estado_id == 4) {
                            $('.read-tick').addClass('read-message');
                        }
                    } else {
                        $('#chat-history-' + receptor_id).append(`
                            <p class="mr-5 message other-message">${element.mensaje}<small class="pl-2">${element.created_at}</small></p>
                        `);
                    }
                });
                $('#chat-history-' + receptor_id).scrollTop($('#chat-history-' + receptor_id)[0].scrollHeight);
            }
        });
    }
});

$('body').on('click', '.start-chat', function ev(e) {
    let receptor_id = $(this).data('receptorid');
    getMessagesFromChat(receptor_id, true);
    $('.send-chat').trigger('click');
});

$('body').on('keyup', '#search-users', function ev(e) {
    let text = $(this).val();
    if (text != '') {
        $('.chat-list').hide();
        $.ajax({
            method: 'GET',
            url: '/index.php?r=chat%2Fget-users',
            data: {
                text: text
            },
            success: function (data) {
                $('.search-box').empty();
                data.forEach(element => {
                    $('.search-box').append(`
                    <div id="${element.id}" class="col-12 mb-5">
                        <img src="${element.url_image}" class="user-search-img" width="40px" alt="logo"?>
                        <h4 class="d-inline-block">${element.login}</h4>
                        <span class="status badge ${(element.estado_id == 'offline') ? 'badge-danger' : 'badge-success'} d-inline-block">${element.estado_id}</span>
                        <span class="badge badge-warning" id="messages-number-${element.id}"></span>
                        <button class="btn main-yellow start-chat" data-receptorid="${element.id}" data-toggle="modal" data-target="#chat-${element.id}">Chat</button>
                        <div class="modal fade chat" id="chat-${element.id}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <img src="${element.url_image}" class="user-search-img" width="40px" alt="logo"?>
                                        <h5 class="modal-title my-auto ml-3">${element.login}</h5>
                                        <span class="status badge ${(element.estado_id == 'offline') ? 'badge-danger' : 'badge-success'} my-auto ml-3">${element.estado_id}</span>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true" class="text-white"><i class="fas fa-times"></i></span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="user-chat-${element.id}" class="user-chat">
                                            <div class="chat-history custom-overflow pr-2 pt-2" data-receptorid="${element.id}" id="chat-history-${element.id}">
                                            </div>
                                            <div class="form-group">
                                                <input type="text" name="chat-message${element.id}" id="chat-message-${element.id}" class="form-control chat-input">
                                            </div>
                                            <div class="form-group">
                                                <button type="button" id="${element.id}" class="btn main-yellow send-chat">Send</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    `);
                });
            }
        });
    } else {
        $('.chat-list').show();
        $('.search-box').empty();
    }
});

$('body').on('click', '.delete-follow-btn', function ev(e) {
    let seguidor_id = $(this).data('follower_id');
    let strings = ['Are you sure you want to delete this item?'];
    $.ajax({
        method: 'GET',
        url: '/index.php?r=site%2Fget-translate',
        data: {
            strings: strings
        },
        success: function (data) {
            krajeeDialogCust2.confirm(data[0], function (result) {
                if (result) {
                    $.ajax({
                        method: 'POST',
                        url: '/index.php?r=seguidores%2Fdelete-follower&seguidor_id=' + seguidor_id,
                        success: function (data) {
                            $('#follower-' + seguidor_id).addClass('fall');
                            $('#follower-' + seguidor_id).on('transitionend', function ev(e){
                                $('#follower-' + seguidor_id).remove();
                                getFollowersData();
                            });
                        }
                    });
                }
            });
        }
    });
});

$('body').on('click', '.delete-song-playlist-btn', function ev(e) {
    let cancion_id = $(this).data('song-id');
    let playlist_id = $('.playlist-id').text();
    let strings = ['DeleteSongName'];
    $.ajax({
        method: 'GET',
        url: '/index.php?r=site%2Fget-translate',
        data: {
            strings: strings
        },
        success: function (data) {
            let message = data[0] + ' ' + $('#song-' + cancion_id + ' h5').text() + '?';
            krajeeDialogCust2.confirm(message, function (result) {
                if (result) {
                    $.ajax({
                        method: 'POST',
                        url: '/index.php?r=canciones-playlist%2Fdelete&cancion_id=' + cancion_id + '&playlist_id=' + playlist_id,
                        success: function (data) {
                            $('#song-' + cancion_id).addClass('fall');
                            $('#song-' + cancion_id).on('transitionend', function ev(e){
                                $('#song-' + cancion_id).remove();
                            });
                        }
                    });
                }
            });
        }
    });
});

$('body').on('change', '.is-album-check', function ev(e) {
    if ($(this).prop('checked')) {
        $('.field-canciones-album_id').show();
        $('#canciones-album_id').attr('name', 'Canciones[album_id]');
        $('#canciones-album_id').prev().attr('name', 'Canciones[album_id]');
        $('#canciones-portada').removeAttr('name');
        $('#canciones-portada').prev().removeAttr('name');
        $('.field-canciones-portada').hide();
    } else {
        $('.field-canciones-album_id').hide();
        $('#canciones-album_id').removeAttr('name');
        $('#canciones-album_id').prev().removeAttr('name');
        $('#canciones-portada').attr('name', 'Canciones[portada]');
        $('#canciones-portada').prev().attr('name', 'Canciones[portada]');
        $('.field-canciones-portada').show();
    }
});

$('body').on('click', '.copy-playlist-btn', function ev(e) {
    let id = $(this).attr('id');
    $.ajax({
        method: 'POST',
        url: '/index.php?r=playlists%2Fcopiar',
        data: {
            id: id
        },
        success: function(data) {
            $('.alert-box').prepend(`
                <div class="toast mb-2" data-delay="5000">
                    <div class="toast-header">
                        <strong class="mr-auto">MUS!C NOW</strong>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="toast-body">
                        <span class="font-weight-bold">${data}</span>!
                    </div>
                </div>
            `);
            $('.toast').not('.hide').toast('show');
        }
    });
});

$('body').on('change', '.song-file-input', function ev(e) {
    let target = e.currentTarget;
    let file = target.files[0];
    let reader = new FileReader();
    if (target.files && file) {
        let reader = new FileReader();

        reader.onload = function (e) {
            audio.src = e.target.result;
            audio.addEventListener('loadedmetadata', function(){
                let duration = audio.duration;
                $('#canciones-duracion').val(parseInt(duration));
                let minutes =  Math.floor(duration / 60);
                let seconds = Math.trunc(duration % 60);
                $('#duration-hidden').val(`PT${minutes}M${seconds}S`);
                $('#canciones-duracion').val(`${minutes} m ${seconds} s`);
            },false);
        };

        reader.readAsDataURL(file);
    }
});

$('body').on('click', '.request-btn', function ev(e) {
    let seguidor_id = $(this).data('id');
    let type = '';
    if ($(this).hasClass('accept')) {
        type = 'accept';
    } else if ($(this).hasClass('delete')) {
        type = 'delete';
    }
    $.ajax({
        method: 'POST',
        url: '/index.php?r=seguidores%2Fsolicitud',
        data: {
            seguidor_id: seguidor_id,
            type: type,
        },
        success: function (data) {
            $('#notificacion-' + seguidor_id).addClass('fall');
            getNewRequests();
            $('#notificacion-' + seguidor_id).on('transitionend', function ev(e) {
                $('#notificacion-' + seguidor_id).remove();
            });
        }
    });
});

$('body').on('click', '.btn-toggle-sidebar', function ev(e) {
    $('#sidebar a.nav-link span').toggle();
    if ($('#sidebar a.nav-link span').is(':hidden')) {
        $('.btn-toggle-sidebar i').removeClass('fa-times');
        $('.btn-toggle-sidebar i').addClass('fa-bars');
        $('#sidebar a').css('width', 'fit-content');
    } else {
        $('.btn-toggle-sidebar i').removeClass('fa-bars');
        $('.btn-toggle-sidebar i').addClass('fa-times');
        $('#sidebar a').css('width', '100%');
    }
});
