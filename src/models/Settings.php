<?php

declare(strict_types=1);

namespace justenough\envfield\models;

use craft\base\Model;

class Settings extends Model
{
    /*
        Blocklist pattern (enabled by default)
        used to preprocess the autosuggest list of all environment variable names
        to remove sensitive values
    */
    public ?string $excludePattern = '/^\$(CONTENT|DOCUMENT|DB|HTTP|SERVER|REDIS|GPG|SCRIPT|REMOTE|REQUEST|PHP|PHPIZE|PATH|SECURITY|FCGI)_|^\$(GATEWAY_INTERFACE|HOME|PATH|PWD|REMOTE|TERM|USER)$/i';
    /* Clearlist pattern

        Regex used to preprocess the autosuggest list of all environment variable names to allow only specific values / prefixes into the autosuggest list

        (not enabled by default, set `excludePattern` to falsy to use)
    */
    public ?string $includePattern = null;
}
