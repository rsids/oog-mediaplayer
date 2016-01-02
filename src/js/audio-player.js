(function($) {
	var players,
        player,
        current,
        currentFormatted,
        animFrame;

	$(document).ready(function() {

		players = document.querySelectorAll('div.oog-player-audio audio');
		if(!players) {
			return;
		}
        var numPlayers = players.length;

        while(--numPlayers >= 0) {
            player = players.item(numPlayers);

            player.addEventListener('durationchange', function(evt) {
                var container = getContainer(evt.currentTarget);
                $(container).attr('duration', evt.currentTarget.duration);
                $('.oog-player-audio-timer', container).text(formatTime(evt.currentTarget.duration));
            });

            player.addEventListener('progress', function(evt) {
                var container = getContainer(evt.currentTarget);
                if(evt.currentTarget.buffered.length == 0) {
                    return;
                }
                $(container).attr('load-progress', evt.currentTarget.buffered.end(0));

            });

            player.addEventListener('timeupdate', function(evt) {
                var container = getContainer(evt.currentTarget);
                current = evt.currentTarget.currentTime;

                if(currentFormatted !== formatTime(current)) {
                    currentFormatted = formatTime(current);
                    $('.oog-player-audio-timer', container).text(currentFormatted);
                }
            });
            player.addEventListener('ended', function() {
                cancelAnimFrame(animFrame);
            });

            player.addEventListener('play', function() {
                animFrame = requestAnimFrame(updateSeekBar);
            });

			// Get title if not set
			var container = getContainer(player);
			if(container.querySelector('.oog-player-audio-title').textContent === '') {
            	getTitle(player.src, container);
			}
        }


		$('a.play-pause-button').on('click', function(evt) {
            evt.preventDefault();
			var container = getContainer(evt.currentTarget),
                player = $('audio', container)[0];

            $('div.oog-player-audio').removeClass('pause active');
            if(player.paused) {
                pauseAllPlayersExcept(player);
                $(container).addClass('pause active');
                player.play();
            } else {
                player.pause();
            }

		});

		$('.oog-player-audio').on('click', '.oog-player-audio-duration', seek);

	});

	function seek(evt) {
		var container = getContainer(evt.currentTarget),
            pos = evt.offsetX / evt.currentTarget.clientWidth;

        if(pos < 0 || pos > 1) {
			return;
        }

        player = $('audio', container)[0];
		player.currentTime = parseFloat($(container).attr('duration')) * pos;
	}

	function updateSeekBar() {
        var container = $('div.oog-player-audio.active'),
            duration = parseFloat(container.attr('duration')),
            loadProgress = parseFloat(container.attr('load-progress'));

		$('.buffered', container).width((loadProgress / duration) * 100 + '%');
		$('.progress', container).width((current / duration) * 100 + '%');

        animFrame = requestAnimFrame(updateSeekBar);
	}

    function getContainer(item) {
        return $(item).closest('div.oog-player-audio');
    }

    function getTitle(url, container) {
        var data = {
            action: 'oog_media_player_get_audio_title',
            file: url
        };
        $.post('/wp-admin/admin-ajax.php', data).success(function(title) {
            $('.oog-player-audio-title', container).text(title);
        });
    }

    function formatTime(time) {
        time = Math.round(time);
        var minutes = '0' + Math.floor(time / 60);
        var seconds = '0' + (time - minutes * 60);
        return minutes.substr(-2) + ":" + seconds.substr(-2);
    }

    /**
     * Pauses all players on the page
     * @param player
     */
    function pauseAllPlayersExcept(player) {
        var np = players.length;

        while(--np >= 0) {
            if(players.item(np) !== player && !players.item(np).paused) {
                players.item(np).pause();
            }
        }
    }

	// Backwords compatibility layer
	window.requestAnimFrame = function(){
		return (
			window.requestAnimationFrame       ||
				window.webkitRequestAnimationFrame ||
				window.mozRequestAnimationFrame    ||
				window.oRequestAnimationFrame      ||
				window.msRequestAnimationFrame     ||
				function(/* function */ callback){
					window.setTimeout(callback, 1000 / 60);
				}
			);
	}();

	window.cancelAnimFrame = function(){
		return (
			window.cancelrequestAnimationFrame       ||
				window.cancelAnimationFrame       ||
				window.webkitCancelRequestAnimationFrame ||
				window.mozCancelRequestAnimationFrame    ||
				window.oCancelRequestAnimationFrame      ||
				window.msCancelRequestAnimationFrame     ||
				function(id){
					window.clearTimeout(id);
				}
			);
	}();

})(jQuery);
