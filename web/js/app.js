var mensajes = 0;
var seguidores;
var songs = [];
var actualSong = 0;
var playlist = [];
var offset = 10;
var interval;

checkTheme();
getStatusFromUsers();

$('body').on('show.bs.modal', '.modal', function (e) {
    interval = setInterval(function(){
        updateChatHistory();
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
            $('.row-comments').empty();
            data.comentarios.forEach(element => {
                $('.row-comments').append(`
                    <div class="col-12 mt-3" id="${element.comentario_id}">
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
                    <div class="col-12 mt-3" id="${data.comentario_id}">
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
    var target = $(this).data('target');
    if (target !== undefined && $('.update-' + target).length) {
        $('.update-' + target).text($(this).val());
    }
});

$('body').on('click', '.delete-comment-btn', function ev(e) {
    var comentario_id = $(this).data('comentario');
    var strings = ['Are you sure you want to delete this item?'];
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
                            $('.row-comments #' + comentario_id).remove();
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
                var id = element.id;
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

function updateChatHistory() {
    $('.chat-history').each(function() {
        var receptor_id = $(this).data('receptorid');
        getMessagesFromChat(receptor_id, false);
    });
}

$('body').on('click', '.send-chat', function ev(e) {
    var receptor_id = $(this).attr('id');
    var mensaje = $('#chat-message-' + receptor_id).val().trim();
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
    var key = (event.keyCode ? event.keyCode : event.which);
    if (key == 13) {
        var receptor_id = $(this).attr('id').split('-')[2];
        var mensaje = $('#chat-message-' + receptor_id).val().trim();
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
    var receptor_id = $(this).data('receptorid');
    getMessagesFromChat(receptor_id, true);
    $('.send-chat').trigger('click');
});

$('body').on('keyup', '#search-users', function ev(e) {
    var text = $(this).val();
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
                        <span class="status badge badge-success d-inline-block">${element.estado_id}</span>
                        <span class="badge badge-warning" id="messages-number-${element.id}"></span>
                        <button class="btn main-yellow start-chat" data-receptorid="${element.id}" data-toggle="modal" data-target="#chat-${element.id}">Chat</button>
                        <div class="modal fade" id="chat-${element.id}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <img src="${element.url_image}" class="user-search-img" width="40px" alt="logo"?>
                                        <h5 class="modal-title my-auto ml-3">${element.login}</h5>
                                        <span class="status badge badge-success my-auto ml-3">${element.estado_id}</span>
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
    var seguidor_id = $(this).data('follower_id');
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
    var cancion_id = $(this).data('song-id');
    var playlist_id = $('.playlist-id').text();
    var strings = ['DeleteSongName'];
    $.ajax({
        method: 'GET',
        url: '/index.php?r=site%2Fget-translate',
        data: {
            strings: strings
        },
        success: function (data) {
            var message = data[0] + ' ' + $('#' + cancion_id + ' h5').text() + '?';
            krajeeDialogCust2.confirm(message, function (result) {
                if (result) {
                    $.ajax({
                        method: 'POST',
                        url: '/index.php?r=canciones-playlist%2Fdelete&cancion_id=' + cancion_id + '&playlist_id=' + playlist_id,
                        success: function (data) {
                            $('#' + cancion_id).addClass('fall');
                            $('#' + cancion_id).on('transitionend', function ev(e){
                                $('#' + cancion_id).remove();
                            });
                        }
                    });
                }
            });
        }
    });
});