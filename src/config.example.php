<?php

declare(strict_types=1);

return [
    /*
        Blocklist pattern (enabled by default)
        used to preprocess the autosuggest list of all environment variable names
        to remove sensitive values
    */
    // 'excludePattern' => '/^\$(CONTENT|DOCUMENT|DB|HTTP|SERVER|REDIS|GPG|SCRIPT|REMOTE|REQUEST|PHP|PHPIZE|PATH|SECURITY|FCGI)_|^\$(GATEWAY_INTERFACE|HOME|PATH|PWD|REMOTE|TERM|USER)$/i',
    // Clearlist pattern for including environment variable names, ignored if $excludePattern is not falsy
    // 'includePattern' => null
];
