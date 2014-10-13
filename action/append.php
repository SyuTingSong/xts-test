<?php

function append() {
    echo 'echo';
    X::apple()->end('abc');
    // @codeCoverageIgnoreStart
    echo 'not echo';
    // @codeCoverageIgnoreEnd
}