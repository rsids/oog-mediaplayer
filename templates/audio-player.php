<?php
echo <<<OOG
    <div class="oog-player-audio">
        <audio src="{$media->url}">
            Your browser doesn't support the <code>audio</code> element.
            Download the file <a href="{$media->url}">here</a>
            </audio>
        <a class="play-pause-button" data-src="{$media->url}">
            <span class="timer"></span>
            <span>{$media->title}&nbsp;<span>
        </a>
        <div class="duration">
            <div class="buffered"></div>
            <div class="progress"></div>
        </div>
    </div>
OOG;
