var mensajes = 0;
var seguidores = 0;
var songs = [];
var actualSong = 0;
var playlist = [];

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
$('.play-btn').on('click', function ev(e) {
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
            $('.full-player').css('display', 'flex');
            if (firstTime) {
                $('.full-player').effect('slide','slow');
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

$('.like-btn').on('click', function ev(e) {
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

$('.cancion').on('click', function ev(e) {
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

$('.playlist-btn').on('click', function ev(e) {
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

$('.comment-btn').on('click', function ev(e) {
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
    $('.full-player').css('display', 'flex');
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
    $('.full-player').css('display', 'flex');
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