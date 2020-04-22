var mensajes = 0;
var seguidores = 0;
var songs = [];
var actualSong = 0;
var playlist = [];
var offset = 10;

checkTheme();

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

// me quedo aqui justo pego esto
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
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
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

var firstTime = true;
$('body').on('click', '.play-btn', function ev(e) {
    songs = [];
    var cancion_id = $(this).attr('id').split('-')[1];
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
            var audio = $('#audio')[0];
            audio.addEventListener('ended', () =>  {
                if (songs.length > 0) {
                    var cancion = songs.shift();
                    $('.info-song img').attr('src', cancion.url_portada);
                    $('.player audio source').attr('src', cancion.url_cancion);
                    $('.artist-info p').html(cancion.titulo);
                    $('.artist-info small').html(cancion.album);
                    $('.play-pause-btn').trigger('click');
                }
            });
        }
    });
});

$('body').on('click', '.like-btn', function ev(e) {
    var cancion_id = $(this).attr('id').split('-')[1];
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
    var cancion_id = $(this).data('target').split('-')[1];
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
            var comentarios = Object.entries(data);
            $('.row-comments').empty();
            comentarios.forEach(element => {
                $('.row-comments').append(`
                    <div class="col-12 mt-3">
                        <div class="row">
                            <a href="/index.php?r=usuarios%2Fperfil&id=${element[1].id}">
                                <img class="user-search-img" src="${element[1].url_image}" alt="perfil" width="50px" height="50px">
                            </a>
                            <div class="col">
                                <a href="/index.php?r=usuarios%2Fperfil&id=${element[1].id}">${element[1].login}</a>
                                <small class="ml-1 comment-time">${element[1].created_at}</small>
                                <p class="m-0">${element[1].comentario}</p>
                            </div>
                        </div>
                    </div>
                `);
            });
        }
    });

});

$('body').on('click', '.playlist-btn', function ev(e) {
    var usuario_id = $(this).data('user');
    var cancion_id = $(this).data('song');
    $.ajax({
        method: 'GET',
        url: '/index.php?r=usuarios%2Fget-playlists&usuario_id=' + usuario_id,
        success: function (data) {
            $('.row-playlists').html('');
            data.forEach(element => {
                $('.row-playlists').append(`
                    <div class="col-12">
                        <h3 class="d-inline-block">${element.titulo}</h3>
                        <button data-song="${cancion_id}" data-playlist="${element.id}" class="ml-auto action-btn outline-transparent add-playlist-btn"><i class="far fa-plus-square"></i></button>
                    </div>
                `);
            });
            $('.add-playlist-btn').on('click', function ev(e) {
                var cancion_id = $(this).data('song');
                var playlist_id = $(this).data('playlist');
                $.ajax({
                    method: 'POST',
                    url: '/index.php?r=canciones-playlist%2Fagregar',
                    data: {
                        cancion_id: cancion_id,
                        playlist_id: playlist_id,
                    },
                    success: function (data) {
                        console.log(data);
                    }
                });
            });
        }
    });
});

$('body').on('click', '.comment-btn', function ev(e) {
    var cancion_id = $(this).attr('id').split('-')[1];
    var comentario = $('#text-area-comment-' + cancion_id).val();
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
                    <div class="col-12 mt-3">
                        <div class="row">
                            <a href="/index.php?r=usuarios%2Fperfil&id=${data.usuario_id}">
                                <img class="user-search-img" src="${data.url_image}" alt="perfil" width="50px" height="50px">
                            </a>
                            <div class="col">
                                <a href="/index.php?r=usuarios%2Fperfil&id=${data.usuario_id}">${data.login}</a>
                                <small class="ml-1 comment-time">${data.created_at}</small>
                                <p>${data.comentario}</p>
                            </div>
                        </div>
                    </div>
                `);
                $('.text-area-comment').val('');
            }
        });
    }
});

$('body').on('click', '.add-btn', function ev() {
    var cancion_id = $(this).data('song');
    $.ajax({
        method: 'GET',
        url: '/index.php?r=canciones%2Fget-song-data&cancion_id=' + cancion_id,
        success: function (data) {
            songs.push({
                url_cancion: data.url_cancion,
                url_portada: data.url_portada,
                titulo: data.titulo,
                album: data.album,
            });
            var audio = $('#audio')[0];
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
    var playlist_id = $(this).attr('id');
    $.ajax({
        method: 'GET',
        url: '/index.php?r=canciones-playlist%2Fget-songs&playlist_id=' + playlist_id,
        success: function(data) {
            data.forEach(element => {
                playlist.push({
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
    var link = $('#add-videoclip-form #link').val();
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
                        <div id="video-${data.id}" class="col-12 col-lg-6 mb-4 fall-animation">
                            <button data-id="${data.id}" class="action-btn remove-videoclip-btn outline-transparent mb-4"><i class="fas fa-trash"></i></button>
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe class="embed-responsive-item" src="${data.link}" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                `);
            } else {
                $('.row-videoclips').prepend(`
                    <div id="video-${data.id}" class="col-12 col-lg-6 mb-4 fall-animation">
                        <button data-id="${data.id}" class="action-btn remove-videoclip-btn outline-transparent mb-4"><i class="fas fa-trash"></i></button>
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" src="${data.link}" allowfullscreen></iframe>
                        </div>
                    </div>
                `);
            }
            $('#add-videoclip-form').trigger("reset");
            $('#videoclip-modal').modal('hide');
        }
    });
});

function initAudioPlayer() {
    GreenAudioPlayer.init({
        selector: '.player',
        stopOthersOnPlay: true,
        showDownloadButton: true,
    });
}

function addInfoSongQueue() {
    var cancion = songs.shift();
    addNewData(cancion);
    $('.audio-player').css('display', 'flex');
    $('.player').css('display', 'flex');
    var audio = $('#audio')[0];
    audio.addEventListener('ended', () =>  {
        if (songs.length > 0) {
            var cancion = songs.shift();
            addNewData(cancion);
            $('.play-pause-btn').trigger('click');
        }
    });
}

function refreshSongPlaylist() {
    var cancion = playlist[actualSong];
    addNewData(cancion);
    $('.audio-player').css('display', 'flex');
    $('.player').css('display', 'flex');
    var audio = $('#audio')[0];
    audio.addEventListener('ended', () =>  {
        if (actualSong < playlist.length - 1) {
            actualSong++;
            var cancion = playlist[actualSong];
            addNewData(cancion);
            $('.play-pause-btn').trigger('click');
        }
    });
}

function addNewData(cancion) {
    $('.info-song img').attr('src', cancion.url_portada);
    $('.player audio source').attr('src', cancion.url_cancion);
    $('.artist-info p').html(cancion.titulo);
    $('.artist-info small').html(cancion.album);
}

$('body').on('click', '.backward-btn', function ev(e) {
    if (actualSong > 0) {
        actualSong--;
        removeActualData();
        initAudioPlayer();
        var cancion = playlist[actualSong];
        addNewData(cancion);
        $('.play-pause-btn').trigger('click');
        audio.addEventListener('ended', () =>  {
            if (actualSong < playlist.length - 1) {
                actualSong++;
                var cancion = playlist[actualSong];
                addNewData(cancion);
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
        var cancion = playlist[actualSong];
        addNewData(cancion);
        $('.play-pause-btn').trigger('click');
    } else if (songs.length > 0) {
        removeActualData();
        initAudioPlayer();
        var cancion = songs.shift();
        addNewData(cancion);
        var audio = $('#audio')[0];
        $('.play-pause-btn').trigger('click');
        audio.addEventListener('ended', () =>  {
            if (songs.length > 0) {
                var cancion = songs.shift();
                addNewData(cancion);
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
        var strings = ['CookieMessage'];
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
    var strings = ['followMessage'];
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
                        $('.alert-box').html('');
                        data.seguidores.forEach(element => {
                            $('.alert-box').append(`
                                    <a href="/index.php?r=usuarios/perfil&id=${element.id}" class="text-decoration-none">
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
                                    </a>
                            `);
                        });
                        $('.toast').toast('show');
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
                $('.chat-notification').trigger('play');
                $('.alert-box').html('');
                data.mensajes.forEach(element => {
                    var time = element.created_at.split(' ')[1];
                    $('.alert-box').append(`
                        <a href="/index.php?r=chat/chat" class="text-decoration-none">
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
                        </a>
                    `);
                });
                $('.toast').toast('show');
            }
            mensajes = data.count;
        }
    });
}

$(window).on('pjax:start', function (){
    setTimeout(function () {
        getFollowersData()
        checkTheme();
        $(".owl-carousel-index").owlCarousel({
            loop: true,
            autoplay:true,
            autoplayTimeout:4000,
            items : 1
        });
    }, 500);
});

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
    var cancion_id = $(this).data('song');
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
    var id = $(this).data('id');
    var strings = ['Are you sure you want to delete this item?'];
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
        var scrollHeight = $(document).height();
        var scrollPosition = $(window).height() + $(window).scrollTop();
        if ((scrollHeight - scrollPosition) / scrollHeight === 0) {
            var strings = ['Comment', 'MaxChar'];
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
                                        <div class="row">
                                            <div class="col">
                                                <div class="song-container">
                                                    <div class="box-3">
                                                        <img class="img-fluid" alt="portada" src="${element.url_portada}">
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
                                                <h4 class="text-center mt-3 mb-5">${element.titulo}</h4>
                                                <div class="modal fade" id="song-${element.id}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-lg-8">
                                                                        <div class="row">
                                                                            <img class="img-fluid col-12" alt="profile-image" src="${element.url_portada}">
                                                                            <div class="col-12 mt-4">
                                                                                <textarea id="text-area-comment-${element.id}" class="form-control text-area-comment" cols="30" rows="3" placeholder="${strings[0]}"></textarea>
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
                                                <div class="modal fade" id="playlist" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-body">
                                                                <h2 class="text-center">Playlists</h2>
                                                                <div class="row row-playlists">
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